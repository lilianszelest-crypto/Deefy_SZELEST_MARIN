<h1 class="h3 mb-3">Créer une playlist vide</h1>
<?php foreach ($errors as $error): ?>
    <div class="alert alert-danger" role="alert"><?= e($error) ?></div>
<?php endforeach; ?>
<form method="post" action="index.php?action=create-playlist" class="col-md-6">
    <input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>">
    <div class="mb-3">
        <label for="nom" class="form-label">Nom de la playlist</label>
        <input type="text" class="form-control" id="nom" name="nom" value="<?= e($nom) ?>" maxlength="100" required>
    </div>
    <button type="submit" class="btn btn-primary">Créer</button>
</form>
