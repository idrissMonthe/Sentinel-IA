@extends('layouts.app')
@section('title', 'Rapport d\'analyse - SENTINEL IA')
@section('content')
<div class="container fade-in" style="max-width: 800px; background: #fff; color: #000; padding: 40px; border-radius: 8px;">
    <div style="border-bottom: 2px solid #000; padding-bottom: 20px; margin-bottom: 30px; text-align: center;">
        <h1 style="color: #000; margin: 0;">Rapport d'Analyse - Sentinel IA</h1>
        <p style="color: #555;">Généré le {{ now()->format('d/m/Y à H:i') }}</p>
    </div>

    <div style="margin-bottom: 30px;">
        <h3 style="color: #000; margin-bottom: 10px;">Détails de l'analyse N°{{ $analyse->id }}</h3>
        <table style="width: 100%; border-collapse: collapse;">
            <tr><td style="padding: 8px; border: 1px solid #ccc; width: 30%;"><strong>Date d'analyse</strong></td><td style="padding: 8px; border: 1px solid #ccc;">{{ $analyse->date_analyse->format('d/m/Y H:i') }}</td></tr>
            <tr><td style="padding: 8px; border: 1px solid #ccc;"><strong>Type de contenu</strong></td><td style="padding: 8px; border: 1px solid #ccc;">{{ ucfirst($analyse->type) }}</td></tr>
            <tr><td style="padding: 8px; border: 1px solid #ccc;"><strong>Score de risque IA</strong></td><td style="padding: 8px; border: 1px solid #ccc;"><strong>{{ $analyse->score_fiabilite }} / 100</strong></td></tr>
        </table>
    </div>

    <div style="margin-bottom: 30px;">
        <h3 style="color: #000; margin-bottom: 10px;">Conclusion Technique</h3>
        <p style="padding: 15px; background: #f9f9f9; border-left: 4px solid #d9534f; margin: 0;">{{ $analyse->conclusion }}</p>
    </div>

    <div style="text-align: center; margin-top: 50px;">
        <button onclick="window.print()" class="btn btn-primary" style="background: #000; color: #fff;">Imprimer ce rapport</button>
        <p style="font-size: 0.8rem; color: #777; margin-top: 15px;">Ce document est généré à titre indicatif par Sentinel IA et peut être remis aux autorités compétentes en cas de dépôt de plainte.</p>
    </div>
</div>
@endsection