<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ConsommationIA;
use App\Models\User;
use Illuminate\Http\Request;

class ConsommationIAController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);
        $data = $request->validate(['jours' => ['nullable', 'integer', 'min:1', 'max:365']]);
        $jours = (int) ($data['jours'] ?? 30);
        $lignes = ConsommationIA::where('created_at', '>=', today()->subDays($jours - 1))
            ->selectRaw('DATE(created_at) as jour, modele, COUNT(*) as appels, SUM(input_tokens) as entrees, SUM(output_tokens) as sorties, SUM(cache_creation_input_tokens) as ecrits, SUM(cache_read_input_tokens) as lus')
            ->groupByRaw('DATE(created_at), modele')->orderByDesc('jour')->get();

        return view('admin.consommation-ia', compact('lignes', 'jours'));
    }
}
