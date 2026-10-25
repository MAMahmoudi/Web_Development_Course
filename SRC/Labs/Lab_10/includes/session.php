<?php
// Starts the session of the visitor, with safe settings (Lab 7).
// Included at the top of every page, BEFORE any HTML: session_start() sends a cookie, that is an HTTP header.

const SESSION_IDLE_MINUTES = 30;   // a logged-in visitor who does nothing for 30 minutes is logged out

if (session_status() === PHP_SESSION_NONE) {
    // refuse a session id that this server did not create (protection against session fixation)
    ini_set('session.use_strict_mode', '1');
    session_name('SYAHA_SESSION');
    session_set_cookie_params([
        'lifetime' => 0,                           // the cookie disappears when the browser is closed
        'path' => '/',
        'httponly' => true,                        // JavaScript cannot read it (an XSS cannot steal it)
        'samesite' => 'Lax',                       // not sent with forms posted by other websites
        'secure' => !empty($_SERVER['HTTPS']),     // over HTTPS only, when the site uses HTTPS
    ]);
    session_start();
}

// idle timeout: a session unused for too long is emptied
if (isset($_SESSION['user'], $_SESSION['last_activity'])
    && time() - $_SESSION['last_activity'] > SESSION_IDLE_MINUTES * 60) {
    $_SESSION = [];
    session_regenerate_id(true);
    $_SESSION['flash'][] = ['Error', 'انتهت الجلسة بعد ' . SESSION_IDLE_MINUTES . ' دقيقة من عدم النشاط. سجلوا الدخول من جديد.'];
}
$_SESSION['last_activity'] = time();
