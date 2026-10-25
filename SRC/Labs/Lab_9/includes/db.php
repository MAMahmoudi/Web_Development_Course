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
        // a failed connection throws a PDOException: the handler of security.php logs its details
        // (host, account...) and shows the visitor a short error page (Lab 9)
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,       // errors become exceptions
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,  // rows as ['column' => value]
            PDO::ATTR_EMULATE_PREPARES => false,               // real prepared statements
        ]);
    }

    return $pdo;
}
