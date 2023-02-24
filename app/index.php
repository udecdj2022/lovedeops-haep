<?php
$servername = "148.213.5.80:3306";
$database = "siceuc";
$username = "root";
$password = "secret";
// Create connection
$conn = mysqli_connect($servername, $username, $password, $database);
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
echo "Connected successfully";
mysqli_close($conn);
?>
