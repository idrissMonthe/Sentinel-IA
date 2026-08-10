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
        Schema::create('preuves', function (Blueprint $table) {
            $table->id();
            // contient : 1 Signalement -- 1..* Preuve => obligatoire, cascade à la suppression
            $table->foreignId('signalement_id')->constrained('signalements')->cascadeOnDelete();
            $table->string('type'); // image, document, lien..
            $table->string('fichier'); // chemin de stockage
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('preuves');
    }
};
