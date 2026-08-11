<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePreuveRequest;
use App\Models\Signalement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PreuveController extends Controller
{
    // <<include>> de Signaler une arnaque — alternative 4.1 : optionnel, non bloquant
    public function store(StorePreuveRequest $request, Signalement $signalement): RedirectResponse
    {

        $data = $request->validate([
            'fichier' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'type' => ['required', 'in:image,document,lien'],
        ]);

        $chemin = $request->file('fichier')->store('preuves', 'private');

        $signalement->preuves()->create([
            'type' => $data['type'],
            'fichier' => $chemin,
        ]);

        return back()->with('status', 'Preuve ajoutée.');
    }
}