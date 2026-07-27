<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('collection_tracking_statuses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');

            $table->string('name', 80);
            $table->string('color', 30)->default('#2563EB');
            $table->unsignedInteger('sort_order')->default(0);

            $table->boolean('is_initial')->default(false);
            $table->boolean('is_final')->default(false);
            $table->boolean('stops_notifications')->default(false);

            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['company_id', 'name'], 'cts_company_name_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('collection_tracking_statuses');
    }
};
