<?php
require_once "db_connect.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Input values secure karna
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $college_name = mysqli_real_escape_string($conn, $_POST['college_name']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);

    // Password ko secure karne ke liye Hash banayein
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check karein ki email pehle se registered hai ya nahi
    $check_email = "SELECT * FROM users WHERE email='$email' OR phone='$phone'";
    $result = mysqli_query($conn, $check_email);

    if (mysqli_num_rows($result) > 0) {
        echo "Email ya Phone number pehle se registered hai!";
    } else {
        // Database me Data Insert karein
        $sql = "INSERT INTO users (full_name, phone, email, password, college_name, gender) 
                VALUES ('$full_name', '$phone', '$email', '$hashed_password', '$college_name', '$gender')";

        if (mysqli_query($conn, $sql)) {
            echo "Account successfully create ho gaya hai! <a href='index.php'>Login karein</a>";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }
}
?>