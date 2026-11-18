<?php
declare(strict_types=1);

/** Accès aux données : uniquement des requêtes préparées. */
final class Repository
{
    /** @return list<array{id:int,nom:string,nb_pistes:int}> */
    public static function allPlaylists(): array
    {
        $sql = 'SELECT p.id, p.nom, COUNT(pt.id_track) AS nb_pistes
                FROM playlist p
                LEFT JOIN playlist2track pt ON pt.id_pl = p.id
                GROUP BY p.id, p.nom
                ORDER BY p.nom';
        return pdo()->query($sql)->fetchAll();
    }

    public static function findPlaylist(int $id): ?array
    {
        $st = pdo()->prepare('SELECT id, nom FROM playlist WHERE id = :id');
        $st->execute(['id' => $id]);
        $row = $st->fetch();
        return $row === false ? null : $row;
    }

    public static function createPlaylist(string $nom): int
    {
        $st = pdo()->prepare('INSERT INTO playlist (nom) VALUES (:nom)');
        $st->execute(['nom' => $nom]);
        return (int) pdo()->lastInsertId();
    }

    public static function tracksOf(int $playlistId): array
    {
        $sql = 'SELECT t.id, t.titre, t.artiste, t.album, t.annee, t.genre, t.duree, t.filename
                FROM track t
                JOIN playlist2track pt ON pt.id_track = t.id
                WHERE pt.id_pl = :id
                ORDER BY pt.no_piste_dans_liste';
        $st = pdo()->prepare($sql);
        $st->execute(['id' => $playlistId]);
        return $st->fetchAll();
    }

    /** Crée la piste et l'ajoute en fin de playlist (transaction). */
    public static function addTrack(int $playlistId, array $t): void
    {
        $pdo = pdo();
        $pdo->beginTransaction();
        try {
            $st = $pdo->prepare(
                'INSERT INTO track (titre, artiste, album, annee, genre, duree, filename)
                 VALUES (:titre, :artiste, :album, :annee, :genre, :duree, :filename)'
            );
            $st->execute([
                'titre'    => $t['titre'],
                'artiste'  => $t['artiste'],
                'album'    => $t['album'],
                'annee'    => $t['annee'],
                'genre'    => $t['genre'],
                'duree'    => $t['duree'],
                'filename' => $t['filename'],
            ]);
            $trackId = (int) $pdo->lastInsertId();

            $st = $pdo->prepare('SELECT COALESCE(MAX(no_piste_dans_liste), 0) + 1 FROM playlist2track WHERE id_pl = :id');
            $st->execute(['id' => $playlistId]);
            $next = (int) $st->fetchColumn();

            $st = $pdo->prepare('INSERT INTO playlist2track (id_pl, id_track, no_piste_dans_liste) VALUES (:pl, :tr, :no)');
            $st->execute(['pl' => $playlistId, 'tr' => $trackId, 'no' => $next]);

            $pdo->commit();
        } catch (Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}
