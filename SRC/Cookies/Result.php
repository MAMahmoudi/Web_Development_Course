<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style type="text/css">
        div {
            <?php // Script 9.2 - view_settings.php

            // Check for a font_size value:
            if (isset($_COOKIE['font_size'])) {
                print "\t\tfont-size: " .
                    htmlentities($_COOKIE['font_size']) . ";\n";
            } else {
                print "\t\tfont-size: medium;";
            }

            // Check for a font_color value:
            if (isset($_COOKIE['font_color'])) {
                print "\t\tbackground-color: #" .
                    htmlentities($_COOKIE['font_color']) . ";\n";
            } else {
                print "\t\tcolor: #000;";
            }
            ?>
        }
    </style>
</head>

<body>

    <div>
        Lorem ipsum, dolor sit amet consectetur adipisicing elit. Odio quisquam quis laudantium harum delectus ut, atque ex deserunt quaerat perspiciatis architecto saepe tempore unde placeat totam officiis, dolorum at necessitatibus!
    </div>
    <a href="Font_Setting.php">Customize Your Settings</a>
    <a href="Reset.php">Reset Your Settings</a>
</body>

</html>