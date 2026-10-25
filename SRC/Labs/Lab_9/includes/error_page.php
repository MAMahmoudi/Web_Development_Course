<?php
// Error page of the website (403, 500...), shown by show_error_page() in security.php.
// Expects $code, $title, $text, and $base ('' or '../' from the admin folder) for the addresses.
?>
<!DOCTYPE html>
<html lang="ar">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= $base ?>../Lab_2/Lab_2_Page.css">
    <link rel="stylesheet" href="<?= $base ?>../Lab_2/NotFound.css">
    <link rel="stylesheet" href="<?= $base ?>../Lab_3/NotFound_Responsive.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap">
    <title>سياحة | <?= e($title) ?></title>
</head>

<body>
    <main>
        <section class="Not_Found">
            <span class="Not_Found_Code"><?= (int) $code ?></span>
            <h1><?= e($title) ?></h1>
            <p><?= e($text) ?></p>
            <a href="<?= $base ?>index.php" class="Not_Found_Btn">العودة إلى الرئيسية</a>
        </section>
    </main>
</body>

</html>
