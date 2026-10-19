<?php
// Registration of a customer account (Lab 7).
require __DIR__ . '/includes/functions.php';
require __DIR__ . '/includes/queries.php';
require __DIR__ . '/includes/auth.php';
require __DIR__ . '/includes/validation.php';

if (current_user() !== null) {
    redirect('my_bookings.php');
}

$rules = [
    'name' => ['required', 'min:3', 'max:100'],
    'email' => ['required', 'email', 'max:255'],
    'password' => ['required', 'min:8', 'max:72'],   // bcrypt only uses the first 72 bytes
    'confirm' => ['required'],
];
$values = array_fill_keys(array_keys($rules), '');
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach (['name', 'email'] as $field) {
        $values[$field] = input($field);
    }
    // a password is never trimmed: its spaces are part of it
    $values['password'] = is_string($_POST['password'] ?? null) ? $_POST['password'] : '';
    $values['confirm'] = is_string($_POST['confirm'] ?? null) ? $_POST['confirm'] : '';
    $errors = validate($values, $rules);

    if (!isset($errors['password']) && !preg_match('/[A-Za-z]/', $values['password'])) {
        $errors['password'] = 'كلمة المرور يجب أن تحتوي على حرف واحد على الأقل.';
    } elseif (!isset($errors['password']) && !preg_match('/[0-9]/', $values['password'])) {
        $errors['password'] = 'كلمة المرور يجب أن تحتوي على رقم واحد على الأقل.';
    }
    if (!isset($errors['confirm']) && $values['confirm'] !== $values['password']) {
        $errors['confirm'] = 'كلمتا المرور غير متطابقتين.';
    }
    if (!isset($errors['email']) && find_user_by_email($values['email']) !== null) {
        $errors['email'] = 'يوجد حساب بهذا البريد الإلكتروني. سجلوا الدخول.';
    }

    if (!$errors) {
        $id = create_user($values['name'], $values['email'], $values['password']);
        login_user(['id' => $id, 'full_name' => $values['name'], 'email' => $values['email'], 'role' => 'customer']);
        flash('Success', 'تم إنشاء حسابكم. مرحباً ' . $values['name'] . '!');
        redirect('my_bookings.php');
    }
    http_response_code(422);
    // the password fields are never sent back to the browser
    $values['password'] = $values['confirm'] = '';
}
?>
<!DOCTYPE html>
<html lang="ar">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Lab_2/Lab_2_Page.css">
    <link rel="stylesheet" href="../Lab_2/Logins.css">
    <link rel="stylesheet" href="../Lab_3/Logins_Responsive.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
        integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="backend.css">
    <title>سياحة | إنشاء حساب</title>
</head>

<body>
    <section class="Logins">
        <a href="index.php" class="Back_Home">&larr; الرئيسية</a>
        <div class="Account_Login Register">
            <form class="Register_Form" action="register.php" method="post" dir="rtl">
                <h1>إنشاء حساب</h1>
                <label for="Name">الإسم الكامل</label>
                <input type="text" id="Name" name="name" value="<?= e($values['name']) ?>" autocomplete="name"
                    required<?= invalid_attributes($errors, 'name', 'Name') ?> />
                <?= field_error($errors, 'name', 'Name') ?>

                <label for="Email">البريد الإلكتروني</label>
                <input type="email" id="Email" name="email" value="<?= e($values['email']) ?>" autocomplete="email"
                    required<?= invalid_attributes($errors, 'email', 'Email') ?> />
                <?= field_error($errors, 'email', 'Email') ?>

                <label for="Password">كلمة المرور (8 أحرف على الأقل، حرف ورقم)</label>
                <input type="password" id="Password" name="password" autocomplete="new-password" minlength="8"
                    required<?= invalid_attributes($errors, 'password', 'Password') ?> />
                <?= field_error($errors, 'password', 'Password') ?>

                <label for="Confirm">تأكيد كلمة المرور</label>
                <input type="password" id="Confirm" name="confirm" autocomplete="new-password"
                    required<?= invalid_attributes($errors, 'confirm', 'Confirm') ?> />
                <?= field_error($errors, 'confirm', 'Confirm') ?>

                <button type="submit" class="LoginBtn">إنشاء الحساب</button>
            </form>
            <a href="login.php" class="Forgot_Password">لديكم حساب؟ سجلوا الدخول</a>
        </div>
    </section>
</body>

</html>
