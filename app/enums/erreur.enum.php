<?php
namespace App\ENUM\ERREUR;

enum ErreurEnum: string
{
    // Erreurs liées à l'authentification
    case LOGIN_REQUIRED = 'L\'email est requis.';
    case LOGIN_EMAIL = 'L\'email n\'est pas valide.';
    case PASSWORD_REQUIRED = 'Le mot de passe est requis.';
    case PASSWORD_INVALID = 'password.invalid';
    case LOGIN_INCORRECT = 'login.incorrect';
    // Erreurs liées aux promotions
    case PROMO_ID_REQUIRED = 'L\'identifiant de la promotion est requis.';
    case PROMO_NAME_REQUIRED = 'Le nom de la promotion est requis.';
    case PROMO_DATE_REQUIRED = 'La date est requise.';
    case PROMO_ADD_FAILED = 'Échec de l\'ajout de la promotion.';
    case PROMO_ACTIVATION_FAILED = 'Échec de l\'activation de la promotion.';
    case PROMO_DATE_INFERIEUR = 'La date de début doit être antérieure à la date de fin.';
    case PROMO_DATE_NORME = 'Les dates doivent être au format JJ-MM-AAAA.';
    case PROMO_STATUT_INVALID = 'Le statut de la promotion est invalide.';
    case REFERENTIEL_ID_REQUIRED = 'L\'ID du référentiel est requis.';
}
