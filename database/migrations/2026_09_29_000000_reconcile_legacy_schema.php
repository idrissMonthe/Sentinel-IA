<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Les migrations initiales ont changé après leur exécution sur certaines bases.
        $users = Schema::getColumnListing('users');
        Schema::table('users', function (Blueprint $table) use ($users) {
            foreach (['nom', 'prenom', 'telephone'] as $column) {
                if (! in_array($column, $users)) {
                    $table->string($column)->nullable();
                }
            }
            if (! in_array('role', $users)) {
                $table->string('role')->default('UTILISATEUR');
            }
            if (! in_array('statut', $users)) {
                $table->string('statut')->default('actif');
            }
            if (! in_array('tentatives_echouees', $users)) {
                $table->integer('tentatives_echouees')->default(0);
            }
            if (in_array('name', $users)) {
                $table->string('name')->nullable()->change();
            }
        });
        if (in_array('name', $users)) {
            DB::table('users')->whereNull('nom')->update(['nom' => DB::raw('name')]);
        }

        $columns = Schema::getColumnListing('signalements');
        Schema::table('signalements', function (Blueprint $table) use ($columns) {
            foreach (['user_id' => 'users', 'moderateur_id' => 'users', 'entite_suspecte_id' => 'entite_suspectes', 'analyse_id' => 'analyses'] as $column => $target) {
                if (! in_array($column, $columns)) {
                    // Ne pas inventer de propriétaire pour les anciennes lignes.
                    $table->foreignId($column)->nullable()->constrained($target)->nullOnDelete();
                }
            }
            if (! in_array('description', $columns)) {
                $table->text('description')->nullable();
            }
            if (! in_array('ville', $columns)) {
                $table->string('ville')->nullable();
            }
            if (! in_array('statut', $columns)) {
                $table->string('statut')->default('en_attente');
            }
        });
        if (in_array('contenu', $columns) && ! in_array('description', $columns)) {
            DB::table('signalements')->update(['description' => DB::raw('contenu')]);
        }
    }

    public function down(): void
    {
        // Réconciliation additive : conserver les colonnes et données récupérées.
        // Leur présence avant cette migration varie selon la base installée.
    }
};
