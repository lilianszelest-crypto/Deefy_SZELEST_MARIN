<?php
declare(strict_types=1);

require __DIR__ . '/src/bootstrap.php';
require __DIR__ . '/src/Repository.php';

const AUDIO_DIR = __DIR__ . '/audio';

/** Playlist courante : l'identifiant et le nom sont stockés en session. */
function setCurrentPlaylist(array $playlist): void
{
    $_SESSION['playlist'] = ['id' => (int) $playlist['id'], 'nom' => (string) $playlist['nom']];
}

function currentPlaylist(): ?array
{
    $current = $_SESSION['playlist'] ?? null;
    if ($current === null) {
        return null;
    }
    // On revérifie en base : la playlist a pu être supprimée entre-temps.
    $playlist = Repository::findPlaylist((int) $current['id']);
    if ($playlist === null) {
        unset($_SESSION['playlist']);
    }
    return $playlist;
}

$action = $_GET['action'] ?? 'playlists';
$action = is_string($action) ? $action : 'playlists';

try {
    switch ($action) {
        // 1. Mes playlists
        case 'playlists':
            render('playlists', ['playlists' => Repository::allPlaylists()], 'Mes playlists');
            break;

        // 2. Afficher une playlist de la liste : elle devient la playlist courante
        case 'playlist':
            $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
            $playlist = $id ? Repository::findPlaylist($id) : null;
            if ($playlist === null) {
                http_response_code(404);
                render('message', ['text' => "Cette playlist n'existe pas."], 'Introuvable');
                break;
            }
            setCurrentPlaylist($playlist);
            redirect('?action=current');

        // 5. Afficher la playlist courante (stockée en session)
        case 'current':
            $playlist = currentPlaylist();
            if ($playlist === null) {
                render('message', ['text' => "Aucune playlist courante. Choisissez-en une dans « Mes playlists » ou créez-en une."], 'Playlist courante');
                break;
            }
            render('playlist', [
                'playlist' => $playlist,
                'tracks'   => Repository::tracksOf((int) $playlist['id']),
            ], $playlist['nom']);
            break;

        // 4. Créer une playlist vide
        case 'create-playlist':
            $nom = '';
            $errors = [];
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (!csrfCheck()) {
                    http_response_code(403);
                    render('message', ['text' => 'Formulaire invalide (jeton CSRF).'], 'Erreur');
                    break;
                }
                $nom = trim((string) ($_POST['nom'] ?? ''));
                if ($nom === '' || mb_strlen($nom) > 100) {
                    $errors[] = 'Le nom est obligatoire (100 caractères maximum).';
                } else {
                    $id = Repository::createPlaylist($nom);
                    setCurrentPlaylist(['id' => $id, 'nom' => $nom]);
                    flash('success', 'Playlist créée.');
                    redirect('?action=current');
                }
            }
            render('create_playlist', ['nom' => $nom, 'errors' => $errors], 'Créer une playlist');
            break;

        // 3. Ajouter une piste à la playlist courante
        case 'add-track':
            $playlist = currentPlaylist();
            if ($playlist === null) {
                render('message', ['text' => "Aucune playlist courante : impossible d'ajouter une piste."], 'Ajouter une piste');
                break;
            }
            $data = ['titre' => '', 'artiste' => '', 'album' => '', 'annee' => '', 'genre' => '', 'duree' => ''];
            $errors = [];
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (!csrfCheck()) {
                    http_response_code(403);
                    render('message', ['text' => 'Formulaire invalide (jeton CSRF).'], 'Erreur');
                    break;
                }
                foreach ($data as $key => $_) {
                    $data[$key] = trim((string) ($_POST[$key] ?? ''));
                }

                if ($data['titre'] === '' || mb_strlen($data['titre']) > 150) {
                    $errors[] = 'Le titre est obligatoire (150 caractères maximum).';
                }
                if ($data['artiste'] === '' || mb_strlen($data['artiste']) > 150) {
                    $errors[] = "L'artiste est obligatoire (150 caractères maximum).";
                }
                if (mb_strlen($data['album']) > 150) {
                    $errors[] = "L'album ne doit pas dépasser 150 caractères.";
                }
                if (mb_strlen($data['genre']) > 50) {
                    $errors[] = 'Le genre ne doit pas dépasser 50 caractères.';
                }
                $annee = $data['annee'] === '' ? null
                    : filter_var($data['annee'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1900, 'max_range' => (int) date('Y')]]);
                if ($annee === false) {
                    $errors[] = "L'année doit être comprise entre 1900 et " . date('Y') . '.';
                }
                $duree = filter_var($data['duree'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 36000]]);
                if ($duree === false) {
                    $errors[] = 'La durée (en secondes) doit être un entier positif.';
                }

                // Fichier audio facultatif : MP3 uniquement, vérifié sur le contenu.
                $filename = null;
                $file = $_FILES['audio'] ?? null;
                if ($file !== null && $file['error'] !== UPLOAD_ERR_NO_FILE) {
                    if ($file['error'] !== UPLOAD_ERR_OK || $file['size'] > 10 * 1024 * 1024) {
                        $errors[] = "Le fichier audio n'a pas pu être envoyé (10 Mo maximum).";
                    } else {
                        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
                        $ext  = strtolower(pathinfo((string) $file['name'], PATHINFO_EXTENSION));
                        if ($ext !== 'mp3' || !in_array($mime, ['audio/mpeg', 'application/octet-stream'], true)) {
                            $errors[] = 'Le fichier audio doit être un MP3.';
                        } else {
                            $filename = bin2hex(random_bytes(16)) . '.mp3';
                        }
                    }
                }

                if (!$errors) {
                    if ($filename !== null && !move_uploaded_file($file['tmp_name'], AUDIO_DIR . '/' . $filename)) {
                        $errors[] = "Impossible d'enregistrer le fichier audio.";
                    } else {
                        Repository::addTrack((int) $playlist['id'], [
                            'titre'    => $data['titre'],
                            'artiste'  => $data['artiste'],
                            'album'    => $data['album'] === '' ? null : $data['album'],
                            'annee'    => $annee,
                            'genre'    => $data['genre'] === '' ? null : $data['genre'],
                            'duree'    => $duree,
                            'filename' => $filename,
                        ]);
                        flash('success', 'Piste ajoutée.');
                        redirect('?action=current');
                    }
                }
            }
            render('add_track', ['playlist' => $playlist, 'data' => $data, 'errors' => $errors], 'Ajouter une piste');
            break;

        default:
            http_response_code(404);
            render('message', ['text' => "Cette page n'existe pas."], 'Introuvable');
    }
} catch (PDOException $ex) {
    error_log($ex->getMessage());
    http_response_code(500);
    render('message', ['text' => 'Erreur de base de données. Réessayez plus tard.'], 'Erreur');
}
