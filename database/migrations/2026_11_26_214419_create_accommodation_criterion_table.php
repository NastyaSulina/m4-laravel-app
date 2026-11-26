<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('accommodation_criterion', function (Blueprint $table) {
            $table->foreignId('accommodation_id');
            $table->foreignId('criterion_id');

            $table->boolean('is_met')->nullable();

            $table->unique(['accommodation_id', 'criterion_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accommodation_criterion');
    }
};
