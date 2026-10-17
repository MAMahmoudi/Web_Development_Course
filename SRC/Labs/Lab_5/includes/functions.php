<?php
require_once __DIR__ . '/config.php';

/** Escapes a value before writing it in the HTML: protects the page against XSS. */
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/** 15000 -> "15 000 دج" */
function money(int $amount): string
{
    return number_format($amount, 0, ',', ' ') . ' دج';
}

/** URL of an image stored in the database (path inside SRC/IMAGES). $prefix is "../" from the admin folder. */
function image_url(string $path, string $prefix = ''): string
{
    return $prefix . IMAGES_URL . $path;
}

/** Sends the browser to another page and stops the script. */
function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

/** Only accepts POST requests (forms that change data). */
function require_post(string $fallback): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirect($fallback);
    }
}
