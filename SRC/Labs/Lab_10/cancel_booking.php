<?php
// A customer cancels one of their bookings (Lab 9).
// The id of the booking comes from the form: the browser can send ANY id, so the owner is checked by the query.
require __DIR__ . '/includes/functions.php';
require __DIR__ . '/includes/queries.php';
require __DIR__ . '/includes/auth.php';   // also checks the CSRF token of the form

require_post('my_bookings.php');
require_login();

$id = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT) ?: 0;
if (cancel_user_booking($id, current_user()['id'])) {
    flash('Success', 'تم إلغاء الحجز رقم ' . $id . '.');
} else {
    // not found, not yours, or already confirmed: the same answer, which reveals nothing
    flash('Error', 'لا يمكن إلغاء هذا الحجز.');
}
redirect('my_bookings.php');
