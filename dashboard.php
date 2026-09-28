<?php
session_start();
require "includes/database_connect.php";

// Check whether user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit();
}

// Get logged-in user's ID
$user_id = $_SESSION["user_id"];

// Get properties that the logged-in user is interested in
$interested_property_ids = array();

$properties = [

    1 => [
        "name" => "Navkar Paying Guest",
        "address" => "44, Juhu Scheme, Juhu, Mumbai, Maharashtra 400058",
        "gender" => "male",
        "rent" => 9500,
        "image" => "img/properties/1/1d4f0757fdb86d5f.jpg",
        "rating" => 4.5
    ],

    2 => [
        "name" => "Ganpati Paying Guest",
        "address" => "Police Beat, Sainath Complex, Besides, SV Rd, Daulat Nagar, Borivali East, Mumbai - 400066",
        "gender" => "unisex",
        "rent" => 8500,
        "image" => "img/properties/1/eace7b9114fd6046.jpg",
        "rating" => 4.3
    ],

    3 => [
        "name" => "PG for Girls Borivali West",
        "address" => "Plot no.258/D4, Gorai no.2, Borivali West, Mumbai, Maharashtra 400092",
        "gender" => "female",
        "rent" => 8000,
        "image" => "img/properties/1/46ebbb537aa9fb0a.jpg",
        "rating" => 3.5
    ]

];

$sql = "SELECT property_id
        FROM interested_users_properties
        WHERE user_id = $user_id";

$result = mysqli_query($conn, $sql);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $interested_property_ids[] = (int) $row['property_id'];
    }
}

// Get user's details from database
$sql = "SELECT id, full_name, email, phone, college_name, gender
        FROM users
        WHERE id = $user_id";

$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) !== 1) {
    die("User details not found.");
}

$user = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard | PG Life</title>

    <link href="css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://use.fontawesome.com/releases/v5.11.2/css/all.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,600;0,700;0,800;1,300;1,400;1,600;1,700;1,800&display=swap" rel="stylesheet" />
    <link href="css/common.css" rel="stylesheet" />
    <link href="css/dashboard.css" rel="stylesheet" />
</head>

<body>

    <div class="header sticky-top">
        <nav class="navbar navbar-expand-md navbar-light">

            <a class="navbar-brand" href="index.php">
                <img src="img/logo.png" />
            </a>

            <button class="navbar-toggler" type="button"
                data-toggle="collapse"
                data-target="#my-navbar">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse justify-content-end" id="my-navbar">
                <ul class="navbar-nav">

                    <div class="nav-name">
                        Hi, <?php echo htmlspecialchars($user['full_name']); ?>
                    </div>

                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">
                            <i class="fas fa-user"></i> Dashboard
                        </a>
                    </li>

                    <div class="nav-vl"></div>

                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </li>

                </ul>
            </div>

        </nav>
    </div>


    <div id="loading">
    </div>


    <nav aria-label="breadcrumb">
        <ol class="breadcrumb py-2">

            <li class="breadcrumb-item">
                <a href="index.php">Home</a>
            </li>

            <li class="breadcrumb-item active" aria-current="page">
                Dashboard
            </li>

        </ol>
    </nav>


    <div class="my-profile page-container">

        <h1>My Profile</h1>

        <div class="row">

            <div class="col-md-3 profile-img-container">
                <i class="fas fa-user profile-img"></i>
            </div>

            <div class="col-md-9">

                <div class="row no-gutters justify-content-between align-items-end">

                    <div class="profile">

                        <div class="name">
                            <?php echo htmlspecialchars($user['full_name']); ?>
                        </div>

                        <div class="email">
                            <?php echo htmlspecialchars($user['email']); ?>
                        </div>

                        <div class="phone">
                            <?php echo htmlspecialchars($user['phone']); ?>
                        </div>

                        <div class="college">
                            <?php echo htmlspecialchars($user['college_name']); ?>
                        </div>

                    </div>

                    <div class="edit">
                        <div class="edit-profile">
                            Edit Profile
                        </div>
                    </div>

                </div>

            </div>

        </div>

    </div>


    <div class="my-interested-properties">

    <div class="page-container">

        <h1>My Interested Properties</h1>

        <?php if (empty($interested_property_ids)) { ?>

            <div class="text-center py-4">
                <p>You have not interested in any property yet.</p>
            </div>

        <?php } else { ?>

            <?php foreach ($interested_property_ids as $property_id) { ?>

                <?php

                // Make sure property exists
                if (!isset($properties[$property_id])) {
                    continue;
                }

                $property = $properties[$property_id];

                ?>

                <div class="property-card property-id-<?= $property_id ?> row">

                    <div class="image-container col-md-4">

                        <img
                            src="<?= htmlspecialchars($property['image']) ?>"
                            alt="<?= htmlspecialchars($property['name']) ?>"
                        />

                    </div>


                    <div class="content-container col-md-8">

                        <div class="row no-gutters justify-content-between">

                            <div
                                class="star-container"
                                title="<?= $property['rating'] ?>"
                            >

                                <?php

                                $rating = $property['rating'];

                                for ($i = 0; $i < 5; $i++) {

                                    if ($rating >= $i + 0.8) {

                                ?>

                                        <i class="fas fa-star"></i>

                                <?php

                                    } elseif ($rating >= $i + 0.3) {

                                ?>

                                        <i class="fas fa-star-half-alt"></i>

                                <?php

                                    } else {

                                ?>

                                        <i class="far fa-star"></i>

                                <?php

                                    }

                                }

                                ?>

                            </div>


                            <div class="interested-container">

                                <i
                                    class="is-interested-image fas fa-heart"
                                    property_id="<?= $property_id ?>"
                                ></i>

                            </div>

                        </div>


                        <div class="detail-container">

                            <div class="property-name">

                                <?= htmlspecialchars($property['name']) ?>

                            </div>


                            <div class="property-address">

                                <?= htmlspecialchars($property['address']) ?>

                            </div>


                            <div class="property-gender">

                                <?php

                                if ($property['gender'] === "male") {

                                    $gender_image = "img/male.png";

                                } elseif ($property['gender'] === "female") {

                                    $gender_image = "img/female.png";

                                } else {

                                    $gender_image = "img/unisex.png";

                                }

                                ?>

                                <img
                                    src="<?= $gender_image ?>"
                                    alt="<?= htmlspecialchars($property['gender']) ?>"
                                >

                            </div>

                        </div>


                        <div class="row no-gutters">

                            <div class="rent-container col-6">

                                <div class="rent">

                                    Rs <?= number_format($property['rent']) ?>/-

                                </div>

                                <div class="rent-unit">

                                    per month

                                </div>

                            </div>


                            <div class="button-container col-6">

                                <a
                                    href="property_detail.php?id=<?= $property_id ?>"
                                    class="btn btn-primary"
                                >
                                    View
                                </a>

                            </div>

                        </div>

                    </div>

                </div>

            <?php } ?>

        <?php } ?>

    </div>

</div>


    <div class="footer">

        <div class="page-container footer-container">

            <div class="footer-cities">

                <div class="footer-city">
                    <a href="property_list.php">PG in Delhi</a>
                </div>

                <div class="footer-city">
                    <a href="property_list.php">PG in Mumbai</a>
                </div>

                <div class="footer-city">
                    <a href="property_list.php">PG in Bangalore</a>
                </div>

                <div class="footer-city">
                    <a href="property_list.php">PG in Hyderabad</a>
                </div>

            </div>

            <div class="footer-copyright">
                © 2020 Copyright PG Life
            </div>

        </div>

    </div>


    <script type="text/javascript" src="js/jquery.js"></script>
    <script type="text/javascript" src="js/bootstrap.min.js"></script>
    <script src="js/dashboard.js"></script>

</body>

</html>