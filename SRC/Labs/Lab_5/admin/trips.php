<?php
require __DIR__ . '/../includes/functions.php';
require __DIR__ . '/../includes/queries.php';

$trips = get_trips(false);   // also the unpublished ones

// result of the previous action (trip_form.php and trip_delete.php redirect here with ?status=...)
$messages = [
    'created' => ['Success', 'تمت إضافة الرحلة.'],
    'updated' => ['Success', 'تم تعديل الرحلة.'],
    'deleted' => ['Success', 'تم حذف الرحلة.'],
    'has_bookings' => ['Error', 'لا يمكن حذف رحلة لديها حجوزات: ألغوا نشرها بدلاً من ذلك.'],
    'not_found' => ['Error', 'هذه الرحلة غير موجودة.'],
];
$status = $messages[$_GET['status'] ?? ''] ?? null;

$page_title = 'الرحلات';
$active = 'trips';
require __DIR__ . '/header.php';
?>
        <?php if ($status): ?>
        <p class="Flash Flash_<?= $status[0] ?>"><?= $status[1] ?></p>
        <?php endif; ?>
        <p><a href="trip_form.php" class="Button"><i class="fa-solid fa-plus"></i> إضافة رحلة</a></p>
        <table class="Admin_Table">
            <tr>
                <th>الصورة</th>
                <th>العنوان</th>
                <th>السعر للشخص</th>
                <th>المدة</th>
                <th>منشورة</th>
                <th></th>
            </tr>
            <?php foreach ($trips as $trip): ?>
            <tr>
                <td><img src="<?= e(image_url($trip['image'], '../')) ?>" alt="" class="Thumbnail" /></td>
                <td><?= e($trip['title']) ?></td>
                <td><?= money((int) $trip['price']) ?></td>
                <td><?= (int) $trip['duration_days'] ?> يوم</td>
                <td><?= $trip['is_published'] ? '<i class="fa-solid fa-check"></i>' : '<i class="fa-solid fa-xmark"></i>' ?></td>
                <td class="Actions">
                    <a href="trip_form.php?id=<?= (int) $trip['id'] ?>" class="Button Small">تعديل</a>
                    <!-- deleting changes data: a POST form, never a simple link (GET) -->
                    <form action="trip_delete.php" method="post" data-confirm="حذف الرحلة «<?= e($trip['title']) ?>»؟">
                        <input type="hidden" name="id" value="<?= (int) $trip['id'] ?>" />
                        <button type="submit" class="Button Small Danger">حذف</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
<?php require __DIR__ . '/footer.php'; ?>
