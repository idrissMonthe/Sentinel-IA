<?php

namespace Tests\Unit;

use App\Enums\StatutSignalement;
use PHPUnit\Framework\TestCase;

class StatutSignalementTest extends TestCase
{
    public function test_le_statut_en_attente_possede_le_bon_libelle(): void
    {
        $this->assertSame('En attente', StatutSignalement::EN_ATTENTE->label());
    }

    public function test_le_statut_valide_possede_la_bonne_couleur(): void
    {
        $this->assertSame('var(--success, #28a745)', StatutSignalement::VALIDE->color());
    }
}
