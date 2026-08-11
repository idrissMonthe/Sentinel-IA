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
        Schema::create('signalements', function (Blueprint $table) {
            $table->id();
            $table->string ('titre');
            $table->text ('contenu');
            $table-> boolean ('est_publie')->default (false);
            // contient : 1 Signalement -- 0..1 Analyse (réutilisation d'une analyse existante)
            $table->foreignId('analyse_id')->nullable()->constrained('analyses')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('signalements');
    }
};
