<!DOCTYPE html>
<html lang="fr">
<?php
require_once __DIR__ . '/../../enums/chemin_page.php';
use App\Enums\CheminPage;
$url = "http://" . $_SERVER["HTTP_HOST"];
$css_ref = CheminPage::CSS_REFERENCIEL->value;
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Référentiels</title>
    <link rel="stylesheet" href="<?= $url . $css_ref ?>">
</head>
<body>
<div class="ref-container">
    <header>
        <h1>Référentiels</h1>
        <?php if ($promotion_active): ?>
            <p>Promotion active : <?= htmlspecialchars($promotion_active['nom']) ?></p>
        <?php else: ?>
            <p>Aucune promotion active.</p>
        <?php endif; ?>
    </header>

    <div class="search-bar">
        <div class="search-container">
            <form action="index.php" method="GET" class="search-form">
                <input type="hidden" name="page" value="<?= isset($_GET['page']) ? htmlspecialchars($_GET['page']) : 'referenciel' ?>">
                <input type="text" name="search" placeholder="Rechercher un référentiel..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                <button type="submit">Rechercher</button>
            </form>
        </div>
        <div class="actions">
            <button class="btn btn-orange" onclick="location.href='?page=all_referenciel'">
                📋 Tous les référentiels
            </button>
            <button class="btn btn-green" onclick="location.href='?page=ajout_ref'">+ Ajouter à une promotion</button>
        </div>
    </div>

    <div class="ref-grid">
        <?php if (!empty($referenciel)): ?>
            <?php foreach ($referenciel as $ref): ?>
                <div class="ref-card">
                    <div class="ref-image">
                        <img src="<?= htmlspecialchars($ref['photo']) ?>" alt="<?= htmlspecialchars($ref['nom']) ?>">
                    </div>
                    <div class="ref-content">
                        <h3><?= htmlspecialchars($ref['nom']) ?></h3>
                        <p class="description">
                            <?= htmlspecialchars($ref['description'] ?? 'Aucune description disponible') ?>
                        </p>
                        <div class="ref-stats">
                            <span><?= $ref['modules'] ?? 0 ?> modules</span>
                            <span><?= $ref['apprenants'] ?? 0 ?> apprenants</span>
                        </div>
                        <div class="ref-capacity">
                            Capacité: <?= htmlspecialchars($ref['capacite'] ?? 'Non spécifiée') ?> places
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Aucun référentiel associé à la promotion active.</p>
        <?php endif; ?>
    </div>

    <div class="pagination">
        <?php if (isset($totalPages) && $totalPages > 1): ?>
            <a href="?page=referenciel&p=<?= max(1, $page - 1) ?>" class="pagination-link <?= $page == 1 ? 'disabled' : '' ?>">&#10094; Précédent</a>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=referenciel&p=<?= $i ?>" class="pagination-link <?= $i == $page ? 'active' : '' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>

            <a href="?page=referenciel&p=<?= min($totalPages, $page + 1) ?>" class="pagination-link <?= $page == $totalPages ? 'disabled' : '' ?>">Suivant &#10095;</a>
        <?php endif; ?>
    </div>
</div>
</body>
</html>