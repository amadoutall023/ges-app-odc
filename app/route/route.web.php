<?php
require_once __DIR__ . '/../enums/chemin_page.php';
use App\Enums\CheminPage;
require_once CheminPage::CONTROLLER->value;
require_once CheminPage::MODEL->value;
// Ajouter cette ligne pour inclure les fonctions de session
require_once CheminPage::SESSION_SERVICE->value;

// Définir la page par défaut
$page = $_GET['page'] ?? 'login';
// Résolution des routes
match ($page) {
    'login', 'logout' => (function () {
        require_once CheminPage::AUTH_CONTROLLER->value;
        voir_page_login();
    })(),
    'resetPassword' => (function () {
        require_once CheminPage::AUTH_CONTROLLER->value;
    })(),
    'dashbord' => (function () {
        require_once CheminPage::AUTH_CONTROLLER->value;
        voir_page_app();
        exit;
    })(),
 'form' => (function () {
    require_once CheminPage::PROMO_CONTROLLER->value;
    afficher_formulaire_promotion();
})(),

'ajouter_promotion' => (function () {
    require_once CheminPage::PROMO_CONTROLLER->value;
    ajouter_promotion();
})(),


'liste_apprenant' => (function () {
        require_once CheminPage::APPRENANT_CONTROLLER->value;
        lister_apprenant();
    })(),
    'liste_promo' => (function () {
        require_once CheminPage::PROMO_CONTROLLER->value;
        // Vérifier si l'utilisateur est connecté
        demarrer_session();
        if (!session_existe('user')) {
            redirect_to_route('index.php', ['page' => 'login']);
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nom_promo'])) {
            traiter_creation_promotion();
        } else {
            afficher_promotions();
        }
    })(),



    'import_apprenants' => (function () {
        require_once CheminPage::APPRENANT_CONTROLLER->value;
        importer_apprenants();
    })(),
    'liste_table_promo' => (function () {
        require_once CheminPage::PROMO_CONTROLLER->value;
        // Vérifier si l'utilisateur est connecté
        demarrer_session();
        if (!session_existe('user')) {
            redirect_to_route('index.php', ['page' => 'login']);
            exit;
        }
        
        afficher_promotions_en_table();
    })(),
   
    'changer_statut_promo' => (function () {
        require_once CheminPage::PROMO_CONTROLLER->value;
        changer_statut_promotion();
    })(),
    'layout' => (function () {
        require_once CheminPage::LAYOUT_CONTROLLER->value;
    })(),
    'referenciel' => (function () {
        require_once CheminPage::REFERENCIEL_CONTROLLER->value;
        // Vérifier si l'utilisateur est connecté
        demarrer_session();
        if (!session_existe('user')) {
            redirect_to_route('index.php', ['page' => 'login']);
            exit;
        }

        afficher_referenciels_promotion_active();
    })(),
'ajout_ref' => (function() {
        require_once CheminPage::REFERENCIEL_CONTROLLER->value;
        // Vérifier si l'utilisateur est connecté
        demarrer_session();
        if (!session_existe('user')) {
            redirect_to_route('index.php', ['page' => 'login']);
            exit;
        }
        
        afficher_form_ref();
    })(),
 'affecter_referentiel' => (function () {
    require_once CheminPage::REFERENCIEL_CONTROLLER->value;
    // Vérifier si l'utilisateur est connecté
    demarrer_session();
    if (!session_existe('user')) {
        redirect_to_route('index.php', ['page' => 'login']);
        exit;
    }

    // Appeler la fonction pour affecter un référentiel à la promotion active
    affecter_referenciel_a_promo_active();
})(),
'affecter_ref_promo' => (function () {
    require_once CheminPage::REFERENCIEL_CONTROLLER->value;
    // Vérifier si l'utilisateur est connecté
    demarrer_session();
    if (!session_existe('user')) {
        redirect_to_route('index.php', ['page' => 'login']);
        exit;
    }

    // Appeler la fonction pour afficher la page d'affectation
    afficher_form_ref();
})(),
'desaffecter_referentiel' => (function () {
    require_once CheminPage::REFERENCIEL_CONTROLLER->value;
    // Vérifier si l'utilisateur est connecté
    demarrer_session();
    if (!session_existe('user')) {
        redirect_to_route('index.php', ['page' => 'login']);
        exit;
    }

    // Appeler la fonction pour désaffecter un référentiel de la promotion active
    desaffecter_referenciel_de_promo_active();
})(),
    'all_referenciel' => (function() {
        require_once CheminPage::REFERENCIEL_CONTROLLER->value;
        // Vérifier si l'utilisateur est connecté
        demarrer_session();
        if (!session_existe('user')) {
            redirect_to_route('index.php', ['page' => 'login']);
            exit;
        }
        
        afficher_tous_les_referentiels();
    })(),
    'creer_referentiel' => (function () {
        require_once CheminPage::REFERENCIEL_CONTROLLER->value;
        creer_referentiel();
    })(),





    // 'apprenant' => (function () {
    //     require_once CheminPage::APPRENANT_CONTROLLER->value;
    //     demarrer_session();
    //     if (!session_existe('user')) {
    //         redirect_to_route('index.php', ['page' => 'login']);
    //         exit;
    //     }

    //     afficher_apprenants();
    // })(),
    'ajout_app' => (function () {
        require_once CheminPage::APPRENANT_CONTROLLER->value;
        // Vérifier si l'utilisateur est connecté
        demarrer_session();
        if (!session_existe('user')) {
            redirect_to_route('index.php', ['page' => 'login']);
            exit;
        }

        afficher_formulaire_apprenant();
    })(),
    'ajouter_apprenant' => (function () {
        require_once CheminPage::APPRENANT_CONTROLLER->value;
        // Vérifier si l'utilisateur est connecté
        demarrer_session();
        if (!session_existe('user')) {
            redirect_to_route('index.php', ['page' => 'login']);
            exit;
        }

        traiter_ajout_apprenant();
    })(),
    'detailapp' => (function () {
        require_once CheminPage::APPRENANT_CONTROLLER->value;
        // Vérifier si l'utilisateur est connecté
        demarrer_session();
        if (!session_existe('user')) {
            redirect_to_route('index.php', ['page' => 'login']);
            exit;
        }

        afficher_detail_apprenant();
    })(),
// 'upload_excel' => (function () {
//     require_once CheminPage::APPRENANT_CONTROLLER->value;
//     // Vérifier si l'utilisateur est connecté
//     demarrer_session();
//     if (!session_existe('user')) {
//         redirect_to_route('index.php', ['page' => 'login']);
//         exit;
//     }

//     upload_excel();
// })(),
    'error' => (function () {
        require_once __DIR__ . '/../controllers/error.controller.php';
        showError("Page introuvable");
    })(),

    default => (function () use ($page) {
        require_once __DIR__ . '/../controllers/error.controller.php';
        showError("404 - Page '$page' non reconnue");
    })()
};