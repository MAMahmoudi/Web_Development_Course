<?php
if (isset(
    $_POST['font_size'],
    $_POST['font_color']
)) {
    // Send the cookies:
    setcookie(
        'font_size',
        $_POST['font_size']
    );
    setcookie(
        'font_color',
        $_POST['font_color']
    );
    // Message to be printed later:
    $msg = '<p>Your settings have been entered! Click <a href="Result.php">here</a>to see them in action.</p>';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Custom Setting</title>
</head>

<body>
    <?php
    if (isset($msg)) {
        print $msg;
    } ?>

    <p>Use this form to set your
        preferences:</p>
    <form action="Font_Setting.php" method="post">
        <select name="font_size">
            <option value="">Font Size</option>
            <option value="xx-small">xx-small
            </option>
            <option value="x-small">x-small
            </option>
            <option value="small">small
            </option>
            <option value="medium">medium
            </option>
            <option value="large">large
            </option>
            <option value="x-large">x-large
            </option>
            <option value="xx-large">xx-large
            </option>
        </select>
        <select name="font_color">
            <option value="">Font Color
            </option>
            <option value="999">Gray</option>
            <option value="0c0">Green</option>
            <option value="00f">Blue</option>
            <option value="c00">Red</option>
            <option value="000">Black</option>
        </select>
        <input type="submit" name="submit" value="Set My Preferences" />
    </form>
</body>

</html>