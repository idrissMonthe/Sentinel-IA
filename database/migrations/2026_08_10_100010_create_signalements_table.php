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
            $table->foreignId ('user_id')->constrained()->onDelete('cascade');
            $table->foreignId ('moderateur_id')->nullable()->constrained('users');
            $table->foreignId ('entite_suspecte_id')->nullable()->constrained();
            $table->string ('titre')->nullable();
            $table->text ('description');
            $table->string ('ville')->nullable();
            $table->string ('statut')->default('en_attente');
            $table->text ('contenu')->nullable();
            $table-> boolean ('est_publie')->default (false);
            // contient : 1 Signalement -- 0..1 Analyse (réutilisation d'une analyse existante)
            $table->foreignId('analyse_id')->nullable()->constrained()->nullOnDelete();
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
