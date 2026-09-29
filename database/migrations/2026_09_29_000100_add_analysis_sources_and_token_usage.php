<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('analyses', function (Blueprint $table) {
            $table->json('source_web')->nullable();
        });
        Schema::create('consommations_ia', function (Blueprint $table) {
            $table->id();
            $table->string('operation');
            $table->string('modele');
            $table->string('request_id')->nullable();
            $table->unsignedInteger('input_tokens')->default(0);
            $table->unsignedInteger('output_tokens')->default(0);
            $table->unsignedInteger('cache_creation_input_tokens')->default(0);
            $table->unsignedInteger('cache_read_input_tokens')->default(0);
            $table->timestamps();
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consommations_ia');
        Schema::table('analyses', fn (Blueprint $table) => $table->dropColumn('source_web'));
    }
};
