<?php

namespace App\Http\Controllers;

use App\Enums\StatutSignalement;
use App\Models\Signalement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ModerationController extends Controller
{


    // File d'attente de modération
    public function index(Request $request)
    {
        $this->authorize('moderer', Signalement::class );

        $signalements = Signalement::where('statut', StatutSignalement::EN_ATTENTE)
            ->with(['utilisateur', 'entiteSuspecte'])
            ->oldest()
            ->paginate(20);

        return view('moderation.index', compact('signalements'));
    }

    // VerifierSignalement() -> validation
    public function valider(Request $request, Signalement $signalement): RedirectResponse
    {
        $this->authorize('valider', $signalement);

        $traite = DB::transaction(function () use ($request, $signalement): bool {
            $updated = Signalement::query()
                ->whereKey($signalement->getKey())
                ->where('statut', StatutSignalement::EN_ATTENTE)
                ->update([
                    'statut' => StatutSignalement::VALIDE,
                    'moderateur_id' => $request->user()->id,
                    'updated_at' => now(),
                ]);

            if ($updated !== 1) {
                return false;
            }

            // Le compteur ne progresse qu'une seule fois, lors du passage
            // effectif de « en attente » à « valide ».
            $signalement->entiteSuspecte?->increment('nombre_signalement');

            return true;
        });

        if (! $traite) {
            return back()->withErrors([
                'moderation' => 'Ce signalement a déjà été traité.',
            ]);
        }

        return back()->with('status', 'Signalement validé.');
    }

    // SupprimerFauxSignalement()
    public function rejeter(Request $request, Signalement $signalement): RedirectResponse
    {
        $this->authorize('rejeter', $signalement);

        $updated = Signalement::query()
            ->whereKey($signalement->getKey())
            ->where('statut', StatutSignalement::EN_ATTENTE)
            ->update([
                'statut' => StatutSignalement::REJETE,
                'moderateur_id' => $request->user()->id,
                'updated_at' => now(),
            ]);

        if ($updated !== 1) {
            return back()->withErrors([
                'moderation' => 'Ce signalement a déjà été traité.',
            ]);
        }

        return back()->with('status', 'Signalement rejeté.');
    }

    // GererDoublons() : entités suspectes ayant plusieurs signalements en attente
    public function doublons(Request $request)
    {
        $this->authorize('moderer',Signalement::class);

        $entites = \App\Models\EntiteSuspecte::with([
            'signalements' => fn ($q) => $q
                ->where('statut', StatutSignalement::EN_ATTENTE)
                ->with('utilisateur')
                ->oldest(),
        ])->withCount([
            'signalements as signalements_en_attente_count' => fn ($q) => $q->where('statut', StatutSignalement::EN_ATTENTE),
        ])
            ->having('signalements_en_attente_count', '>', 1)
            ->paginate(10);

        return view('moderation.doublons', compact('entites'));
    }
}
