<?php
declare(strict_types=1);
require_once __DIR__ . '/../enums/validator.enum.php';
require_once __DIR__ . '/../enums/erreur.enum.php';
require_once __DIR__ . '/../models/model.php';
require_once __DIR__ . '/../enums/chemin_page.php';
use App\ENUM\VALIDATOR\ValidatorMethode;
use App\ENUM\ERREUR\ErreurEnum;
use App\Models\JSONMETHODE;
use App\Enums\CheminPage;

global $validator;
$validator = [
    // Validation de l'email
    ValidatorMethode::EMAIL->value => function (string $email): ?string {
        if (empty($email)) {
            return ErreurEnum::LOGIN_REQUIRED->value;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ErreurEnum::LOGIN_EMAIL->value;
        }
        return null;
    },

    // Validation du mot de passe
    ValidatorMethode::PASSWORD->value => function (string $password): ?string {
        if (empty($password)) {
            return ErreurEnum::PASSWORD_REQUIRED->value;
        }
        if (strlen($password) < 6) {
            return ErreurEnum::PASSWORD_INVALID->value;
        }
        return null;
    },
    // Validation pour les référentiels
ValidatorMethode::VALIDATE_REFERENTIELS->value => function (array $referentiels): ?string {
    if (empty($referentiels)) {
        return "Au moins un référentiel doit être sélectionné.";
    }
    return null;
},

    // Validation combinée utilisateur
    ValidatorMethode::USER->value => function (string $email, string $password) use (&$validator): array {
        $erreurs = [];
        $email_error = $validator[ValidatorMethode::EMAIL->value]($email);
        if ($email_error) {
            $erreurs['email'] = $email_error;
        }
        $password_error = $validator[ValidatorMethode::PASSWORD->value]($password);
        if ($password_error) {
            $erreurs['password'] = $password_error;
        }
        return $erreurs;
    },

    // Validation du nom de la promotion
    ValidatorMethode::PROMO_NAME->value => function (string $nom): ?string {
        if (empty($nom)) {
            return ErreurEnum::PROMO_NAME_REQUIRED->value;
        }
        return null;
    },

    // Validation des dates de promotion
    ValidatorMethode::PROMO_DATE->value => function (string $date): ?string {
        if (empty($date)) {
            return ErreurEnum::PROMO_DATE_REQUIRED->value;
        }
        $format = 'd-m-Y';
        $dateObj = DateTime::createFromFormat($format, $date);
        if (!$dateObj || $dateObj->format($format) !== $date) {
            return ErreurEnum::PROMO_DATE_NORME->value;
        }
        return null;
    },
    // À ajouter dans votre tableau $validator dans validator.service.php
ValidatorMethode::PROMO->value => function (string $nom): ?string {
    if (empty($nom)) {
        return ErreurEnum::PROMO_NAME_REQUIRED->value;
    }
    return null;
},

    ValidatorMethode::PROMO_DATE_VALIDE->value => function (string $dateDebut, string $dateFin): ?string {
        $startDate = DateTime::createFromFormat('d-m-Y', $dateDebut);
        $endDate = DateTime::createFromFormat('d-m-Y', $dateFin);

        if (!$startDate || !$endDate) {
            return ErreurEnum::PROMO_DATE_NORME->value;
        }

        if ($startDate > $endDate) {
            return ErreurEnum::PROMO_DATE_INFERIEUR->value;
        }

        return null;
    },

    // Validation des dates combinées (date de début < date de fin)
    ValidatorMethode::PROMO_DATE_RANGE->value => function (string $dateDebut, string $dateFin): ?string {
        $startDate = DateTime::createFromFormat('d-m-Y', $dateDebut);
        $endDate = DateTime::createFromFormat('d-m-Y', $dateFin);

        if (!$startDate || !$endDate) {
            return ErreurEnum::PROMO_DATE_NORME->value;
        }

        if ($startDate > $endDate) {
            return ErreurEnum::PROMO_DATE_INFERIEUR->value;
        }

        return null;
    },

    // Validation du statut de la promotion
    ValidatorMethode::PROMO_STATUT->value => function (string $statut): ?string {
        $validStatuts = ['Active', 'Inactive'];
        if (!in_array($statut, $validStatuts, true)) {
            return ErreurEnum::PROMO_STATUT_INVALID->value;
        }
        return null;
    },

    // Validation générale
    ValidatorMethode::VALID_GENERAL->value => function (array $data) use (&$validator): array {
        $errors = [];

        // Validation du nom de la promotion
        $errors['nom_promo'] = $validator[ValidatorMethode::PROMO->value]($data['nom_promo'] ?? '');

        // Validation de la date de début
        $errors['date_debut'] = $validator[ValidatorMethode::PROMO_DATE->value]($data['date_debut'] ?? '');

        // Validation de la date de fin
        $errors['date_fin'] = $validator[ValidatorMethode::PROMO_DATE->value]($data['date_fin'] ?? '');

        // Validation des dates combinées
        if (empty($errors['date_debut']) && empty($errors['date_fin'])) {
            $date_error = $validator[ValidatorMethode::PROMO_DATE_VALIDE->value](
                $data['date_debut'] ?? '',
                $data['date_fin'] ?? ''
            );
            if ($date_error) {
                $errors['date_combined'] = $date_error;
            }
        }

        // Filtrer les erreurs non nulles
        return array_filter($errors);
    },
    // ValidatorMethode::INSCRIRE_APPRENANT->value => function (array $data): array {
    //     return valider_apprenant($data);
    // },
    
    ValidatorMethode::VALIDATE_AFFECTER_REF->value => function (?string $referentielId): void {
        if (empty($referentielId)) {
            throw new Exception(ErreurEnum::REFERENTIEL_ID_REQUIRED->value);
        }
    },

    // Validation pour affecter un référentiel
    ValidatorMethode::VALIDATE_AFFECTER_REF->value => function (?string $referentielId): void {
        if (empty($referentielId)) {
            throw new Exception(ErreurEnum::REFERENTIEL_ID_REQUIRED->value);
        }
    },
    





    VALIDATORMETHODE::APPRENANT->value => function (array $data): array {
        $errors = [];

        // Nom complet obligatoire
        if (empty(trim($data['nom_complet'] ?? ''))) {
            $errors['nom_complet'] = ErreurEnum::APPRENANT_NOM_REQUIRED->value;
        }

        // // Login obligatoire et valide
        // if (empty(trim($data['login'] ?? ''))) {
        //     $errors['login'] = ErreurEnum::APPRENANT_EMAIL_REQUIRED->value;
        // } else {
        //     $login = trim($data['login']);
        //     if (!filter_var($login, FILTER_VALIDATE_EMAIL)) {
        //         $errors['login'] = ErreurEnum::APPRENANT_EMAIL_INVALID->value;
        //     }
        // }




        $login = trim($data['email'] ?? '');

        if ($login === '') {
            $errors['email'] = ErreurEnum::APPRENANT_EMAIL_REQUIRED->value;
        } elseif (!filter_var($login, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = ErreurEnum::APPRENANT_EMAIL_INVALID->value;
        } else {
            // Vérification de l'unicité
            $chemin = CheminPage::DATA_JSON->value;
            global $model_tab;
            $utilisateurs = $model_tab[JSONMETHODE::JSONTOARRAY->value]($chemin, 'utilisateurs');
        
            $loginExiste = array_filter($utilisateurs, fn($u) => isset($u['email']) && strtolower($u['email']) === strtolower($login));
        
            if (!empty($loginExiste)) {
                $errors['email'] = ErreurEnum::APPRENANT_EMAIL_UNIQUE->value;
            }
        }
        







        // Téléphone obligatoire, valide et unique
        // if (empty(trim($data['telephone'] ?? ''))) {
        //     $errors['telephone'] = ErreurEnum::APPRENANT_TELEPHONE_REQUIRED->value;
        // } elseif (!preg_match('/^\d{9}$/', $data['telephone'])) {
        //     $errors['telephone'] = ErreurEnum::APPRENANT_TELEPHONE_INVALID->value;
        // } else {
        //     $chemin = \App\Enums\CheminPage::DATA_JSON->value;
        //     if (file_exists($chemin)) {
        //         $contenu = json_decode(file_get_contents($chemin), true);
        //         $utilisateurs = $contenu['utilisateurs'] ?? [];

        //         $telephoneSaisi = trim($data['telephone']);
        //         $doublon = array_filter($utilisateurs, fn($u) => ($u['telephone'] ?? '') === $telephoneSaisi);

        //         if (!empty($doublon)) {
        //             $errors['telephone'] = ErreurEnum::APPRENANT_TELEPHONE_EXISTS->value;
        //         }
        //     }
        // }



        
        // Date de naissance obligatoire et format YYYY-MM-DD
        // if (empty(trim($data['date_naissance'] ?? ''))) {
        //     $errors['date_naissance'] = ErreurEnum::APPRENANT_DATE_NAISSANCE_REQUIRED->value;
        // } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['date_naissance'])) {
        //     $errors['date_naissance'] = ErreurEnum::APPRENANT_DATE_NAISSANCE_INVALID->value;
        // }

        // Lieu de naissance obligatoire
        // if (empty(trim($data['lieu_naissance'] ?? ''))) {
        //     $errors['lieu_naissance'] = ErreurEnum::APPRENANT_LIEU_NAISSANCE_REQUIRED->value;
        // }

        // Adresse obligatoire
        // if (empty(trim($data['adresse'] ?? ''))) {
        //     $errors['adresse'] = ErreurEnum::APPRENANT_ADRESSE_REQUIRED->value;
        // }

        // Référentiel obligatoire
        // if (empty($data['referenciel'])) {
        //     $errors['referenciel'] = ErreurEnum::APPRENANT_REFERENTIEL_REQUIRED->value;
        // }

        // Tuteur - Nom obligatoire
        // if (empty(trim($data['tuteur_nom'] ?? ''))) {
        //     $errors['tuteur_nom'] = ErreurEnum::APPRENANT_TUTEUR_NOM_REQUIRED->value;
        // }

        // Tuteur - Lien de parenté obligatoire
        // if (empty(trim($data['lien_parente'] ?? ''))) {
        //     $errors['lien_parente'] = ErreurEnum::APPRENANT_LIEN_PARENT_REQUIRED->value;
        // }

        // Tuteur - Adresse obligatoire
        // if (empty(trim($data['tuteur_adresse'] ?? ''))) {
        //     $errors['tuteur_adresse'] = ErreurEnum::APPRENANT_TUTEUR_ADRESSE_REQUIRED->value;
        // }

        // Tuteur - Téléphone obligatoire et format 9 chiffres
        // if (empty(trim($data['tuteur_telephone'] ?? ''))) {
        //     $errors['tuteur_telephone'] = ErreurEnum::APPRENANT_TUTEUR_TELEPHONE_REQUIRED->value;
        // } elseif (!preg_match('/^\d{9}$/', $data['tuteur_telephone'])) {
        //     $errors['tuteur_telephone'] = ErreurEnum::APPRENANT_TUTEUR_TELEPHONE_INVALID->value;
        // }

        return $errors;
    },
   
];

function clear_errors() {
    if (isset($_SESSION['errors'])) {
        unset($_SESSION['errors']);
    }
}
function get_error($field) {
    return $_SESSION['errors'][$field] ?? null;
}

$ValidatorService = [
    ValidatorMethode::VALIDATE_FICHIER_EXCEL->value => function ($nomComplet, $adresse, $email, $referentiel) {
        return !empty($nomComplet) && !empty($adresse) && filter_var($email, FILTER_VALIDATE_EMAIL) && !empty($referentiel);
    }
    
];