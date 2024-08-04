<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Authentification.css">
    <title>Document</title>
</head>

<body>
    <?php
    session_start();
    if (isset($_SESSION['valid_user'])) {
        // Display the following form if the user is already logged in.
        echo '   <form action="Cookie.php" method="post">';
        echo '   <label for="Laguage">Laguage:</label>';
        echo '   <input type="text" name="Laguage" id="Laguage" size="30" />';
        echo '   <button type="submit" name="Save">Save</button>';
        echo '  </form>';
    }
    if (isset($_POST['Laguage'])) {
        setcookie('Laguage', $_POST['Laguage'], time() + 60 * 60 * 24 * 7);
        if (isset($_COOKIE['Laguage'])) {
            switch ($_COOKIE['Laguage']) {
                case 'Arabic':
                    echo 'مرحبا';
                    break;
                case 'English':
                    echo 'Hello';
                    break;
                case 'French':
                    echo 'Bonjour';
                    break;
                default:
                    echo 'مرحبا';
                    break;
            }
        }
        echo '<br><a href="Logout.php">Logout</a>';
    }

    ?>
</body>

</html>