<?php
require __DIR__ . '/includes/functions.php';
require __DIR__ . '/includes/queries.php';

// 1. read the data
$destinations = get_destinations();
$trips = get_trips();
$booking_trips = $trips;

// 2. display the page
$page_title = 'سياحة';
$active = 'home';
$styles = ['Hero', 'Slider', 'Destination', 'Trip', 'Features'];
$responsive = ['Hero', 'Destination', 'Trip', 'Features'];
$scripts = ['Image_Slider', 'Read_More', 'Favourites', 'Booking'];
$return = 'index.php';

require __DIR__ . '/includes/header.php';
?>
    <main>
<?php require __DIR__ . '/includes/hero.php'; ?>
        <!--Destination section starts here-->
        <section class="Destinations" id="Destinations">
            <h1>مختلف الوجهات</h1>
            <?php foreach ($destinations as $index => $destination): ?>
            <!-- one item out of two is reversed: zig-zag layout -->
            <div class="<?= $index % 2 === 0 ? 'Destination_Item' : 'Destination_Item_Reverse' ?>">
                <div class="Destination_Image">
                    <img src="<?= e(image_url($destination['image_1'])) ?>" alt="<?= e($destination['title']) ?>" />
                    <img src="<?= e(image_url($destination['image_2'])) ?>" alt="<?= e($destination['title']) ?>" />
                </div>
                <div class="Destination_Text">
                    <h2><?= e($destination['title']) ?></h2>
                    <p><?= e($destination['description']) ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </section>
        <!--Destination section ends here-->
<?php require __DIR__ . '/includes/trips_section.php'; ?>
<?php require __DIR__ . '/includes/booking_dialog.php'; ?>
<?php require __DIR__ . '/includes/features.php'; ?>
    </main>
<?php require __DIR__ . '/includes/footer.php'; ?>
