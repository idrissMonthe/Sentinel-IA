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
        Schema::create('entite_suspectes', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['numero','email', 'lien']);
            $table->string('valeur');
            $table->unsignedInteger('nombre_signalement')->default(0);
            $table->timestamp('date_apparition')->useCurrent();
            $table->timestamps();
            // Pour empêcher les doublons d'entité (une même valeur = une seule fiche recherchable)
            $table->unique(['type','valeur']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entite_suspectes');
    }
};
