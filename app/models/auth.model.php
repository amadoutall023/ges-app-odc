<?php
declare(strict_types=1);
require_once __DIR__ . '/../enums/chemin_page.php';
require_once __DIR__ . '/../enums/model.enum.php';
use App\Enums\CheminPage;
use App\Models\JSONMETHODE;
use App\Models\AUTHMETHODE;

global $auth_model;
$auth_model = [
    AUTHMETHODE::LOGIN->value => function (string $login, string $password, string $chemin): ?array {
        global $model_tab;
        $utilisateurs = $model_tab[JSONMETHODE::JSONTOARRAY->value]($chemin, 'utilisateurs');

        // Recherche de l'utilisateur
        $utilisateur = array_values(array_filter($utilisateurs, fn($u) =>
            isset($u['login'], $u['password']) && $u['login'] === $login
        ));

        // Retourne le premier utilisateur trouvé ou null
        return $utilisateur[0] ?? null;
    },


    // Réinitialise le mot de passe 
    AUTHMETHODE::RESET_PASSWORD->value => function (string $login, string $newPasswordHashed, string $chemin): bool {
        global $model_tab;

        $data = $model_tab[JSONMETHODE::JSONTOARRAY->value]($chemin);
        $utilisateurs = $data['utilisateurs'] ?? [];
        $modifie = false;

        $utilisateurs = array_map(function ($u) use ($login, $newPasswordHashed, &$modifie) {
            if (isset($u['login']) && $u['login'] === $login) {
                $u['password'] = $newPasswordHashed;
                $u['changer'] = true;
                $modifie = true;
            }
            return $u;
        }, $utilisateurs);

        if (!$modifie) return false;

        $data['utilisateurs'] = $utilisateurs;
        return $model_tab[JSONMETHODE::ARRAYTOJSON->value]($data, $chemin);
    }

];


