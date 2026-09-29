<?php

namespace Tests\Feature;

use App\Models\EntiteSuspecte;
use App\Models\Signalement;
use App\Models\Statistique;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StatistiqueTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_statistics_work_with_an_empty_database(): void
    {
        $this->get(route('statistiques.index'))->assertOk()->assertSee('Données insuffisantes');
        $this->assertDatabaseCount('statistiques', 1);
        $this->assertSame(0, Statistique::first()->total_signalement_actifs);
    }

    public function test_public_breakdowns_exclude_unvalidated_reports(): void
    {
        $user = User::factory()->create();
        foreach (['valide' => ['email', 'Douala'], 'en_attente' => ['numero', 'Yaoundé'], 'rejete' => ['lien', 'Bafoussam']] as $status => [$type, $ville]) {
            $entity = EntiteSuspecte::create(['type' => $type, 'valeur' => $type]);
            Signalement::create(['user_id' => $user->id, 'entite_suspecte_id' => $entity->id, 'description' => 'Test', 'ville' => $ville, 'statut' => $status]);
        }
        $stats = Statistique::recalculer();
        $this->assertSame(1, $stats->total_signalement_actifs);
        $this->assertSame([['label' => 'Email', 'total' => 1]], $stats->menaces_frequentes);
        $this->assertSame([['label' => 'Douala', 'total' => 1]], $stats->zones_touchees);
        $this->assertSame(1, $stats->utilisateurs_proteges);
    }

    public function test_expired_statistics_are_recalculated(): void
    {
        Statistique::create(['date_derniere_mise_a_jour' => now()->subHours(2), 'analyses_effectuees' => 99]);
        $this->get(route('statistiques.index'))->assertOk()->assertViewHas('statistiques', fn ($stats) => $stats->analyses_effectuees === 0);
        $this->assertDatabaseCount('statistiques', 2);
    }
}
