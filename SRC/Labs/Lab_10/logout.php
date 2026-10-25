<?php
// Logout (Lab 7). POST only: a link (GET) could be triggered by an image or a page of another website.
require __DIR__ . '/includes/functions.php';
require __DIR__ . '/includes/auth.php';

require_post('index.php');

logout_user();
flash('Success', 'تم تسجيل الخروج.');
redirect('index.php');
