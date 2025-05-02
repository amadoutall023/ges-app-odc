<!DOCTYPE html>
<html lang="fr">
<?php
require_once __DIR__ . '/../../enums/chemin_page.php';
use App\Enums\CheminPage;
$url = "http://" . $_SERVER["HTTP_HOST"];
$css_ref = CheminPage::CSS_ADDREFERENCIEL->value; ;
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tous les Référentiels</title>
    <link rel="stylesheet" href="<?= $url . $css_ref ?>">
</head>
<body>
    <div class="ref-container">
        <div class="ref-header">
            <a href="?page=referenciel" class="back-link">
                <i class="fas fa-arrow-left"></i> 
                Retour aux référentiels actifs
            </a>
            <h1>Tous les Référentiels</h1>
            <p>Liste complète des référentiels de formation</p>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($_SESSION['success']) ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
            <?= htmlspecialchars($referenciel['nom']) ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <div class="search-actions">
            <div class="search-bar">
                <input type="text" placeholder="Rechercher un référentiel...">
            </div>
            
            <button class="btn-green" onclick="location.href='#popup-create'">+ Créer un référentiel</button>
        </div>

        <div class="ref-grid">
            <?php foreach ($referenciel as $ref): ?>
                <div class="ref-card">
                    <img class="im" src="<?= htmlspecialchars($ref['photo']) ?>" alt="<?= htmlspecialchars($ref['nom']) ?>">
                    <div class="ref-content">
                        <h3><?= htmlspecialchars($ref['nom']) ?></h3>
                        <p><?= htmlspecialchars($ref['description'] ?? 'Aucune description disponible') ?></p>
                        <div class="ref-info">
                            <span>Capacité: <?= htmlspecialchars($ref['capacite'] ?? 'Non spécifiée') ?> places</span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="pagination">
    <?php if (isset($totalPages) && $totalPages > 1): ?>
        <a href="?page=all_referenciel&p=<?= max(1, $page - 1) ?>" class="pagination-link <?= $page == 1 ? 'disabled' : '' ?>">&#10094; Précédent</a>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=all_referenciel&p=<?= $i ?>" class="pagination-link <?= $i == $page ? 'active' : '' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>

        <a href="?page=all_referenciel&p=<?= min($totalPages, $page + 1) ?>" class="pagination-link <?= $page == $totalPages ? 'disabled' : '' ?>">Suivant &#10095;</a>
    <?php endif; ?>
</div>
    <div id="popup-create" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Créer un référentiel</h2>
            <a href="#" class="close-btn">&times;</a>
        </div>
        <form method="POST" action="?page=creer_referentiel" class="modal-body" enctype="multipart/form-data">
            <div class="form-group">
                <label>Libellé référentiel</label>
                <input type="text" name="libelle_ref" placeholder="Cloud & Cybersec" required>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" placeholder="Description du référentiel" required></textarea>
            </div>
            <div class="form-group">
                <label>Capacité</label>
                <input type="text" name="capacite" placeholder="Capacité maximale" required>
            </div>
            <div class="form-group">
                <label>Image</label>
                <input type="file" name="photo" id="photo" accept="image/*" required>
            </div>
            <button type="submit" class="btn-submit">Créer</button>
        </form>
    </div>
</div>


</body>
</html>