<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Cuentas por pagar importadas (egresos). Espejo de `receivables` pero para
 * el otro lado del flujo de caja. Sin bitacora de gestion propia (a
 * diferencia de receivables) — solo alimenta la proyeccion de Cash Flow,
 * igual que la Fase 4 de Invenza donde CxP no tiene "gestion", solo saldo
 * y vencimiento.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payables', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');

            $table->string('vendor_name', 180);
            $table->string('vendor_email', 180)->nullable();

            $table->string('document_number', 80)->nullable();
            $table->string('external_reference', 120)->nullable();

            $table->decimal('total', 14, 2);
            $table->decimal('balance', 14, 2);
            $table->date('due_date')->nullable();

            $table->string('status', 20)->default('PENDIENTE');

            $table->timestamps();

            $table->unique(['company_id', 'document_number'], 'payables_company_document_unique');
            $table->index(['company_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payables');
    }
};
