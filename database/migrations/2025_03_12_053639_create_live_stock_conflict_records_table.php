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
        Schema::create('live_stock_conflict_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organisation_id');
            $table->unsignedBigInteger('species_id');
            $table->unsignedBigInteger('live_stock_type_id');
            $table->integer('killed')->default(0);
            $table->integer('injured')->default(0);
            $table->year('period');
            $table->timestamps();

            // Add a unique constraint to prevent duplicate records
            $table->unique(['organisation_id', 'species_id', 'live_stock_type_id', 'period'], 'livestock_conflict_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('live_stock_conflict_records');
    }
};
