<?php
// Copy this file to config.local.php (ignored by Git) and write the settings of THIS computer or server.
// Only the values that differ from the defaults of config.php are needed.
return [
    'DB_HOST' => '127.0.0.1',
    'DB_PORT' => 3306,
    'DB_NAME' => 'syaha',
    'DB_USER' => 'syaha_app',          // on a server: a MySQL account for the site only, never root
    'DB_PASS' => 'a long random password',
    'APP_DEBUG' => true,               // your computer only: show the PHP errors
];
