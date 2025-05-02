<?php
require_once __DIR__ . '/../enums/chemin_page.php';
require_once __DIR__ . '/../models/ref.model.php';
require_once __DIR__ . '/../models/model.php';

use App\Enums\CheminPage;
use App\Models\REFMETHODE;

function paginer(array $elements, int $parPage = 3): array {
    $page = isset($_GET['p']) ? max(1, (int) $_GET['p']) : 1; // Page actuelle
    $totalElements = count($elements); // Nombre total d'éléments
    $totalPages = ceil($totalElements / $parPage); // Nombre total de pages
    $debut = ($page - 1) * $parPage; // Index de départ pour la page actuelle
    $elementsPage = array_slice($elements, $debut, $parPage); // Éléments pour la page actuelle

    return [
        'elements' => $elementsPage,
        'page' => $page,
        'totalPages' => $totalPages,
        'totalElements' => $totalElements
    ];
}

function afficher_referenciels(): void {
    $cheminFichier = __DIR__ . '/../data/data.json'; // Chemin vers le fichier JSON
    $data = json_decode(file_get_contents($cheminFichier), true);

    // Trouver la promotion active
    $promotionActive = null;
    foreach ($data['promotions'] as $promo) {
        if (isset($promo['statut']) && strtolower($promo['statut']) === 'active') {
            $promotionActive = $promo;
            break;
        }
    }

    if (!$promotionActive) {
        $referentielsAssocies = [];
        $promotionActive = null;
    } else {
        // Filtrer les référentiels associés à la promotion active
        $referentielsAssocies = array_filter($data['referenciel'], function ($ref) use ($promotionActive) {
            return in_array($ref['id'], $promotionActive['referenciel_ids'] ?? []);
        });
    }
    $pagination = paginer($referentielsAssocies, 3); // 6 éléments par page

    // Passer les données à la vue
    render('referenciel/referenciel', [
        'referenciel' => $pagination['elements'],
        'promotion_active' => $promotionActive,
        'page' => $pagination['page'],
        'totalPages' => $pagination['totalPages']
    ]);
}

function afficher_tous_les_referentiels(): void {
    $cheminFichier = __DIR__ . '/../data/data.json'; // Chemin vers le fichier JSON
    $data = json_decode(file_get_contents($cheminFichier), true);

    // Charger les référentiels
    $referenciel = $data['referenciel'] ?? [];
    $pagination = paginer($referenciel, 3); // 6 éléments par page

    // Passer les données à la vue
    render('referenciel/all_referenciel', [
        'referenciel' => $pagination['elements'],
        'page' => $pagination['page'],
        'totalPages' => $pagination['totalPages']
    ]);
}

function ajouter_referenciel(): void {
    global $ref_model;
    
    if (empty($_POST['nom']) || empty($_POST['capacite'])) {
        // Gérer l'erreur
        return;
    }
    
    $cheminPhoto = gerer_upload_photo($_FILES['photo']);
   
    $nouveau_ref = [
        'id' => time(), // Utilisation du timestamp comme ID
        'nom' => $_POST['nom'],
        'capacite' => (int)$_POST['capacite'],
        'photo' => $cheminPhoto,
        'modules' => 0,
        'apprenants' => 0
    ];
    
    // Ajout dans le JSON
    $ref_model[REFMETHODE::AJOUTER->value]($nouveau_ref);
    
    // Redirection
    header('Location: ?page=referenciel');
    exit;
}

function affecter_referentiel(): void {
    $cheminFichier = __DIR__ . '/../data/data.json'; // Chemin vers le fichier JSON
    $data = json_decode(file_get_contents($cheminFichier), true);

    // Récupérer les données du formulaire
    $referencielIds = $_POST['referenciel_ids'] ?? null;
    $promotionId = $_POST['promotion_id'] ?? null;

    if (!$referencielIds || !$promotionId) {
        ajouter_message('error', 'Tous les champs sont obligatoires.');
        header('Location: ?page=referenciel');
        exit;
    }

    // Trouver la promotion et ajouter le référentiel
    foreach ($data['promotions'] as &$promo) {
        if ($promo['id'] === $promotionId) {
            // Ajouter l'ID du référentiel à la liste des référentiels associés
            $promo['referenciel_ids'] = array_merge($promo['referenciel_ids'] ?? [], [$referencielId]);
            break;
        }
    }

    // Sauvegarder les modifications dans le fichier JSON
    file_put_contents($cheminFichier, json_encode($data, JSON_PRETTY_PRINT));

    // Redirection avec message de succès
    ajouter_message('success', 'Référentiel affecté à la promotion active avec succès.');
    header('Location: ?page=referenciel');
    exit;
}

function creer_referentiel(): void {
    $cheminFichier = __DIR__ . '/../data/data.json'; // Chemin vers le fichier JSON
    $data = json_decode(file_get_contents($cheminFichier), true);

    // Vérifier si un fichier a été téléchargé
    if (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
        ajouter_message('error', 'Erreur lors du téléchargement de l\'image.');
        header('Location: ?page=all_referenciel');
        exit;
    }

    // Déplacer le fichier téléchargé vers le dossier des images
    $dossierCible = __DIR__ . '/../../uploads/referentiels/';
    if (!is_dir($dossierCible)) {
        mkdir($dossierCible, 0777, true); // Créer le dossier s'il n'existe pas
    }

    $nomFichier = uniqid() . '-' . basename($_FILES['photo']['name']);
    $cheminPhoto = $dossierCible . $nomFichier;

    if (!move_uploaded_file($_FILES['photo']['tmp_name'], $cheminPhoto)) {
        ajouter_message('error', 'Impossible de sauvegarder l\'image.');
        header('Location: ?page=all_referenciel');
        exit;
    }

    // Construire le chemin relatif pour l'affichage
    $cheminPhotoRelatif = '/uploads/referentiels/' . $nomFichier;

    // Ajouter le nouveau référentiel
    $nouveauReferentiel = [
        'id' => uniqid(),
        'nom' => $_POST['libelle_ref'],
        'description' => $_POST['description'],
        'capacite' => (int)$_POST['capacite'],
        'photo' => $cheminPhotoRelatif,
        'modules' => 0,
        'apprenants' => 0
    ];

    $data['referenciel'][] = $nouveauReferentiel;

    // Sauvegarder les modifications dans le fichier JSON
    file_put_contents($cheminFichier, json_encode($data, JSON_PRETTY_PRINT));

    ajouter_message('success', 'referenciel créé avec succès.');
    header('Location: ?page=all_referenciel');
    exit;
}

function ajouter_referenciel_a_promotion(): void {
    $cheminFichier = __DIR__ . '/../data/data.json';
    $data = json_decode(file_get_contents($cheminFichier), true);

    $nouveauReferenciel = [
        'id' => uniqid(),
        'nom' => $_POST['nom'],
        'capacite' => (int)$_POST['capacite'],
        'photo' => $_POST['photo'],
        'modules' => 0,
        'apprenants' => 0
    ];

    $data['referenciel'][] = $nouveauReferenciel;

    file_put_contents($cheminFichier, json_encode($data, JSON_PRETTY_PRINT));
    $_SESSION['success'] = "Référentiel ajouté à la promotion avec succès.";
    header('Location: ?page=promotions');
    exit;
}

function ajouter_promotion(): void {
    $cheminFichier = __DIR__ . '/../data/data.json'; // Chemin vers le fichier JSON
    $data = json_decode(file_get_contents($cheminFichier), true);

    // Récupérer les données du formulaire
    $nomPromotion = $_POST['nom_promo'] ?? null;
    $dateDebut = $_POST['date_debut'] ?? null;
    $dateFin = $_POST['date_fin'] ?? null;
    $referentielIds = $_POST['referenciel_ids'] ?? []; // Tableau d'IDs de référentiels
    $nbrApprenants = $_POST['nbr_apprenants'] ?? null;

    if (!$nomPromotion || !$dateDebut || !$dateFin || empty($referentielIds)) {
        ajouter_message('error', 'Tous les champs sont obligatoires.');
        header('Location: ?page=liste_promo');
        exit;
    }

    // Vérifier si les référentiels existent
    $referentielsValides = [];
    foreach ($referentielIds as $referentielId) {
        foreach ($data['referenciel'] as $ref) {
            if ($ref['id'] === $referentielId) {
                $referentielsValides[] = $referentielId;
                break;
            }
        }
    }

    if (empty($referentielsValides)) {
        ajouter_message('error', 'Les référentiels sélectionnés sont invalides.');
        header('Location: ?page=liste_promo');
        exit;
    }

    // Ajouter la promotion
    $nouvellePromotion = [
        'id' => time(), // Utilisation du timestamp comme ID unique
        'nom' => $nomPromotion,
        'dateDebut' => $dateDebut,
        'dateFin' => $dateFin,
        'referenciel_ids' => $referentielsValides, // Liste des IDs de référentiels
        'photo' => '', // Vous pouvez ajouter un champ pour la photo si nécessaire
        'statut' => 'Inactive', // Par défaut, la promotion est inactive
        'nbrApprenant' => (int)$nbrApprenants
    ];

    $data['promotions'][] = $nouvellePromotion;

    // Sauvegarder les modifications dans le fichier JSON
    file_put_contents($cheminFichier, json_encode($data, JSON_PRETTY_PRINT));

    ajouter_message('success', 'Promotion ajoutée avec succès.');
    header('Location: ?page=liste_promo');
    exit;
}

function affecter_referentiels_a_promotion(): void {
    global $ref_model, $validator;

    try {
        // ID de la promotion active
        $promotionId = '1'; // Exemple d'ID de promotion

        // IDs des référentiels à affecter
        $referencielIds = ['6807cd9467f6c', '6808a4121f28f'];

        // Valider chaque référentiel
        foreach ($referentielIds as $referentielId) {
            $validator[VALIDATORMETHODE::VALIDATE_AFFECTER_REF->value]($referencielId);
        }

        // Affecter les référentiels à la promotion
        $ref_model[REFMETHODE::AFFECTER_PROMOTION->value]($promotionId, $referencielIds);

        echo "Les référentiels ont été affectés avec succès à la promotion.";
    } catch (Exception $e) {
        echo "Erreur : " . $e->getMessage();
    }
}

function affecter_referenciel_a_promo_active(): void {
    $cheminFichier = __DIR__ . '/../data/data.json'; // Chemin vers le fichier JSON
    $data = json_decode(file_get_contents($cheminFichier), true);

    // Vérifier la méthode HTTP et les données du formulaire
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['referenciel_ids'])) {
        ajouter_message('error', 'Aucun référentiel sélectionné.');
        header('Location: ?page=affecter_ref_promo');
        exit;
    }

    $referencielId = (int) $_POST['referenciel_ids'];

    // Trouver la promotion active
    $promotionActive = null;
    foreach ($data['promotions'] as &$promo) {
        if ($promo['statut'] === 'Active') {
            $promotionActive = &$promo;
            break;
        }
    }

    if (!$promotionActive) {
        ajouter_message('error', 'Aucune promotion active trouvée.');
        header('Location: ?page=affecter_ref_promo');
        exit;
    }

    // Ajouter le référentiel à la promotion active
    if (!in_array($referencielId, $promotionActive['referenciel_ids'] ?? [])) {
        $promotionActive['referenciel_ids'][] = $referencielId;
    }

    // Sauvegarder les modifications
    file_put_contents($cheminFichier, json_encode($data, JSON_PRETTY_PRINT));

    ajouter_message('success', 'Référentiel affecté avec succès à la promotion active.');
    header('Location: ?page=affecter_ref_promo');
    exit;
}

function afficher_referenciels_promo(): void {
    $cheminFichier = __DIR__ . '/../data/data.json'; // Chemin vers le fichier JSON
    $data = json_decode(file_get_contents($cheminFichier), true);

    // Trouver la promotion active
    $promotionActive = null;
    foreach ($data['promotions'] as $promo) {
        if ($promo['statut'] === 'Active') {
            $promotionActive = $promo;
            break;
        }
    }

    $referentielsAffectes = [];
    if ($promotionActive && !empty($promotionActive['referenciel_ids'])) {
        $referentielsAffectes = array_filter($data['referenciel'], function ($ref) use ($promotionActive) {
            return in_array($ref['id'], $promotionActive['referenciel_ids']);
        });
    }

    $referentielsNonAffectes = array_filter($data['referenciel'], function ($ref) use ($referentielsAffectes) {
        return !in_array($ref, $referentielsAffectes);
    });

    // Passer les données à la vue
    render('referenciel/affecter_ref_promo', [
        'referenciels_affectes' => $referentielsAffectes,
        'referenciels_non_affectes' => $referentielsNonAffectes,
        'promotion_active' => $promotionActive
    ]);
}

//-----------------------------------------------------------------------------
//ajout ref
function afficher_form_ref(): void {
    $cheminFichier = __DIR__ . '/../data/data.json'; // Chemin vers le fichier JSON
    $data = json_decode(file_get_contents($cheminFichier), true);

    // Calculer les statistiques
    $nbApprenants = count($data['apprenants'] ?? []);
    $nbReferentiels = count($data['referenciel'] ?? []);
    $nbPromotionsActives = count(array_filter($data['promotions'] ?? [], fn($promo) => strtolower($promo['statut']) === 'active'));
    $nbPromotions = count($data['promotions'] ?? []);

    // Trouver la promotion active
    $promotion_active = null;
    foreach ($data['promotions'] as $promo) {
        if (isset($promo['statut']) && strtolower($promo['statut']) === 'active') {
            $promotion_active = $promo;
            break;
        }
    }

    // Récupérer les référentiels non affectés
    $referentiels_non_affectes = array_filter($data['referenciel'], function ($ref) use ($promotion_active) {
        return !in_array($ref['id'], $promotion_active['referenciel_ids'] ?? []);
    });

    // Récupérer les référentiels affectés
    $referentiels_affectes = array_filter($data['referenciel'], function ($ref) use ($promotion_active) {
        return in_array($ref['id'], $promotion_active['referenciel_ids'] ?? []);
    });

    // Passer les données à la vue
    render('referenciel/ajout_ref', [
        'nbApprenants' => $nbApprenants,
        'nbReferentiels' => $nbReferentiels,
        'nbPromotionsActives' => $nbPromotionsActives,
        'nbPromotions' => $nbPromotions,
        'referentiels_non_affectes' => $referentiels_non_affectes,
        'referentiels_affectes' => $referentiels_affectes,
        'promotion_active' => $promotion_active
    ]);
}

function affecter_referentiels(): void {
    $cheminFichier = __DIR__ . '/../data/data.json'; // Chemin vers le fichier JSON
    $data = json_decode(file_get_contents($cheminFichier), true);

    // Récupérer les données du formulaire
    $referentielIds = $_POST['referentiel_ids'] ?? []; // Référentiels sélectionnés

    // Trouver la promotion active
    foreach ($data['promotions'] as &$promo) {
        if (isset($promo['statut']) && strtolower($promo['statut']) === 'active') {
            $promo['referenciel_ids'] = array_unique($referentielIds); // Mettre à jour les référentiels affectés
            break;
        }
    }

    // Sauvegarder les modifications
    file_put_contents($cheminFichier, json_encode($data, JSON_PRETTY_PRINT));

    // Rediriger avec un message de succès
    ajouter_message('success', 'Les référentiels ont été affectés avec succès.');
    header('Location: ?page=ajout_ref');
    exit;
}

function afficher_ajout_ref(): void {
    $cheminFichier = __DIR__ . '/../data/data.json'; // Chemin vers le fichier JSON
    $data = json_decode(file_get_contents($cheminFichier), true);

    // Trouver la promotion active
    $promotion_active = null;
    foreach ($data['promotions'] as $promo) {
        if (isset($promo['statut']) && strtolower($promo['statut']) === 'active') {
            $promotion_active = $promo;
            break;
        }
    }

    // Récupérer les référentiels non affectés
    $referentiels_non_affectes = array_filter($data['referenciel'], function ($ref) use ($promotion_active) {
        return !in_array($ref['id'], $promotion_active['referenciel_ids'] ?? []);
    });

    // Récupérer les référentiels affectés
    $referentiels_affectes = array_filter($data['referenciel'], function ($ref) use ($promotion_active) {
        return in_array($ref['id'], $promotion_active['referenciel_ids'] ?? []);
    });

    // Calculer les statistiques
    $nbApprenants = count($data['apprenants'] ?? []);
    $nbReferentiels = count($data['referenciel'] ?? []);
    $nbPromotionsActives = count(array_filter($data['promotions'], fn($promo) => strtolower($promo['statut']) === 'active'));
    $nbPromotions = count($data['promotions'] ?? []);

    // Passer les données à la vue
    render('referenciel/ajout_ref', [
        'nbApprenants' => $nbApprenants,
        'nbReferentiels' => $nbReferentiels,
        'nbPromotionsActives' => $nbPromotionsActives,
        'nbPromotions' => $nbPromotions,
        'referentiels_non_affectes' => $referentiels_non_affectes,
        'referentiels_affectes' => $referentiels_affectes,
        'promotion_active' => $promotion_active
    ]);
}

function desaffecter_referenciel_de_promo_active(): void {
    $cheminFichier = __DIR__ . '/../data/data.json'; // Chemin vers le fichier JSON
    $data = json_decode(file_get_contents($cheminFichier), true);

    // Vérifier la méthode HTTP et les données du formulaire
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['referenciel_ids'])) {
        ajouter_message('error', 'Aucun référentiel sélectionné.');
        header('Location: ?page=affecter_ref_promo');
        exit;
    }

    $referencielId = (int) $_POST['referenciel_ids'];

    // Trouver la promotion active
    $promotionActive = null;
    foreach ($data['promotions'] as &$promo) {
        if ($promo['statut'] === 'Active') {
            $promotionActive = &$promo;
            break;
        }
    }

    if (!$promotionActive) {
        ajouter_message('error', 'Aucune promotion active trouvée.');
        header('Location: ?page=affecter_ref_promo');
        exit;
    }

    // Retirer le référentiel de la promotion active
    $promotionActive['referenciel_ids'] = array_filter(
        $promotionActive['referenciel_ids'] ?? [],
        fn($id) => $id !== $referencielId
    );

    // Sauvegarder les modifications
    file_put_contents($cheminFichier, json_encode($data, JSON_PRETTY_PRINT));

    ajouter_message('success', 'Référentiel désaffecté avec succès de la promotion active.');
    header('Location: ?page=affecter_ref_promo');
    exit;
}

function afficher_referentiels_non_affectes(): void {
    global $ref_model;

    $referentiels_non_affectes = $ref_model[REFMETHODE::GET_NON_AFFECTES->value]();

    render('referentiel/affecter', [
        'referentiels_non_affectes' => $referentiels_non_affectes
    ]);
}

function afficher_referenciels_promotion_active(): void {
    $cheminFichier = __DIR__ . '/../data/data.json'; // Chemin vers le fichier JSON
    $data = json_decode(file_get_contents($cheminFichier), true);

    // Trouver la promotion active
    $promotion_active = null;
    foreach ($data['promotions'] as $promo) {
        if (isset($promo['statut']) && strtolower($promo['statut']) === 'active') {
            $promotion_active = $promo;
            break;
        }
    }

    // Récupérer les référentiels associés à la promotion active
    $referenciel = [];
    if ($promotion_active && !empty($promotion_active['referenciel_ids'])) {
        $referenciel = array_filter($data['referenciel'], function ($ref) use ($promotion_active) {
            return in_array($ref['id'], $promotion_active['referenciel_ids']);
        });
    }

    // Passer les données à la vue
    render('referenciel/referenciel', [
        'promotion_active' => $promotion_active,
        'referenciel' => $referenciel
    ]);
}





