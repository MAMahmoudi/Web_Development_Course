<?php
require __DIR__ . '/../includes/functions.php';
require __DIR__ . '/../includes/queries.php';
require __DIR__ . '/guard.php';   // admins only (Lab 7)
require __DIR__ . '/../includes/upload.php';

require_post('trips.php');

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$trip = $id ? get_trip($id) : null;
if ($trip === null) {
    flash('Error', 'هذه الرحلة غير موجودة.');
    redirect('trips.php');
}

if (!delete_trip($id)) {
    flash('Error', 'لا يمكن حذف رحلة لديها حجوزات: ألغوا نشرها بدلاً من ذلك.');
    redirect('trips.php');
}
// the row is gone: its uploaded photo (if any) can be deleted too
delete_uploaded_image($trip['image']);
flash('Success', 'تم حذف الرحلة.');
redirect('trips.php');
