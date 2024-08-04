<?php
setcookie('Laguage', $_POST['Laguage'], time() - 60 * 60 * 24 * 7);
$_SESSION = array();
header("Location: Session.php");
exit();
