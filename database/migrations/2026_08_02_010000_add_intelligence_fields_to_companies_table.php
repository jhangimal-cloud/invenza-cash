<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Capa "Invenza Intelligence" (App\Services\Intelligence\*), portada de
 * invenza-desarrollo. Aqui NO hay tabla de settings separada por empresa
 * (a diferencia del ERP) - se sigue el patron ya existente en este repo de
 * columnas directas en `companies` (ver max_users).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->boolean('intelligence_enabled')->default(false)->after('max_users');
            $table->decimal('intelligence_monthly_budget_usd', 10, 2)->nullable()->after('intelligence_enabled');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['intelligence_enabled', 'intelligence_monthly_budget_usd']);
        });
    }
};
