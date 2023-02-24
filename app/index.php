<?php
$servername = "10.152.183.163:3306";
$database = "febrero";
$username = "root";
$password = "super-secret-password";
// Create connection
$conn = mysqli_connect($servername, $username, $password, $database);
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
echo "Connected successfully";
mysqli_close($conn);
?>
