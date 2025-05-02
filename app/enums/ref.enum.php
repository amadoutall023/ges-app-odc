namespace App\Models;

enum REFMETHODE: string {
    case GET_ALL = 'get_all';
    case GET_NON_AFFECTES = 'get_non_affectes';
    case AFFECTER = 'affecter';
    case DESAFFECTER = 'desaffecter';
    case AFFECTER_REF_PROMO_ACTIVE = 'affecter_ref_promo_active';
}