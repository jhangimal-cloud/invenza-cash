<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('collection_tracking_activities', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('tracking_id');
            $table->unsignedBigInteger('user_id')->nullable();

            $table->enum('activity_type', [
                'note',
                'call',
                'whatsapp',
                'email_sent',
                'email_received',
                'meeting',
                'status_change',
                'reminder',
                'promise_payment',
                'system',
            ])->default('note');

            $table->enum('direction', ['internal', 'outbound', 'inbound'])->default('internal');

            $table->unsignedBigInteger('old_status_id')->nullable();
            $table->unsignedBigInteger('new_status_id')->nullable();

            $table->string('subject', 220)->nullable();
            $table->longText('body')->nullable();

            $table->decimal('promised_amount', 14, 2)->nullable();
            $table->date('promised_payment_date')->nullable();

            $table->dateTime('activity_at')->nullable();
            $table->dateTime('next_follow_up_at')->nullable();

            $table->timestamps();

            $table->index(['company_id', 'tracking_id']);
            $table->index(['tracking_id', 'activity_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('collection_tracking_activities');
    }
};
