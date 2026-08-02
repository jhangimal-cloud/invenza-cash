<?php

namespace App\Services\Intelligence\Providers;

class LlmCompletionResult
{
    public function __construct(
        public readonly string $text,
        public readonly int $inputTokens,
        public readonly int $outputTokens,
        public readonly string $model,
    ) {
    }
}
