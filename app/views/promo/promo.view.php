<!DOCTYPE html>
<html lang="fr">
<?php
require_once __DIR__ . '/../../enums/chemin_page.php';
use App\Enums\CheminPage;
$url = "http://" . $_SERVER["HTTP_HOST"];
$css_promo = CheminPage::CSS_PROMO->value;


?>
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Promotions</title>
    <link rel="stylesheet" href="<?= $url . $css_promo ?>" />
</head>
<body>
<div class="promo-container">
    <header class="header">
        <h2>Promotion</h2>
        <p>Gérer les promotions de l'école</p>
    </header>
   

    <div class="stats">
        <div class="stat orange">
            <div class="stat-content">
            
            <strong class="stat-value"><?= $nbApprenants ?></strong>
                <span class="stat-label">Apprenants</span> 
            </div>
            <div class="icon"><img src="/assets/images/icone1.png" alt=""></div>
        </div>
        <div class="stat orange">
            <div class="stat-content">
            <strong class="stat-value"><?= $nbReferentiels ?></strong>
                <span class="stat-label">Référentiels</span>
            </div>
            <div class="icon"><img src="/assets/images/ICONE2.png" alt=""></div>
        </div>
        <div class="stat orange" id='QQ'>
            <div class="stat-content">
            <strong class="stat-value"><?= $nbPromotionsActives ?></strong>
                <span class="stat-label">Promotions actives</span>
            </div>
            <div class="icon"><img src="/assets/images/ICONE3.png" alt=""></div>
        </div>
        <div class="stat orange">
            <div class="stat-content">
            <strong class="stat-value"><?= $nbPromotions ?></strong>
                <span class="stat-label">Total promotions</span>
            </div>
            <div class="icon"><img src="/assets/images/ICONE4.png" alt=""></div>
        </div>
        <a href="?page=form" class="add-btn">+ Ajouter une promotion</a>
    </div>

    <div class="search-filter">
        <form method="GET" action="" style="display: flex; flex: 1;">
            <input type="hidden" name="page" value="liste_promo" />
            <input type="text" name="search" placeholder="Rechercher une promotion..."value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" />

            <select name="filtre_statut" onchange="this.form.submit()">
                <option value="tous" <?= ($_GET['filtre_statut'] ?? '') === 'tous' ? 'selected' : '' ?>>Tous</option>
                <option value="Active" <?= ($_GET['filtre_statut'] ?? '') === 'Active' ? 'selected' : 'inactives' ?>>Actives</option>
                <option value="Inactive" <?= ($_GET['filtre_statut'] ?? '') === 'Inactive' ? 'selected' : '' ?>>Inactives</option>
            </select>
            <button type="submit" class="submit-btn">Rechercher</button>
        </form>
        <div class="view-toggle">
            <form method="GET" action="">
                <button class="active">Grille</button>
                <input type="hidden" name="page" value="liste_table_promo" />
                <button type="submit">Liste</button>
            </form>
        </div>
    </div>

    <!-- Liste des promotions -->
    <div class="card-grid">
<?php
    $filtreStatut = $_GET['filtre_statut'] ?? 'tous'; // Valeur par défaut
    foreach ($promotions as $promo):
        if ($filtreStatut === 'tous' || $promo['statut'] === $filtreStatut): ?>
            <div class="promo-card">
            
                <div class="promo-header">
               
                    <div class="switch-container">
                   
                        <form method="POST" action="?page=changer_statut_promo" class="toggle-form">
                            <input type="hidden" name="promo_id" value="<?= htmlspecialchars($promo['id']) ?>">
                            <input type="hidden" name="nouveau_statut" value="<?= $promo['statut'] === 'Active' ? 'Inactive' : 'Active' ?>">
                            <label class="toggle-label <?= $promo['statut'] === 'Active' ? 'active' : '' ?>">
                                <span class="status-pill"><?= $promo['statut'] === 'Active' ? 'Active' : 'Inactive' ?></span>
                                <div class="power-button">
                                    <span class="power-dot"></span>
                                </div>
                                <button type="submit" style="display:none;"></button>
                            </label>
                        </form>
                    </div>
                </div>
                <div class="promo-body">
                <div class="promo-image">
                        <img src="<?= $promo['photo'] ?>" alt="<?= $promo['nom'] ?>">
                    </div>
                    <div class="promo-details">
                        <h3><?= htmlspecialchars($promo['nom']) ?></h3>
                        <div class="promo-date">Date: <?= htmlspecialchars($promo['date'] ?? 'N/A') ?></div>
                        <div class="promo-students">Nombre d’étudiants : <?= htmlspecialchars($promo['etudiants'] ?? 0) ?></div>
                    </div>
                </div>
                <div class="promo-footer">
                    <a class="details-btn" href="?page=details_promo&id=<?= $promo['id'] ?>">Voir les détails</a>
                </div>
            </div>
        <?php endif;
    endforeach; ?>
</div>
<?php if ($total > 1): ?>
    <div class="custom-pagination">
        <!-- Flèche gauche -->
        <a href="?page=liste_promo&p=<?= max(1, $page - 1) ?>" class="arrow <?= $page === 1 ? 'disabled' : '' ?>">&#10094;</a>

        <!-- Pages -->
        <?php for ($i = 1; $i <= $total; $i++): ?>
            <a href="?page=liste_promo&p=<?= $i ?>" class="page-number <?= $i === $page ? 'active' : '' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>

        <!-- Flèche droite -->
        <a href="?page=liste_promo&p=<?= min($total, $page + 1) ?>" class="arrow <?= $page === $total ? 'disabled' : '' ?>">&#10095;</a>
    </div>

    <!-- Affichage "1 à 5 pour 8" -->
    <div class="pagination-info">
        <?= $debut + 1 ?> à <?= min($debut + $parPage, $totalPromotions) ?> pour <?= $totalPromotions ?>
    </div>
<?php endif; ?>



</body>
</html>