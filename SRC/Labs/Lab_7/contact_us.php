<?php
require __DIR__ . '/includes/functions.php';
require __DIR__ . '/includes/queries.php';
require __DIR__ . '/includes/auth.php';
require __DIR__ . '/includes/validation.php';

$subjects = [
    'info' => 'معلومات عامة',
    'booking' => 'حجز رحلة',
    'custom' => 'رحلة حسب الطلب',
    'other' => 'أخرى',
];

// the rules of the form: the same as the JavaScript of Lab 4, checked again by the server
$rules = [
    'name' => ['required', 'min:3', 'max:100'],
    'email' => ['required', 'email', 'max:255'],
    'phone' => ['phone'],
    'subject' => ['required', 'in:' . implode(',', array_keys($subjects))],
    'message' => ['required', 'min:20', 'max:500'],
];

// 1. initial values: empty fields, or the name and e-mail of the logged-in user (Lab 7)
$values = array_fill_keys(array_keys($rules), '');
$values['name'] = current_user()['full_name'] ?? '';
$values['email'] = current_user()['email'] ?? '';
$errors = [];

// 2. the form was sent: read, validate, save, redirect (Post/Redirect/Get)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($rules as $field => $field_rules) {
        $values[$field] = input($field);
    }

    $errors = validate($values, $rules);

    if (!$errors) {
        create_message([
            'full_name' => $values['name'],
            'email' => $values['email'],
            'phone' => $values['phone'],
            'subject' => $values['subject'],
            'message' => $values['message'],
        ]);
        flash('Success', 'شكراً لكم! تم استلام رسالتكم وسنرد عليكم في أقرب وقت.');
        redirect('contact_us.php#Contact');
    }
    // 422: the request was understood but its data is not valid
    http_response_code(422);
}

// 3. display the page (with the errors and the values typed, if any)
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
            <?php if ($errors): ?>
            <p class="Flash Flash_Error" role="alert">
                لم يتم إرسال الرسالة: صححوا الحقول المشار إليها (<?= count($errors) ?>).
            </p>
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
                <!-- sticky form: the values typed come back in the fields, each error is under its field -->
                <form action="contact_us.php#Contact" method="post">
                    <label for="Name">الإسم الكامل</label>
                    <input type="text" id="Name" name="name" value="<?= e($values['name']) ?>" autocomplete="name"
                        required<?= invalid_attributes($errors, 'name', 'Name') ?> />
                    <?= field_error($errors, 'name', 'Name') ?>

                    <label for="Email">البريد الإلكتروني</label>
                    <input type="email" id="Email" name="email" value="<?= e($values['email']) ?>" autocomplete="email"
                        required<?= invalid_attributes($errors, 'email', 'Email') ?> />
                    <?= field_error($errors, 'email', 'Email') ?>

                    <label for="Phone">رقم الهاتف</label>
                    <input type="tel" id="Phone" name="phone" value="<?= e($values['phone']) ?>" autocomplete="tel"
                        placeholder="05XXXXXXXX" pattern="0[5-7][0-9]{8}"<?= invalid_attributes($errors, 'phone', 'Phone') ?> />
                    <?= field_error($errors, 'phone', 'Phone') ?>

                    <label for="Subject">الموضوع</label>
                    <select id="Subject" name="subject" required<?= invalid_attributes($errors, 'subject', 'Subject') ?>>
                        <option value="">اختر موضوعاً</option>
                        <?php foreach ($subjects as $key => $label): ?>
                        <option value="<?= $key ?>" <?= $key === $values['subject'] ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?= field_error($errors, 'subject', 'Subject') ?>

                    <label for="Message">الرسالة</label>
                    <textarea id="Message" name="message" rows="5" maxlength="500"
                        required<?= invalid_attributes($errors, 'message', 'Message') ?>><?= e($values['message']) ?></textarea>
                    <?= field_error($errors, 'message', 'Message') ?>

                    <button type="submit">إرسال</button>
                </form>
            </div>
            <iframe class="Contact_Map" title="موقع الوكالة على الخريطة" loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"
                src="https://www.openstreetmap.org/export/embed.html?bbox=3.03%2C36.75%2C3.07%2C36.78&amp;layer=mapnik&amp;marker=36.765%2C3.05"></iframe>
        </div>
    </main>
<?php require __DIR__ . '/includes/footer.php'; ?>
