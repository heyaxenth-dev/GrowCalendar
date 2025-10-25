<?php 

$localhost = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "growcalendar_db";

// Establishing connection
$conn = mysqli_connect($localhost, $dbusername, $dbpassword, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>