<?php
require __DIR__ . '/../includes/functions.php';
require __DIR__ . '/../includes/queries.php';

require_post('trips.php');

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id || get_trip($id) === null) {
    redirect('trips.php?status=not_found');
}

redirect(delete_trip($id) ? 'trips.php?status=deleted' : 'trips.php?status=has_bookings');
