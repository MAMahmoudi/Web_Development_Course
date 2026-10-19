<?php
// Every SQL query of the website, as small functions (Lab 5, search_trips() added in Lab 6, users in Lab 7).
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

/**
 * Published trips filtered by the search form of the services page (Lab 6).
 * $max_price and $max_days are null when the visitor did not choose them.
 */
function search_trips(?int $max_price, ?int $max_days, string $sort): array
{
    // ORDER BY cannot use a placeholder: the column comes from this fixed list, never from the visitor
    $orders = [
        'price_asc' => 'price ASC',
        'price_desc' => 'price DESC',
        'duration' => 'duration_days ASC, price ASC',
    ];
    $order = $orders[$sort] ?? 'id';

    // the conditions are added only when needed; the values always go through placeholders
    $where = ['is_published = 1'];
    $parameters = [];
    if ($max_price !== null) {
        $where[] = 'price <= :max_price';
        $parameters['max_price'] = $max_price;
    }
    if ($max_days !== null) {
        $where[] = 'duration_days <= :max_days';
        $parameters['max_days'] = $max_days;
    }

    $statement = db()->prepare('SELECT * FROM trips WHERE ' . implode(' AND ', $where) . ' ORDER BY ' . $order);
    $statement->execute($parameters);
    return $statement->fetchAll();
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
            'INSERT INTO bookings (trip_id, user_id, full_name, email, departure_date, people, total_price)
             VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        $statement->execute([
            $booking['trip_id'],
            $booking['user_id'] ?? null,           // null: booked without an account
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

/** The bookings of one account, with the title of their trip, most recent first (Lab 7). */
function get_user_bookings(int $user_id): array
{
    $statement = db()->prepare(
        'SELECT bookings.*, trips.title AS trip_title, trips.image AS trip_image
         FROM bookings
         JOIN trips ON trips.id = bookings.trip_id
         WHERE bookings.user_id = ?
         ORDER BY bookings.departure_date DESC'
    );
    $statement->execute([$user_id]);
    return $statement->fetchAll();
}

// ------------------------------------------------------------------ users (Lab 7)

/** The account of an e-mail address, or null. */
function find_user_by_email(string $email): ?array
{
    $statement = db()->prepare('SELECT * FROM users WHERE email = ?');
    $statement->execute([$email]);
    return $statement->fetch() ?: null;
}

/** Creates a customer account and returns its id. The password is hashed here, never stored as such. */
function create_user(string $full_name, string $email, string $password): int
{
    $statement = db()->prepare('INSERT INTO users (full_name, email, password_hash) VALUES (?, ?, ?)');
    $statement->execute([$full_name, $email, password_hash($password, PASSWORD_DEFAULT)]);
    return (int) db()->lastInsertId();
}

/** Replaces the hash of a password (when PHP recommends a stronger algorithm, see password_needs_rehash). */
function update_password_hash(int $user_id, string $password): void
{
    db()->prepare('UPDATE users SET password_hash = ? WHERE id = ?')
        ->execute([password_hash($password, PASSWORD_DEFAULT), $user_id]);
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
