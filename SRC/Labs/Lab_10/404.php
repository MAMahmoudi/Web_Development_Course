<?php
require __DIR__ . '/includes/functions.php';
require __DIR__ . '/includes/queries.php';
require __DIR__ . '/includes/auth.php';

// the status code tells browsers and search engines that the page does not exist
http_response_code(404);

$page_title = 'سياحة | الصفحة غير موجودة';
$active = '';
$styles = ['NotFound'];
$responsive = ['NotFound'];
$scripts = [];

require __DIR__ . '/includes/header.php';
?>
    <main>
        <section class="Not_Found">
            <span class="Not_Found_Code">404</span>
            <h1>لم يتم العثور على هذه الصفحة</h1>
            <p>ربما تم نقل الصفحة أو حذفها، أو أن الرابط غير صحيح.</p>
            <a href="index.php" class="Not_Found_Btn">العودة إلى الرئيسية</a>
        </section>
    </main>
<?php require __DIR__ . '/includes/footer.php'; ?>
