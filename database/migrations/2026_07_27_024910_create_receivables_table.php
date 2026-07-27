<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Cartera por cobrar importada desde fuera (CSV hoy, API de sistemas
 * externos tipo SAP/CONTPAQi en el futuro). Reemplaza la unificacion
 * AccountReceivable+CreditSale de Invenza — aca solo hay UNA fuente: lo
 * que la empresa importa/conecta.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('receivables', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');

            $table->string('customer_name', 180);
            $table->string('customer_email', 180)->nullable();

            $table->string('document_number', 80)->nullable();
            $table->string('external_reference', 120)->nullable();

            $table->decimal('total', 14, 2);
            $table->decimal('balance', 14, 2);
            $table->date('due_date')->nullable();

            $table->string('status', 20)->default('PENDIENTE');

            $table->timestamps();

            $table->unique(['company_id', 'document_number'], 'receivables_company_document_unique');
            $table->index(['company_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('receivables');
    }
};
