<h1 class="h3 mb-3">Mes playlists</h1>
<?php if (!$playlists): ?>
    <p class="text-muted">Aucune playlist pour le moment.</p>
<?php else: ?>
    <ul class="list-group mb-3">
        <?php foreach ($playlists as $pl): ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <a href="index.php?action=playlist&amp;id=<?= (int) $pl['id'] ?>"><?= e($pl['nom']) ?></a>
                <span class="badge text-bg-secondary"><?= (int) $pl['nb_pistes'] ?> piste(s)</span>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
<a class="btn btn-primary" href="index.php?action=create-playlist">Créer une playlist vide</a>
