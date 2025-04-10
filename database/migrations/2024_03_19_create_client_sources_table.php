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
        Schema::create('client_sources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organisation_id')->constrained()->onDelete('cascade');
            $table->year('period');
            $table->unsignedTinyInteger('month')->nullable();
            $table->integer('north_america')->default(0);
            $table->integer('europe_asia')->default(0);
            $table->integer('africa')->default(0);
            $table->integer('asia')->default(0);
            $table->integer('middle_east')->default(0);
            $table->integer('south_america')->default(0);
            $table->integer('oceania')->default(0);
            $table->timestamps();
            $table->unique(['organisation_id', 'period', 'month'], 'unique_client_source_period');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_sources');
    }
}; 