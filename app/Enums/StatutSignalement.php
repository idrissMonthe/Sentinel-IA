<?php
namespace App\Enums; 
Enum StatutSignalement: String {
    case EN_ATTENTE = 'en_attente';
    case VALIDE = 'valide';
    case REJETE = 'rejete';
    public function label(): string
    {
        return match($this) {
            self::EN_ATTENTE => 'en_attente',
            self::VALIDE => 'valide',
            self::REJETE => 'rejete',
        };
    }
    public function color(): string 
    {
        return match($this) {
            self::EN_ATTENTE => 'var(--warning, #ffc107)',
            self::VALIDE => 'var(--success, #28a745)',
            self::REJETE => 'var(--danger, #dc3545)',
        };
    }
}
?>