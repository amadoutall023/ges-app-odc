<!DOCTYPE html>
<html lang="fr">
<?php
require_once __DIR__ . '/../../enums/chemin_page.php';
use App\Enums\CheminPage;
$url = "http://" . $_SERVER["HTTP_HOST"];
$css_ref = CheminPage::CSS_FPROMO->value;
$errors = $_SESSION['errors'] ?? [];
$old_inputs = $_SESSION['old_inputs'] ?? [];
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer une Promotion</title>
    <link rel="stylesheet" href="<?= $url . $css_ref ?>">
</head>
<body>

<div class="container">
    <h1>Créer une nouvelle promotion</h1>
    <p class="subtitle">Remplissez les informations ci-dessous pour créer une nouvelle promotion.</p>

    <form class="modal-form" method="POST" action="index.php?page=ajouter_promotion" enctype="multipart/form-data">
        <input type="hidden" name="nouvelle_promo" value="1">

        <!-- Nom de la promotion -->
        <label>
            Nom de la promotion
            <input
                type="text"
                name="nom_promo"
                placeholder="Ex: Promotion 2025"
                value="<?= htmlspecialchars($_SESSION['old_inputs']['nom_promo'] ?? '') ?>"
                class="<?= !empty($_SESSION['errors']['nom_promo']) ? 'alert' : '' ?>"
            />
            <?php if (!empty($_SESSION['errors']['nom_promo'])): ?>
                <p class="error-message"><?= htmlspecialchars($_SESSION['errors']['nom_promo']) ?></p>
            <?php endif; ?>
        </label>

        <!-- Champ pour uploader une photo -->
        <label>
            Photo de la promotion
            <input
                type="file"
                name="photo"
                accept="image/*"
                class="<?= !empty($_SESSION['errors']['photo']) ? 'alert' : '' ?>"
            />
            <?php if (!empty($_SESSION['errors']['photo'])): ?>
                <p class="error-message"><?= htmlspecialchars($_SESSION['errors']['photo']) ?></p>
            <?php endif; ?>
        </label>

        <!-- Dates -->
        <div class="date-fields">
            <label>
                Date de début
                <input
                    type="text"
                    name="date_debut"
                    value="<?= htmlspecialchars($_SESSION['old_inputs']['date_debut'] ?? '') ?>"
                    class="<?= !empty($_SESSION['errors']['date_debut']) ? 'alert' : '' ?>"
                />
                <?php if (!empty($_SESSION['errors']['date_debut'])): ?>
                    <p class="error-message"><?= htmlspecialchars($_SESSION['errors']['date_debut']) ?></p>
                <?php endif; ?>
            </label>

            <label>
                Date de fin
                <input
                    type="text"
                    name="date_fin"
                    value="<?= htmlspecialchars($_SESSION['old_inputs']['date_fin'] ?? '') ?>"
                    class="<?= !empty($_SESSION['errors']['date_fin']) ? 'alert' : '' ?>"
                />
                <?php if (!empty($_SESSION['errors']['date_fin'])): ?>
                    <p class="error-message"><?= htmlspecialchars($_SESSION['errors']['date_fin']) ?></p>
                <?php endif; ?>
                <?php if (!empty($_SESSION['errors']['date_combined'])): ?>
                    <p class="error-message"><?= htmlspecialchars($_SESSION['errors']['date_combined']) ?></p>
                <?php endif; ?>
            </label>
        </div>

        <!-- Référentiels -->
        <div class="form-group">
            <label>Référentiels</label>
            <div class="checkbox-group">
                <?php
                $checked_ids = $_SESSION['old_inputs']['referenciel_ids'] ?? [];
                if (!is_array($checked_ids)) {
                    $checked_ids = []; // Force $checked_ids à être un tableau
                }
                ?>
                <?php foreach ($referenciel as $ref): ?>
                    <label>
                        
                    <input type="checkbox" name="referenciel_ids[]" value="<?= htmlspecialchars($ref['id']) ?>">

                        <?= htmlspecialchars($ref['nom']) ?>
                    </label>
                <?php endforeach; ?>
                <?php if (!empty($_SESSION['errors']['date_range'])): ?>
                    <p class="error-message"><?= htmlspecialchars($_SESSION['errors']['date_range']) ?></p>
                <?php endif; ?>
            </div>
            <?php if (!empty($_SESSION['errors']['referenciel'])): ?>
                <p class="error-message"><?= htmlspecialchars($_SESSION['errors']['referenciel']) ?></p>
            <?php endif; ?>
        </div>

        <!-- Actions -->
        <div class="modal-actions">
            <a href="?page=liste_promo" class="cancel-btn">Annuler</a>
            <button type="submit" class="submit-btn">Créer la promotion</button>
        </div>
    </form>
</div>
<?php 
supprimer_session('errors');
supprimer_session('old_inputs');
?>

</body>
</html>