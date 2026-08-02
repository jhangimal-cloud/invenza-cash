<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Log inmutable de cada llamada a Invenza Intelligence. El gasto mensual
 * por empresa se calcula EN VIVO sumando esta tabla (nunca se cachea un
 * contador). Portado de invenza-desarrollo, sin branch_id (este repo no
 * tiene sucursales).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_usage_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('module', 50)->nullable();
            $table->string('task', 100)->nullable();
            $table->string('provider', 30)->default('anthropic');
            $table->string('model', 100)->nullable();
            $table->unsignedInteger('input_tokens')->default(0);
            $table->unsignedInteger('output_tokens')->default(0);
            $table->decimal('estimated_cost_usd', 10, 4)->default(0);
            $table->string('status', 20)->default('ok'); // ok | error | blocked_quota | blocked_disabled
            $table->text('error_message')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['company_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_usage_records');
    }
};
