<!DOCTYPE html>
<html lang="fr">
<?php 
require_once __DIR__ . '/../../enums/chemin_page.php';
use App\Enums\CheminPage;
$url = "http://" . $_SERVER["HTTP_HOST"];
$css_path = CheminPage::CSS_DETAILAPP->value;
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= $url . $css_path ?>">
    <title>Tableau de bord étudiant</title>
</head>
<body>
    <div class="container">
    <div class="topbar">
    <a href="?page=import_apprenants" class="back-button">⬅ Retour sur la liste</a>

    <div class="profile">
        <div class="profile-pic">
            <img src="<?= htmlspecialchars($apprenant['photo'] ?? 'https://via.placeholder.com/50') ?>" alt="Photo Profil">
        </div>
        <div class="profile-info">
            <h3 class="profile-name">
                <?= htmlspecialchars($apprenant['prenom'] ?? '') . ' ' . htmlspecialchars($apprenant['nom'] ?? '') ?>
            </h3>
            <div class="status-badge">
                <?= htmlspecialchars($apprenant['referentiel'] ?? 'Non spécifié') ?>
            </div>
        </div>
    </div>

    <div class="contact-info">
        <div class="info-item">
            <span class="info-icon">📱</span>
            <span><?= htmlspecialchars($apprenant['telephone'] ?? 'Non renseigné') ?></span>
        </div>
        <div class="info-item">
            <span class="info-icon">✉️</span>
            <span><?= htmlspecialchars($apprenant['email'] ?? 'Non renseigné') ?></span>
        </div>
        <div class="info-item">
            <span class="info-icon">🏠</span>
            <span><?= htmlspecialchars($apprenant['adresse'] ?? 'Non renseigné') ?></span>
        </div>
    </div>

    <button class="action-button">+</button>
</div>


        <!-- Main Content -->
        <div class="main-content">
            <!-- Stats Row -->
            <div class="stats-row">
                <div class="stat-card">
                    <div class="stat-icon green-icon">✓</div>
                    <div>
                        <div class="stat-number">44</div>
                        <div class="stat-label">Présence(s)</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon orange-icon">⏰</div>
                    <div>
                        <div class="stat-number">16</div>
                        <div class="stat-label">Retard(s)</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon red-icon">⚠️</div>
                    <div>
                        <div class="stat-number">6</div>
                        <div class="stat-label">Absence(s)</div>
                    </div>
                </div>
            </div>

            <!-- Tabs -->
            <div class="tabs">
                <div class="tab active">Programme & Modules</div>
                <div class="tab"> absences étudiant</div>
            </div>

            <!-- Brand Indicator -->
            <div class="brand-indicator"></div>

            <!-- Course Grid -->
            <div class="course-grid">
                <?php 
                $courses = [
                    ['durée' => '24 jours', 'titre' => ' Langage C', 'sous_titre' => 'Complexité algorithmique & pratique codage en langage C', 'date' => '15 Février 2025', 'heure' => '12:45 pm', 'status' => 'en cours'],
                    ['durée' => '10 jours', 'titre' => 'Frontend 1: Html, Css & JS', 'sous_titre' => 'Création d\'interfaces de design avec animations avancées !', 'date' => '24 Mars 2025', 'heure' => '12:45 pm', 'status' => 'Démarré'],
                    ['durée' => '27 jours', 'titre' => 'Backend 1: Algo  & POO', 'sous_titre' => 'Codage orienté objet', 'date' => '23 Mars 2024', 'heure' => '12:45 pm', 'status' => 'En cours'],
                    ['durée' => '18 jours', 'titre' => 'Frontend 2: laravel & TS + Tailwind', 'sous_titre' => 'UI Design & Développement', 'date' => '23 Mars 2024', 'heure' => '12:45 pm', 'status' => 'en cours'],
                    ['durée' => '31 jours', 'titre' => 'Backend 2: java ', 'sous_titre' => 'Méthodologie Clean Code', 'date' => '23 Mars 2024', 'heure' => '12:45 pm', 'status' => 'Démarré'],
                    ['durée' => '19 jours', 'titre' => 'Frontend 3: js react', 'sous_titre' => 'Développement SPA avec React', 'date' => '23 Mars 2024', 'heure' => '12:45 pm', 'status' => 'termine']
                ];

                foreach ($courses as $course): ?>
                    <div class="course-card">
                        <div class="course-header">
                            <div class="course-duration"><i>⏱</i> <?= $course['durée'] ?></div>
                            <div class="menu-dots">...</div>
                            <h3 class="course-title"><?= $course['titre'] ?></h3>
                            <p class="course-subtitle"><?= $course['sous_titre'] ?></p>
                            <span class="course-label <?= $course['status'] == 'En cours' ? 'in-progress-badge' : '' ?>">
                                <?= $course['status'] ?>
                            </span>
                        </div>
                        <div class="course-footer">
                            <div class="course-date"><i>📅</i> <?= $course['date'] ?></div>
                            <div class="course-time"><i>⏰</i> <?= $course['heure'] ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</body>
</html>
