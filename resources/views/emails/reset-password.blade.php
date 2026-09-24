@extends('emails.layout')

@section('title', 'Réinitialisation de mot de passe')

@section('content')
<h1 style="color:#38ef7d; font-size:20px; margin-top:0;">Réinitialisation de votre mot de passe</h1>

<p>Bonjour {{ $user->prenom ?? '' }},</p>

<p>Vous avez demandé la réinitialisation de votre mot de passe Sentinel IA. Cliquez sur le bouton ci-dessous pour en choisir un nouveau. Ce lien expire dans 60 minutes.</p>

<table role="presentation" cellpadding="0" cellspacing="0" style="margin: 25px 0;">
<tr>
<td bgcolor="#0072ff" style="border-radius: 8px;">
<a href="{{ $url }}" style="display:inline-block; padding:12px 24px; color:#ffffff; font-weight:600; text-decoration:none; font-size:15px;">
Réinitialiser mon mot de passe
</a>
</td>
</tr>
</table>

<p style="color:#94a3b8; font-size:13px;">Si vous n'êtes pas à l'origine de cette demande, ignorez cet email — votre mot de passe restera inchangé.</p>
@endsection