<?php
session_start();
if (isset($_POST['userid']) && isset($_POST['password'])) {
    // if the user has just tried to log in
    $userid = $_POST['userid'];
    $password = $_POST['password'];
    if ($userid == 'admin' && $password == '123') {
        // if they are in the database register the user id
        $_SESSION['valid_user'] = $userid;
        header("Location: Cookie.php");
        exit();
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
    <form action="Session.php" method="post">
        <label for="userid">UserID:</label>
        <input type="text" name="userid" id="userid" size="30" /> <br> <br>
        <label for="password">Password:</label>
        <input type="password" name="password" id="password" size="30" /> <br> <br>
        <button type="submit" name="login">Login</button>
    </form>
</body>

</html>
