<?php
// The bookings of the logged-in customer (Lab 7): a page that only exists for a logged-in visitor.
require __DIR__ . '/includes/functions.php';
require __DIR__ . '/includes/queries.php';
require __DIR__ . '/includes/auth.php';

require_login();
$user = current_user();

// the id comes from the session, never from the URL: a visitor cannot see the bookings of someone else
$bookings = get_user_bookings($user['id']);
$statuses = ['pending' => 'في الانتظار', 'confirmed' => 'مؤكد', 'cancelled' => 'ملغى'];

$page_title = 'سياحة | حجوزاتي';
$active = 'account';
$styles = [];
$responsive = [];
$scripts = [];

require __DIR__ . '/includes/header.php';
?>
    <main class="Account_Page">
        <section class="Account">
            <h1>حجوزاتي</h1>
            <p class="Account_Who">
                <i class="fa-solid fa-user"></i> <?= e($user['full_name']) ?> &ndash; <?= e($user['email']) ?>
            </p>
            <?php if (!$bookings): ?>
            <p class="Flash Flash_Info">لا توجد حجوزات بعد. <a href="services.php#Trips">اكتشفوا رحلاتنا</a>.</p>
            <?php else: ?>
            <div class="Account_Bookings">
                <?php foreach ($bookings as $booking): ?>
                <article class="Account_Booking">
                    <img src="<?= e(image_url($booking['trip_image'])) ?>" alt="<?= e($booking['trip_title']) ?>" />
                    <div>
                        <h2><?= e($booking['trip_title']) ?></h2>
                        <p><i class="fa-solid fa-calendar"></i> <?= e($booking['departure_date']) ?></p>
                        <p><i class="fa-solid fa-users"></i> <?= (int) $booking['people'] ?> مسافر(ين) &ndash; <?= money((int) $booking['total_price']) ?></p>
                        <span class="Status Status_<?= e($booking['status']) ?>"><?= e($statuses[$booking['status']] ?? $booking['status']) ?></span>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            <p><a href="services.php#Trips" class="Account_Link"><i class="fa-solid fa-plus"></i> حجز رحلة جديدة</a></p>
        </section>
    </main>
<?php require __DIR__ . '/includes/footer.php'; ?>
