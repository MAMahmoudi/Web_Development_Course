<?php
// Every SQL query of the website, as small functions.
// The pages never write SQL themselves: they call these functions.
// Every value that comes from outside (form, URL) goes through a placeholder (? or :name).
require_once __DIR__ . '/db.php';

// ------------------------------------------------------------------ public pages (READ)

function get_destinations(): array
{
    return db()->query('SELECT * FROM destinations ORDER BY sort_order')->fetchAll();
}

function get_services(): array
{
    return db()->query('SELECT * FROM services ORDER BY sort_order')->fetchAll();
}

function get_team(): array
{
    return db()->query('SELECT * FROM team_members ORDER BY sort_order')->fetchAll();
}

/** Published trips for the visitors, or all of them for the admin area. */
function get_trips(bool $published_only = true): array
{
    $sql = 'SELECT * FROM trips' . ($published_only ? ' WHERE is_published = 1' : '') . ' ORDER BY id';
    return db()->query($sql)->fetchAll();
}

/** One trip, or null if the id does not exist. */
function get_trip(int $id): ?array
{
    $statement = db()->prepare('SELECT * FROM trips WHERE id = ?');
    $statement->execute([$id]);
    return $statement->fetch() ?: null;
}

// ------------------------------------------------------------------ trips (CREATE, UPDATE, DELETE)

/** Inserts a trip and returns its new id. */
function create_trip(array $trip): int
{
    $statement = db()->prepare(
        'INSERT INTO trips (title, description, image, price, duration_days, is_published)
         VALUES (:title, :description, :image, :price, :duration_days, :is_published)'
    );
    $statement->execute([
        'title' => $trip['title'],
        'description' => $trip['description'],
        'image' => $trip['image'],
        'price' => $trip['price'],
        'duration_days' => $trip['duration_days'],
        'is_published' => $trip['is_published'],
    ]);
    return (int) db()->lastInsertId();
}

/** Updates a trip; returns false if the id does not exist. */
function update_trip(int $id, array $trip): bool
{
    $statement = db()->prepare(
        'UPDATE trips
         SET title = :title, description = :description, image = :image, price = :price,
             duration_days = :duration_days, is_published = :is_published
         WHERE id = :id'
    );
    $statement->execute([
        'id' => $id,
        'title' => $trip['title'],
        'description' => $trip['description'],
        'image' => $trip['image'],
        'price' => $trip['price'],
        'duration_days' => $trip['duration_days'],
        'is_published' => $trip['is_published'],
    ]);
    // rowCount() is 0 when nothing changed too: check that the trip exists instead
    return $statement->rowCount() > 0 || get_trip($id) !== null;
}

/**
 * Deletes a trip. Returns false if it has bookings:
 * the foreign key of the bookings table (ON DELETE RESTRICT) forbids it.
 */
function delete_trip(int $id): bool
{
    try {
        $statement = db()->prepare('DELETE FROM trips WHERE id = ?');
        $statement->execute([$id]);
        return $statement->rowCount() === 1;
    } catch (PDOException $e) {
        if ($e->getCode() === '23000') {  // integrity constraint violation
            return false;
        }
        throw $e;
    }
}

// ------------------------------------------------------------------ bookings

/**
 * Records a booking and returns its id, or null if the trip is not available.
 * The price is read from the database: a total sent by the browser could have been modified.
 * Both queries run in a transaction: they succeed or fail together.
 */
function create_booking(array $booking): ?int
{
    $pdo = db();
    $pdo->beginTransaction();
    try {
        $statement = $pdo->prepare('SELECT price FROM trips WHERE id = ? AND is_published = 1 FOR UPDATE');
        $statement->execute([$booking['trip_id']]);
        $price = $statement->fetchColumn();
        if ($price === false) {
            $pdo->rollBack();
            return null;
        }

        $statement = $pdo->prepare(
            'INSERT INTO bookings (trip_id, full_name, email, departure_date, people, total_price)
             VALUES (?, ?, ?, ?, ?, ?)'
        );
        $statement->execute([
            $booking['trip_id'],
            $booking['full_name'],
            $booking['email'],
            $booking['departure_date'],
            $booking['people'],
            $price * $booking['people'],
        ]);
        $id = (int) $pdo->lastInsertId();
        $pdo->commit();
        return $id;
    } catch (PDOException $e) {
        $pdo->rollBack();
        throw $e;
    }
}

/** All the bookings with the title of their trip (JOIN), most recent first. */
function get_bookings(): array
{
    return db()->query(
        'SELECT bookings.*, trips.title AS trip_title
         FROM bookings
         JOIN trips ON trips.id = bookings.trip_id
         ORDER BY bookings.created_at DESC, bookings.id DESC'
    )->fetchAll();
}

function set_booking_status(int $id, string $status): bool
{
    if (!in_array($status, ['pending', 'confirmed', 'cancelled'], true)) {
        return false;
    }
    $statement = db()->prepare('UPDATE bookings SET status = ? WHERE id = ?');
    $statement->execute([$status, $id]);
    return $statement->rowCount() === 1;
}

// ------------------------------------------------------------------ contact messages

function create_message(array $message): int
{
    $statement = db()->prepare(
        'INSERT INTO contact_messages (full_name, email, phone, subject, message)
         VALUES (?, ?, ?, ?, ?)'
    );
    $statement->execute([
        $message['full_name'],
        $message['email'],
        $message['phone'] === '' ? null : $message['phone'],   // empty optional field -> NULL
        $message['subject'],
        $message['message'],
    ]);
    return (int) db()->lastInsertId();
}

function get_messages(): array
{
    return db()->query('SELECT * FROM contact_messages ORDER BY created_at DESC, id DESC')->fetchAll();
}

function mark_message_read(int $id): void
{
    db()->prepare('UPDATE contact_messages SET is_read = 1 WHERE id = ?')->execute([$id]);
}

function delete_message(int $id): void
{
    db()->prepare('DELETE FROM contact_messages WHERE id = ?')->execute([$id]);
}

// ------------------------------------------------------------------ dashboard

/** A few numbers for the admin dashboard, each from one aggregate query. */
function get_statistics(): array
{
    $pdo = db();
    return [
        'trips' => (int) $pdo->query('SELECT COUNT(*) FROM trips WHERE is_published = 1')->fetchColumn(),
        'pending' => (int) $pdo->query("SELECT COUNT(*) FROM bookings WHERE status = 'pending'")->fetchColumn(),
        'unread' => (int) $pdo->query('SELECT COUNT(*) FROM contact_messages WHERE is_read = 0')->fetchColumn(),
        'revenue' => (int) $pdo->query("SELECT COALESCE(SUM(total_price), 0) FROM bookings WHERE status = 'confirmed'")->fetchColumn(),
    ];
}
