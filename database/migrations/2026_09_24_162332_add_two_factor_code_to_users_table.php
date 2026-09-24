<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('code_2fa')->nullable();
            $table->timestamp('code_2fa_expire_a')->nullable();
            $table->unsignedInteger('code_2fa_tentatives')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['code_2fa', 'code_2fa_expire_a', 'code_2fa_tentatives']);
        });
    }
};