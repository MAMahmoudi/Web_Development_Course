<?php
// Secure upload of the photos of the trips (Lab 6).

const UPLOAD_FOLDER = 'uploads/trips/';     // relative to SRC/Labs/Lab_6
const UPLOAD_MAX_BYTES = 2 * 1024 * 1024;   // 2 MB
const UPLOAD_TYPES = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];

/**
 * Checks and stores one uploaded image.
 * Returns [path, error]: [null, null] when no file was chosen, [null, 'message'] when the file is refused,
 * ['uploads/trips/xxx.jpg', null] when it was saved.
 */
function store_uploaded_image(?array $file): array
{
    if ($file === null || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return [null, null];
    }

    // 1. did the upload itself work?
    $error = match ($file['error']) {
        UPLOAD_ERR_OK => null,
        UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'الصورة كبيرة جداً (2 ميغابايت على الأكثر).',
        UPLOAD_ERR_PARTIAL => 'لم يكتمل رفع الصورة، أعيدوا المحاولة.',
        default => 'تعذر رفع الصورة.',
    };
    if ($error !== null) {
        return [null, $error];
    }

    // 2. the size, checked by the server (MAX_FILE_SIZE in the form is only a hint for the browser)
    if ($file['size'] > UPLOAD_MAX_BYTES) {
        return [null, 'الصورة كبيرة جداً (2 ميغابايت على الأكثر).'];
    }

    // 3. the real type, read from the content of the file: the name and the type sent by the browser can lie
    $type = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    if (!isset(UPLOAD_TYPES[$type]) || getimagesize($file['tmp_name']) === false) {
        return [null, 'يجب أن تكون الصورة من نوع JPEG أو PNG أو WebP.'];
    }

    // 4. a new random name: never keep the name chosen by the visitor (../../index.php, photo.php.jpg...)
    $name = bin2hex(random_bytes(16)) . '.' . UPLOAD_TYPES[$type];
    $path = UPLOAD_FOLDER . $name;
    if (!move_uploaded_file($file['tmp_name'], __DIR__ . '/../' . $path)) {
        return [null, 'تعذر حفظ الصورة.'];
    }
    return [$path, null];
}

/** Deletes an uploaded image (the photos of SRC/IMAGES are never deleted). */
function delete_uploaded_image(string $path): void
{
    if (str_starts_with($path, UPLOAD_FOLDER) && basename($path) === substr($path, strlen(UPLOAD_FOLDER))) {
        $file = __DIR__ . '/../' . $path;
        if (is_file($file)) {
            unlink($file);
        }
    }
}
