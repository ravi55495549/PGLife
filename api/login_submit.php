<?php

session_start();

require_once __DIR__ . "/../includes/database_connect.php";

header('Content-Type: application/json');

$response = array(
    "success" => false,
    "message" => ""
);

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode([
        "success" => false,
        "message" => "Invalid request method."
    ]);
    exit;
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    echo json_encode([
        "success" => false,
        "message" => "Please enter both email and password."
    ]);
    exit;
}

$email = mysqli_real_escape_string($conn, $email);

$sql = "SELECT id, full_name, password FROM users WHERE email='$email'";
$result = mysqli_query($conn, $sql);

if (!$result) {
    echo json_encode([
        "success" => false,
        "message" => "Database query failed."
    ]);
    exit;
}

if (mysqli_num_rows($result) !== 1) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid email or password."
    ]);
    exit;
}

$row = mysqli_fetch_assoc($result);

if (!password_verify($password, $row['password'])) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid email or password."
    ]);
    exit;
}

session_regenerate_id(true);

$_SESSION['user_id'] = $row['id'];
$_SESSION['full_name'] = $row['full_name'];

echo json_encode([
    "success" => true,
    "message" => "Login successful!"
]);

exit;
?>