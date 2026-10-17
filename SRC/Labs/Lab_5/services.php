<?php
require __DIR__ . '/includes/functions.php';
require __DIR__ . '/includes/queries.php';

$services = get_services();
$trips = get_trips();

$page_title = 'سياحة | جميع الخدمات';
$active = 'services';
$styles = ['Hero', 'Services', 'Trip'];
$responsive = ['Hero', 'Services', 'Trip'];
$scripts = ['Read_More', 'Favourites', 'Booking'];
$return = 'services.php';

require __DIR__ . '/includes/header.php';
?>
    <main>
        <section id="Hero_ID">
            <div class="Hero">
                <img src="../../IMAGES/Oran.jpg" alt="وهران" />
            </div>
            <div class="Hero_Text">
                <h1>مرحبا بكم في موقعنا</h1>
                <p>نحن نقدم لكم أفضل الخدمات</p>
                <a href="contact_us.php" class="Show" id="Contact_Us_Btn_ID">تواصل معنا</a>
            </div>
        </section>
        <!--Services section starts here-->
        <section class="Services">
            <h1>جميع الخدمات</h1>
            <p class="Services_Intro">نرافقكم في كل مراحل رحلتكم، من الحجز إلى العودة.</p>
            <div class="Services_Grid">
                <?php foreach ($services as $service): ?>
                <div class="Service_Card">
                    <i class="<?= e($service['icon']) ?>"></i>
                    <h3><?= e($service['title']) ?></h3>
                    <p><?= e($service['description']) ?></p>
                    <span class="Service_Price"><?= e($service['price_label']) ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
        <!--Services section ends here-->
<?php require __DIR__ . '/includes/trips_section.php'; ?>
<?php require __DIR__ . '/includes/booking_dialog.php'; ?>
    </main>
<?php require __DIR__ . '/includes/footer.php'; ?>
