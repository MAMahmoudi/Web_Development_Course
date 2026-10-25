<?php
// Security settings shared by every page (Lab 9): errors, HTTP headers and CSRF tokens.
// Included by auth.php, so by every page, before any HTML: headers must be sent before the page.
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';

// ------------------------------------------------------------------ errors

// a visitor never sees a PHP error: it may show paths, SQL, or the name of the database account
ini_set('display_errors', APP_DEBUG ? '1' : '0');
ini_set('log_errors', '1');            // C:\xampp\php\logs\php_error_log, or the log of the server
error_reporting(E_ALL);

/** The folder of the pages seen from the current page: '' for the public pages, '../' from the admin folder. */
function site_base(): string
{
    return basename(dirname($_SERVER['SCRIPT_FILENAME'])) === 'admin' ? '../' : '';
}

/** Stops the page and shows an error page (403, 500...) with the style of the website. */
function show_error_page(int $code, string $title, string $text): never
{
    http_response_code($code);
    $base = site_base();
    require __DIR__ . '/error_page.php';
    exit;
}

// an exception that nobody caught (database down, bug...): logged with its details, a short message for the visitor
set_exception_handler(function (Throwable $error): void {
    error_log(get_class($error) . ': ' . $error->getMessage() . ' in ' . $error->getFile() . ':' . $error->getLine());
    show_error_page(500, 'حدث خطأ في الخادم',
        APP_DEBUG ? get_class($error) . ': ' . $error->getMessage()   // details on your computer only
                  : 'نعتذر عن هذا الخطأ. أعيدوا المحاولة بعد قليل.');
});

// ------------------------------------------------------------------ HTTP security headers

header_remove('X-Powered-By');                                   // do not tell which PHP version runs the site
header("Content-Security-Policy: default-src 'self'; "
    . "script-src 'self'; "                                         // no inline script, no script of another site
    . "style-src 'self' https://fonts.googleapis.com https://cdnjs.cloudflare.com; "
    . "font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com; "
    . "img-src 'self' data:; "
    . "frame-src https://www.openstreetmap.org; "                   // the map of the contact page
    . "frame-ancestors 'none'; "                                    // no other site can show Syaha in a frame
    . "form-action 'self'; base-uri 'self'; object-src 'none'");
header('X-Content-Type-Options: nosniff');                        // a file is only used as its declared type
header('X-Frame-Options: DENY');                                  // frame-ancestors, for older browsers
header('Referrer-Policy: strict-origin-when-cross-origin');       // other sites do not see our URLs
header('Permissions-Policy: camera=(), microphone=(), geolocation=()');

// ------------------------------------------------------------------ CSRF tokens

require_once __DIR__ . '/session.php';

/** The secret token of the session: random, unknown to other websites. */
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/** The hidden field to put in EVERY form sent with POST. */
function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '" />';
}

// every POST request is checked here, before the page runs: a form posted by another website has no token
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sent = $_POST['csrf_token'] ?? '';
    if (!is_string($sent) || !hash_equals(csrf_token(), $sent)) {   // hash_equals: a comparison in constant time
        error_log('CSRF check failed for ' . $_SERVER['REQUEST_URI']);
        $_SESSION['flash'][] = ['Error', 'انتهت صلاحية النموذج أو لم يُرسل من موقعنا. أعيدوا المحاولة.'];
        header('Location: ' . $_SERVER['REQUEST_URI'], true, 303);  // the same page, with GET: nothing is changed
        exit;
    }
}
