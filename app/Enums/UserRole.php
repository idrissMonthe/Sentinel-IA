<?php
namespace App\Enums;
Enum UserRole:string {
    case UTILISATEUR = 'UTILISATEUR';
    case MODERATEUR = "MODERATEUR";
    case ADMINISTRATEUR = "ADMINISTRATEUR";
} 
?>