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
        Schema::create('poaching_incident_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('poaching_incident_id')->constrained()->onDelete('cascade');
            $table->foreignId('poaching_method_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('poaching_incident_methods');
    }
};
