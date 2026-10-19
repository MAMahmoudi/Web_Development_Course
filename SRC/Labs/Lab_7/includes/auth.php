<?php
// Accounts, protected pages and flash messages (Lab 7). Everything is kept in $_SESSION.
require_once __DIR__ . '/session.php';
require_once __DIR__ . '/functions.php';

const LOGIN_MAX_FAILURES = 5;      // after 5 wrong passwords...
const LOGIN_LOCK_SECONDS = 60;     // ...the form is locked for one minute

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
    unset($_SESSION['login_failures'], $_SESSION['login_locked_until']);
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

// ------------------------------------------------------------------ too many wrong passwords

/** Seconds to wait before the next attempt (0: allowed). */
function login_wait(): int
{
    return max(0, ($_SESSION['login_locked_until'] ?? 0) - time());
}

function login_failed(): void
{
    $_SESSION['login_failures'] = ($_SESSION['login_failures'] ?? 0) + 1;
    if ($_SESSION['login_failures'] >= LOGIN_MAX_FAILURES) {
        $_SESSION['login_locked_until'] = time() + LOGIN_LOCK_SECONDS;
        $_SESSION['login_failures'] = 0;
    }
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
        http_response_code(403);
        $base = dirname($login) === '.' ? '' : dirname($login) . '/';   // '../' from the admin folder
        require __DIR__ . '/forbidden.php';
        exit;
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
