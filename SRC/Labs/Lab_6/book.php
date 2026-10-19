<?php
// The booking page.
//   GET  book.php?trip=3          shows the form (visitors without JavaScript, or a link shared)
//   POST book.php                 the dialog or the form was sent: validate, then save and redirect,
//                                 or show the form again with the errors under the fields
require __DIR__ . '/includes/functions.php';
require __DIR__ . '/includes/queries.php';
require __DIR__ . '/includes/validation.php';

$trips = get_trips();
$trip_ids = array_map(fn ($trip) => (string) $trip['id'], $trips);

$rules = [
    'trip_id' => ['required', 'in:' . implode(',', $trip_ids)],
    'name' => ['required', 'min:3', 'max:100'],
    'email' => ['required', 'email', 'max:255'],
    'date' => ['required', 'date:' . BOOKING_MIN_DAYS],
    'people' => ['required', 'int:1,10'],
];

// only come back to one of our own pages
$return = input('return', $_SERVER['REQUEST_METHOD'] === 'POST' ? 'post' : 'get');
$return = in_array($return, ['index.php', 'services.php'], true) ? $return : 'index.php';

$values = array_fill_keys(array_keys($rules), '');
$values['trip_id'] = input('trip', 'get');
$values['people'] = '1';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($rules as $field => $field_rules) {
        $values[$field] = input($field);
    }
    $errors = validate($values, $rules);

    if (!$errors) {
        $id = create_booking([
            'trip_id' => (int) $values['trip_id'],
            'full_name' => $values['name'],
            'email' => $values['email'],
            'departure_date' => $values['date'],
            'people' => (int) $values['people'],
        ]);
        if ($id !== null) {
            redirect($return . '?booked=' . $id . '#Trips');
        }
        // the trip was unpublished between the display of the form and its sending
        $errors['trip_id'] = 'هذه الرحلة لم تعد متاحة.';
    }
    http_response_code(422);
}

$earliest = (new DateTime('today +' . BOOKING_MIN_DAYS . ' days'))->format('Y-m-d');

$page_title = 'سياحة | احجز رحلتك';
$active = '';
$styles = [];
$responsive = [];
$scripts = [];

require __DIR__ . '/includes/header.php';
?>
    <main class="Booking_Page">
        <form class="Booking_Form" action="book.php" method="post">
            <h2>احجز رحلتك</h2>
            <?php if ($errors): ?>
            <p class="Flash Flash_Error" role="alert">لم يتم تسجيل الحجز: صححوا الحقول المشار إليها.</p>
            <?php endif; ?>
            <input type="hidden" name="return" value="<?= e($return) ?>" />

            <label for="Booking_Trip">الرحلة</label>
            <select id="Booking_Trip" name="trip_id" required<?= invalid_attributes($errors, 'trip_id', 'Booking_Trip') ?>>
                <option value="">اختر رحلة</option>
                <?php foreach ($trips as $trip): ?>
                <option value="<?= (int) $trip['id'] ?>" <?= (string) $trip['id'] === $values['trip_id'] ? 'selected' : '' ?>>
                    <?= e($trip['title']) ?> (<?= money((int) $trip['price']) ?> للشخص)
                </option>
                <?php endforeach; ?>
            </select>
            <?= field_error($errors, 'trip_id', 'Booking_Trip') ?>

            <label for="Booking_Name">الإسم الكامل</label>
            <input type="text" id="Booking_Name" name="name" value="<?= e($values['name']) ?>" autocomplete="name"
                required<?= invalid_attributes($errors, 'name', 'Booking_Name') ?> />
            <?= field_error($errors, 'name', 'Booking_Name') ?>

            <label for="Booking_Email">البريد الإلكتروني</label>
            <input type="email" id="Booking_Email" name="email" value="<?= e($values['email']) ?>" autocomplete="email"
                required<?= invalid_attributes($errors, 'email', 'Booking_Email') ?> />
            <?= field_error($errors, 'email', 'Booking_Email') ?>

            <label for="Booking_Date">تاريخ الانطلاق</label>
            <input type="date" id="Booking_Date" name="date" value="<?= e($values['date']) ?>" min="<?= $earliest ?>"
                required<?= invalid_attributes($errors, 'date', 'Booking_Date') ?> />
            <?= field_error($errors, 'date', 'Booking_Date') ?>

            <label for="Booking_People">عدد المسافرين</label>
            <input type="number" id="Booking_People" name="people" value="<?= e($values['people']) ?>" min="1" max="10"
                required<?= invalid_attributes($errors, 'people', 'Booking_People') ?> />
            <?= field_error($errors, 'people', 'Booking_People') ?>

            <div class="Booking_Buttons">
                <button type="submit" class="Booking_Confirm">تأكيد الحجز</button>
                <a href="<?= e($return) ?>#Trips" class="Booking_Cancel">إلغاء</a>
            </div>
        </form>
    </main>
<?php require __DIR__ . '/includes/footer.php'; ?>
