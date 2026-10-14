<?php
// 1. Stabilim căile către directoarele proiectului.
$directorGalerie = __DIR__ . '/galerie';
$directorMiniaturi = __DIR__ . '/miniaturi';

// 2. Creăm automat directorul galerie dacă nu există.
if (!is_dir($directorGalerie)) {
    if (!mkdir($directorGalerie, 0755, true)) {
        die('Eroare: directorul galerie nu a putut fi creat.');
    }
}

// 3. Creăm automat directorul miniaturi dacă nu există.
if (!is_dir($directorMiniaturi)) {
    if (!mkdir($directorMiniaturi, 0755, true)) {
        die('Eroare: directorul miniaturi nu a putut fi creat.');
    }
}

// 4. Verificăm dacă formularul a trimis o imagine.
if (!isset($_FILES['imagine'])) {
    header('Location: index.php?mesaj=' . urlencode('Selectează o imagine.'));
    exit;
}

$fisier = $_FILES['imagine'];

// 5. Verificăm erorile de încărcare PHP.
if ($fisier['error'] !== UPLOAD_ERR_OK) {
    $erori = [
        UPLOAD_ERR_INI_SIZE => 'Imaginea depășește limita permisă de PHP.',
        UPLOAD_ERR_FORM_SIZE => 'Imaginea este prea mare.',
        UPLOAD_ERR_PARTIAL => 'Imaginea a fost încărcată doar parțial.',
        UPLOAD_ERR_NO_FILE => 'Nu ai selectat nicio imagine.'
    ];

    $mesaj = $erori[$fisier['error']] ?? 'Eroare necunoscută la încărcare.';

    header('Location: index.php?mesaj=' . urlencode($mesaj));
    exit;
}

// 6. Verificăm limita de 2 MB.
if ($fisier['size'] > 2 * 1024 * 1024) {
    header('Location: index.php?mesaj=' . urlencode('Imaginea trebuie să aibă maximum 2 MB.'));
    exit;
}

// 7. Verificăm extensia imaginii.
$extensie = strtolower(pathinfo($fisier['name'], PATHINFO_EXTENSION));

if (!in_array($extensie, ['jpg', 'jpeg', 'png', 'gif'])) {
    header('Location: index.php?mesaj=' . urlencode('Acceptăm doar JPG, JPEG, PNG și GIF.'));
    exit;
}

// 8. Verificăm dacă fișierul este o imagine reală.
$informatii = getimagesize($fisier['tmp_name']);

if ($informatii === false) {
    header('Location: index.php?mesaj=' . urlencode('Fișierul nu este o imagine validă.'));
    exit;
}

// 9. Generăm un nume sigur și unic pentru imagine.
$nume = bin2hex(random_bytes(8)) . '.' . $extensie;

// 10. Construim căile către fișierele finale.
$original = $directorGalerie . '/' . $nume;
$miniatura = $directorMiniaturi . '/' . $nume;

// 11. Mutăm imaginea încărcată în directorul galerie.
if (!move_uploaded_file($fisier['tmp_name'], $original)) {
    header('Location: index.php?mesaj=' . urlencode('Nu s-a putut salva imaginea. Verifică permisiunile folderului.'));
    exit;
}

// 12. Verificăm dacă PHP poate genera miniaturi.
if (!function_exists('imagecreatetruecolor')) {
    unlink($original);

    header('Location: index.php?mesaj=' . urlencode('GD nu este activat în PHP. Activează extensia GD.'));
    exit;
}

// 13. Deschidem imaginea în funcție de format.
if ($extensie == 'jpg' || $extensie == 'jpeg') {
    $sursa = imagecreatefromjpeg($original);
} elseif ($extensie == 'png') {
    $sursa = imagecreatefrompng($original);
} else {
    $sursa = imagecreatefromgif($original);
}

// 14. Verificăm dacă imaginea poate fi procesată.
if (!$sursa) {
    unlink($original);

    header('Location: index.php?mesaj=' . urlencode('Nu s-a putut procesa imaginea.'));
    exit;
}

// 15. Citim dimensiunile imaginii originale.
$latime = imagesx($sursa);
$inaltime = imagesy($sursa);

// 16. Calculăm dimensiunile miniaturii fără a mări imaginea.
$factor = min(300 / $latime, 200 / $inaltime, 1);

$latimeNoua = max(1, (int) ($latime * $factor));
$inaltimeNoua = max(1, (int) ($inaltime * $factor));

// 17. Creăm miniatura.
$imagineNoua = imagecreatetruecolor($latimeNoua, $inaltimeNoua);

// Păstrăm transparența pentru PNG și GIF.
if ($extensie == 'png' || $extensie == 'gif') {
    imagealphablending($imagineNoua, false);
    imagesavealpha($imagineNoua, true);

    $transparent = imagecolorallocatealpha($imagineNoua, 0, 0, 0, 127);
    imagefill($imagineNoua, 0, 0, $transparent);
}

// 18. Redimensionăm imaginea.
imagecopyresampled(
    $imagineNoua,
    $sursa,
    0,
    0,
    0,
    0,
    $latimeNoua,
    $inaltimeNoua,
    $latime,
    $inaltime
);

// 19. Salvăm miniatura.
if ($extensie == 'jpg' || $extensie == 'jpeg') {
    $salvat = imagejpeg($imagineNoua, $miniatura, 85);
} elseif ($extensie == 'png') {
    $salvat = imagepng($imagineNoua, $miniatura);
} else {
    $salvat = imagegif($imagineNoua, $miniatura);
}

// 20. Eliberăm memoria.
imagedestroy($sursa);
imagedestroy($imagineNoua);

// 21. Verificăm dacă miniatura a fost salvată.
if (!$salvat) {
    unlink($original);

    header('Location: index.php?mesaj=' . urlencode('Nu s-a putut salva miniatura.'));
    exit;
}

// 22. Revenim la pagina principală cu un mesaj.
header('Location: index.php?mesaj=' . urlencode('Imaginea a fost încărcată cu succes!'));
exit;
?>