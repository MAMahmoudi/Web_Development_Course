<?php
session_start();
if (isset($_POST['userid']) && isset($_POST['password'])) {
    // if the user has just tried to log in
    $userid = $_POST['userid'];
    $password = $_POST['password'];
    if ($userid == 'admin' && $password == '123') {
        // if they are in the database register the user id
        $_SESSION['valid_user'] = $userid;
        $_SESSION['pic'] = $path;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Authentification.css">
    <title>Document</title>
</head>

<body>
    <h1>Home Page</h1>
    <?php
    if (isset($_SESSION['valid_user'])) {
        echo '<p>You are logged in as: ' . $_SESSION['valid_user'] . ' <br />';
        echo '<a href="logout.php">Log out</a></p>';
    } else {
        if (isset($userid)) {
            // if they've tried and failed to log in
            echo '<p>Could not log you in.</p>';
        } else {
            // they have not tried to log in yet or have logged out
            echo '<p>You are not logged in.</p>';
        }
        // provide form to log in
        echo '<form action="Authentification.php" method="post">';
        echo '<fieldset>';
        echo '<legend>Login Now!</legend>';
        echo '<p><label for="userid">UserID:</label>';
        echo '<input type="text" name="userid" id="userid" size="30"/></p>';
        echo '<p><label for="password">Password:</label>';
        echo '<input type="password" name="password" id="password" size="30"/></p>';
        echo '</fieldset>';
        echo '<button type="submit" name="login">Login</button>';
        echo '</form>';
    }
    ?>
    <p><a href="Members_Only.php">Go to Members Section</a></p>
</body>

</html>