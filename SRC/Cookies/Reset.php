<?php // Script 9.4 - reset.php

// Delete the cookies:
setcookie('font_size', '', time() - 600);
setcookie('font_color', '', time() - 600);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Settings</title>
</head>

<body>
    <p>Your settings have been reset! Click
        <a href="Font_Setting.php">here</a> to
        go back to the main page.
    </p>
</body>

</html>