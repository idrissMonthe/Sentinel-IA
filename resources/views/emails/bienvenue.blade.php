@extends('emails.layout')

@section('title', 'Bienvenue sur Sentinel IA')

@section('content')
<h1 style="color:#38ef7d; font-size:20px; margin-top:0;">Bienvenue, {{ $user->prenom }} !</h1>

<p>Votre compte Sentinel IA a été créé avec succès ({{ $user->email }}).</p>

<p>Vous pouvez dès maintenant :</p>
<ul style="color:#94a3b8; padding-left:20px;">
    <li>Analyser un contenu suspect grâce à notre IA de détection</li>
    <li>Signaler une arnaque et consulter notre base collaborative</li>
    <li>Être alerté des dernières menaces identifiées</li>
</ul>

<table role="presentation" cellpadding="0" cellspacing="0" style="margin: 25px 0;">
<tr>
<td bgcolor="#0072ff" style="border-radius: 8px;">
<a href="{{ route('accueil') }}" style="display:inline-block; padding:12px 24px; color:#ffffff; font-weight:600; text-decoration:none; font-size:15px;">
Accéder à mon espace
</a>
</td>
</tr>
</table>

<p style="color:#94a3b8; font-size:13px;">Si vous n'êtes pas à l'origine de cette création de compte, contactez immédiatement notre équipe.</p>
@endsection