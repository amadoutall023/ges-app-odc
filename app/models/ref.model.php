<?php
require_once __DIR__ . '/../enums/model.enum.php';
require_once __DIR__ . '/../enums/chemin_page.php';

use App\Models\REFMETHODE;
use App\Models\JSONMETHODE;
use App\Enums\CheminPage;

global $ref_model;

$ref_model = [
    REFMETHODE::GET_ALL->value => function(): array {
        global $model_tab;
        $chemin = CheminPage::DATA_JSON->value;
        return $model_tab[JSONMETHODE::JSONTOARRAY->value]($chemin)['referenciel'] ?? [];
    },
    
    REFMETHODE::AJOUTER->value => function(array $referenciel): bool {
        global $model_tab;
        $chemin = CheminPage::DATA_JSON->value;
        $data = $model_tab[JSONMETHODE::JSONTOARRAY->value]($chemin);
        
        if (!isset($data['referenciel'])) {
            $data['referenciel'] = [];
        }
        
        // Ajouter le nouveau référentiel
        $data['referenciel'][] = $referenciel;
        
        return $model_tab[JSONMETHODE::ARRAYTOJSON->value]($data, $chemin);
    },
    
    REFMETHODE::AFFECTER->value => function(int $ref_id, int $promo_id): bool {
        global $model_tab;
        $chemin = CheminPage::DATA_JSON->value;
        $data = $model_tab[JSONMETHODE::JSONTOARRAY->value]($chemin);
        
        // Vérifier si la promotion existe et mettre à jour son référentiel
        if (isset($data['promotions'])) {
            $data['promotions'] = array_map(function($promo) use ($ref_id, $promo_id) {
                if ($promo['id'] === $promo_id) {
                    $promo['referenciel_ids'] = $ref_id;
                }
                return $promo;
            }, $data['promotions']);
            
            return $model_tab[JSONMETHODE::ARRAYTOJSON->value]($data, $chemin);
        }
        
        return false;
    },
    REFMETHODE::AFFECTER_PROMOTION->value => function(string $promotionId, array $referentielIds): bool {
        global $model_tab;
        $chemin = CheminPage::DATA_JSON->value;
        $data = $model_tab[JSONMETHODE::JSONTOARRAY->value]($chemin);

        // Trouver la promotion et mettre à jour les référentiels
        foreach ($data['promotions'] as &$promo) {
            if ($promo['id'] === $promotionId) {
                // Remplacer les référentiels existants par les nouveaux
                $promo['referenciel_ids'] = $referentielIds;
                break;
            }
        }

        // Sauvegarder les modifications
        return $model_tab[JSONMETHODE::ARRAYTOJSON->value]($data, $chemin);
    },
    REFMETHODE::AFFECTER->value => function(int $ref_id, int $promo_id): bool {
        global $model_tab;
        $chemin = CheminPage::DATA_JSON->value;
        $data = $model_tab[JSONMETHODE::JSONTOARRAY->value]($chemin);
        
        // Vérifier si la promotion existe et mettre à jour son référentiel
        if (isset($data['promotions'])) {
            $data['promotions'] = array_map(function($promo) use ($ref_id, $promo_id) {
                if ($promo['id'] === $promo_id) {
                    $promo['referenciel_ids'] = $ref_id;
                }
                return $promo;
            }, $data['promotions']);
            
            return $model_tab[JSONMETHODE::ARRAYTOJSON->value]($data, $chemin);
        }
        
        return false;
    },
    REFMETHODE::GET_NON_AFFECTES->value => function(): array {
        global $model_tab;
        $chemin = CheminPage::DATA_JSON->value;
    
        $data = $model_tab[JSONMETHODE::JSONTOARRAY->value]($chemin);
        $referentiels = $data['referenciel'] ?? [];
        $promos = $data['promotions'] ?? [];
    
        // Trouver la promo active
        $promo_active = current(
            array_filter($promos, fn($promo) => ($promo['statut'] ?? '') === 'Active')
        );
    
        $ids_actifs = is_array($promo_active) && isset($promo_active['referenciel'])
            ? $promo_active['referenciel']
            : [];
    
        // Renvoyer les référentiels qui ne sont PAS dans la promo active
        return array_filter($referentiels, fn($ref) => !in_array($ref['id'], $ids_actifs));
    },
    REFMETHODE::AFFECTER_REF_PROMO_ACTIVE->value => function(int $ref_id): bool {
        global $model_tab;
        $chemin = CheminPage::DATA_JSON->value;
        $data = $model_tab[JSONMETHODE::JSONTOARRAY->value]($chemin);

        if (!isset($data['promotions'])) return false;

        $data['promotions'] = array_map(function($promo) use ($ref_id) {
            if (($promo['statut'] ?? '') === 'Active') {
                if (!in_array($ref_id, $promo['referenciel'])) {
                    $promo['referenciel'][] = $ref_id;
                }
            }
            return $promo;
        }, $data['promotions']);

        return $model_tab[JSONMETHODE::ARRAYTOJSON->value]($data, $chemin);
    },
    REFMETHODE::DESAFFECTER->value => function(int $ref_id): bool {
        global $model_tab;
        $chemin = CheminPage::DATA_JSON->value;
        $data = $model_tab[JSONMETHODE::JSONTOARRAY->value]($chemin);

        if (!isset($data['promotions'])) return false;

        // Désaffecter le référentiel de la promotion active
        $data['promotions'] = array_map(function($promo) use ($ref_id) {
            if (($promo['statut'] ?? '') === 'Active') {
                $promo['referenciel'] = array_filter($promo['referenciel'], fn($id) => $id !== $ref_id);
            }
            return $promo;
        }, $data['promotions']);

        return $model_tab[JSONMETHODE::ARRAYTOJSON->value]($data, $chemin);
    },
];


