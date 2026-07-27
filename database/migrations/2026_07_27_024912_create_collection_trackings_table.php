<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Gestion de cobro de una cuenta por cobrar importada. A diferencia de
 * Invenza (que necesita source_type/source_id polimorfico por tener DOS
 * fuentes de saldo), aca solo existe `receivables`, asi que se simplifica
 * a un FK directo `receivable_id`.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('collection_trackings', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('receivable_id')->unique();

            $table->string('tracking_code', 80)->unique();

            $table->unsignedBigInteger('responsible_user_id')->nullable();
            $table->unsignedBigInteger('status_id')->nullable();

            $table->string('title', 220);
            $table->decimal('balance_amount', 14, 2)->default(0);
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');

            $table->date('original_due_date')->nullable();
            $table->dateTime('next_follow_up_at')->nullable();
            $table->dateTime('last_activity_at')->nullable();
            $table->dateTime('closed_at')->nullable();

            $table->text('internal_notes')->nullable();

            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['company_id', 'status_id']);
            $table->index(['company_id', 'next_follow_up_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('collection_trackings');
    }
};
