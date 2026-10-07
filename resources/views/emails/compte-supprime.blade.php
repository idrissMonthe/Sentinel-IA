@extends('emails.layout')

@section('title', 'Confirmation de suppression de compte')

@section('content')
<h1 style="color:#38ef7d; font-size:20px; margin-top:0;">Confirmation de suppression</h1>
<p>Bonjour {{ $prenom }},</p>
<p>Votre compte Sentinel IA associé à l’adresse <strong>{{ $email }}</strong> a bien été supprimé.</p>
<p>Vous ne pouvez désormais plus vous connecter avec ce compte. Certaines informations liées à des signalements publiés peuvent être conservées, sans accès à votre espace personnel, afin d'assurer la fiabilité et la sécurité de la plateforme.</p>
<p style="color:#94a3b8; font-size:13px;">Si vous n'êtes pas à l'origine de cette demande, contactez-nous sans attendre.</p>
@endsection
