<?php
namespace App\Enums;

enum CheminPage: string
{
    // Contrôleurs
    case CONTROLLER = __DIR__ . '/../controllers/controller.php';
    case AUTH_CONTROLLER = __DIR__ . '/../controllers/auth.controller.php';
    case LAYOUT_CONTROLLER = __DIR__ . '/../controllers/layout.controller.php';
    case REFERENCIEL_CONTROLLER = __DIR__ . '/../controllers/referenciel.controller.php';
    case APPRENANT_CONTROLLER = __DIR__ . '/../controllers/apprenant.controller.php';
    
    case PROMO_CONTROLLER = __DIR__ . '/../controllers/promo.controller.php';
    case ERROR_CONTROLLER = __DIR__ . '/../controllers/error.controller.php';

    // Modèle
    case MODEL = __DIR__ . '/../models/model.php';
    case AUTH_MODEL = __DIR__ . '/../models/auth.model.php';
    case REF_MODEL = __DIR__ . '/../models/ref.model.php';
    case PROMO_MODEL = __DIR__ . '/../models/promo.model.php';
    case APPRENANT_MODEL = __DIR__ . '/../models/apprenant.model.php';


    // Données
    case DATA_JSON = __DIR__ . '/../data/data.json';

    // Routes
    case ROUTE_WEB = __DIR__ . '/../route/route.web.php';

    // Services
    case SESSION_SERVICE = __DIR__ . '/../services/session.service.php';
    case VALIDATOR_SERVICE = __DIR__ . '/../services/validator.service.php';

    // Layouts
    case LAYOUT_BASE = __DIR__ . '/../views/layout/base.layout.php';

    // Vues
    case VIEW_LOGIN = __DIR__ . '/../views/login/login.view.php';
    case VIEW_RESETPASSWORD = __DIR__ . '/../views/login/reset_password.view.php';
    case VIEW_PROMO = __DIR__ . '/../views/promo/promo.view.php';
    case FORM_PROMO = __DIR__ . '/../views/promo/form.view.php';
    case VIEW_REFERENCIEL = __DIR__ . '/../views/referenciel/referenciel.view.php';
    case VIEW_APPRENANT = __DIR__ . '/../views/apprenant/apprenant.view.php';
    case VIEW_AJOUT_APP = __DIR__ . '/../views/apprenant/ajout_app.view.php';
    case VIEW_DETAIL_APP = __DIR__ . '/../views/apprenant/detailapp.view.php';
    case VIEW_IMPORT = __DIR__ . '/../views/apprenant/import_result.view.php';
    case VIEW_AJOUT_REFERENCIEL = __DIR__ . '/../views/referenciel/ajout_ref.view.php';



    // Traductions
    case ERROR_FR = __DIR__ . '/../views/translate/fr/error.fr.php';
    case MESSAGE_FR = __DIR__ . '/../views/translate/fr/message.fr.php';

    // Public
    case INDEX_PUBLIC = __DIR__ . '/../../public/index.php';
    case CSS_DASHBOARD = __DIR__ . '/../../public/assets/css/dashboard.css';
    //////////////////////////////////////////////page  login ////////////////////////////////////////
    case CSS_LOGIN = '/assets/css/login/login.css';
    case CSS_RESETLOGIN = '/assets/css/login/resetlog.css';
    case CSS_PROMO = '/assets/css/promo/promo.css';
    case CSS_FPROMO = '/assets/css/promo/form.css';
    case CSS_REFERENCIEL = '/assets/css/referenciel/referenciel.css';
    case CSS_ADDREFERENCIEL = '/assets/css/referenciel/all_referenciel.css';
    case CSS_AJOUT_REFERENCIEL = '/assets/css/referenciel/ajouter_ref.css';
    case CSS_APPRENANT = '/assets/css/apprenant/apprenant.css';
    case CSS_AJOUT_APP = '/assets/css/apprenant/ajout_app.css';
    case CSS_DETAILAPP = '/assets/css/apprenant/detailapp.css';
    

    case IMG_LOGO_LOGIN = '/assets/images/login/logo_odc.png';
    case IMG_LOGO_SIDEBAR = __DIR__ . '/assets/images/sidebar/logo.png';
}
