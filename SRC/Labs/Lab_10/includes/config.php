<?php
// Settings of the Syaha website.
// The secrets (database password...) are NOT written here: this file is in Git, and everyone who can read the
// repository could read them. Each computer or server has its own config.local.php, ignored by Git (Lab 9):
//   copy config.local.example.php to config.local.php and write the settings of this computer in it.

$local = is_file(__DIR__ . '/config.local.php') ? require __DIR__ . '/config.local.php' : [];

// the default values are the ones of XAMPP: they only work on your own computer
define('DB_HOST', $local['DB_HOST'] ?? '127.0.0.1');
define('DB_PORT', $local['DB_PORT'] ?? 3306);
define('DB_NAME', $local['DB_NAME'] ?? 'syaha');
define('DB_USER', $local['DB_USER'] ?? 'root');
define('DB_PASS', $local['DB_PASS'] ?? '');

// true on your computer: PHP errors are shown. ALWAYS false on a server: errors go to the log only.
define('APP_DEBUG', $local['APP_DEBUG'] ?? false);

unset($local);

// the images of the site are in SRC/IMAGES, two folders above the pages of the lab
const IMAGES_URL = '../../IMAGES/';

// a booking must be made at least this many days before departure
const BOOKING_MIN_DAYS = 3;
