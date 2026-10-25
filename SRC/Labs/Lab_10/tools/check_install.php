<?php
// Checks that this computer (or server) can run Syaha (Lab 10).
// Run it in a terminal, from the folder of the project:   php tools/check_install.php
// It is not a page of the website: tools/.htaccess refuses it to the browsers.
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

$root = dirname(__DIR__);
$failures = 0;

function report(string $status, string $message): void
{
    global $failures;
    if ($status === 'FAIL') {
        $failures++;
    }
    echo str_pad("[$status]", 7) . $message . PHP_EOL;
}

// ------------------------------------------------------------------ PHP
version_compare(PHP_VERSION, '8.1.0', '>=')
    ? report('OK', 'PHP ' . PHP_VERSION)
    : report('FAIL', 'PHP ' . PHP_VERSION . ': Syaha needs PHP 8.1 or newer');

foreach (['pdo_mysql' => 'the database', 'mbstring' => 'Arabic texts', 'fileinfo' => 'the uploads'] as $extension => $use) {
    extension_loaded($extension)
        ? report('OK', "extension $extension")
        : report('FAIL', "extension $extension is missing ($use): enable it in php.ini");
}

// ------------------------------------------------------------------ settings
require $root . '/includes/config.php';

is_file($root . '/includes/config.local.php')
    ? report('OK', 'includes/config.local.php found')
    : report('WARN', 'no includes/config.local.php: the XAMPP defaults are used (root, no password)');
APP_DEBUG
    ? report('WARN', 'APP_DEBUG is true: never on a public server')
    : report('OK', 'APP_DEBUG is false: errors are logged, not shown');
DB_USER === 'root'
    ? report('WARN', 'the database account is root: create an account for the site on a server')
    : report('OK', 'database account: ' . DB_USER);

// ------------------------------------------------------------------ database
try {
    $pdo = new PDO('mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER, DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    report('OK', 'connected to the database ' . DB_NAME . ' on ' . DB_HOST . ':' . DB_PORT);

    // the tables expected: the ones created by database/syaha.sql
    preg_match_all('/CREATE TABLE (\w+)/', file_get_contents($root . '/database/syaha.sql'), $matches);
    $existing = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
    $missing = array_diff($matches[1], $existing);
    $missing
        ? report('FAIL', 'missing tables: ' . implode(', ', $missing) . ' (import database/syaha.sql)')
        : report('OK', count($matches[1]) . ' tables');

    if (!$missing) {
        // the demo password of the admin must be changed before the site is public
        $hash = $pdo->query("SELECT password_hash FROM users WHERE email = 'admin@syaha.example'")->fetchColumn();
        $hash && password_verify('Syaha_Admin1', $hash)
            ? report('WARN', 'the admin account still has its demo password')
            : report('OK', 'no demo password for the admin account');
    }
} catch (PDOException $e) {
    report('FAIL', 'database: ' . $e->getMessage());
}

// ------------------------------------------------------------------ files
is_writable($root . '/uploads/trips')
    ? report('OK', 'uploads/trips is writable')
    : report('FAIL', 'uploads/trips is not writable: the photos of the trips cannot be uploaded');
foreach (['uploads/trips/.htaccess', 'includes/.htaccess', 'database/.htaccess', 'tools/.htaccess'] as $file) {
    is_file($root . '/' . $file)
        ? report('OK', "$file protects its folder")
        : report('FAIL', "$file is missing");
}

echo PHP_EOL . ($failures ? "$failures problem(s) to fix." : 'Ready.') . PHP_EOL;
exit($failures ? 1 : 0);
