<?php
// The login page of Lab 2. The forms are not processed yet: logging in is the subject of Lab 7 (sessions).
?>
<!DOCTYPE html>
<html lang="en">

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
    <title>Login</title>
</head>

<body>
    <section class="Logins">
        <a href="index.php" class="Back_Home">&larr; Back to home</a>
        <div class="Account_Login">
            <form class="Form" name="Account_Login" method="POST" autocomplete="off">
                <h1>Account login</h1>
                <input type="email" placeholder="Enter your email or phone number" name="mostakhdim"
                    class="Email_Password" required>
                <input type="password" placeholder="Enter your password" name="kalimat_sir" class="Email_Password"
                    required>
                <button type="submit" class="LoginBtn" id="Account_Login_Btn_ID"
                    name="AccountLogin_submit">Login</button>
            </form>
            <a href="#" class="Forgot_Password">Forgot your password?</a>
            <a href="#" class="CreateAccountBtn">Create new account</a>
        </div>
        <div class="Page_Login">
            <form class="Form" name="Page_Login" method="POST" autocomplete="off">
                <h1>Page login</h1>
                <input type="email" placeholder="Enter your email or phone number" name="mostakhdim_safeha"
                    class="Email_Password" required>
                <input type="password" placeholder="Enter your password" name="kalimat_sir_safeha"
                    class="Email_Password" required>
                <button type="submit" class="LoginBtn" id="Page_Login_Btn_ID" name="PageLogin_submit">Login</button>
            </form>
            <a href="#" class="Forgot_Password">Forgot your password?</a>
            <a href="#" class="CreateAccountBtn">Create new page</a>
        </div>
    </section>
    <script src="../Lab_4+/Login_Form.js"></script>
</body>

</html>
