<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analyses', function (Blueprint $table) {
            $table->id(); // IdAnalyse
            // declenche : 1 Utilisateur -- 0..* Analyse
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('date_analyse')->useCurrent();
            $table->decimal('score_fiabilite', 5, 2)->nullable(); // rempli après appel IA
            $table->text('conclusion')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analyses');
    }
};