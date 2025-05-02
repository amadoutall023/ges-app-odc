<?php
global $model_tab;
require_once __DIR__ . '/../enums/model.enum.php';
require_once __DIR__ . '/../enums/chemin_page.php';

use App\Enums\CheminPage;
use App\Models\JSONMETHODE;
use App\Models\PROMOMETHODE;

$json = CheminPage::DATA_JSON->value;

$jsontoarray = $model_tab[JSONMETHODE::JSONTOARRAY->value];
global $promos;

$promos = [
    // Récupérer toutes les promotions
    PROMOMETHODE::GET_ALL->value => fn() => $jsontoarray($json, "promotions"),

    // Ajouter une nouvelle promotion
    PROMOMETHODE::AJOUTER_PROMO->value => function (array $nouvellePromo, string $chemin): bool {
        global $model_tab;

        // Charger les données existantes depuis le fichier JSON
        $data = $model_tab[JSONMETHODE::JSONTOARRAY->value]($chemin);

        if (!isset($data['promotions'])) {
            $data['promotions'] = [];
        }

        // Ajout de la nouvelle promotion
        $data['promotions'][] = $nouvellePromo;

        // Sauvegarde des modifications dans le fichier JSON
        return $model_tab[JSONMETHODE::ARRAYTOJSON->value]($data, $chemin);
    },

    // Activer une promotion et désactiver les autres
    PROMOMETHODE::ACTIVER_PROMO->value => function (int $idPromo, string $chemin): bool {
        global $model_tab;

        // Charger les données existantes depuis le fichier JSON
        $data = $model_tab[JSONMETHODE::JSONTOARRAY->value]($chemin);

        if (!isset($data['promotions'])) {
            return false; // Aucune promotion à activer
        }

        // Parcourir les promotions pour mettre à jour leur statut
        foreach ($data['promotions'] as &$promo) {
            $promo['statut'] = ($promo['id'] === $idPromo) ? 'Active' : 'Inactive';
        }

        // Sauvegarde des modifications dans le fichier JSON
        return $model_tab[JSONMETHODE::ARRAYTOJSON->value]($data, $chemin);
    },
    PROMOMETHODE::GET_ACTIVE_PROMO->value => function () use ($jsontoarray, $json) {
        $promotions = $jsontoarray($json, "promotions");
        if (!$promotions) return null;

        foreach ($promotions as $promo) {
            if ($promo['statut'] === 'Active') {
                $dateActuelle = new DateTime();
                $dateDebut = new DateTime($promo['date_debut']);
                $dateFin = new DateTime($promo['date_fin']);

                // Vérifier si la promotion est en cours
                if ($dateActuelle >= $dateDebut && $dateActuelle <= $dateFin) {
                    return $promo;
                }
            }
        }
        return null;
    },

    PROMOMETHODE::GET_BY_ID->value => function (int $idPromo) use ($jsontoarray, $json) {
        $promotions = $jsontoarray($json, "promotions");
        if (!$promotions) return null;

        foreach ($promotions as $promo) {
            if ($promo['id'] === $idPromo) {
                return $promo;
            }
        }
        return null;
    }
];

