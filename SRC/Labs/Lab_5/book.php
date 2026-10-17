<?php
// Receives the booking dialog (includes/booking_dialog.php), saves the booking and goes back to the page.
// This file displays nothing: it always ends with a redirect.
require __DIR__ . '/includes/functions.php';
require __DIR__ . '/includes/queries.php';

require_post('index.php');

// only come back to one of our own pages
$return = in_array($_POST['return'] ?? '', ['index.php', 'services.php'], true) ? $_POST['return'] : 'index.php';

$trip_id = filter_input(INPUT_POST, 'trip_id', FILTER_VALIDATE_INT);
$people = filter_input(INPUT_POST, 'people', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 10]]);
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$name = trim($_POST['name'] ?? '');

// the departure must be a real date, at least BOOKING_MIN_DAYS days from today
$departure = DateTime::createFromFormat('!Y-m-d', $_POST['date'] ?? '');
$earliest = new DateTime('today +' . BOOKING_MIN_DAYS . ' days');
$date_ok = $departure !== false && $departure->format('Y-m-d') === ($_POST['date'] ?? '') && $departure >= $earliest;

// filter_input returns false (invalid) or null (missing): both mean "rejected"
if (!$trip_id || !$people || !$email || mb_strlen($name) < 3 || !$date_ok) {
    redirect($return . '?booking=error#Trips');
}

$id = create_booking([
    'trip_id' => $trip_id,
    'full_name' => $name,
    'email' => $email,
    'departure_date' => $departure->format('Y-m-d'),
    'people' => $people,
]);

redirect($id === null ? $return . '?booking=error#Trips' : $return . '?booked=' . $id . '#Trips');
