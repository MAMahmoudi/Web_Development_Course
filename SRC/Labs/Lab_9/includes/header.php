<?php
// Beginning of every public page: <head>, navigation bar and flash messages.
// The page sets before including this file:
//   $page_title   text of <title>
//   $active       key of the current page in the menu ('home', 'services', 'about', 'contact', 'account' or '')
//   $styles       stylesheets of its components (in Lab_2) and $responsive (in Lab_3)

$user = current_user();

// the first item depends on the visitor (Lab 7): login, or their own page
$menu = $user === null
    ? ['login' => ['login.php', 'تسجيل الدخول', 'fa-solid fa-right-to-bracket']]
    : ['account' => is_admin()
        ? ['admin/index.php', 'الإدارة', 'fa-solid fa-gauge']
        : ['my_bookings.php', 'حجوزاتي', 'fa-solid fa-suitcase-rolling']];
$menu += [
    'contact' => ['contact_us.php', 'إتصل بنا', 'fa-solid fa-envelope'],
    'services' => ['services.php', 'جميع الخدمات', 'fa-solid fa-briefcase'],
    'about' => ['about_us.php', 'من نحن؟', 'fa-solid fa-circle-info'],
    'home' => ['index.php', 'الرئيسية', 'fa-solid fa-house'],
];
?>
<!DOCTYPE html>
<html lang="ar">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php foreach (['Nav_Bar', 'Lab_2_Page', ...$styles, 'Lab_2_Footer'] as $style): ?>
    <link rel="stylesheet" href="../Lab_2/<?= $style ?>.css">
    <?php endforeach; ?>
    <!-- responsive design (Lab 3) -->
    <?php foreach (['Nav_Bar', ...$responsive, 'Footer'] as $style): ?>
    <link rel="stylesheet" href="../Lab_3/<?= $style ?>_Responsive.css">
    <?php endforeach; ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
        integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- JavaScript (Lab 4) and back end (Labs 5 to 9) -->
    <link rel="stylesheet" href="../Lab_4+/Interactions.css">
    <link rel="stylesheet" href="backend.css">
    <title><?= e($page_title) ?></title>
</head>

<body>
    <header>
        <nav class="Nav_Bar_Items">
            <h1 class="Nav_Bar_Logo">سياحة</h1>
            <div class="Menu_Icon" id="Menu_Icon_ID">
                <i class="fas fa-bars" id="Burger_Icon_ID"></i>
            </div>
            <ul class="Nav_Bar_Menu" id="Nav_Bar_Menu_ID">
                <?php if ($user !== null): ?>
                <li>
                    <!-- logging out changes the session: a POST form, not a link -->
                    <form action="logout.php" method="post" class="Logout_Form">
                        <?= csrf_field() ?>
                        <button type="submit" class="Nav_Bar_Menu_Item" title="<?= e($user['full_name']) ?>">
                            <span>خروج</span>
                            <i class="fa-solid fa-right-from-bracket"></i>
                        </button>
                    </form>
                </li>
                <?php endif; ?>
                <?php foreach ($menu as $key => [$url, $label, $icon]): ?>
                <li>
                    <a href="<?= $url ?>" class="Nav_Bar_Menu_Item<?= $key === $active ? ' Active' : '' ?>">
                        <span><?= $label ?></span>
                        <i class="<?= $icon ?>"></i>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
        </nav>
    </header>
    <?php $flashes = take_flashes(); ?>
    <?php if ($flashes): ?>
    <!-- messages kept in the session by the previous page (Lab 7) -->
    <div class="Flash_Stack" role="status">
        <?php foreach ($flashes as [$type, $message]): ?>
        <p class="Flash Flash_<?= e($type) ?>"><?= e($message) ?></p>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
