<?php

return [
    'admin_email' => env('PLATFORM_ADMIN_EMAIL', 'jhangimal@invenza.app'),

    // Tope de usuarios por empresa segun el plan actual (unico plan, $29/mes).
    // Si un cliente necesita mas asientos, se sube a mano por company_id
    // (companies.max_users) - no hay todavia sistema de planes/facturacion.
    'default_max_users_per_company' => (int) env('DEFAULT_MAX_USERS_PER_COMPANY', 5),
];
