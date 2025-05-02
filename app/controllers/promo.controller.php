<?php 
require_once __DIR__ . '/../enums/chemin_page.php'; 
require_once __DIR__ . '/../enums/model.enum.php'; 
require_once __DIR__ . '/../enums/message.enum.php'; 
require_once __DIR__ . '/../enums/erreur.enum.php'; 
require_once __DIR__ . '/../services/session.service.php';
require_once __DIR__ . '/../services/validator.service.php';  

use App\ENUM\ERREUR\ErreurEnum; 
use App\Enums\CheminPage; 
use App\Models\PROMOMETHODE; 
use App\Models\JSONMETHODE; 
use App\ENUM\MESSAGE\MSGENUM; 
use App\ENUM\VALIDATOR\VALIDATORMETHODE; 
require_once CheminPage::PROMO_MODEL->value;  

function afficher_promotions($message = null, $errors = []): void {
    global $promos, $total, $model_tab;

    $cheminFichier = CheminPage::DATA_JSON->value;

    // ✅ Récupérer toutes les promotions
    $liste_promos = $promos["get_all"]();

    // ✅ Appliquer recherche et filtre avant pagination
    $liste_promos = afficher_promotions_filtrees($liste_promos);

    // ✅ Appliquer la pagination
    $pagination = paginer($liste_promos, 5);

    // ✅ Charger les référentiels
    $data = $model_tab[JSONMETHODE::JSONTOARRAY->value]($cheminFichier);
    $referenciel = $data['referenciel'] ?? [];

    // ✅ Statistiques globales (ou basées sur $liste_promos filtré, si tu préfères)
    $nbPromotions = count($liste_promos);
    $nbPromotionsActives = 0;
    $nbReferenciel = count($referenciel);
    $nbApprenants = 0;

    foreach ($liste_promos as $promo) {
        if (isset($promo['statut']) && strtolower($promo['statut']) === 'active') {
            $nbPromotionsActives++;
        }
        if (isset($promo['nbrApprenant'])) {
            $nbApprenants += (int) $promo['nbrApprenant'];
        }
    }

    // ✅ Afficher la vue
    render("promo/promo", [
        "promotions" => $pagination['elements'],
        "message" => $message,
        "total" => $pagination['totalPages'],
        "debut" => $pagination['debut'],
        "parPage" => $pagination['parPage'],
        "totalPromotions" => $pagination['totalElements'],
        "errors" => $errors,
        "nbApprenants" => $nbApprenants,
        "nbReferentiels" => $nbReferenciel,
        "nbPromotionsActives" => $nbPromotionsActives,
        "nbPromotions" => $nbPromotions,
        "referenciel" => $referenciel
    ]);
}

function afficher_promotions_filtrees(array $promotions): array {
    $filtreStatut = $_GET['filtre_statut'] ?? 'tous';
    $search = $_GET['search'] ?? '';

    // Filtrer par statut
    $promotionsFiltrees = array_filter($promotions, function ($promo) use ($filtreStatut) {
        return $filtreStatut === 'tous' || $promo['statut'] === $filtreStatut;
    });

    // Filtrer par recherche
    if (!empty($search)) {
        $promotionsFiltrees = array_filter($promotionsFiltrees, function ($promo) use ($search) {
            return stripos($promo['nom'], $search) !== false;
        });
    }

    return $promotionsFiltrees;
}

function charger_promotions_existantes(string $chemin): array {
    global $model_tab;
    return $model_tab[JSONMETHODE::JSONTOARRAY->value]($chemin);
}

function charger_donnees(): array {
    $cheminFichier = __DIR__ . '/../data/data.json';
    if (!file_exists($cheminFichier)) {
        return ['promotions' => [], 'referenciel' => []];
    }

    $data = json_decode(file_get_contents($cheminFichier), true);
    return $data;
}

function creer_donnees_promotion(array $post, array $donneesExistantes, string $cheminPhoto): array {
    $promotions = $donneesExistantes['promotions'] ?? [];
    $nouvelId = getNextPromoId($promotions);

    $referenciel_ids = isset($_POST['referenciel_ids']) && is_array($_POST['referenciel_ids']) 
    ? array_map('intval', $_POST['referenciel_ids']) 
    : [];

    return [
        "id" => $nouvelId,
        "nom" => $post['nom_promo'],
        "dateDebut" => $post['date_debut'],
        "dateFin" => $post['date_fin'],
        "referenciel_ids" => $referenciel_ids,
        "photo" => $cheminPhoto,
        "statut" => "Inactive",
        "nbrApprenant" => 0
    ];
}

function afficher_promotions_en_table(): void {
    global $promos;

    // Récupérer toutes les promotions
    $liste_promos = $promos["get_all"]();

    // Récupérer le critère de recherche depuis l'URL
    $searchTerm = $_GET['search'] ?? '';

    // Appliquer la recherche
    $promotions = rechercher_promotions($liste_promos, $searchTerm);

    // Paramètres de pagination
    $perPage = isset($_GET['limit']) ? (int)$_GET['limit'] : 5; // Nombre d'éléments par page (par défaut 5)
    $page = isset($_GET['p']) ? max(1, (int)$_GET['p']) : 1; // Page actuelle

    // Appliquer la pagination
    $pagination = paginer_promotions($promotions, $perPage, $page);

    // Passer les promotions paginées à la vue
    render("promo/liste_promo", [
        'promotions' => $pagination['elements'], // Promotions pour la page actuelle
        'page' => $pagination['page'],          // Page actuelle
        'totalPages' => $pagination['totalPages'], // Nombre total de pages
        'perPage' => $pagination['parPage'],    // Nombre d'éléments par page
        'totalPromotions' => $pagination['totalElements'], // Nombre total de promotions
        'start' => $pagination['debut']        // Index de départ
    ]);
}

function traiter_activation_promotion(): void {
    global $promos;
    
    if (isset($_GET['activer_promo'])) {
        $idPromo = (int) $_GET['activer_promo'];
        $cheminFichier = CheminPage::DATA_JSON->value;
        
        $promos[PROMOMETHODE::ACTIVER_PROMO->value]($idPromo, $cheminFichier);
    }
}

function changer_statut_promotion(): void {
    $cheminFichier = CheminPage::DATA_JSON->value; // Chemin vers le fichier JSON
    $data = json_decode(file_get_contents($cheminFichier), true);

    $promoId = $_POST['promo_id'] ?? null;
    $nouveauStatut = $_POST['nouveau_statut'] ?? null;

    if (!$promoId || !$nouveauStatut) {
        $_SESSION['error'] = "Données invalides.";
        header('Location: ?page=liste_promo');
        exit;
    }

    // Si le nouveau statut est "Active", désactiver toutes les autres promotions
    if ($nouveauStatut === 'Active') {
        foreach ($data['promotions'] as &$promo) {
            if ($promo['statut'] === 'Active') {
                $promo['statut'] = 'Inactive';
            }
        }
    }

    // Trouver la promotion et changer son statut
    foreach ($data['promotions'] as &$promo) {
        if ($promo['id'] == $promoId) {
            $promo['statut'] = $nouveauStatut;
            break;
        }
    }

    // Sauvegarder les modifications dans le fichier JSON
    file_put_contents($cheminFichier, json_encode($data, JSON_PRETTY_PRINT));

    // Redirection après succès
    header('Location: ?page=liste_promo');
    exit;
}

function paginer(array $elements, int $parPage = 5): array {
    // Récupérer la page actuelle depuis les paramètres GET
    $page = isset($_GET['p']) ? max(1, (int)$_GET['p']) : 1;

    // Calculer le nombre total d'éléments et de pages
    $totalElements = count($elements);
    $totalPages = ceil($totalElements / $parPage);

    // Calculer l'index de départ pour la page actuelle
    $debut = ($page - 1) * $parPage;

    // Extraire les éléments pour la page actuelle
    $elementsPage = array_slice($elements, $debut, $parPage);

    // Retourner les données de pagination
    return [
        'elements' => $elementsPage, // Les éléments pour la page actuelle
        'page' => $page,            // La page actuelle
        'totalPages' => $totalPages, // Nombre total de pages
        'totalElements' => $totalElements, // Nombre total d'éléments
        'debut' => $debut,          // Index de départ
        'parPage' => $parPage       // Nombre d'éléments par page
    ];
}

function paginer_promotions(array $promotions, int $parPage = 5, int $pageActuelle = 1): array {
    // Calculer le nombre total d'éléments et de pages
    $totalElements = count($promotions); // Utilisez $promotions au lieu de $elements
    $totalPages = ceil($totalElements / $parPage);

    // S'assurer que la page actuelle est valide
    $pageActuelle = max(1, min($pageActuelle, $totalPages));

    // Calculer l'index de départ pour la page actuelle
    $debut = ($pageActuelle - 1) * $parPage;

    // Extraire les éléments pour la page actuelle
    $elementsPage = array_slice($promotions, $debut, $parPage);

    // Retourner les données de pagination
    return [
        'elements' => $elementsPage, // Les éléments pour la page actuelle
        'page' => $pageActuelle,     // La page actuelle
        'totalPages' => $totalPages, // Nombre total de pages
        'totalElements' => $totalElements, // Nombre total d'éléments
        'debut' => $debut,           // Index de départ
        'parPage' => $parPage        // Nombre d'éléments par page
    ];
}

// Dans app/controllers/promo.controller.php

function afficher_formulaire_promotion(): void {
    // Démarrer la session
    demarrer_session();

    // Charger les référentiels pour les afficher dans le formulaire
    $cheminFichier = CheminPage::DATA_JSON->value;
    $data = json_decode(file_get_contents($cheminFichier), true);

    $referenciel = $data['referenciel'] ?? [];

    // Passer les données à la vue
    render('promo/form', [
        'referenciel' => $referenciel
    ]);
}

function ajouter_promotion(): void {
    demarrer_session();

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nouvelle_promo'])) {
        $data = [
            'nom_promo' => $_POST['nom_promo'] ?? '',
            'date_debut' => $_POST['date_debut'] ?? '',
            'date_fin' => $_POST['date_fin'] ?? '',
            'referenciel_ids' => $_POST['referenciel_ids'] ?? [],
        ];

        stocker_session('old_inputs', $data);
        $erreurs = valider_donnees_promotion($data);

        if (!empty($erreurs)) {
            stocker_session('errors', $erreurs);
            // Rediriger vers la page du formulaire (pas 'ajout_promo')
            header('Location: index.php?page=form');
            exit;
        }

        // Si validation OK, traiter la création
        traiter_creation_promotion();
    } else {
        header('Location: index.php?page=form');
        exit;
    }
}

function traiter_creation_promotion(): void {
    global $model_tab, $promos;

    $cheminFichier = CheminPage::DATA_JSON->value;

    // Vérifier si les données du formulaire sont soumises
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $donneesExistantes = charger_promotions_existantes($cheminFichier);
        $erreurs = valider_donnees_promotion($_POST);

        if (!empty($erreurs)) {
            // Stocker les erreurs en session pour les afficher dans le formulaire
            stocker_session('errors', $erreurs);
            // Rediriger vers le formulaire d'ajout
            header('Location: index.php?page=form');
            exit;
        }

        try {
            // Vérifier si un fichier a été téléchargé
            $cheminPhoto = null;
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                $cheminPhoto = gerer_upload_photo($_FILES['photo']);
            }

            // Pour les référentiels, prendre le premier référentiel sélectionné
            $referenciel_ids = isset($_POST['referenciel_ids']) && is_array($_POST['referenciel_ids']) 
            ? array_map('intval', $_POST['referenciel_ids']) 
            : [];

            $nouvellePromotion = [
                "id" => getNextPromoId($donneesExistantes['promotions'] ?? []),
                "nom" => $_POST['nom_promo'],
                "dateDebut" => $_POST['date_debut'],
                "dateFin" => $_POST['date_fin'],
                "referenciel_ids" => $referenciel_ids, // Stocker tous les référentiels
                "photo" => $cheminPhoto,
                "statut" => "Inactive",
                "nbrApprenant" => 0
            ];

            $promos[PROMOMETHODE::AJOUTER_PROMO->value]($nouvellePromotion, $cheminFichier);

            // Rediriger vers la liste des promotions avec message de succès
            header('Location: index.php?page=liste_promo&message=success');
            exit;
        } catch (Exception $e) {
            $erreurs[] = $e->getMessage();
            stocker_session('errors', $erreurs);
            header('Location: index.php?page=form');
            exit;
        }
    }

    // Si on arrive ici, rediriger vers le formulaire
    header('Location: index.php?page=form');
    exit;
}

function valider_donnees_promotion(array $donnees): array {
    $erreurs = [];

    // Validation du nom de la promotion
    if (empty($donnees['nom_promo'])) {
        $erreurs['nom_promo'] = 'Le nom de la promotion est requis.';
    }

    // Validation des dates
    if (empty($donnees['date_debut'])) {
        $erreurs['date_debut'] = 'La date de début est requise.';
    }
    if (empty($donnees['date_fin'])) {
        $erreurs['date_fin'] = 'La date de fin est requise.';
    }
    if (!empty($donnees['date_debut']) && !empty($donnees['date_fin'])) {
        $pattern = '/^\d{4}-\d{2}-\d{2}$/';
        if (!preg_match($pattern, $donnees['date_debut']) || !preg_match($pattern, $donnees['date_fin'])) {
            $erreurs['date_debut'] = 'Les dates doivent être au format AAAA-MM-JJ.';
            $erreurs['date_fin'] = 'Les dates doivent être au format AAAA-MM-JJ.';
        } else {
            $debut = strtotime($donnees['date_debut']);
            $fin = strtotime($donnees['date_fin']);
            if ($debut >= $fin) {
                $erreurs['date_combined'] = 'La date de début doit être antérieure à la date de fin.';
            }
        }
    }
// Validation des référentiels
// Validation des référentiels
// if (empty($donnees['referentiel_id']) || !is_array($donnees['referentiel_id']) || count($donnees['referentiel_id']) === 0) {
//     $erreurs['referentiels'] = 'Veuillez sélectionner au moins un référentiel.';
// }

    // Validation des référentiels
    // if (empty($donnees['referenciel_id']) || !is_array($donnees['referenciel_id'])) {
    //     $erreurs['referentiels'] = 'Veuillez sélectionner au moins un référentiel.';
    // }

    return  $erreurs;
}

function affecter_referentiels_a_promotion(): void {
    global $ref_model;

    // ID de la promotion active
    $promotionId = '1'; // Exemple d'ID de promotion

    // IDs des référentiels à affecter
    // $referentielIds = ['6807cd9467f6c', '6808a4121f28f'];

    // Appeler la fonction d'affectation
    $result = $ref_model[REFMETHODE::AFFECTER_PROMOTION->value]($promotionId, $referentielIds);

    if ($result) {
        echo "Les référentiels ont été affectés avec succès à la promotion.";
    } else {
        echo "Échec de l'affectation des référentiels.";
    }
}

function rechercher_promotions(array $promotions, string $critere): array {
    if (empty($critere)) {
        return $promotions;
    }

    return array_filter($promotions, function ($promo) use ($critere) {
        return stripos($promo['nom'], $critere) !== false;
    });
}

function valider_formulaire_promotion(): void {
    global $validator;

    // Exemple de validation pour un formulaire de promotion
    $data = $_POST;
    $errors = $validator[ValidatorMethode::VALID_GENERAL->value]($data);

    if (!empty($errors)) {
        // Gérer les erreurs (par exemple, les afficher dans la vue)
        render('promo/form', ['errors' => $errors, 'data' => $data]);
    } else {
        // Traiter les données valides
        enregistrer_promotion($data);
    }
}

function gerer_upload_photo(array $photo): string {
    // Vérifier si le fichier est valide
    if ($photo['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Erreur lors du téléchargement de la photo.');
    }

    // Vérifier le type MIME du fichier
    $typesAutorises = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($photo['type'], $typesAutorises, true)) {
        throw new Exception('Le fichier doit être une image (JPEG, PNG ou GIF).');
    }

    // Déplacer le fichier vers le répertoire de destination
    $dossierDestination = __DIR__ . '/../uploads/';
    if (!is_dir($dossierDestination)) {
        mkdir($dossierDestination, 0755, true);
    }

    $nomFichier = uniqid('promo_') . '.' . pathinfo($photo['name'], PATHINFO_EXTENSION);
    $cheminComplet = $dossierDestination . $nomFichier;

    if (!move_uploaded_file($photo['tmp_name'], $cheminComplet)) {
        throw new Exception('Impossible de déplacer le fichier téléchargé.');
    }

    return 'uploads/' . $nomFichier;
}
function get_active_promo(): ?array {
    global $model_tab;

    return $model_tab[PROMOMETHODE::GET_ACTIVE_PROMO->value]();

}
