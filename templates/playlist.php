<h1 class="h3 mb-3"><?= e($playlist['nom']) ?></h1>
<?php if (!$tracks): ?>
    <p class="text-muted">Cette playlist est vide.</p>
<?php else: ?>
    <?php $total = 0; ?>
    <div class="table-responsive">
        <table class="table table-striped align-middle">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Titre</th>
                    <th scope="col">Artiste</th>
                    <th scope="col">Album</th>
                    <th scope="col">Année</th>
                    <th scope="col">Genre</th>
                    <th scope="col">Durée</th>
                    <th scope="col">Écoute</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tracks as $i => $t): ?>
                    <?php $total += (int) $t['duree']; ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= e($t['titre']) ?></td>
                        <td><?= e($t['artiste']) ?></td>
                        <td><?= e($t['album']) ?></td>
                        <td><?= e($t['annee']) ?></td>
                        <td><?= e($t['genre']) ?></td>
                        <td><?= e(formatDuration((int) $t['duree'])) ?></td>
                        <td>
                            <?php if ($t['filename']): ?>
                                <audio controls preload="none" src="audio/<?= e($t['filename']) ?>"></audio>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <p><strong><?= count($tracks) ?></strong> piste(s) – durée totale : <?= e(formatDuration($total)) ?></p>
<?php endif; ?>
<a class="btn btn-primary" href="index.php?action=add-track">Ajouter une piste</a>
