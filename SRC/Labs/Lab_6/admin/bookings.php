<?php
require __DIR__ . '/../includes/functions.php';
require __DIR__ . '/../includes/queries.php';

$statuses = ['pending' => 'في الانتظار', 'confirmed' => 'مؤكد', 'cancelled' => 'ملغى'];

// change the status of a booking, then reload the list (Post/Redirect/Get)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    if ($id) {
        set_booking_status($id, $_POST['status'] ?? '');
    }
    redirect('bookings.php');
}

$bookings = get_bookings();

$page_title = 'الحجوزات';
$active = 'bookings';
require __DIR__ . '/header.php';
?>
        <table class="Admin_Table">
            <tr>
                <th>#</th>
                <th>الرحلة</th>
                <th>الزبون</th>
                <th>تاريخ الانطلاق</th>
                <th>المسافرون</th>
                <th>المجموع</th>
                <th>الحالة</th>
            </tr>
            <?php foreach ($bookings as $booking): ?>
            <tr>
                <td><?= (int) $booking['id'] ?></td>
                <!-- trip_title comes from the trips table, thanks to the JOIN of get_bookings() -->
                <td><?= e($booking['trip_title']) ?></td>
                <td><?= e($booking['full_name']) ?><br /><small><?= e($booking['email']) ?></small></td>
                <td><?= e($booking['departure_date']) ?></td>
                <td><?= (int) $booking['people'] ?></td>
                <td><?= money((int) $booking['total_price']) ?></td>
                <td>
                    <form method="post" class="Inline_Form">
                        <input type="hidden" name="id" value="<?= (int) $booking['id'] ?>" />
                        <select name="status" class="Status Status_<?= e($booking['status']) ?>" aria-label="الحالة">
                            <?php foreach ($statuses as $value => $label): ?>
                            <option value="<?= $value ?>" <?= $value === $booking['status'] ? 'selected' : '' ?>><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="Button Small">حفظ</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php if (!$bookings): ?>
        <p>لا توجد حجوزات بعد.</p>
        <?php endif; ?>
<?php require __DIR__ . '/footer.php'; ?>
