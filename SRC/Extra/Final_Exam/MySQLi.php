<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MySQLi</title>
</head>

<body>
    <main>
        <form action="MySQLi.php" method="post">
            <label for="Min_price">Min price:</label>
            <input type="text" name="Min_price" id="Min_price"> <br> <br>
            <label for="Max_price">Max price:</label>
            <input type="text" name="Max_price" id="Max_price"> <br> <br>
            <input type="submit" name="submit" value="Submit">
        </form>
    </main>
</body>

</html>
<?php
$My_Connection = new mysqli('localhost', 'root', '', 'Exam', 3366);
if (mysqli_connect_errno()) {
    echo 'Error: Could not connect to database. Please try again later.';
    exit;
} else {
    if (isset($_POST['submit'])) {
        if (empty($_POST['Min_price']) || empty($_POST['Max_price'])) {
            echo 'Please fill in all fields.';
            exit;
        } else {
            $Min_price = $_POST['Min_price'];
            $Max_price = $_POST['Max_price'];
            $query = "SELECT Product_Name, Product_Description, Price FROM `product` WHERE `Price` BETWEEN ? AND ?";
            $stmt = $My_Connection->prepare($query);
            $stmt->bind_param('ii', $Min_price, $Max_price);
            $stmt->execute();
            $stmt->bind_result($aaa, $bbb, $ccc);
            while ($stmt->fetch()) {
                echo "<h3>" . $aaa . "</h3>";
                echo "<p>" . $bbb . "</p>";
                echo "<h2> " . $ccc . "</h2>";
            }
        }
    }
}
