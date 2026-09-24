<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title', 'Sentinel IA')</title>
</head>
<body style="margin:0; padding:0; background-color:#060913; font-family: Arial, Helvetica, sans-serif;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#060913; padding: 30px 0;">
<tr>
<td align="center">
<table role="presentation" width="600" cellpadding="0" cellspacing="0" style="background-color:#0f172a; border:1px solid #1e3a5f; border-radius:12px; overflow:hidden;">
<tr>
<td style="background-color:#0f172a; padding:25px 30px; border-bottom:1px solid #1e3a5f;">
<span style="font-size:22px; font-weight:800; letter-spacing:2px; color:#f8fafc;">SENTINEL<span style="color:#38ef7d;">IA</span></span>
</td>
</tr>
<tr>
<td style="padding:30px; color:#f8fafc; font-size:15px; line-height:1.6;">
@yield('content')
</td>
</tr>
<tr>
<td style="padding:20px 30px; border-top:1px solid #1e3a5f; color:#94a3b8; font-size:12px; text-align:center;">
Sentinel IA — Plateforme de détection et de signalement des arnaques numériques.<br>
Cet email vous a été envoyé automatiquement, merci de ne pas y répondre.
</td>
</tr>
</table>
</td>
</tr>
</table>
</body>
</html>