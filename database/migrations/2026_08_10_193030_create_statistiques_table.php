<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('statistiques', function (Blueprint $table) {
            $table->id();
            $table->timestamp('date_derniere_mise_a_jour')->useCurrent();
            $table->unsignedInteger('total_signalement_actifs')->default(0);
            $table->unsignedInteger('total_entites_bannies')->default(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('statistiques');
    }
};