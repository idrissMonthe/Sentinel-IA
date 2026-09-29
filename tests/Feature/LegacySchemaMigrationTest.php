<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class LegacySchemaMigrationTest extends TestCase
{
    public function test_legacy_schema_is_repaired_without_losing_existing_rows(): void
    {
        // Base dédiée en mémoire : aucune opération sur la base configurée dans .env.
        config(['database.default' => 'legacy_test', 'database.connections.legacy_test' => ['driver' => 'sqlite', 'database' => ':memory:', 'foreign_key_constraints' => true]]);
        try {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('name');
            });
            foreach (['analyses', 'entite_suspectes'] as $name) {
                Schema::create($name, fn (Blueprint $table) => $table->id());
            }
            Schema::create('signalements', function (Blueprint $table) {
                $table->id();
                $table->text('contenu')->nullable();
            });
            DB::table('users')->insert(['name' => 'Ancien compte']);
            DB::table('signalements')->insert(['contenu' => 'Ancien signalement']);
            $migration = require database_path('migrations/2026_09_29_000000_reconcile_legacy_schema.php');
            $migration->up();
            $migration->up(); // Les bases déjà à jour doivent rester utilisables.
            $this->assertDatabaseHas('users', ['name' => 'Ancien compte', 'nom' => 'Ancien compte', 'role' => 'UTILISATEUR']);
            $this->assertDatabaseHas('signalements', ['description' => 'Ancien signalement', 'statut' => 'en_attente', 'user_id' => null]);
            $this->assertTrue(Schema::hasColumn('signalements', 'ville'));
            DB::table('users')->insert(['nom' => 'Nouveau', 'prenom' => 'Compte']);
            $this->assertDatabaseCount('users', 2);
        } finally {
            DB::purge('legacy_test');
        }
    }
}
