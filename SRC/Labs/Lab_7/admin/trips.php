<?php
require __DIR__ . '/../includes/functions.php';
require __DIR__ . '/../includes/queries.php';
require __DIR__ . '/guard.php';   // admins only (Lab 7)

$trips = get_trips(false);   // also the unpublished ones

// the result of the previous action is a flash message (Lab 7), shown by header.php

$page_title = 'الرحلات';
$active = 'trips';
require __DIR__ . '/header.php';
?>
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
                <td>
                    <div class="Actions">
                        <a href="trip_form.php?id=<?= (int) $trip['id'] ?>" class="Button Small">تعديل</a>
                        <!-- deleting changes data: a POST form, never a simple link (GET) -->
                        <form action="trip_delete.php" method="post" data-confirm="حذف الرحلة «<?= e($trip['title']) ?>»؟">
                            <input type="hidden" name="id" value="<?= (int) $trip['id'] ?>" />
                            <button type="submit" class="Button Small Danger">حذف</button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
<?php require __DIR__ . '/footer.php'; ?>
