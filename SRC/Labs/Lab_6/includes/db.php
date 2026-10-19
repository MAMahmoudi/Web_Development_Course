<?php
require_once __DIR__ . '/config.php';

/**
 * Returns the connection to the database, created the first time it is needed.
 * Every query of a page then reuses the same connection.
 */
function db(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,       // errors become exceptions
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,  // rows as ['column' => value]
                PDO::ATTR_EMULATE_PREPARES => false,               // real prepared statements
            ]);
        } catch (PDOException $e) {
            // the details (host, user...) go to the error log, not to the visitor
            error_log('Database connection failed: ' . $e->getMessage());
            http_response_code(500);
            exit('<p style="font-family: sans-serif; padding: 2rem">The database is not available. '
                . 'Is MySQL started in XAMPP, and was database/syaha.sql imported?</p>');
        }
    }

    return $pdo;
}
