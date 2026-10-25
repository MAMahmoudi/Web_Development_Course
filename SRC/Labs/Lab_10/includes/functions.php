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

/**
 * URL of an image stored in the database.
 * Photos of the demo are in SRC/IMAGES ("Lab_2/City3.jpg"); uploaded photos are in this folder ("uploads/trips/...").
 * $prefix is "../" from the admin folder.
 */
function image_url(string $path, string $prefix = ''): string
{
    return $prefix . (str_starts_with($path, 'uploads/') ? '' : IMAGES_URL) . $path;
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

/**
 * A text sent by a form ($_POST or $_GET), trimmed.
 * A field can be sent as an array (name="email[]"): it is refused, otherwise trim() would fail.
 */
function input(string $name, string $method = 'post'): string
{
    $value = ($method === 'get' ? $_GET : $_POST)[$name] ?? '';
    return is_string($value) ? trim($value) : '';
}

// ------------------------------------------------------------------ displaying the errors of a form

/** The message of a field, under the field (the class is the one used by the JavaScript of Lab 4). */
function field_error(array $errors, string $field, string $id): string
{
    if (!isset($errors[$field])) {
        return '';
    }
    return '<small class="Field_Error" id="' . $id . '_Error" aria-live="polite">' . e($errors[$field]) . '</small>';
}

/** Attributes of an invalid field: red border and link to its message for screen readers. */
function invalid_attributes(array $errors, string $field, string $id): string
{
    return isset($errors[$field]) ? ' class="Invalid" aria-invalid="true" aria-describedby="' . $id . '_Error"' : '';
}
