<?php

header('Content-Type: application/json');

require("../includes/database_connect.php");

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        "success" => false,
        "message" => "Invalid request method."
    ]);
    exit;
}

// Get form data safely
$full_name = $_POST['full_name'] ?? '';
$phone = $_POST['phone'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$college_name = $_POST['college_name'] ?? '';
$gender = $_POST['gender'] ?? '';

// Check required fields
if (
    empty($full_name) ||
    empty($phone) ||
    empty($email) ||
    empty($password) ||
    empty($college_name) ||
    empty($gender)
) {
    echo json_encode([
        "success" => false,
        "message" => "Please fill all the required fields."
    ]);
    exit;
}

// Hash password
$password = password_hash($password, PASSWORD_DEFAULT);

// Check whether email already exists
$sql = "SELECT * FROM users WHERE email='$email'";
$result = mysqli_query($conn, $sql);

if (!$result) {
    echo json_encode([
        "success" => false,
        "message" => "Something went wrong!"
    ]);
    exit;
}

$row_count = mysqli_num_rows($result);

if ($row_count != 0) {
    echo json_encode([
        "success" => false,
        "message" => "This email id is already registered with us!"
    ]);
    exit;
}

// Insert new user
$sql = "INSERT INTO users 
        (email, password, full_name, phone, gender, college_name) 
        VALUES 
        ('$email', '$password', '$full_name', '$phone', '$gender', '$college_name')";

$result = mysqli_query($conn, $sql);

if (!$result) {
    echo json_encode([
        "success" => false,
        "message" => "Something went wrong!"
    ]);
    exit;
}

// Successful signup
echo json_encode([
    "success" => true,
    "message" => "Your account has been created successfully!"
]);

mysqli_close($conn);
?>