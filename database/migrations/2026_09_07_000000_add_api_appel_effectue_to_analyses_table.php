<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('analyses', function (Blueprint $table) {
            // Les lignes existantes conservent leur poids dans le quota pendant la migration.
            $table->boolean('api_appel_effectue')->default(true)->after('conclusion');
        });
    }

    public function down(): void
    {
        Schema::table('analyses', function (Blueprint $table) {
            $table->dropColumn('api_appel_effectue');
        });
    }
};
