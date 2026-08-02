<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Invenza Intelligence — configuración de dominio
    |--------------------------------------------------------------------------
    |
    | Las credenciales de conexión (api_key, base_url, etc.) viven en
    | config/services.php ('anthropic'). Aquí solo va configuración de
    | negocio. Portado de invenza-desarrollo (el ERP).
    |
    */

    'default_model' => env('ANTHROPIC_DEFAULT_MODEL', 'claude-sonnet-5'),

    'default_max_tokens' => 1024,

    /*
    |--------------------------------------------------------------------------
    | Tabla de precios por modelo
    |--------------------------------------------------------------------------
    |
    | USD por MILLÓN de tokens. Se usa solo para estimar el costo de cada
    | llamada y compararlo contra el presupuesto mensual de la empresa
    | (ver App\Services\Intelligence\IntelligenceService). ⚠️ Confirmar/
    | actualizar contra la página de precios oficial de Anthropic antes de
    | confiar en el control de presupuesto para producción real.
    |
    */
    'pricing' => [
        'claude-sonnet-5' => [
            'input' => 3.00,
            'output' => 15.00,
        ],
        'claude-haiku-4-5-20251001' => [
            'input' => 0.80,
            'output' => 4.00,
        ],
    ],

];
