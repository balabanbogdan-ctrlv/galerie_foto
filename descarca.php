<?php
// Stabilim directorul imaginilor originale.
$directorGalerie = __DIR__ . '/galerie';

// Preluăm numele imaginii.
$imagine = $_GET['imagine'] ?? '';

// Verificăm dacă numele este valid.
if (!preg_match('/^[a-f0-9]{16}\.(jpg|jpeg|png|gif)$/', $imagine)) {
    header('Location: index.php?mesaj=' . urlencode('Imagine invalidă.'));
    exit;
}

// Construim calea către fișier.
$cale = $directorGalerie . '/' . $imagine;

// Verificăm existența imaginii.
if (!is_file($cale)) {
    header('Location: index.php?mesaj=' . urlencode('Imaginea nu există.'));
    exit;
}

// Oprim orice altă ieșire înainte de descărcare.
header('Content-Type: application/octet-stream');

// Cerem browserului să descarce fișierul.
header('Content-Disposition: attachment; filename="' . $imagine . '"');

// Trimitem dimensiunea fișierului.
header('Content-Length: ' . filesize($cale));

// Trimitem conținutul imaginii către browser.
readfile($cale);
exit;
?>