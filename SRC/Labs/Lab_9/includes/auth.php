<?php
// Accounts, protected pages and flash messages (Lab 7), hardened in Lab 9.
require_once __DIR__ . '/security.php';   // errors, headers, session and CSRF check (Lab 9)
require_once __DIR__ . '/db.php';

// too many wrong passwords (Lab 9): counted in the database, so deleting the cookie does not reset them
const LOGIN_WINDOW_MINUTES = 15;
const LOGIN_MAX_FAILURES = 5;      // wrong passwords for one e-mail in 15 minutes
const LOGIN_MAX_FAILURES_IP = 20;  // wrong passwords from one IP address in 15 minutes (any e-mail)

// ------------------------------------------------------------------ who is logged in?

/** The logged-in user (id, full_name, email, role), or null for a visitor. */
function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function is_admin(): bool
{
    return (current_user()['role'] ?? '') === 'admin';
}

/** Stores the user in the session after a successful login or registration. */
function login_user(array $user): void
{
    // a new session id: an id known before the login (given by an attacker) becomes useless
    session_regenerate_id(true);
    $_SESSION['user'] = [
        'id' => (int) $user['id'],
        'full_name' => $user['full_name'],
        'email' => $user['email'],
        'role' => $user['role'],
    ];
}

/** Destroys the session: data on the server and cookie in the browser. */
function logout_user(): void
{
    $_SESSION = [];
    $cookie = session_get_cookie_params();
    setcookie(session_name(), '', [
        'expires' => time() - 3600,     // a date in the past deletes the cookie
        'path' => $cookie['path'],
        'httponly' => $cookie['httponly'],
        'samesite' => $cookie['samesite'],
        'secure' => $cookie['secure'],
    ]);
    session_destroy();
    session_start();                    // a new, empty session for the flash message
    session_regenerate_id(true);
}

// ------------------------------------------------------------------ too many wrong passwords (Lab 9)

/** True when this e-mail, or the IP address of the visitor, failed too often in the last 15 minutes. */
function login_blocked(string $email): bool
{
    $since = 'attempted_at > NOW() - INTERVAL ' . LOGIN_WINDOW_MINUTES . ' MINUTE';
    $by_email = db()->prepare("SELECT COUNT(*) FROM login_attempts WHERE email = ? AND $since");
    $by_email->execute([mb_strtolower($email)]);
    $by_ip = db()->prepare("SELECT COUNT(*) FROM login_attempts WHERE ip = ? AND $since");
    $by_ip->execute([$_SERVER['REMOTE_ADDR']]);
    return $by_email->fetchColumn() >= LOGIN_MAX_FAILURES || $by_ip->fetchColumn() >= LOGIN_MAX_FAILURES_IP;
}

/** Records a wrong password (and forgets the attempts older than one day). */
function login_failed(string $email): void
{
    db()->prepare('INSERT INTO login_attempts (email, ip) VALUES (?, ?)')
        ->execute([mb_strtolower($email), $_SERVER['REMOTE_ADDR']]);
    db()->exec('DELETE FROM login_attempts WHERE attempted_at < NOW() - INTERVAL 1 DAY');
}

/** A successful login clears the failures of the account. */
function login_succeeded(string $email): void
{
    db()->prepare('DELETE FROM login_attempts WHERE email = ?')->execute([mb_strtolower($email)]);
}

// ------------------------------------------------------------------ protected pages

/**
 * Stops the page if the visitor is not logged in (or does not have the role).
 * $login is the URL of login.php seen from the page ("../login.php" from the admin folder).
 */
function require_login(?string $role = null, string $login = 'login.php'): void
{
    $user = current_user();
    if ($user === null) {
        flash('Error', 'سجلوا الدخول للوصول إلى هذه الصفحة.');
        redirect($login . '?next=' . urlencode($_SERVER['REQUEST_URI']));
    }
    if ($role !== null && $user['role'] !== $role) {
        // logged in, but not allowed: 403 Forbidden
        show_error_page(403, 'ليست لديكم صلاحية الوصول إلى هذه الصفحة',
            'أنتم مسجلون باسم ' . $user['full_name'] . '، وهذه الصفحة مخصصة للإدارة.');
    }
}

/**
 * The address to go to after the login: only a page of this website.
 * "next" comes from the URL: an attacker could put https://evil.example in it.
 */
function safe_next(string $next, string $default): string
{
    $path = parse_url($next, PHP_URL_PATH) ?? '';
    $dir = dirname($_SERVER['SCRIPT_NAME']);   // the folder of Lab_7, e.g. /SRC/Labs/Lab_7
    if (str_starts_with($next, $dir . '/') && !str_contains($next, '..') && !str_contains($next, '//')
        && preg_match('#\.php$#', $path)) {
        return $next;
    }
    return $default;
}

// ------------------------------------------------------------------ flash messages

/** A message kept in the session until the next page displays it (after a redirect). */
function flash(string $type, string $message): void
{
    $_SESSION['flash'][] = [$type, $message];
}

/** The messages waiting, which are removed from the session: they are shown only once. */
function take_flashes(): array
{
    $messages = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $messages;
}
