<h1 class="h3 mb-3">Ajouter une piste à « <?= e($playlist['nom']) ?> »</h1>
<?php foreach ($errors as $error): ?>
    <div class="alert alert-danger" role="alert"><?= e($error) ?></div>
<?php endforeach; ?>
<form method="post" action="index.php?action=add-track" enctype="multipart/form-data" class="col-md-6">
    <input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>">
    <div class="mb-3">
        <label for="titre" class="form-label">Titre</label>
        <input type="text" class="form-control" id="titre" name="titre" value="<?= e($data['titre']) ?>" maxlength="150" required>
    </div>
    <div class="mb-3">
        <label for="artiste" class="form-label">Artiste</label>
        <input type="text" class="form-control" id="artiste" name="artiste" value="<?= e($data['artiste']) ?>" maxlength="150" required>
    </div>
    <div class="mb-3">
        <label for="album" class="form-label">Album</label>
        <input type="text" class="form-control" id="album" name="album" value="<?= e($data['album']) ?>" maxlength="150">
    </div>
    <div class="mb-3">
        <label for="annee" class="form-label">Année</label>
        <input type="number" class="form-control" id="annee" name="annee" value="<?= e($data['annee']) ?>" min="1900" max="<?= date('Y') ?>">
    </div>
    <div class="mb-3">
        <label for="genre" class="form-label">Genre</label>
        <input type="text" class="form-control" id="genre" name="genre" value="<?= e($data['genre']) ?>" maxlength="50">
    </div>
    <div class="mb-3">
        <label for="duree" class="form-label">Durée (en secondes)</label>
        <input type="number" class="form-control" id="duree" name="duree" value="<?= e($data['duree']) ?>" min="1" max="36000" required>
    </div>
    <div class="mb-3">
        <label for="audio" class="form-label">Fichier audio (MP3, facultatif)</label>
        <input type="file" class="form-control" id="audio" name="audio" accept=".mp3,audio/mpeg">
    </div>
    <button type="submit" class="btn btn-primary">Ajouter</button>
    <a class="btn btn-outline-secondary" href="index.php?action=current">Annuler</a>
</form>
