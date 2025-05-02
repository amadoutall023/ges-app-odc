<?php
declare(strict_types=1);
require_once __DIR__ . '/../enums/validator.enum.php';
require_once __DIR__ . '/../enums/erreur.enum.php';

use App\ENUM\VALIDATOR\ValidatorMethode;
use App\ENUM\ERREUR\ErreurEnum;

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
    ValidatorMethode::INSCRIRE_APPRENANT->value => function (array $data): array {
        return valider_apprenant($data);
    },
    
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
    }
    
];

function valider_apprenant(array $data): array {
    $erreurs = [];

    // Validation des champs de l'apprenant
    if (empty($data['prenom'])) {
        $erreurs['prenom'] = "Le prénom est obligatoire.";
    }

    if (empty($data['nom'])) {
        $erreurs['nom'] = "Le nom est obligatoire.";
    }

    if (empty($data['date_naissance'])) {
        $erreurs['date_naissance'] = "La date de naissance est obligatoire.";
    } elseif (!DateTime::createFromFormat('d/m/Y', $data['date_naissance'])) {
        $erreurs['date_naissance'] = "La date de naissance doit être au format JJ/MM/AAAA.";
    }

    if (empty($data['lieu_naissance'])) {
        $erreurs['lieu_naissance'] = "Le lieu de naissance est obligatoire.";
    }

    if (empty($data['adresse'])) {
        $erreurs['adresse'] = "L'adresse est obligatoire.";
    }

    if (empty($data['email'])) {
        $erreurs['email'] = "L'email est obligatoire.";
    } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $erreurs['email'] = "L'email n'est pas valide.";
    }

    if (empty($data['telephone'])) {
        $erreurs['telephone'] = "Le numéro de téléphone est obligatoire.";
    }

    // Validation des champs du tuteur
    if (empty($data['tuteur_nom'])) {
        $erreurs['tuteur_nom'] = "Le nom du tuteur est obligatoire.";
    }

    if (empty($data['lien_parente'])) {
        $erreurs['lien_parente'] = "Le lien de parenté est obligatoire.";
    }

    if (empty($data['tuteur_adresse'])) {
        $erreurs['tuteur_adresse'] = "L'adresse du tuteur est obligatoire.";
    }

    if (empty($data['tuteur_telephone'])) {
        $erreurs['tuteur_telephone'] = "Le numéro de téléphone du tuteur est obligatoire.";
    } elseif (!preg_match('/^\+?[0-9\s\-]+$/', $data['tuteur_telephone'])) {
        $erreurs['tuteur_telephone'] = "Le numéro de téléphone du tuteur n'est pas valide.";
    }

    return $erreurs;
}

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