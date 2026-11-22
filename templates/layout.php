<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title) ?> – Deefy</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body>
<nav class="navbar navbar-expand-md navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="index.php">Deefy</a>
        <ul class="navbar-nav me-auto">
            <li class="nav-item"><a class="nav-link" href="index.php?action=playlists">Mes playlists</a></li>
            <li class="nav-item"><a class="nav-link" href="index.php?action=create-playlist">Créer une playlist vide</a></li>
            <li class="nav-item"><a class="nav-link" href="index.php?action=current">Playlist courante<?= isset($_SESSION['playlist']) ? ' : ' . e($_SESSION['playlist']['nom']) : '' ?></a></li>
        </ul>
    </div>
</nav>
<main class="container">
    <?php if ($flash !== null): ?>
        <div class="alert alert-<?= e($flash['type']) ?>" role="alert"><?= e($flash['message']) ?></div>
    <?php endif; ?>
    <?= $content ?>
</main>
</body>
</html>
