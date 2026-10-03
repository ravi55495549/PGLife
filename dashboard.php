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

// --------------------------------------------------
// PROPERTY DATA
// --------------------------------------------------

$properties = [

    1 => [
        "name" => "Navkar Paying Guest",
        "address" => "44, Juhu Scheme, Juhu, Mumbai, Maharashtra 400058",
        "gender" => "male",
        "rent" => 9500,
        "daily_rent" => 350,
        "image" => "img/properties/1/1d4f0757fdb86d5f.jpg",
        "rating" => 4.5
    ],

    2 => [
        "name" => "Ganpati Paying Guest",
        "address" => "Police Beat, Sainath Complex, Besides, SV Rd, Daulat Nagar, Borivali East, Mumbai - 400066",
        "gender" => "unisex",
        "rent" => 8500,
        "daily_rent" => 350,
        "image" => "img/properties/1/eace7b9114fd6046.jpg",
        "rating" => 4.3
    ],

    3 => [
        "name" => "PG for Girls Borivali West",
        "address" => "Plot no.258/D4, Gorai no.2, Borivali West, Mumbai, Maharashtra 400092",
        "gender" => "female",
        "rent" => 8000,
        "daily_rent" => 300,
        "image" => "img/properties/1/46ebbb537aa9fb0a.jpg",
        "rating" => 3.5
    ]

];


// --------------------------------------------------
// GET INTERESTED PROPERTIES
// --------------------------------------------------

$interested_property_ids = array();

$sql = "SELECT property_id
        FROM interested_users_properties
        WHERE user_id = $user_id";

$result = mysqli_query($conn, $sql);

if ($result) {

    while ($row = mysqli_fetch_assoc($result)) {

        $interested_property_ids[] = (int) $row['property_id'];

    }

}


// --------------------------------------------------
// GET USER DETAILS
// --------------------------------------------------

$sql = "SELECT id, full_name, email, phone, college_name, gender
        FROM users
        WHERE id = $user_id";

$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) !== 1) {
    die("User details not found.");
}

$user = mysqli_fetch_assoc($result);


// --------------------------------------------------
// GET USER BOOKINGS
// --------------------------------------------------

$bookings = array();

$booking_sql = "
    SELECT
        id,
        property_id,
        move_in_date,
        to_date,
        duration,
        rooms,
        adults,
        children,
        total_amount,
        booking_date
    FROM bookings
    WHERE user_id = $user_id
    ORDER BY booking_date DESC
";

$booking_result = mysqli_query($conn, $booking_sql);

if ($booking_result) {

    while ($booking_row = mysqli_fetch_assoc($booking_result)) {

        $bookings[] = $booking_row;

    }

}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Dashboard | PG Life</title>


    <link href="css/bootstrap.min.css" rel="stylesheet" />

    <link
        href="https://use.fontawesome.com/releases/v5.11.2/css/all.css"
        rel="stylesheet"
    />

    <link
        href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,600;0,700;0,800;1,300;1,400;1,600;1,700;1,800&display=swap"
        rel="stylesheet"
    />

    <link href="css/common.css" rel="stylesheet" />

    <link href="css/dashboard.css" rel="stylesheet" />


    <style>

        /* ==========================================
           MY BOOKINGS
        ========================================== */

        .my-bookings {
            padding: 35px 0;
            background-color: #f7f7f7;
        }

        .my-bookings h1 {
            margin-bottom: 25px;
        }

        .booking-card {
            background: #ffffff;
            border-radius: 8px;
            margin-bottom: 20px;
            padding: 25px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            border: 1px solid #eeeeee;
        }

        .booking-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 15px;
            margin-bottom: 20px;
            border-bottom: 1px solid #eeeeee;
        }

        .booking-property-name {
            font-size: 21px;
            font-weight: 600;
            color: #333333;
        }

        .booking-id {
            font-size: 13px;
            color: #888888;
        }

        .booking-detail {
            margin-bottom: 18px;
        }

        .booking-detail-label {
            font-size: 13px;
            color: #888888;
            margin-bottom: 5px;
        }

        .booking-detail-value {
            font-size: 16px;
            font-weight: 600;
            color: #333333;
        }

        .booking-total {
            background: #f8f9fa;
            border-radius: 6px;
            padding: 15px;
            margin-top: 5px;
        }

        .booking-total-label {
            font-size: 14px;
            color: #777777;
        }

        .booking-total-amount {
            font-size: 22px;
            font-weight: 700;
            color: #007bff;
        }

        .booking-view-button {
            margin-top: 15px;
        }

        .booking-view-button a {
            min-width: 130px;
        }


        /* ==========================================
           MOBILE
        ========================================== */

        @media (max-width: 767px) {

            .booking-card {
                padding: 18px;
            }

            .booking-card-header {
                display: block;
            }

            .booking-id {
                margin-top: 5px;
            }

            .booking-property-name {
                font-size: 18px;
            }

        }

    </style>

</head>


<body>


    <!-- ==========================================
         HEADER
    ========================================== -->

    <div class="header sticky-top">

        <nav class="navbar navbar-expand-md navbar-light">

            <a class="navbar-brand" href="index.php">

                <img src="img/logo.png" />

            </a>


            <button
                class="navbar-toggler"
                type="button"
                data-toggle="collapse"
                data-target="#my-navbar"
            >

                <span class="navbar-toggler-icon"></span>

            </button>


            <div
                class="collapse navbar-collapse justify-content-end"
                id="my-navbar"
            >

                <ul class="navbar-nav">


                    <div class="nav-name">

                        Hi,
                        <?php
                        echo htmlspecialchars($user['full_name']);
                        ?>

                    </div>


                    <li class="nav-item">

                        <a
                            class="nav-link"
                            href="dashboard.php"
                        >

                            <i class="fas fa-user"></i>
                            Dashboard

                        </a>

                    </li>


                    <div class="nav-vl"></div>


                    <li class="nav-item">

                        <a
                            class="nav-link"
                            href="logout.php"
                        >

                            <i class="fas fa-sign-out-alt"></i>
                            Logout

                        </a>

                    </li>


                </ul>

            </div>

        </nav>

    </div>


    <!-- ==========================================
         LOADING
    ========================================== -->

    <div id="loading">
    </div>


    <!-- ==========================================
         BREADCRUMB
    ========================================== -->

    <nav aria-label="breadcrumb">

        <ol class="breadcrumb py-2">


            <li class="breadcrumb-item">

                <a href="index.php">
                    Home
                </a>

            </li>


            <li
                class="breadcrumb-item active"
                aria-current="page"
            >

                Dashboard

            </li>


        </ol>

    </nav>


    <!-- ==========================================
         MY PROFILE
    ========================================== -->

    <div class="my-profile page-container">

        <h1>
            My Profile
        </h1>


        <div class="row">


            <div class="col-md-3 profile-img-container">

                <i class="fas fa-user profile-img"></i>

            </div>


            <div class="col-md-9">


                <div
                    class="row no-gutters justify-content-between align-items-end"
                >


                    <div class="profile">


                        <div class="name">

                            <?php
                            echo htmlspecialchars(
                                $user['full_name']
                            );
                            ?>

                        </div>


                        <div class="email">

                            <?php
                            echo htmlspecialchars(
                                $user['email']
                            );
                            ?>

                        </div>


                        <div class="phone">

                            <?php
                            echo htmlspecialchars(
                                $user['phone']
                            );
                            ?>

                        </div>


                        <div class="college">

                            <?php
                            echo htmlspecialchars(
                                $user['college_name']
                            );
                            ?>

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


    <!-- ==========================================
         MY BOOKINGS
    ========================================== -->

    <div class="my-bookings">

        <div class="page-container">


            <h1>
                My Bookings
            </h1>


            <?php if (empty($bookings)) { ?>


                <div class="text-center py-4">

                    <i
                        class="fas fa-calendar-check"
                        style="
                            font-size: 45px;
                            color: #cccccc;
                            margin-bottom: 15px;
                        "
                    ></i>


                    <p>
                        You have not made any booking yet.
                    </p>


                </div>


            <?php } else { ?>


                <?php foreach ($bookings as $booking) { ?>


                    <?php

                    /*
                     * Find property details using property_id
                     */

                    $booking_property_id =
                        (int) $booking['property_id'];


                    if (isset($properties[$booking_property_id])) {

                        $booking_property =
                            $properties[$booking_property_id];

                    } else {

                        $booking_property = [

                            "name" =>
                                "Property #" .
                                $booking_property_id,

                            "daily_rent" => 0

                        ];

                    }


                    /*
                     * Format dates
                     */

                    $from_date = date(
                        "d M Y",
                        strtotime($booking['move_in_date'])
                    );


                    $to_date = date(
                        "d M Y",
                        strtotime($booking['to_date'])
                    );


                    $booked_on = date(
                        "d M Y, h:i A",
                        strtotime($booking['booking_date'])
                    );


                    ?>


                    <div class="booking-card">


                        <!-- BOOKING HEADER -->

                        <div class="booking-card-header">


                            <div>

                                <div class="booking-property-name">

                                    <i class="fas fa-home"></i>

                                    <?= htmlspecialchars(
                                        $booking_property['name']
                                    ) ?>

                                </div>


                                <div class="booking-id">

                                    Booking ID:
                                    #<?= (int) $booking['id'] ?>

                                </div>

                            </div>


                        </div>


                        <!-- BOOKING DETAILS -->

                        <div class="row">


                            <!-- FROM -->

                            <div class="col-md-3 col-6">

                                <div class="booking-detail">

                                    <div class="booking-detail-label">

                                        <i class="fas fa-calendar-alt"></i>
                                        From

                                    </div>


                                    <div class="booking-detail-value">

                                        <?= $from_date ?>

                                    </div>

                                </div>

                            </div>


                            <!-- TO -->

                            <div class="col-md-3 col-6">

                                <div class="booking-detail">

                                    <div class="booking-detail-label">

                                        <i class="fas fa-calendar-alt"></i>
                                        To

                                    </div>


                                    <div class="booking-detail-value">

                                        <?= $to_date ?>

                                    </div>

                                </div>

                            </div>


                            <!-- DURATION -->

                            <div class="col-md-3 col-6">

                                <div class="booking-detail">

                                    <div class="booking-detail-label">

                                        <i class="fas fa-clock"></i>
                                        Duration

                                    </div>


                                    <div class="booking-detail-value">

                                        <?= (int) $booking['duration'] ?>
                                        Days

                                    </div>

                                </div>

                            </div>


                            <!-- ROOMS -->

                            <div class="col-md-3 col-6">

                                <div class="booking-detail">

                                    <div class="booking-detail-label">

                                        <i class="fas fa-door-open"></i>
                                        Rooms

                                    </div>


                                    <div class="booking-detail-value">

                                        <?= (int) $booking['rooms'] ?>

                                    </div>

                                </div>

                            </div>


                            <!-- ADULTS -->

                            <div class="col-md-3 col-6">

                                <div class="booking-detail">

                                    <div class="booking-detail-label">

                                        <i class="fas fa-user"></i>
                                        Adults

                                    </div>


                                    <div class="booking-detail-value">

                                        <?= (int) $booking['adults'] ?>

                                    </div>

                                </div>

                            </div>


                            <!-- CHILDREN -->

                            <div class="col-md-3 col-6">

                                <div class="booking-detail">

                                    <div class="booking-detail-label">

                                        <i class="fas fa-child"></i>
                                        Children

                                    </div>


                                    <div class="booking-detail-value">

                                        <?= (int) $booking['children'] ?>

                                    </div>

                                </div>

                            </div>


                            <!-- DAILY RATE -->

                            <div class="col-md-3 col-6">

                                <div class="booking-detail">

                                    <div class="booking-detail-label">

                                        <i class="fas fa-rupee-sign"></i>
                                        Daily Rate

                                    </div>


                                    <div class="booking-detail-value">

                                        Rs
                                        <?= number_format(
                                            $booking_property['daily_rent']
                                        ) ?>
                                        /day

                                    </div>

                                </div>

                            </div>


                            <!-- BOOKED ON -->

                            <div class="col-md-3 col-6">

                                <div class="booking-detail">

                                    <div class="booking-detail-label">

                                        <i class="fas fa-history"></i>
                                        Booked On

                                    </div>


                                    <div class="booking-detail-value">

                                        <?= $booked_on ?>

                                    </div>

                                </div>

                            </div>


                        </div>


                        <!-- TOTAL -->

                        <div class="booking-total">


                            <div class="row align-items-center">


                                <div class="col-md-8">

                                    <div class="booking-total-label">

                                        Total Booking Amount

                                    </div>


                                    <div class="booking-total-amount">

                                        Rs
                                        <?= number_format(
                                            (int) $booking['total_amount']
                                        ) ?>

                                    </div>

                                </div>


                                <div class="col-md-4 text-md-right">


                                    <div class="booking-view-button">

                                        <a
                                            href="property_detail.php?id=<?= $booking_property_id ?>"
                                            class="btn btn-primary"
                                        >

                                            View Property

                                        </a>

                                    </div>


                                </div>


                            </div>


                        </div>


                    </div>


                <?php } ?>


            <?php } ?>


        </div>

    </div>


    <!-- ==========================================
         MY INTERESTED PROPERTIES
    ========================================== -->

    <div class="my-interested-properties">

        <div class="page-container">


            <h1>
                My Interested Properties
            </h1>


            <?php if (empty($interested_property_ids)) { ?>


                <div class="text-center py-4">

                    <p>
                        You have not interested in any property yet.
                    </p>

                </div>


            <?php } else { ?>


                <?php foreach (
                    $interested_property_ids
                    as $property_id
                ) { ?>


                    <?php

                    // Make sure property exists

                    if (!isset($properties[$property_id])) {
                        continue;
                    }

                    $property =
                        $properties[$property_id];

                    ?>


                    <div
                        class="property-card property-id-<?= $property_id ?> row"
                    >


                        <div class="image-container col-md-4">


                            <img
                                src="<?= htmlspecialchars(
                                    $property['image']
                                ) ?>"
                                alt="<?= htmlspecialchars(
                                    $property['name']
                                ) ?>"
                            />


                        </div>


                        <div class="content-container col-md-8">


                            <div
                                class="row no-gutters justify-content-between"
                            >


                                <div
                                    class="star-container"
                                    title="<?= $property['rating'] ?>"
                                >


                                    <?php

                                    $rating =
                                        $property['rating'];

                                    for (
                                        $i = 0;
                                        $i < 5;
                                        $i++
                                    ) {


                                        if (
                                            $rating >=
                                            $i + 0.8
                                        ) {


                                    ?>

                                            <i
                                                class="fas fa-star"
                                            ></i>


                                    <?php

                                        } elseif (
                                            $rating >=
                                            $i + 0.3
                                        ) {


                                    ?>

                                            <i
                                                class="fas fa-star-half-alt"
                                            ></i>


                                    <?php

                                        } else {


                                    ?>

                                            <i
                                                class="far fa-star"
                                            ></i>


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

                                    <?= htmlspecialchars(
                                        $property['name']
                                    ) ?>

                                </div>


                                <div class="property-address">

                                    <?= htmlspecialchars(
                                        $property['address']
                                    ) ?>

                                </div>


                                <div class="property-gender">


                                    <?php

                                    if (
                                        $property['gender']
                                        === "male"
                                    ) {

                                        $gender_image =
                                            "img/male.png";

                                    } elseif (
                                        $property['gender']
                                        === "female"
                                    ) {

                                        $gender_image =
                                            "img/female.png";

                                    } else {

                                        $gender_image =
                                            "img/unisex.png";

                                    }

                                    ?>


                                    <img
                                        src="<?= $gender_image ?>"
                                        alt="<?= htmlspecialchars(
                                            $property['gender']
                                        ) ?>"
                                    >


                                </div>


                            </div>


                            <div class="row no-gutters">


                                <div
                                    class="rent-container col-6"
                                >


                                   <div class="rent">

                                        Rs
                                        <?= number_format($property['rent']) ?>/-

                                    </div>


                                    <div class="rent-unit">

                                        per month

                                    </div>


                                </div>


                                <div
                                    class="button-container col-6"
                                >


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


    <!-- ==========================================
         FOOTER
    ========================================== -->

    <div class="footer">


        <div class="page-container footer-container">


            <div class="footer-cities">


                <div class="footer-city">

                    <a href="property_list.php">
                        PG in Delhi
                    </a>

                </div>


                <div class="footer-city">

                    <a href="property_list.php">
                        PG in Mumbai
                    </a>

                </div>


                <div class="footer-city">

                    <a href="property_list.php">
                        PG in Bangalore
                    </a>

                </div>


                <div class="footer-city">

                    <a href="property_list.php">
                        PG in Hyderabad
                    </a>

                </div>


            </div>


            <div class="footer-copyright">

                © 2026 Copyright PG Life

            </div>


        </div>

    </div>


    <!-- ==========================================
         JAVASCRIPT
    ========================================== -->

    <script
        type="text/javascript"
        src="js/jquery.js"
    ></script>

    <script
        type="text/javascript"
        src="js/bootstrap.min.js"
    ></script>

    <script
        src="js/dashboard.js"
    ></script>


</body>

</html>