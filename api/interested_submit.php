<?php

session_start();

require_once __DIR__ . "/../includes/database_connect.php";

header("Content-Type: application/json");


/*
|--------------------------------------------------------------------------
| CHECK LOGIN
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION['user_id'])) {

    echo json_encode([
        "success" => false,
        "login_required" => true,
        "message" => "Please login first."
    ]);

    exit;
}


/*
|--------------------------------------------------------------------------
| CHECK REQUEST METHOD
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    echo json_encode([
        "success" => false,
        "message" => "Invalid request method."
    ]);

    exit;
}


/*
|--------------------------------------------------------------------------
| GET DATA
|--------------------------------------------------------------------------
*/

$user_id = (int) $_SESSION['user_id'];
$property_id = isset($_POST['property_id'])
    ? (int) $_POST['property_id']
    : 0;


/*
|--------------------------------------------------------------------------
| VALIDATE PROPERTY ID
|--------------------------------------------------------------------------
*/

if ($property_id <= 0) {

    echo json_encode([
        "success" => false,
        "message" => "Invalid property."
    ]);

    exit;
}


/*
|--------------------------------------------------------------------------
| CHECK CURRENT INTEREST
|--------------------------------------------------------------------------
*/

$sql = "SELECT id
        FROM interested_users_properties
        WHERE user_id = $user_id
        AND property_id = $property_id
        LIMIT 1";

$result = mysqli_query($conn, $sql);

if (!$result) {

    echo json_encode([
        "success" => false,
        "message" => "Database error."
    ]);

    exit;
}


/*
|--------------------------------------------------------------------------
| REMOVE INTEREST
|--------------------------------------------------------------------------
*/

if (mysqli_num_rows($result) > 0) {

    $row = mysqli_fetch_assoc($result);

    $interest_id = (int) $row['id'];

    $sql = "DELETE FROM interested_users_properties
            WHERE id = $interest_id";

    if (!mysqli_query($conn, $sql)) {

        echo json_encode([
            "success" => false,
            "message" => "Unable to remove interest."
        ]);

        exit;
    }

    $is_interested = false;

}


/*
|--------------------------------------------------------------------------
| ADD INTEREST
|--------------------------------------------------------------------------
*/

else {

    $sql = "INSERT INTO interested_users_properties
            (user_id, property_id)
            VALUES
            ($user_id, $property_id)";

    if (!mysqli_query($conn, $sql)) {

        echo json_encode([
            "success" => false,
            "message" => "Unable to add interest."
        ]);

        exit;
    }

    $is_interested = true;
}


/*
|--------------------------------------------------------------------------
| GET UPDATED INTEREST COUNT
|--------------------------------------------------------------------------
*/

$sql = "SELECT COUNT(*) AS interested_count
        FROM interested_users_properties
        WHERE property_id = $property_id";

$result = mysqli_query($conn, $sql);

if (!$result) {

    echo json_encode([
        "success" => false,
        "message" => "Unable to get interest count."
    ]);

    exit;
}


$row = mysqli_fetch_assoc($result);

$interested_count = (int) $row['interested_count'];


/*$sql = "SELECT id FROM properties WHERE id = $property_id";
|--------------------------------------------------------------------------
| SEND RESPONSE
|--------------------------------------------------------------------------
*/

echo json_encode([
    "success" => true,
    "is_interested" => $is_interested,
    "interested_count" => $interested_count,
    "message" => $is_interested
        ? "Property added to your interested properties."
        : "Property removed from your interested properties."
]);

exit;

?>