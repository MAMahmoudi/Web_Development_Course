<?php
// Page shown by require_login() to a logged-in visitor without the right role (403 Forbidden).
// It can be shown from the admin folder: every address starts with $base ('' or '../').
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
    <title>سياحة | غير مسموح</title>
</head>

<body>
    <main>
        <section class="Not_Found">
            <span class="Not_Found_Code">403</span>
            <h1>ليست لديكم صلاحية الوصول إلى هذه الصفحة</h1>
            <p>أنتم مسجلون باسم <?= e($user['full_name']) ?>، وهذه الصفحة مخصصة للإدارة.</p>
            <a href="<?= $base ?>index.php" class="Not_Found_Btn">العودة إلى الرئيسية</a>
        </section>
    </main>
</body>

</html>
