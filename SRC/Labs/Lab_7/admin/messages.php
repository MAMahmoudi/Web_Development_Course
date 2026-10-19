<?php
require __DIR__ . '/../includes/functions.php';
require __DIR__ . '/../includes/queries.php';
require __DIR__ . '/guard.php';   // admins only (Lab 7)

$subjects = ['info' => 'معلومات عامة', 'booking' => 'حجز رحلة', 'custom' => 'رحلة حسب الطلب', 'other' => 'أخرى'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    if ($id && ($_POST['action'] ?? '') === 'read') {
        mark_message_read($id);
    } elseif ($id && ($_POST['action'] ?? '') === 'delete') {
        delete_message($id);
    }
    redirect('messages.php');
}

$messages = get_messages();

$page_title = 'الرسائل';
$active = 'messages';
require __DIR__ . '/header.php';
?>
        <?php foreach ($messages as $message): ?>
        <article class="Message <?= $message['is_read'] ? '' : 'Unread' ?>">
            <header>
                <strong><?= e($message['full_name']) ?></strong>
                <a href="mailto:<?= e($message['email']) ?>"><?= e($message['email']) ?></a>
                <?php if ($message['phone'] !== null): ?>
                <span dir="ltr"><?= e($message['phone']) ?></span>
                <?php endif; ?>
                <span class="Tag"><?= e($subjects[$message['subject']] ?? $message['subject']) ?></span>
                <time><?= e($message['created_at']) ?></time>
            </header>
            <!-- nl2br keeps the line breaks of the message; e() first, so that no HTML can be injected -->
            <p><?= nl2br(e($message['message'])) ?></p>
            <div class="Actions">
                <?php if (!$message['is_read']): ?>
                <form method="post">
                    <input type="hidden" name="id" value="<?= (int) $message['id'] ?>" />
                    <button type="submit" name="action" value="read" class="Button Small">تمت القراءة</button>
                </form>
                <?php endif; ?>
                <form method="post" data-confirm="حذف هذه الرسالة؟">
                    <input type="hidden" name="id" value="<?= (int) $message['id'] ?>" />
                    <button type="submit" name="action" value="delete" class="Button Small Danger">حذف</button>
                </form>
            </div>
        </article>
        <?php endforeach; ?>
        <?php if (!$messages): ?>
        <p>لا توجد رسائل.</p>
        <?php endif; ?>
<?php require __DIR__ . '/footer.php'; ?>
