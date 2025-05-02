<?php
// Charger les données
$data = charger_donnees();
$promotions = $data['promotions'] ?? [];
$referentiels = $data['referentiels'] ?? [];

// Fonction pour récupérer les noms des référentiels
function getReferentielNames(array $referenciel_ids, array $referentiels): string {
    $noms = array_map(function ($id) use ($referentiels) {
        foreach ($referentiels as $ref) {
            if ($ref['id'] == $id) {
                return $ref['nom'];
            }
        }
        return null;
    }, $referenciel_ids);

    return implode(', ', array_filter($noms));
}

// Pagination
$perPage = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$total = count($promotions);
$pages = ceil($total / $perPage);
$start = ($page - 1) * $perPage;
$paginatedPromos = array_slice($promotions, $start, $perPage);
?>

<!DOCTYPE html>
<html lang="fr">
<head>

<?php
require_once __DIR__ . '/../../enums/chemin_page.php';
use App\Enums\CheminPage;
$url = "http://" . $_SERVER["HTTP_HOST"];
$css_promo = CheminPage::CSS_PROMO->value;
?>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestion des promotions</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="<?= $url . $css_promo ?>">
</head>
<body>
  <!-- En-tête -->
  <div class="header">
    <h1>Promotion</h1>
    
    <span class="count"> 19 apprenants</span>
  </div>
  
  <!-- Barre d'outils -->
  <div class="toolbar">
    <div class="search-box">
      <i class="fa fa-search"></i>
      <input type="text" placeholder="Rechercher...">
    </div>
    <div class="filter-dropdown">
      <select>
        <option>Filtre par classe</option>
      </select>
    </div>
    <div class="filter-dropdown">
      <select>
        <option>Filtre par status</option>
      </select>
    </div>
    <button >
      <a href="?page=form" class="add-btn">+ Ajouter une promotion</a>
    </button>
  </div>
  
  <!-- Cartes d'information -->
  <div class="cards">
    <div class="card">
      <div class="icon">
        <i class="fa fa-graduation-cap"></i>
      </div>
      <div class="info">
        <div class="number">20</div>
        <div class="label">Apprenants</div>
      </div>
    </div>
    <div class="card">
      <div class="icon">
        <i class="fa fa-folder"></i>
      </div>
      <div class="info">
        <div class="number">5</div>
        <div class="label">Référentiels</div>
      </div>
    </div>
    <div class="card">
      <div class="icon">
        <i class="fa fa-user-graduate"></i>
      </div>
      <div class="info">
        <div class="number">5</div>
        <div class="label">Stagiaires</div>
      </div>
    </div>
    <div class="card">
      <div class="icon">
        <i class="fa fa-users"></i>
      </div>
      <div class="info">
        <div class="number">13</div>
        <div class="label">Permanent</div>
      </div>
    </div>
  </div>
  
  <!-- Tableau -->
  <table>
    <thead>
        <tr>
            <th>Photo</th>
            <th>Promotion</th>
            <th>Date de début</th>
            <th>Date de fin</th>
            <th>Référentiel</th>
            <th>Statut</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($paginatedPromos as $promo): ?>
            <tr>
                <td class="photo-cell">
                    <img src="<?= htmlspecialchars($promo['photo']) ?>" alt="photo" width="50">
                </td>
                <td class="promo-cell"><?= htmlspecialchars($promo['nom']) ?></td>
                <td class="date-cell"><?= htmlspecialchars($promo['dateDebut']) ?></td>
                <td class="date-cell"><?= htmlspecialchars($promo['dateFin']) ?></td>
                <td>
                    <div class="tag">
                        <?php
                        $referenciel_ids = $promo['referenciel_ids'] ?? [];
                        if (!is_array($referenciel_ids)) {
                            $referenciel_ids = [];
                        }

                        $referentielNames = getReferentielNames($referenciel_ids, $referentiels);
                        foreach (explode(', ', $referentielNames) as $name): ?>
                            <span class="tag"><?= htmlspecialchars($name) ?></span>
                        <?php endforeach; ?>
                    </div>
                </td>
                <td><span class="status <?= strtolower($promo['statut']) ?>"><?= htmlspecialchars($promo['statut']) ?></span></td>
                <td class="action-cell"><span class="dots">•••</span></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
  </table>

  <!-- Pagination -->
  <div class="pagination-container">
    <?php if ($page > 1): ?>
        <a href="?page=liste_table_promo&p=<?= $page - 1 ?>&limit=<?= $perPage ?>" class="pagination-link prev">Précédent</a>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $pages; $i++): ?>
        <a href="?page=liste_table_promo&p=<?= $i ?>&limit=<?= $perPage ?>" class="pagination-link <?= $i === $page ? 'active' : '' ?>">
            <?= $i ?>
        </a>
    <?php endfor; ?>

    <?php if ($page < $pages): ?>
        <a href="?page=liste_table_promo&p=<?= $page + 1 ?>&limit=<?= $perPage ?>" class="pagination-link next">Suivant</a>
    <?php endif; ?>
  </div>
  
</div>

</body>
</html>