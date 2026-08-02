<?php

namespace App\Console\Commands;

use App\Services\Intelligence\IntelligenceService;
use Illuminate\Console\Command;

class IntelligenceTestCommand extends Command
{
    protected $signature = 'intelligence:test {company_id : ID de la empresa} {prompt : Mensaje de prueba a enviar}';

    protected $description = 'Prueba de punta a punta de la capa Invenza Intelligence (activacion, presupuesto, llamada real, log de uso).';

    public function handle(IntelligenceService $service): int
    {
        $companyId = (int) $this->argument('company_id');
        $prompt = (string) $this->argument('prompt');

        $result = $service->run(
            companyId: $companyId,
            module: 'test',
            task: 'manual_test',
            systemPrompt: 'Eres un asistente de prueba para validar la integración de Invenza Intelligence. Responde en una sola frase corta.',
            userPrompt: $prompt
        );

        if (!$result['ok']) {
            $this->error('Bloqueado o con error: ' . $result['message']);

            return self::FAILURE;
        }

        $this->info('Respuesta del modelo:');
        $this->line($result['text']);
        $this->line('');
        $this->line('Modelo: ' . $result['model']);
        $this->line('Tokens entrada/salida: ' . $result['input_tokens'] . ' / ' . $result['output_tokens']);
        $this->line('Costo estimado: $' . number_format($result['estimated_cost_usd'], 4));

        return self::SUCCESS;
    }
}
