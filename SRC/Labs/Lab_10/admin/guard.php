<?php
// Included at the very top of EVERY page of the admin area (Lab 7), before anything is read or changed:
// a visitor who is not logged in as an admin never reaches the code of the page.
require_once __DIR__ . '/../includes/auth.php';

require_login('admin', '../login.php');
