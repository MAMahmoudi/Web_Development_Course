<?php
// The trip cards (home and services pages), built from the trips table.
// Expects $trips and $return, and shows the result of a booking (book.php redirects with ?booked=).
?>
        <!--Trip section starts here-->
        <section class="Trip" id="Trips">
            <h1>رحلات</h1>
            <?php if (isset($_GET['booked'])): ?>
            <p class="Flash Flash_Success" role="status">
                تم تسجيل حجزكم رقم <?= (int) $_GET['booked'] ?> بنجاح. سنتصل بكم لتأكيده.
            </p>
            <?php endif; ?>
            <div class="Trip_Cards">
                <?php if (!$trips): ?>
                <p class="Flash Flash_Error">لا توجد رحلات تطابق هذا البحث.</p>
                <?php endif; ?>
                <?php foreach ($trips as $trip): ?>
                <div class="Trip_Card">
                    <div class="Trip_Image">
                        <img src="<?= e(image_url($trip['image'])) ?>" alt="<?= e($trip['title']) ?>" />
                    </div>
                    <h4><?= e($trip['title']) ?></h4>
                    <p class="Trip_Facts">
                        <span><i class="fa-solid fa-clock"></i> <?= (int) $trip['duration_days'] ?> يوم</span>
                        <span><i class="fa-solid fa-tag"></i> <?= money((int) $trip['price']) ?> للشخص</span>
                    </p>
                    <p><?= e($trip['description']) ?></p>
                    <!-- a real link: it works without JavaScript; with JavaScript it opens the dialog (Booking.js) -->
                    <a href="book.php?trip=<?= (int) $trip['id'] ?>&amp;return=<?= e($return) ?>" class="Book_Btn">
                        <i class="fa-solid fa-calendar-check"></i> احجز الآن
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
        <!--Trip section ends here-->
