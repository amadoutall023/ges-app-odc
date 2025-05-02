<?php
require_once __DIR__ . '/../enums/chemin_page.php';
use App\Enums\CheminPage;
require_once CheminPage::CONTROLLER->value;



function uploadPhoto(array $file, string $uploadDir, string $defaultPath = "assets/images/promo/default.jpg"): ?string {
    if (isset($file) && $file['error'] === UPLOAD_ERR_OK) {
        $photoName = basename($file['name']);
        $photoPath = rtrim($uploadDir, '/') . '/' . $photoName;

        if (move_uploaded_file($file['tmp_name'], $photoPath)) {
            return "assets/images/promo/" . $photoName;
        }

        return null;
    }

    return $defaultPath;
}





/**
 * Calcule le prochain ID pour une nouvelle promotion.
 */
function getNextPromoId(array $promotions): int {
    if (empty($promotions)) {
        return 1; // Retourne 1 si aucune promotion n'existe
    }

    // Récupérer le plus grand ID existant
    $maxId = max(array_column($promotions, 'id'));
    return $maxId + 1; // Retourne l'ID suivant
}


function render(string $vue, array $donnees = [], ?string $layout = 'base.layout'): void {
    $baseViewPath = dirname(__DIR__) . '/views/';
    $baseLayoutPath = $baseViewPath . 'layout/';

    // Gestion du chemin absolu ou relatif
    $cheminVue = str_ends_with($vue, '.php') ? $vue : $baseViewPath . trim($vue, '/') . '.view.php';

    if (!file_exists($cheminVue)) {
        throw new Exception("Vue '$cheminVue' introuvable.");
    }

    extract($donnees);
    ob_start();
    require $cheminVue;
    $contenu = ob_get_clean();

    if ($layout !== null) {
        $cheminLayout = $baseLayoutPath . trim($layout, '/') . '.php';

        if (!file_exists($cheminLayout)) {
            throw new Exception("Layout '$layout' introuvable.");
        }

        require $cheminLayout;
    } else {
        echo $contenu;
    }
}

function redirect_to_route(string $route, array $params = []): void {
    // Si on a des paramètres à passer dans l'URL
    if (!empty($params)) {
        $query = http_build_query($params);
        $route .= (strpos($route, '?') === false ? '?' : '&') . $query;
    }

    header("Location: $route");
    exit(); // Toujours arrêter l'exécution après une redirection
}

/**
 * Vérifie si la requête est une requête POST et si un champ spécifique est défini.
 */
function is_post_request_with_field(string $field): bool {
    return $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST[$field]);
}





?>