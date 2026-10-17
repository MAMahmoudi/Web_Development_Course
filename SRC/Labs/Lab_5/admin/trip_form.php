<?php
// Creates a trip (trip_form.php) or edits one (trip_form.php?id=3).
require __DIR__ . '/../includes/functions.php';
require __DIR__ . '/../includes/queries.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: null;
$trip = ['title' => '', 'description' => '', 'image' => '', 'price' => '', 'duration_days' => 1, 'is_published' => 1];

if ($id !== null) {
    $trip = get_trip($id);
    if ($trip === null) {
        redirect('trips.php?status=not_found');
    }
}

// the images that can be chosen: the photos of SRC/IMAGES (uploading files is the subject of Lab 6)
$images = array_map(
    fn ($path) => substr($path, strlen(__DIR__ . '/../' . IMAGES_URL)),
    array_merge(glob(__DIR__ . '/../' . IMAGES_URL . 'Lab_2/*.jpg'), glob(__DIR__ . '/../' . IMAGES_URL . '*.jpg'))
);

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $trip = [
        'title' => trim($_POST['title'] ?? ''),
        'description' => trim($_POST['description'] ?? ''),
        'image' => $_POST['image'] ?? '',
        'price' => filter_input(INPUT_POST, 'price', FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]),
        'duration_days' => filter_input(INPUT_POST, 'duration_days', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 30]]),
        'is_published' => isset($_POST['is_published']) ? 1 : 0,   // an unchecked checkbox is not sent at all
    ];

    if ($trip['title'] === '' || mb_strlen($trip['title']) > 100) {
        $errors[] = 'العنوان إجباري (100 حرف على الأكثر).';
    }
    if ($trip['description'] === '') {
        $errors[] = 'الوصف إجباري.';
    }
    if (!in_array($trip['image'], $images, true)) {
        $errors[] = 'الرجاء اختيار صورة.';
    }
    if ($trip['price'] === false || $trip['price'] === null) {
        $errors[] = 'السعر يجب أن يكون عدداً صحيحاً موجباً.';
    }
    if ($trip['duration_days'] === false || $trip['duration_days'] === null) {
        $errors[] = 'المدة بين 1 و 30 يوماً.';
    }

    if (!$errors) {
        if ($id === null) {
            create_trip($trip);
            redirect('trips.php?status=created');
        }
        update_trip($id, $trip);
        redirect('trips.php?status=updated');
    }
}

$page_title = $id === null ? 'إضافة رحلة' : 'تعديل رحلة';
$active = 'trips';
require __DIR__ . '/header.php';
?>
        <?php if ($errors): ?>
        <div class="Flash Flash_Error">
            <ul>
                <?php foreach ($errors as $error): ?>
                <li><?= e($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>
        <form method="post" class="Admin_Form">
            <label for="Title">العنوان</label>
            <input type="text" id="Title" name="title" value="<?= e($trip['title']) ?>" maxlength="100" required />

            <label for="Description">الوصف</label>
            <textarea id="Description" name="description" rows="6" required><?= e($trip['description']) ?></textarea>

            <label for="Image">الصورة</label>
            <select id="Image" name="image" required>
                <option value="">اختر صورة</option>
                <?php foreach ($images as $image): ?>
                <option value="<?= e($image) ?>" <?= $image === $trip['image'] ? 'selected' : '' ?>><?= e($image) ?></option>
                <?php endforeach; ?>
            </select>

            <div class="Row">
                <div>
                    <label for="Price">السعر للشخص (دج)</label>
                    <input type="number" id="Price" name="price" value="<?= e((string) $trip['price']) ?>" min="0" step="500" required />
                </div>
                <div>
                    <label for="Duration">المدة (أيام)</label>
                    <input type="number" id="Duration" name="duration_days" value="<?= e((string) $trip['duration_days']) ?>" min="1" max="30" required />
                </div>
            </div>

            <label class="Checkbox">
                <input type="checkbox" name="is_published" <?= $trip['is_published'] ? 'checked' : '' ?> />
                منشورة على الموقع
            </label>

            <div class="Row">
                <button type="submit" class="Button"><i class="fa-solid fa-floppy-disk"></i> حفظ</button>
                <a href="trips.php" class="Button Secondary">إلغاء</a>
            </div>
        </form>
<?php require __DIR__ . '/footer.php'; ?>
