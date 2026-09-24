@extends('emails.layout')

@section('title', 'Code de vérification')

@section('content')
<h1 style="color:#38ef7d; font-size:20px; margin-top:0;">Votre code de vérification</h1>
<p>Bonjour {{ $user->prenom }},</p>
<p>Voici votre code à usage unique pour terminer votre connexion à Sentinel IA. Il expire dans 10 minutes.</p>

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin: 25px 0;">
<tr>
<td align="center" style="background-color:#1e293b; border:1px solid #1e3a5f; border-radius:8px; padding: 20px;">
<span style="font-family: monospace; font-size: 32px; letter-spacing: 8px; color:#38ef7d; font-weight:bold;">{{ $code }}</span>
</td>
</tr>
</table>

<p style="color:#94a3b8; font-size:13px;">Si vous n'êtes pas à l'origine de cette tentative de connexion, changez votre mot de passe immédiatement.</p>
@endsection