<?php
// Stabilim unde vor fi păstrate imaginile.
$directorGalerie = __DIR__ . '/galerie';
$directorMiniaturi = __DIR__ . '/miniaturi';

// Creăm directoarele automat dacă nu există.
if (!is_dir($directorGalerie)) {
    mkdir($directorGalerie, 0755, true);
}

if (!is_dir($directorMiniaturi)) {
    mkdir($directorMiniaturi, 0755, true);
}

// Citim numele fișierelor din galerie.
$imagini = scandir($directorGalerie);

// Eliminăm elementele speciale "." și "..".
$imagini = array_diff($imagini, ['.', '..']);

// Păstrăm doar imaginile cu extensiile permise.
$imagini = array_filter($imagini, function ($imagine) {
    $extensie = strtolower(pathinfo($imagine, PATHINFO_EXTENSION));

    return in_array($extensie, ['jpg', 'jpeg', 'png', 'gif']);
});

// Preluăm mesajul transmis de celelalte pagini.
$mesaj = $_GET['mesaj'] ?? '';
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>GalerieFoto</title>

    <!-- Încărcăm Tailwind CSS pentru stilizarea paginii. -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

</head>

<body class="min-h-screen bg-slate-100 text-slate-800">

    <!-- Antetul paginii. -->
    <header class="bg-indigo-700 p-6 text-center text-white shadow">
        <h1 class="text-3xl font-bold">GalerieFoto</h1>
        <p class="mt-2">Încarcă și gestionează imaginile tale</p>
    </header>

    <main class="mx-auto max-w-6xl p-4 sm:p-8">

        <!-- Afișăm mesajele de succes sau eroare. -->
        <?php if ($mesaj != ''): ?>
            <div class="mb-6 rounded-lg bg-white p-4 shadow">
                <?= htmlspecialchars($mesaj, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <!-- Formularul trimite imaginea către incarca.php. -->
        <section class="mb-10 rounded-xl bg-white p-6 shadow">
            <h2 class="mb-4 text-xl font-bold">Încarcă o imagine</h2>

            <form action="incarca.php"
                  method="POST"
                  enctype="multipart/form-data"
                  class="flex flex-col gap-4 sm:flex-row sm:items-center">

                <input
                    type="file"
                    name="imagine"
                    accept=".jpg,.jpeg,.png,.gif"
                    required
                    class="w-full rounded-lg border p-3"
                >

                <button
                    type="submit"
                    class="rounded-lg bg-indigo-600 px-6 py-3 font-semibold text-white hover:bg-indigo-700"
                >
                    Încarcă imaginea
                </button>
            </form>

            <p class="mt-3 text-sm text-slate-500">
                Formate permise: JPEG, PNG și GIF. Dimensiune maximă: 2 MB.
            </p>
        </section>

        <!-- Grila în care afișăm imaginile încărcate. -->
        <section>
            <div class="mb-5 flex flex-wrap items-center justify-between gap-2">
                <h2 class="text-2xl font-bold">Galeria mea</h2>

                <span class="rounded-full bg-indigo-100 px-4 py-2 text-indigo-700">
                    <?= count($imagini) ?> imagini
                </span>
            </div>

            <?php if (count($imagini) == 0): ?>

                <!-- Mesajul apare când galeria este goală. -->
                <div class="rounded-xl bg-white p-10 text-center shadow">
                    <p class="text-lg text-slate-500">
                        Nu există imagini. Încarcă prima ta fotografie!
                    </p>
                </div>

            <?php else: ?>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">

                    <?php foreach ($imagini as $imagine): ?>

                        <!-- Fiecare imagine este afișată într-un card. -->
                        <div class="overflow-hidden rounded-xl bg-white shadow">

                            <a href="vizualizare.php?imagine=<?= rawurlencode($imagine) ?>">
                                <img
                                    src="miniaturi/<?= rawurlencode($imagine) ?>"
                                    alt="Fotografie din galerie"
                                    class="h-56 w-full object-cover"
                                >
                            </a>

                            <div class="p-4">
                                <p class="mb-3 break-all font-medium">
                                    <?= htmlspecialchars($imagine, ENT_QUOTES, 'UTF-8') ?>
                                </p>

                                <!-- Butoanele pentru gestionarea imaginii. -->
                                <div class="flex flex-wrap gap-2">

                                    <a
                                        href="vizualizare.php?imagine=<?= rawurlencode($imagine) ?>"
                                        class="rounded bg-indigo-600 px-3 py-2 text-sm text-white hover:bg-indigo-700"
                                    >
                                        Vizualizare
                                    </a>

                                    <a
                                        href="descarca.php?imagine=<?= rawurlencode($imagine) ?>"
                                        class="rounded bg-emerald-600 px-3 py-2 text-sm text-white hover:bg-emerald-700"
                                    >
                                        Descarcă
                                    </a>

                                    <!-- Ștergerea se face printr-un formular POST. -->
                                    <form action="sterge.php" method="POST"
                                          onsubmit="return confirm('Sigur dorești să ștergi imaginea?');">

                                        <input type="hidden" name="imagine"
                                               value="<?= htmlspecialchars($imagine, ENT_QUOTES, 'UTF-8') ?>">

                                        <button
                                            type="submit"
                                            class="rounded bg-red-600 px-3 py-2 text-sm text-white hover:bg-red-700"
                                        >
                                            Șterge
                                        </button>
                                    </form>

                                </div>
                            </div>
                        </div>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>
        </section>
    </main>

    <footer class="mt-10 bg-slate-800 p-5 text-center text-white">
        GalerieFoto — Proiect realizat în PHP și Tailwind CSS
    </footer>

</body>
</html>