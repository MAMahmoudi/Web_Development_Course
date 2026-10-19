<?php
// The booking dialog of Lab 4, now sent to book.php.
// The options come from $booking_trips (all the published trips); $return is the page to come back to.
?>
        <!--Booking dialog starts here-->
        <dialog class="Booking_Dialog" id="Booking_Dialog" aria-labelledby="Booking_Title">
            <form class="Booking_Form" id="Booking_Form" action="book.php" method="post">
                <h2 id="Booking_Title">احجز رحلتك</h2>
                <input type="hidden" name="return" value="<?= e($return) ?>" />
                <label for="Booking_Trip">الرحلة</label>
                <select id="Booking_Trip" name="trip_id" required>
                    <option value="">اختر رحلة</option>
                    <?php foreach ($booking_trips as $trip): ?>
                    <option value="<?= (int) $trip['id'] ?>" data-price="<?= (int) $trip['price'] ?>">
                        <?= e($trip['title']) ?> (<?= money((int) $trip['price']) ?> للشخص)
                    </option>
                    <?php endforeach; ?>
                </select>
                <label for="Booking_Name">الإسم الكامل</label>
                <input type="text" id="Booking_Name" name="name" minlength="3" maxlength="100" autocomplete="name" required />
                <label for="Booking_Email">البريد الإلكتروني</label>
                <input type="email" id="Booking_Email" name="email" maxlength="255" autocomplete="email" required />
                <label for="Booking_Date">تاريخ الانطلاق</label>
                <input type="date" id="Booking_Date" name="date" required />
                <label for="Booking_People">عدد المسافرين</label>
                <input type="number" id="Booking_People" name="people" min="1" max="10" value="1" required />
                <p class="Booking_Total">المجموع: <output id="Booking_Total" for="Booking_Trip Booking_People">0 دج</output></p>
                <div class="Booking_Buttons">
                    <button type="submit" class="Booking_Confirm">تأكيد الحجز</button>
                    <button type="button" class="Booking_Cancel" id="Booking_Cancel">إلغاء</button>
                </div>
                <p class="Booking_Message" id="Booking_Message" role="status"></p>
            </form>
        </dialog>
        <!--Booking dialog ends here-->
