<?php

namespace App\Services\Intelligence;

use App\Models\Company;
use App\Services\Intelligence\Providers\LlmProviderInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Capa central "Invenza Intelligence", portada de invenza-desarrollo (el
 * ERP). Cualquier funcion futura de este producto llama a run() en vez de
 * hablar directo con el proveedor de IA - asi se comparte el control de
 * presupuesto/activacion por empresa y el log de uso (ai_usage_records).
 *
 * Diferencia real frente al ERP: aqui no existe una tabla de settings
 * separada por empresa - is_enabled/monthly_budget_usd viven como columnas
 * directas en `companies` (mismo patron ya usado en este repo para
 * max_users), asi que se lee el modelo Company directo.
 *
 * El gasto mensual se calcula EN VIVO sumando ai_usage_records del mes
 * actual (no se cachea un contador).
 */
class IntelligenceService
{
    public function __construct(private LlmProviderInterface $provider)
    {
    }

    public function run(
        int $companyId,
        string $module,
        string $task,
        string $systemPrompt,
        string $userPrompt,
        array $options = []
    ): array {
        $company = Company::find($companyId);

        if (!$company || !$company->intelligenceEnabled()) {
            $this->logUsage($companyId, $module, $task, $options, 0, 0, 0, 'blocked_disabled',
                'Invenza Intelligence no está activado para esta empresa.');

            return $this->blocked('Invenza Intelligence no está activado para esta empresa.');
        }

        $budget = $company->intelligenceMonthlyBudget();

        if ($budget !== null) {
            $spent = $this->monthSpend($companyId);

            if ($spent >= $budget) {
                $message = 'Se alcanzó el presupuesto mensual de IA de esta empresa ($'
                    . number_format($budget, 2) . ').';

                $this->logUsage($companyId, $module, $task, $options, 0, 0, 0, 'blocked_quota', $message);

                return $this->blocked($message);
            }
        }

        try {
            $result = $this->provider->complete($systemPrompt, $userPrompt, $options);
        } catch (Throwable $e) {
            $message = 'Error llamando al proveedor de IA: ' . $e->getMessage();

            $this->logUsage($companyId, $module, $task, $options, 0, 0, 0, 'error', $message);

            return ['ok' => false, 'message' => $message, 'text' => null];
        }

        $cost = $this->estimateCost($result->model, $result->inputTokens, $result->outputTokens);

        $this->logUsage(
            $companyId, $module, $task, $options,
            $result->inputTokens, $result->outputTokens, $cost, 'ok', null, $result->model
        );

        return [
            'ok' => true,
            'text' => $result->text,
            'input_tokens' => $result->inputTokens,
            'output_tokens' => $result->outputTokens,
            'estimated_cost_usd' => $cost,
            'model' => $result->model,
        ];
    }

    private function monthSpend(int $companyId): float
    {
        return (float) DB::table('ai_usage_records')
            ->where('company_id', $companyId)
            ->where('created_at', '>=', Carbon::now()->startOfMonth())
            ->sum('estimated_cost_usd');
    }

    private function estimateCost(string $model, int $inputTokens, int $outputTokens): float
    {
        $pricing = config('intelligence.pricing.' . $model);

        if (!$pricing) {
            // Modelo sin precio configurado en config/intelligence.php - no se puede
            // estimar, se registra en 0 en vez de inventar un numero.
            return 0.0;
        }

        $cost = ($inputTokens / 1_000_000 * $pricing['input'])
            + ($outputTokens / 1_000_000 * $pricing['output']);

        return round($cost, 4);
    }

    private function logUsage(
        int $companyId,
        string $module,
        string $task,
        array $options,
        int $inputTokens,
        int $outputTokens,
        float $cost,
        string $status,
        ?string $errorMessage,
        ?string $model = null
    ): void {
        DB::table('ai_usage_records')->insert([
            'company_id' => $companyId,
            'user_id' => $options['user_id'] ?? null,
            'module' => $module,
            'task' => $task,
            'provider' => 'anthropic',
            'model' => $model ?: ($options['model'] ?? config('intelligence.default_model')),
            'input_tokens' => $inputTokens,
            'output_tokens' => $outputTokens,
            'estimated_cost_usd' => $cost,
            'status' => $status,
            'error_message' => $errorMessage,
            'created_at' => now(),
        ]);
    }

    private function blocked(string $message): array
    {
        return ['ok' => false, 'message' => $message, 'text' => null];
    }
}
