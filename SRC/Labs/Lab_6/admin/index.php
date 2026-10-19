<?php
require __DIR__ . '/../includes/functions.php';
require __DIR__ . '/../includes/queries.php';

$statistics = get_statistics();

$page_title = 'لوحة التحكم';
$active = 'dashboard';
require __DIR__ . '/header.php';
?>
        <div class="Stats">
            <a href="trips.php" class="Stat">
                <i class="fa-solid fa-route"></i>
                <strong><?= $statistics['trips'] ?></strong>
                <span>رحلات منشورة</span>
            </a>
            <a href="bookings.php" class="Stat">
                <i class="fa-solid fa-hourglass-half"></i>
                <strong><?= $statistics['pending'] ?></strong>
                <span>حجوزات في الانتظار</span>
            </a>
            <a href="messages.php" class="Stat">
                <i class="fa-solid fa-envelope"></i>
                <strong><?= $statistics['unread'] ?></strong>
                <span>رسائل غير مقروءة</span>
            </a>
            <div class="Stat">
                <i class="fa-solid fa-coins"></i>
                <strong><?= money($statistics['revenue']) ?></strong>
                <span>مداخيل الحجوزات المؤكدة</span>
            </div>
        </div>
<?php require __DIR__ . '/footer.php'; ?>
