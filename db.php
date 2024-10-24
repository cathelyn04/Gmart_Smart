<?php
$servername = "localhost"; // or your server name
$username = "root"; // your database username (default for XAMPP)
$password = ""; // your database password (default for XAMPP is empty)
$dbname = "online_shop"; // your database name

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
