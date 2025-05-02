<!DOCTYPE html>
<html lang="fr">
<?php
require_once __DIR__ . '/../../enums/chemin_page.php';
use App\Enums\CheminPage;
$url = "http://" . $_SERVER["HTTP_HOST"];
$css_path = CheminPage::CSS_AJOUT_APP->value;
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajout apprenant</title>
    <link rel="stylesheet" href="<?= $url . $css_path ?>">
    <style>
        .error-message {
            color: red;
            font-size: 0.9em;
            margin-top: 5px;
        }
    </style>
</head>
<body>
  <div class="form-container">
    <div class="form-header">Ajout apprenant</div>

    <form id="apprenantForm" method="post" action="index.php?page=ajouter_apprenant" enctype="multipart/form-data">
      <div class="form-section">
        <div class="section-title">
          <span>Informations de l'apprenant</span>
        </div>
        
        <div class="form-row">
          <div class="form-field">
            <label for="NOM">Prénom(s)</label>
            <input type="text" id="nom_complet" name="nom_complet" value="<?= htmlspecialchars($old['nom_complet'] ?? '') ?>">
            <?php if (!empty($erreurs['nom_complet'])): ?>
                <p class="error-message"><?= htmlspecialchars($erreurs['prenom']) ?></p>
            <?php endif; ?>
          </div>
        
        
        <div class="form-row">
          <div class="form-field">
            <label for="date_naissance">Date de naissance</label>
            <input type="text" id="date_naissance" name="date_naissance" value="<?= htmlspecialchars($old['date_naissance'] ?? '') ?>" placeholder="JJ/MM/AAAA">
            <?php if (!empty($erreurs['date_naissance'])): ?>
                <p class="error-message"><?= htmlspecialchars($erreurs['date_naissance']) ?></p>
            <?php endif; ?>
          </div>
          <div class="form-field">
            <label for="lieu_naissance">Lieu de naissance</label>
            <input type="text" id="lieu_naissance" name="lieu_naissance" value="<?= htmlspecialchars($old['lieu_naissance'] ?? '') ?>">
            <?php if (!empty($erreurs['lieu_naissance'])): ?>
                <p class="error-message"><?= htmlspecialchars($erreurs['lieu_naissance']) ?></p>
            <?php endif; ?>
          </div>
        </div>
        
        <div class="form-row">
          <div class="form-field">
            <label for="adresse">Adresse</label>
            <input type="text" id="adresse" name="adresse" value="<?= htmlspecialchars($old['adresse'] ?? '') ?>">
            <?php if (!empty($erreurs['adresse'])): ?>
                <p class="error-message"><?= htmlspecialchars($erreurs['adresse']) ?></p>
            <?php endif; ?>
          </div>
          <div class="form-field">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($old['email'] ?? '') ?>">
            <?php if (!empty($erreurs['email'])): ?>
                <p class="error-message"><?= htmlspecialchars($erreurs['email']) ?></p>
            <?php endif; ?>
          </div>
        </div>
        
        <div class="form-row">
          <div class="form-field">
            <label for="telephone">Téléphone</label>
            <input type="tel" id="telephone" name="telephone" value="<?= htmlspecialchars($old['telephone'] ?? '') ?>">
            <?php if (!empty($erreurs['telephone'])): ?>
                <p class="error-message"><?= htmlspecialchars($erreurs['telephone']) ?></p>
            <?php endif; ?>
          </div>
        </div>
        <div class="form-row">
        <div class="form-row">
            <div class="form-field">
                <label>Référentiel</label>
                <label><input type="radio" name="referenciel" value="DEV WEB" <?= isset($old['referenciel']) && $old['referenciel'] == 'DEV WEB' ? 'checked' : '' ?>> DEV WEB</label>
                <label><input type="radio" name="referenciel" value="DATA" <?= isset($old['referenciel']) && $old['referenciel'] == 'DATA' ? 'checked' : '' ?>> DATA</label>
                <label><input type="radio" name="referenciel" value="REF DIGITALE" <?= isset($old['referenciel']) && $old['referenciel'] == 'REF DIGITALE' ? 'checked' : '' ?>> REF DIGITALE</label>
                <?php if (!empty($erreurs['referenciel'])): ?>
                    <p class="error-message"><?= htmlspecialchars($erreurs['referentiel']) ?></p>
                <?php endif; ?>
                </div>
        <div class="document-upload">
        <input type="file" id="photo" name="photo" accept="image/*">
    <label for="document">Télécharger un document</label>
    </div>
  </div>
        
        <div class="form-section">
          <div class="section-title">
            <span>Informations du tuteur</span>
          </div>
          
          <div class="form-row">
            <div class="form-field">
              <label for="tuteur_nom">Prénom(s) & nom</label>
              <input type="text" id="tuteur_nom" name="tuteur_nom" value="<?= htmlspecialchars($old['tuteur_nom'] ?? '') ?>">
              <?php if (!empty($erreurs['tuteur_nom'])): ?>
                  <p class="error-message"><?= htmlspecialchars($erreurs['tuteur_nom']) ?></p>
              <?php endif; ?>
            </div>
            <div class="form-field">
              <label for="lien_parente">Lien de parenté</label>
              <input type="text" id="lien_parente" name="lien_parente" value="<?= htmlspecialchars($old['lien_parente'] ?? '') ?>">
              <?php if (!empty($erreurs['lien_parente'])): ?>
                  <p class="error-message"><?= htmlspecialchars($erreurs['lien_parente']) ?></p>
              <?php endif; ?>
            </div>
          </div>
          
          <div class="form-row">
            <div class="form-field">
              <label for="tuteur_adresse">Adresse</label>
              <input type="text" id="tuteur_adresse" name="tuteur_adresse" value="<?= htmlspecialchars($old['tuteur_adresse'] ?? '') ?>">
              <?php if (!empty($erreurs['tuteur_adresse'])): ?>
                  <p class="error-message"><?= htmlspecialchars($erreurs['tuteur_adresse']) ?></p>
              <?php endif; ?>
            </div>
            <div class="form-field">
              <label for="tuteur_telephone">Téléphone</label>
              <input type="tel" id="tuteur_telephone" name="tuteur_telephone" value="<?= htmlspecialchars($old['tuteur_telephone'] ?? '') ?>">
              <?php if (!empty($erreurs['tuteur_telephone'])): ?>
                  <p class="error-message"><?= htmlspecialchars($erreurs['tuteur_telephone']) ?></p>
              <?php endif; ?>
            </div>
          </div>
        </div>
        
        <div class="button-group">
          <button type="button" class="cancel-btn"><a href="?page=import_apprenants" class="cancel-btn">Annuler</a></button>
          <button type="submit" class="submit-btn">Enregistrer</button>
        </div>
    </form>
  </div>
</body>
 
</html>