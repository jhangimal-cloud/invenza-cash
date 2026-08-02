<?php

namespace App\Services\Intelligence\Providers;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class AnthropicProvider implements LlmProviderInterface
{
    protected string $baseUrl;
    protected ?string $apiKey;
    protected string $defaultModel;
    protected int $timeout;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) config('services.anthropic.base_url'), '/');
        $this->apiKey = config('services.anthropic.api_key');
        $this->defaultModel = (string) config('services.anthropic.default_model', 'claude-sonnet-5');
        $this->timeout = (int) config('services.anthropic.timeout', 30);
    }

    public function complete(string $systemPrompt, string $userPrompt, array $options = []): LlmCompletionResult
    {
        if (empty($this->apiKey)) {
            throw new RuntimeException('No hay ANTHROPIC_API_KEY configurada en el .env.');
        }

        $model = (string) ($options['model'] ?? $this->defaultModel);
        $maxTokens = (int) ($options['max_tokens'] ?? config('intelligence.default_max_tokens', 1024));

        $response = $this->http()->post($this->baseUrl . '/v1/messages', [
            'model' => $model,
            'max_tokens' => $maxTokens,
            'system' => $systemPrompt,
            'messages' => [
                ['role' => 'user', 'content' => $userPrompt],
            ],
        ]);

        if (! $response->successful()) {
            throw new RuntimeException(
                'Anthropic respondió con error HTTP ' . $response->status() . ' - ' . $response->body()
            );
        }

        $json = $response->json();

        $text = collect($json['content'] ?? [])
            ->where('type', 'text')
            ->pluck('text')
            ->implode('');

        return new LlmCompletionResult(
            text: $text,
            inputTokens: (int) ($json['usage']['input_tokens'] ?? 0),
            outputTokens: (int) ($json['usage']['output_tokens'] ?? 0),
            model: (string) ($json['model'] ?? $model),
        );
    }

    protected function http(): PendingRequest
    {
        return Http::timeout($this->timeout)
            ->acceptJson()
            ->asJson()
            ->withHeaders([
                'x-api-key' => $this->apiKey,
                'anthropic-version' => '2023-06-01',
            ]);
    }
}
