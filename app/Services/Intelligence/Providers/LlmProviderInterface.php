<?php

namespace App\Services\Intelligence\Providers;

/**
 * Interfaz delgada de proveedor de IA - permite agregar/cambiar de
 * proveedor (OpenAI, etc.) mas adelante sin tocar IntelligenceService.
 * Portada de invenza-desarrollo (el ERP).
 */
interface LlmProviderInterface
{
    public function complete(string $systemPrompt, string $userPrompt, array $options = []): LlmCompletionResult;
}
