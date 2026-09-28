<?php
$host = "localhost";
$user = "root";       // Aapka MySQL username
$password = "";       // Aapka MySQL password (default blank hota hai XAMPP me)
$dbname = "pglife";

$conn = mysqli_connect($host, $user, $password, $dbname);

if (!$conn) {
    die("Database Connection Failed: " . mysqli_connect_error());
}
?>