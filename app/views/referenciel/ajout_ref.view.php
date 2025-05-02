<!-- affectation_apprenants.html.php -->
<!DOCTYPE html>
<html lang="fr">
<?php
require_once __DIR__ . '/../../enums/chemin_page.php';
use App\Enums\CheminPage;
$url = "http://" . $_SERVER["HTTP_HOST"];
$css_ref = CheminPage::CSS_AJOUT_REFERENCIEL->value;
?>
<head>
  <meta charset="UTF-8">
  <title>Affectation des référentiels</title>
  <link rel="stylesheet" href="<?= $url . $css_ref ?>">
</head>
<body>
    <div class="group">

        <!-- Zone des statistiques -->
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
            <div class="stat orange" id="QQ">
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
        </div>

       <!-- Liste des référentiels non affectés -->
<section class="referentiels-section">
    <h2>Affecter un référentiel</h2>
    <form method="POST" action="?page=affecter_referentiel">
        <select name="referenciel_id" required>
            <option value="">-- Sélectionnez un référentiel --</option>
            <?php foreach ($referentiels_non_affectes as $ref): ?>
                <option value="<?= $ref['id'] ?>"><?= htmlspecialchars($ref['nom']) ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn-valider">Affecter</button>
    </form>
</section>

<!-- Liste des référentiels de la promotion active -->
<section class="referentiels-section">
    <h2>Désaffecter un référentiel</h2>
    <form method="POST" action="?page=desaffecter_referentiel">
        <select name="referenciel_id" required>
            <option value="">-- Sélectionnez un référentiel --</option>
            <?php foreach ($referentiels_affectes as $ref): ?>
                <option value="<?= $ref['id'] ?>"><?= htmlspecialchars($ref['nom']) ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn-cancel">Désaffecter</button>
    </form>
</section>

            <!-- Promotion active -->
            <section class="promotion-section">
                <h2>Promotion active</h2>
                <div class="promotion-info">
                    <strong>Nom :</strong> <?= htmlspecialchars($promotion_active['nom']) ?><br>
                    <strong>Date de début :</strong> <?= htmlspecialchars($promotion_active['dateDebut']) ?><br>
                    <strong>Date de fin :</strong> <?= htmlspecialchars($promotion_active['dateFin']) ?>
                </div>
            </section>

            <!-- <div class="action-buttons">
            <form method="POST" action="?page=desaffecter_referentiel">
                  <input type="hidden" name="referenciel_id" value="<?= $ref['id'] ?>">
                 <button type="submit">Désaffecter</button>
            </form>
            <form method="POST" action="?page=affecter_referentiel">
                <input type="hidden" name="referenciel_id" value="<?= $ref['id'] ?>">
                 <button type="submit">Affecter</button>
            </form>
            </div> -->
        </div>

    </div>
</body>
</html>
