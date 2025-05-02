<?php
namespace App\Models;

enum JSONMETHODE: string
{
    case ARRAYTOJSON = 'array_to_json';
    case JSONTOARRAY = 'json_to_array';
}

enum AUTHMETHODE: string
{
    case LOGIN = 'login';
    case LOGOUT = 'logout';
    case REGISTER = 'register';
    case FORGOT_PASSWORD = 'forgot_password';
    case RESET_PASSWORD = "reset_password";
}

enum PROMOMETHODE: string {
    case GET_ALL = 'get_all';
    case ACTIVER_PROMO = 'activer_promo';
    case AJOUTER_PROMO = 'ajouter_promo'; // Nouvelle constante ajoutée
    case GET_ACTIVE = 'get_active'; // Nouvelle méthode pour récupérer la promotion active
    case GET_ACTIVE_PROMO = 'get_active_promo';
    case GET_BY_ID = 'get_by_id';
}

enum REFMETHODE: string {
    case GET_ALL = 'get_all';
    case GET_ACTIVE = 'get_active';
    case AJOUTER = 'ajouter';
    case AFFECTER = 'affecter';
    case AFFECTER_PROMOTION = 'affecter_promotion'; // Ajoutez cette constante
    case GET_NON_AFFECTES = 'get_non_affectes';
    case AFFECTER_REF_PROMO_ACTIVE = 'affecter_ref_promo_active';
    case DESAFFECTER = 'desaffecter';
    
}

enum APPMETHODE: string {
    case GET_ALL = 'get_all';
    case GET_ACTIVE = 'get_active';
    case AJOUTER = 'ajouter';
    case IMPORTER = 'importer';

    
}