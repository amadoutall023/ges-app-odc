<?php
declare(strict_types=1);

namespace App\ENUM\VALIDATOR;

enum ValidatorMethode: string {
    case EMAIL = 'email';
    case PASSWORD = 'password';
    case USER = 'user';
    case PROMO = 'promo';
    case PROMO_DATE = 'promo_date';
    case PROMO_DATE_VALIDE = 'promo_date_valide';
    case PROMO_STATUT = 'promo_statut';
    case PROMO_NAME = 'promo_name'; // Ajoutez cette constante
    case VALIDATE_AFFECTER_REF = 'validate_affecter_ref';
    case PROMO_DATE_RANGE = 'promo_date_range'; // Ajoutez cette constante
    case VALIDATE_REFERENTIELS = 'validate_referentiels'; // Ajoutez cette constante
    case VALID_GENERAL = 'valid_general'; // Corrigé pour être une clé et non un message
    case VALIDATE_FICHIER_EXCEL = 'validate_fichier_excel';
    case GENERER_MATRICULE = 'generer_matricule';
    case INSCRIRE_APPRENANT = 'inscrire_apprenant';
   
}
