<?php
declare(strict_types=1);

session_start();

/** Échappement HTML (protection XSS) : à utiliser pour toute donnée affichée. */
function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Connexion PDO unique ; requêtes préparées natives (protection injection SQL). */
function pdo(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        $config = require __DIR__ . '/../config.php';
        $pdo = new PDO($config['dsn'], $config['user'], $config['pass'], [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    }
    return $pdo;
}

/** Jeton CSRF stocké en session. */
function csrfToken(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrfCheck(): bool
{
    $sent = $_POST['csrf'] ?? '';
    return is_string($sent) && hash_equals(csrfToken(), $sent);
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function pullFlash(): ?array
{
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}

function redirect(string $query): never
{
    header('Location: index.php' . $query);
    exit;
}

function formatDuration(int $seconds): string
{
    return sprintf('%d:%02d', intdiv($seconds, 60), $seconds % 60);
}

/** Rend un template dans le layout. */
function render(string $template, array $vars = [], string $title = 'Deefy'): void
{
    extract($vars, EXTR_SKIP);
    ob_start();
    require __DIR__ . '/../templates/' . $template . '.php';
    $content = ob_get_clean();
    $flash = pullFlash();
    require __DIR__ . '/../templates/layout.php';
}
