<?php
require __DIR__ . '/includes/functions.php';
require __DIR__ . '/includes/queries.php';

$subjects = [
    'info' => 'معلومات عامة',
    'booking' => 'حجز رحلة',
    'custom' => 'رحلة حسب الطلب',
    'other' => 'أخرى',
];
$values = ['name' => '', 'email' => '', 'phone' => '', 'subject' => '', 'message' => ''];
$errors = [];

// the form was sent: check it, save it, then redirect (Post/Redirect/Get)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($values as $key => $value) {
        $values[$key] = trim($_POST[$key] ?? '');
    }

    // minimal checks: the browser already checked the form (Lab 4), but its checks can be bypassed.
    // Lab 6 is about validating forms on the server in detail.
    if (mb_strlen($values['name']) < 3) {
        $errors[] = 'الرجاء إدخال الإسم الكامل.';
    }
    if (!filter_var($values['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'البريد الإلكتروني غير صحيح.';
    }
    if ($values['phone'] !== '' && !preg_match('/^0[5-7][0-9]{8}$/', $values['phone'])) {
        $errors[] = 'رقم الهاتف غير صحيح.';
    }
    if (!array_key_exists($values['subject'], $subjects)) {
        $errors[] = 'الرجاء اختيار موضوع.';
    }
    if (mb_strlen($values['message']) < 20) {
        $errors[] = 'الرسالة قصيرة جداً.';
    }

    if (!$errors) {
        create_message([
            'full_name' => $values['name'],
            'email' => $values['email'],
            'phone' => $values['phone'],
            'subject' => $values['subject'],
            'message' => $values['message'],
        ]);
        // a reload of the next page will not send the message a second time
        redirect('contact_us.php?sent=1');
    }
}

$page_title = 'سياحة | إتصل بنا';
$active = 'contact';
$styles = ['Hero', 'Contact_Us'];
$responsive = ['Hero', 'Contact_Us'];
$scripts = ['Contact_Form'];

require __DIR__ . '/includes/header.php';
?>
    <main>
        <section id="Hero_ID">
            <div class="Hero">
                <img src="../../IMAGES/Oran.jpg" alt="وهران" />
            </div>
            <div class="Hero_Text">
                <h1>مرحبا بكم في موقعنا</h1>
                <p>نحن نقدم لكم أفضل الخدمات</p>
                <a href="#Contact" class="Show" id="Contact_Us_Btn_ID">تواصل معنا</a>
            </div>
        </section>
        <div class="Contact_Us_Container" id="Contact">
            <h1>إتصل بنا</h1>
            <?php if (isset($_GET['sent'])): ?>
            <p class="Flash Flash_Success" role="status">شكراً لكم! تم استلام رسالتكم وسنرد عليكم في أقرب وقت.</p>
            <?php endif; ?>
            <?php if ($errors): ?>
            <div class="Flash Flash_Error" role="alert">
                <p>لم يتم إرسال الرسالة:</p>
                <ul>
                    <?php foreach ($errors as $error): ?>
                    <li><?= e($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
            <div class="Contact_Layout">
                <div class="Contact_Info">
                    <div class="Contact_Info_Item">
                        <i class="fa-solid fa-location-dot"></i>
                        <div>
                            <h4>العنوان</h4>
                            <p>شارع ديدوش مراد، الجزائر العاصمة</p>
                        </div>
                    </div>
                    <div class="Contact_Info_Item">
                        <i class="fa-solid fa-phone"></i>
                        <div>
                            <h4>الهاتف</h4>
                            <a href="tel:+213555000000"><span dir="ltr">+213 555 00 00 00</span></a>
                        </div>
                    </div>
                    <div class="Contact_Info_Item">
                        <i class="fa-solid fa-envelope"></i>
                        <div>
                            <h4>البريد الإلكتروني</h4>
                            <a href="mailto:contact@syaha.example">contact@syaha.example</a>
                        </div>
                    </div>
                    <div class="Contact_Info_Item">
                        <i class="fa-solid fa-clock"></i>
                        <div>
                            <h4>ساعات العمل</h4>
                            <p>الأحد - الخميس: 08:00 - 17:00</p>
                        </div>
                    </div>
                </div>
                <!-- the values typed before an error are put back in the fields ("sticky" form) -->
                <form action="contact_us.php" method="post">
                    <label for="Name">الإسم الكامل</label>
                    <input type="text" id="Name" name="name" value="<?= e($values['name']) ?>" autocomplete="name" required />
                    <label for="Email">البريد الإلكتروني</label>
                    <input type="email" id="Email" name="email" value="<?= e($values['email']) ?>" autocomplete="email" required />
                    <label for="Phone">رقم الهاتف</label>
                    <input type="tel" id="Phone" name="phone" value="<?= e($values['phone']) ?>" autocomplete="tel"
                        placeholder="05XXXXXXXX" pattern="0[5-7][0-9]{8}" />
                    <label for="Subject">الموضوع</label>
                    <select id="Subject" name="subject" required>
                        <option value="">اختر موضوعاً</option>
                        <?php foreach ($subjects as $key => $label): ?>
                        <option value="<?= $key ?>" <?= $key === $values['subject'] ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label for="Message">الرسالة</label>
                    <textarea id="Message" name="message" rows="5" required><?= e($values['message']) ?></textarea>
                    <button type="submit">إرسال</button>
                </form>
            </div>
            <iframe class="Contact_Map" title="موقع الوكالة على الخريطة" loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"
                src="https://www.openstreetmap.org/export/embed.html?bbox=3.03%2C36.75%2C3.07%2C36.78&amp;layer=mapnik&amp;marker=36.765%2C3.05"></iframe>
        </div>
    </main>
<?php require __DIR__ . '/includes/footer.php'; ?>
