<?php
// Stabilim directorul imaginilor originale.
$directorGalerie = __DIR__ . '/galerie';

// Preluăm numele imaginii din adresa paginii.
$imagine = $_GET['imagine'] ?? '';

// Acceptăm doar nume de fișiere generate de aplicație.
if (!preg_match('/^[a-f0-9]{16}\.(jpg|jpeg|png|gif)$/', $imagine)) {
    header('Location: index.php?mesaj=' . urlencode('Imagine invalidă.'));
    exit;
}

// Construim calea către imagine.
$cale = $directorGalerie . '/' . $imagine;

// Verificăm dacă imaginea există.
if (!is_file($cale)) {
    header('Location: index.php?mesaj=' . urlencode('Imaginea nu există.'));
    exit;
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vizualizare imagine</title>

    <!-- Folosim Tailwind CSS pentru design. -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-slate-100 p-4">

    <main class="mx-auto max-w-5xl">

        <!-- Buton pentru revenirea la galerie. -->
        <a href="index.php"
           class="mb-6 inline-block rounded-lg bg-indigo-600 px-5 py-3 text-white hover:bg-indigo-700">
            Înapoi la galerie
        </a>

        <div class="rounded-xl bg-white p-4 shadow sm:p-6">

            <h1 class="mb-5 break-all text-xl font-bold">
                <?= htmlspecialchars($imagine, ENT_QUOTES, 'UTF-8') ?>
            </h1>

            <!-- Afișăm imaginea originală, nu miniatura. -->
            <img
                src="galerie/<?= rawurlencode($imagine) ?>"
                alt="Imagine la dimensiunea originală"
                class="mx-auto h-auto max-w-full rounded-lg"
            >

            <!-- Oferim și posibilitatea de descărcare. -->
            <a href="descarca.php?imagine=<?= rawurlencode($imagine) ?>"
               class="mt-5 inline-block rounded-lg bg-emerald-600 px-5 py-3 text-white hover:bg-emerald-700">
                Descarcă imaginea
            </a>

        </div>
    </main>

</body>
</html>