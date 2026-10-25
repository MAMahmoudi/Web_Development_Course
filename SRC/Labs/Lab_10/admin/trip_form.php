<?php
// Creates a trip (trip_form.php) or edits one (trip_form.php?id=3).
// The photo is chosen among the photos of the demo, or uploaded (Lab 6).
require __DIR__ . '/../includes/functions.php';
require __DIR__ . '/../includes/queries.php';
require __DIR__ . '/guard.php';   // admins only (Lab 7)
require __DIR__ . '/../includes/validation.php';
require __DIR__ . '/../includes/upload.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: null;
$trip = ['title' => '', 'description' => '', 'image' => '', 'price' => '', 'duration_days' => '1', 'is_published' => 1];

if ($id !== null) {
    $trip = get_trip($id);
    if ($trip === null) {
        flash('Error', 'هذه الرحلة غير موجودة.');
        redirect('trips.php');
    }
}

// the photos of SRC/IMAGES that can be chosen in the list
$images = array_map(
    fn ($path) => substr($path, strlen(__DIR__ . '/../' . IMAGES_URL)),
    array_merge(glob(__DIR__ . '/../' . IMAGES_URL . 'Lab_2/*.jpg'), glob(__DIR__ . '/../' . IMAGES_URL . '*.jpg'))
);
// an uploaded photo of this trip stays a valid choice
if (str_starts_with($trip['image'], 'uploads/')) {
    array_unshift($images, $trip['image']);
}

$rules = [
    'title' => ['required', 'max:100'],
    'description' => ['required', 'min:20'],
    'price' => ['required', 'int:0,'],
    'duration_days' => ['required', 'int:1,30'],
];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old_image = $trip['image'];
    foreach (array_keys($rules) as $field) {
        $trip[$field] = input($field);
    }
    $trip['is_published'] = isset($_POST['is_published']) ? 1 : 0;   // an unchecked checkbox is not sent at all
    $errors = validate($trip, $rules);

    // the photo: an uploaded file wins over the list
    [$uploaded, $upload_error] = store_uploaded_image($_FILES['image_file'] ?? null);
    if ($upload_error !== null) {
        $errors['image'] = $upload_error;
    } elseif ($uploaded !== null) {
        $trip['image'] = $uploaded;
    } else {
        $trip['image'] = input('image');
        if (!in_array($trip['image'], $images, true)) {
            $errors['image'] = 'اختاروا صورة من القائمة أو ارفعوا صورة.';
        }
    }

    if (!$errors) {
        $trip['price'] = (int) $trip['price'];
        $trip['duration_days'] = (int) $trip['duration_days'];
        if ($id === null) {
            create_trip($trip);
        } else {
            update_trip($id, $trip);
        }
        // the previous uploaded photo is not used any more
        if ($old_image !== $trip['image']) {
            delete_uploaded_image($old_image);
        }
        flash('Success', $id === null ? 'تمت إضافة الرحلة.' : 'تم تعديل الرحلة.');
        redirect('trips.php');
    }

    // the form is shown again: a photo uploaded with other errors is not kept
    if ($uploaded !== null) {
        delete_uploaded_image($uploaded);
        $trip['image'] = $old_image;
    }
    http_response_code(422);
}

$page_title = $id === null ? 'إضافة رحلة' : 'تعديل رحلة';
$active = 'trips';
require __DIR__ . '/header.php';
?>
        <?php if ($errors): ?>
        <p class="Flash Flash_Error">صححوا الحقول المشار إليها (<?= count($errors) ?>).</p>
        <?php endif; ?>
        <!-- enctype="multipart/form-data" is required to send files -->
        <form method="post" enctype="multipart/form-data" class="Admin_Form">
            <?= csrf_field() ?>
            <label for="Title">العنوان</label>
            <input type="text" id="Title" name="title" value="<?= e($trip['title']) ?>" maxlength="100"
                required<?= invalid_attributes($errors, 'title', 'Title') ?> />
            <?= field_error($errors, 'title', 'Title') ?>

            <label for="Description">الوصف</label>
            <textarea id="Description" name="description" rows="6"
                required<?= invalid_attributes($errors, 'description', 'Description') ?>><?= e($trip['description']) ?></textarea>
            <?= field_error($errors, 'description', 'Description') ?>

            <label for="Image">الصورة</label>
            <div class="Image_Choice">
                <?php if ($trip['image'] !== ''): ?>
                <img src="<?= e(image_url($trip['image'], '../')) ?>" alt="" class="Thumbnail" />
                <?php endif; ?>
                <div>
                    <select id="Image" name="image"<?= invalid_attributes($errors, 'image', 'Image') ?>>
                        <option value="">اختر صورة</option>
                        <?php foreach ($images as $image): ?>
                        <option value="<?= e($image) ?>" <?= $image === $trip['image'] ? 'selected' : '' ?>><?= e($image) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label for="Image_File" class="Small_Label">أو ارفعوا صورة (JPEG أو PNG أو WebP، 2 ميغابايت على الأكثر)</label>
                    <input type="hidden" name="MAX_FILE_SIZE" value="<?= UPLOAD_MAX_BYTES ?>" />
                    <input type="file" id="Image_File" name="image_file" accept="image/jpeg,image/png,image/webp" />
                </div>
            </div>
            <?= field_error($errors, 'image', 'Image') ?>

            <div class="Row">
                <div>
                    <label for="Price">السعر للشخص (دج)</label>
                    <input type="number" id="Price" name="price" value="<?= e((string) $trip['price']) ?>" min="0" step="500"
                        required<?= invalid_attributes($errors, 'price', 'Price') ?> />
                    <?= field_error($errors, 'price', 'Price') ?>
                </div>
                <div>
                    <label for="Duration">المدة (أيام)</label>
                    <input type="number" id="Duration" name="duration_days" value="<?= e((string) $trip['duration_days']) ?>" min="1" max="30"
                        required<?= invalid_attributes($errors, 'duration_days', 'Duration') ?> />
                    <?= field_error($errors, 'duration_days', 'Duration') ?>
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
