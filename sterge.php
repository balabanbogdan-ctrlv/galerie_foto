<?php
// Stabilim directoarele imaginilor.
$directorGalerie = __DIR__ . '/galerie';
$directorMiniaturi = __DIR__ . '/miniaturi';

// Verificăm dacă formularul a trimis numele imaginii.
if (!isset($_POST['imagine'])) {
    header('Location: index.php?mesaj=' . urlencode('Nu ai selectat o imagine.'));
    exit;
}

// Preluăm numele imaginii.
$imagine = $_POST['imagine'];

// Verificăm dacă numele este valid.
if (!preg_match('/^[a-f0-9]{16}\.(jpg|jpeg|png|gif)$/', $imagine)) {
    header('Location: index.php?mesaj=' . urlencode('Imagine invalidă.'));
    exit;
}

// Construim căile către original și miniatură.
$caleOriginal = $directorGalerie . '/' . $imagine;
$caleMiniatura = $directorMiniaturi . '/' . $imagine;

// Verificăm dacă imaginea originală există.
if (!is_file($caleOriginal)) {
    header('Location: index.php?mesaj=' . urlencode('Imaginea nu există.'));
    exit;
}

// Ștergem imaginea originală.
if (unlink($caleOriginal)) {

    // Ștergem și miniatura, dacă există.
    if (is_file($caleMiniatura)) {
        unlink($caleMiniatura);
    }

    // Anunțăm utilizatorul că operația a reușit.
    header('Location: index.php?mesaj=' . urlencode('Imaginea a fost ștearsă.'));
    exit;

} else {

    // Afișăm un mesaj dacă ștergerea a eșuat.
    header('Location: index.php?mesaj=' . urlencode('Imaginea nu a putut fi ștearsă.'));
    exit;
}
?>