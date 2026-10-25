<?php
// End-to-end "smoke test" of Syaha (Lab 10): requests the pages like a browser and checks the answers.
// Run it in a terminal while Apache and MySQL are started, with the address of the site:
//   php tools/smoke_test.php http://localhost/<project folder>/SRC/Labs/Lab_10/
// It only reads pages and sends forms WITHOUT their token: it changes nothing in the database.
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}
if ($argc < 2) {
    exit('Usage: php tools/smoke_test.php http://localhost/<project folder>/SRC/Labs/Lab_10/' . PHP_EOL);
}

$base = rtrim($argv[1], '/') . '/';
$passed = $failed = 0;

/** One HTTP request (no redirect followed): returns [status, headers, body]. */
function request(string $url, ?array $post = null): array
{
    $curl = curl_init($url);
    curl_setopt_array($curl, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER => true,
        CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_TIMEOUT => 10,
    ]);
    if ($post !== null) {
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post));
    }
    $response = curl_exec($curl);
    if ($response === false) {
        exit('Cannot reach ' . $url . ': ' . curl_error($curl) . PHP_EOL);
    }
    $size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
    $status = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
    curl_close($curl);
    return [$status, strtolower(substr($response, 0, $size)), substr($response, $size)];
}

function check(string $name, bool $ok): void
{
    global $passed, $failed;
    $ok ? $passed++ : $failed++;
    echo ($ok ? '[OK]   ' : '[FAIL] ') . $name . PHP_EOL;
}

// public pages
foreach (['index.php', 'services.php', 'about_us.php', 'contact_us.php', 'login.php', 'register.php', 'book.php?trip=1'] as $page) {
    [$status, , $body] = request($base . $page);
    check("$page answers 200", $status === 200 && str_contains($body, '</html>'));
}
[$status] = request($base . 'services.php?max_price=20000&sort=price_asc');
check('search with GET', $status === 200);
[$status] = request($base . '404.php');
check('404.php answers 404', $status === 404);

// security headers
[, $headers] = request($base . 'index.php');
foreach (['content-security-policy', 'x-content-type-options', 'x-frame-options', 'referrer-policy'] as $header) {
    check("header $header", str_contains($headers, $header . ':'));
}
check('session cookie HttpOnly and SameSite', str_contains($headers, 'httponly') && str_contains($headers, 'samesite=lax'));
check('no X-Powered-By', !str_contains($headers, 'x-powered-by:'));

// protected pages
foreach (['my_bookings.php', 'admin/index.php', 'admin/trips.php', 'admin/bookings.php', 'admin/messages.php'] as $page) {
    [$status, $headers] = request($base . $page);
    check("$page redirects a visitor to the login", $status === 302 && str_contains($headers, 'location: ') && str_contains($headers, 'login.php?next='));
}

// forms posted without their CSRF token: refused (303 to the same page), nothing changed
foreach (['contact_us.php', 'book.php', 'login.php', 'admin/trip_delete.php'] as $page) {
    [$status] = request($base . $page, ['name' => 'smoke test', 'email' => 'test@example.com', 'id' => '1']);
    check("POST $page without token is refused", $status === 303);
}

// files that must never be served (Apache with .htaccess)
foreach (['includes/config.php', 'database/syaha.sql', 'tools/check_install.php'] as $file) {
    [$status] = request($base . $file);
    check("$file is not served", $status === 403 || $status === 404);
}

echo PHP_EOL . "$passed passed, $failed failed." . PHP_EOL;
exit($failed ? 1 : 0);
