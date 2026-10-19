<?php
// Login (Lab 7): checks the e-mail and the password, then stores the user in the session.
require __DIR__ . '/includes/functions.php';
require __DIR__ . '/includes/queries.php';
require __DIR__ . '/includes/auth.php';
require __DIR__ . '/includes/validation.php';

const REMEMBER_COOKIE = 'syaha_email';   // a harmless preference: the e-mail typed last time
const REMEMBER_DAYS = 30;

// already logged in: nothing to do here
if (current_user() !== null) {
    redirect(is_admin() ? 'admin/index.php' : 'my_bookings.php');
}

$next = input('next', $_SERVER['REQUEST_METHOD'] === 'POST' ? 'post' : 'get');
$values = ['email' => $_COOKIE[REMEMBER_COOKIE] ?? '', 'password' => ''];
$remember = isset($_COOKIE[REMEMBER_COOKIE]);
$errors = [];
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $values = ['email' => input('email'), 'password' => $_POST['password'] ?? ''];
    $remember = isset($_POST['remember']);
    $errors = validate($values, ['email' => ['required', 'email'], 'password' => ['required']]);

    if (login_wait() > 0) {
        $error = 'محاولات كثيرة. أعيدوا المحاولة بعد ' . login_wait() . ' ثانية.';
    } elseif (!$errors) {
        $user = find_user_by_email($values['email']);
        // the same message whether the e-mail or the password is wrong: do not tell which accounts exist
        if ($user === null || !password_verify($values['password'], $user['password_hash'])) {
            login_failed();
            $error = 'البريد الإلكتروني أو كلمة المرور غير صحيحة.';
        } else {
            // PHP may recommend a stronger algorithm than the one used for this hash: update it now
            if (password_needs_rehash($user['password_hash'], PASSWORD_DEFAULT)) {
                update_password_hash((int) $user['id'], $values['password']);
            }
            login_user($user);

            // "remember my e-mail": a cookie with the e-mail only (never the password, never the role)
            setcookie(REMEMBER_COOKIE, $remember ? $values['email'] : '', [
                'expires' => $remember ? time() + REMEMBER_DAYS * 24 * 3600 : time() - 3600,
                'path' => '/',
                'httponly' => true,
                'samesite' => 'Lax',
                'secure' => !empty($_SERVER['HTTPS']),
            ]);

            flash('Success', 'مرحباً ' . $user['full_name'] . '!');
            redirect(safe_next($next, is_admin() ? 'admin/index.php' : 'my_bookings.php'));
        }
    }
    http_response_code($error !== '' || $errors ? 422 : 200);
}
$flashes = take_flashes();
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
    <link rel="stylesheet" href="../Lab_4+/Interactions.css">
    <link rel="stylesheet" href="backend.css">
    <title>سياحة | تسجيل الدخول</title>
</head>

<body>
    <section class="Logins">
        <a href="index.php" class="Back_Home">&larr; الرئيسية</a>
        <div class="Account_Login">
            <form class="Form" action="login.php" method="post" dir="rtl">
                <h1>تسجيل الدخول</h1>
                <?php foreach ($flashes as [$type, $message]): ?>
                <p class="Login_Notice Flash_<?= e($type) ?>"><?= e($message) ?></p>
                <?php endforeach; ?>
                <?php if ($error !== ''): ?>
                <p class="Login_Notice Flash_Error" role="alert"><?= e($error) ?></p>
                <?php endif; ?>
                <input type="hidden" name="next" value="<?= e($next) ?>" />
                <input type="email" name="email" value="<?= e($values['email']) ?>" placeholder="البريد الإلكتروني"
                    class="Email_Password" autocomplete="username" data-email-only required
                    aria-label="البريد الإلكتروني"<?= invalid_attributes($errors, 'email', 'Login_Email') ?> />
                <?= field_error($errors, 'email', 'Login_Email') ?>
                <input type="password" name="password" placeholder="كلمة المرور" class="Email_Password"
                    autocomplete="current-password" required aria-label="كلمة المرور"<?= invalid_attributes($errors, 'password', 'Login_Password') ?> />
                <?= field_error($errors, 'password', 'Login_Password') ?>
                <label class="Remember">
                    <input type="checkbox" name="remember" <?= $remember ? 'checked' : '' ?> />
                    تذكر بريدي الإلكتروني
                </label>
                <button type="submit" class="LoginBtn">دخول</button>
            </form>
            <a href="register.php" class="CreateAccountBtn">إنشاء حساب جديد</a>
        </div>
    </section>
    <script src="../Lab_4+/Login_Form.js"></script>
</body>

</html>
