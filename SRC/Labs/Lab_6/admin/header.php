<?php
// Beginning of every page of the admin area. The page sets $page_title and $active.
$admin_menu = [
    'dashboard' => ['index.php', 'لوحة التحكم', 'fa-solid fa-gauge'],
    'trips' => ['trips.php', 'الرحلات', 'fa-solid fa-route'],
    'bookings' => ['bookings.php', 'الحجوزات', 'fa-solid fa-calendar-check'],
    'messages' => ['messages.php', 'الرسائل', 'fa-solid fa-envelope'],
];
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
        integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="admin.css">
    <title><?= e($page_title) ?> | إدارة سياحة</title>
</head>

<body>
    <aside class="Admin_Side">
        <h1>سياحة <small>الإدارة</small></h1>
        <nav>
            <?php foreach ($admin_menu as $key => [$url, $label, $icon]): ?>
            <a href="<?= $url ?>" class="<?= $key === $active ? 'Active' : '' ?>"><i class="<?= $icon ?>"></i> <?= $label ?></a>
            <?php endforeach; ?>
            <a href="../index.php"><i class="fa-solid fa-arrow-up-right-from-square"></i> الموقع</a>
        </nav>
    </aside>
    <main class="Admin_Main">
        <p class="Admin_Warning">
            <i class="fa-solid fa-triangle-exclamation"></i>
            لا يوجد تسجيل دخول بعد: أي شخص يعرف هذا العنوان يمكنه تعديل الموقع. يتم حماية هذه الصفحات في المخبر 7 (الجلسات).
        </p>
        <h2 class="Admin_Title"><?= e($page_title) ?></h2>
