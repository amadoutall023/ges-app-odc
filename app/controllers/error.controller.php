<?php
// Gestion des erreurs<?php

require_once __DIR__ . '/../controllers/error.controller.php';
function showError(string $message): void {
    echo "<h1>Erreur</h1>";
    echo "<p>$message</p>";
    exit;
}