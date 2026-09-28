<?php
$host = "127.0.0.1";
$user = "root";
$password = "";
$dbname = "pglife";

$conn = mysqli_connect($host, $user, $password, $dbname);

if (!$conn) {
    die("Database Connection Failed: " . mysqli_connect_error());
}
?>