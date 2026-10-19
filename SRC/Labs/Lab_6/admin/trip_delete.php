<?php
require __DIR__ . '/../includes/functions.php';
require __DIR__ . '/../includes/queries.php';
require __DIR__ . '/../includes/upload.php';

require_post('trips.php');

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$trip = $id ? get_trip($id) : null;
if ($trip === null) {
    redirect('trips.php?status=not_found');
}

if (!delete_trip($id)) {
    redirect('trips.php?status=has_bookings');
}
// the row is gone: its uploaded photo (if any) can be deleted too
delete_uploaded_image($trip['image']);
redirect('trips.php?status=deleted');
