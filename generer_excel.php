<?php
require 'vendor/autoload.php'; // Assure-toi d'avoir installé PhpSpreadsheet via Composer

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Créer une nouvelle feuille Excel
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// En-têtes des colonnes
$colonnes = [
    'nom_complet', 'date_naissance', 'lieu_naissance', 'adresse',
    'login', 'telephone', 'document', 'tuteur_nom', 'lien_parente',
    'tuteur_adresse', 'tuteur_telephone', 'referenciel'
];

$sheet->fromArray($colonnes, null, 'A1');

// Enregistre le fichier
$writer = new Xlsx($spreadsheet);
$fichier = 'amadou.xlsx';
$writer->save($fichier);

echo "Fichier '$fichier' généré avec succès.";
