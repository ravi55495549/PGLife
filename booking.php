<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

require_once "includes/database_connect.php";

$user_id = $_SESSION['user_id'];

$property_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($property_id <= 0) {
    die("Invalid property.");
}


/*
|--------------------------------------------------------------------------
| Property Details
|--------------------------------------------------------------------------
*/

$properties = [

    1 => [
        "name" => "Navkar Paying Guest",
        "gender" => "Male",
        "rent" => 9500,
        "daily_rent" => 350,
        "location" => "Mumbai"
    ],

    2 => [
        "name" => "Ganpati Paying Guest",
        "gender" => "Unisex",
        "rent" => 8500,
        "daily_rent" => 350,
        "location" => "Mumbai"
    ],

    3 => [
        "name" => "PG for Girls Borivali West",
        "gender" => "Female",
        "rent" => 8000,
        "daily_rent" => 300,
        "location" => "Mumbai"
    ]

];


if (!isset($properties[$property_id])) {
    die("Property not found.");
}


$property = $properties[$property_id];


/*
|--------------------------------------------------------------------------
| Booking Submission
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $posted_property_id = isset($_POST['property_id'])
        ? (int) $_POST['property_id']
        : 0;
    $guest_name = trim($_POST['guest_name'] ?? '');

    $guest_email = trim($_POST['guest_email'] ?? '');

    $guest_phone = trim($_POST['guest_phone'] ?? '');

    $special_requests =
        trim($_POST['special_requests'] ?? '');

    $terms_accepted =
        isset($_POST['terms_accepted'])
            ? $_POST['terms_accepted']
            : '';
        
    if ($guest_name === '') {

        die('Please enter guest name.');

    }


    if (!filter_var($guest_email, FILTER_VALIDATE_EMAIL)) {

        die('Please enter a valid email address.');

    }


    if (!preg_match('/^[0-9]{10}$/', $guest_phone)) {

        die('Please enter a valid 10-digit phone number.');

    }


    if ($terms_accepted !== '1') {

        die('Please accept the Terms & Conditions.');

    }            

    $from_date = trim($_POST['from_date'] ?? '');
    $to_date = trim($_POST['to_date'] ?? '');
    $rooms = $_POST['rooms'] ?? [];

    if ($posted_property_id !== $property_id) {
        die('Invalid property.');
    }

    if ($from_date === '' || $to_date === '') {
        die('Please select both From and To dates.');
    }

    $from_timestamp = strtotime($from_date);
    $to_timestamp = strtotime($to_date);

    if ($from_timestamp === false || $to_timestamp === false || $to_timestamp <= $from_timestamp) {
        die('Invalid date range.');
    }

    $duration = (int) round(
        ($to_timestamp - $from_timestamp) / (60 * 60 * 24)
    );

    if ($duration <= 0) {
        die('Invalid booking duration.');
    }

    $room_count = count($rooms);
    $total_adults = 0;
    $total_children = 0;

    foreach ($rooms as $room) {
        $adults = isset($room['adults']) ? (int) $room['adults'] : 1;
        $children = isset($room['children']) ? (int) $room['children'] : 0;

        if ($adults < 1 || $adults > 4 || $children < 0 || $children > 4) {
            die('Invalid guest count.');
        }

        $total_adults += $adults;
        $total_children += $children;
    }

    if ($room_count < 1) {
        die('At least one room is required.');
    }

    $total_amount =
        (int) $property['daily_rent'] *
        $duration *
        $room_count;

    $_SESSION['pending_booking'] = array(
    'user_id' => (int) $user_id,
    'property_id' => (int) $property_id,
    'guest_name' => $guest_name,
    'guest_email' => $guest_email,
    'guest_phone' => $guest_phone,
    'special_requests' => $special_requests,
    'terms_accepted' => true,
    'from_date' => $from_date,
    'to_date' => $to_date,
    'duration' => $duration,
    'rooms' => $room_count,
    'adults' => $total_adults,
    'children' => $total_children,
    'total_amount' => $total_amount
);

header("Location: payment.php");
exit;

        }

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>
        Book <?= htmlspecialchars($property['name']) ?> | PG Life
    </title>


    <!-- Bootstrap -->

    <link
        href="css/bootstrap.min.css"
        rel="stylesheet"
    />


    <!-- Font Awesome -->

    <link
        href="https://use.fontawesome.com/releases/v5.11.2/css/all.css"
        rel="stylesheet"
    />


    <!-- Google Font -->

    <link
        href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700;800&display=swap"
        rel="stylesheet"
    />


    <!-- Existing PGLife CSS -->

    <link
        href="css/common.css"
        rel="stylesheet"
    />


    <style>

        body {
            background: #f6f7fb;
            font-family: 'Open Sans', sans-serif;
        }


        /* =====================================================
           BOOKING PAGE
        ===================================================== */

        .booking-page {
            padding: 45px 0 70px;
        }


        .booking-heading {
            text-align: center;
            margin-bottom: 35px;
        }


        .booking-heading h1 {
            font-size: 30px;
            font-weight: 700;
            color: #333;
            margin-bottom: 8px;
        }


        .booking-heading p {
            color: #777;
            font-size: 15px;
            margin: 0;
        }


        /* =====================================================
           MAIN BOOKING CARD
        ===================================================== */

        .booking-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }


        /* =====================================================
           PROPERTY SECTION
        ===================================================== */

        .property-section {
            padding: 30px;
            border-bottom: 1px solid #eee;
        }


        .property-image {
            width: 100%;
            height: 230px;
            border-radius: 10px;
            background: linear-gradient(
                135deg,
                #f1f1f1,
                #e7e7e7
            );
            display: flex;
            align-items: center;
            justify-content: center;
        }


        .property-image i {
            font-size: 60px;
            color: #cfcfcf;
        }


        .property-info {
            padding-left: 10px;
        }


        .property-info h2 {
            font-size: 25px;
            font-weight: 700;
            color: #333;
            margin-bottom: 12px;
        }


        .property-location {
            color: #777;
            margin-bottom: 18px;
        }


        .property-location i {
            color: #ff5a5f;
            margin-right: 6px;
        }


        .property-tag {
            display: inline-block;
            padding: 6px 14px;
            background: #f1f3f5;
            border-radius: 20px;
            font-size: 13px;
            color: #555;
            margin-right: 7px;
        }


        .property-rent {
            margin-top: 25px;
        }


        .property-rent .amount {
            font-size: 28px;
            font-weight: 700;
            color: #ff5a5f;
        }


        .property-rent .period {
            color: #777;
            font-size: 14px;
        }


        /* =====================================================
           BOOKING DETAILS SECTION
        ===================================================== */

        .booking-form-section {
            padding: 30px;
        }


        .section-title {
            font-size: 20px;
            font-weight: 700;
            color: #333;
            margin-bottom: 25px;
        }
        /* =====================================================
        GUEST INFORMATION
        ===================================================== */

        .guest-information {
            margin-bottom: 30px;
        }


        .guest-information-title {
            font-size: 20px;
            font-weight: 700;
            color: #333;
            margin-bottom: 20px;
        }


        .guest-input-row {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
        }


        .guest-input-group {
            flex: 1;
        }


        .guest-input {
            width: 100%;
            height: 48px;
            border: 1px solid #d5d9dc;
            border-radius: 5px;
            padding: 0 13px;
            font-size: 14px;
            color: #333;
            background: #fff;
        }


        .guest-input:focus {
            outline: none;
            border-color: #ff5a5f;
            box-shadow: 0 0 0 0.1rem rgba(255, 90, 95, 0.15);
        }


        .phone-wrapper {
            display: flex;
            height: 48px;
        }


        .country-code {
            width: 95px;
            border: 1px solid #d5d9dc;
            border-right: none;
            border-radius: 5px 0 0 5px;
            background: #fafafa;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            color: #555;
            font-size: 14px;
        }


        .phone-input {
            flex: 1;
            border: 1px solid #d5d9dc;
            border-radius: 0 5px 5px 0;
            padding: 0 13px;
            font-size: 14px;
        }


        .phone-input:focus {
            outline: none;
            border-color: #ff5a5f;
        }


        .special-request {
            width: 100%;
            min-height: 75px;
            resize: vertical;
            border: 1px solid #d5d9dc;
            border-radius: 5px;
            padding: 13px;
            font-size: 14px;
            font-family: inherit;
        }


        .special-request:focus {
            outline: none;
            border-color: #ff5a5f;
        }


        .guest-message {
            text-align: center;
            margin-top: 12px;
            font-size: 13px;
            color: #555;
        }


        .guest-message strong {
            color: #4b8f5a;
            display: block;
            margin-top: 8px;
        }


        .terms-box {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-top: 18px;
            font-size: 13px;
            color: #555;
        }


        .terms-checkbox {
            width: 18px;
            height: 18px;
            margin-top: 1px;
            cursor: pointer;
            flex-shrink: 0;
        }


        .terms-label {
            line-height: 1.5;
            cursor: pointer;
        }


        .terms-link {
            color: #4b8f5a;
            font-weight: 600;
            text-decoration: none;
        }


        .terms-link:hover {
            text-decoration: underline;
        }


        .terms-error {
            display: none;
            color: #dc3545;
            font-size: 12px;
            margin-top: 7px;
        }

        /* Terms & Conditions Modal */
        #terms-conditions .modal-content {
            border: none;
            border-radius: 10px;
            overflow: hidden;
        }

        #terms-conditions .modal-header {
            background: #4b8f5a;
            color: #fff;
            border-bottom: none;
        }

        #terms-conditions .modal-title {
            font-weight: 700;
        }

        #terms-conditions .modal-header .close {
            color: #fff;
            opacity: 1;
            text-shadow: none;
        }

        #terms-conditions .modal-body {
            padding: 22px 25px 18px;
            color: #555;
            font-size: 14px;
            line-height: 1.6;
        }

        #terms-conditions .modal-body p {
            margin-bottom: 14px;
        }

        #terms-conditions .modal-body p:last-child {
            margin-bottom: 0;
        }

        #terms-conditions .modal-footer {
            border-top: 1px solid #eee;
        }


        /* =====================================================
        GUEST INFORMATION RESPONSIVE
        ===================================================== */

        @media (max-width: 767px) {

            .guest-input-row {
                flex-direction: column;
                gap: 15px;
            }

        }

        /* =====================================================
           DATE BOX
        ===================================================== */

        .date-row {
            display: flex;
            align-items: flex-end;
            gap: 20px;
            margin-bottom: 30px;
        }


        .date-box {
            flex: 1;
        }


        .date-label {
            display: block;
            font-size: 13px;
            color: #555;
            margin-bottom: 7px;
            font-weight: 600;
        }


        .date-input-wrapper {
            position: relative;
        }


        .date-input-wrapper i {
            position: absolute;
            right: 15px;
            top: 15px;
            color: #999;
            pointer-events: none;
        }


        .date-input {
            width: 100%;
            height: 48px;
            border: 1px solid #d8dce0;
            border-radius: 5px;
            padding: 0 45px 0 14px;
            font-size: 14px;
            color: #333;
            background: #fff;
        }


        .date-input:focus {
            outline: none;
            border-color: #ff5a5f;
            box-shadow: 0 0 0 0.1rem rgba(255, 90, 95, 0.15);
        }


        /* =====================================================
           ROOM SECTION
        ===================================================== */

        .rooms-container {
            margin-top: 5px;
        }


        .room-card {
            border: 1px solid #e1e4e7;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 15px;
            background: #fff;
        }


        .room-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 18px;
        }


        .room-title {
            font-size: 16px;
            font-weight: 700;
            color: #333;
            margin: 0;
        }


        .remove-room {
            border: none;
            background: transparent;
            color: #777;
            font-size: 18px;
            cursor: pointer;
            padding: 0 5px;
        }


        .remove-room:hover {
            color: #ff5a5f;
        }


        .guest-columns {
            display: flex;
            gap: 45px;
        }


        .guest-group {
            display: flex;
            flex-direction: column;
        }


        .guest-label {
            font-size: 12px;
            color: #666;
            margin-bottom: 7px;
        }


        .guest-control {
            display: flex;
            height: 38px;
        }


        .guest-btn {
            width: 36px;
            height: 38px;
            border: 1px solid #d5d9dc;
            background: #fff;
            color: #555;
            font-size: 16px;
            cursor: pointer;
        }


        .guest-btn:first-child {
            border-radius: 4px 0 0 4px;
        }


        .guest-btn:last-child {
            border-radius: 0 4px 4px 0;
        }


        .guest-btn:hover {
            background: #f5f5f5;
        }


        .guest-number {
            width: 45px;
            height: 38px;
            border-top: 1px solid #d5d9dc;
            border-bottom: 1px solid #d5d9dc;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            color: #333;
            background: #fafafa;
        }


        /* =====================================================
           ADD ROOM BUTTON
        ===================================================== */

        .add-room-btn {
            border: none;
            background: transparent;
            color: #4b8f5a;
            font-size: 14px;
            padding: 5px 0;
            cursor: pointer;
        }


        .add-room-btn:hover {
            color: #35733f;
        }


        .add-room-btn i {
            margin-right: 8px;
        }


        /* =====================================================
           SUMMARY CARD
        ===================================================== */

        .summary-card {
            background: #fafafa;
            border: 1px solid #eee;
            border-radius: 10px;
            padding: 25px;
            height: 100%;
        }


        .summary-title {
            font-size: 19px;
            font-weight: 700;
            margin-bottom: 22px;
            color: #333;
        }


        .summary-row {
            display: flex;
            justify-content: space-between;
            gap: 15px;
            margin-bottom: 15px;
            font-size: 14px;
            color: #555;
        }


        .summary-row strong {
            color: #333;
            text-align: right;
        }


        .summary-divider {
            border-top: 1px solid #ddd;
            margin: 20px 0;
        }


        .summary-total {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }


        .summary-total span:first-child {
            font-size: 15px;
            font-weight: 700;
            color: #333;
        }


        .summary-total span:last-child {
            font-size: 22px;
            font-weight: 700;
            color: #ff5a5f;
        }


        .confirm-btn {
            width: 100%;
            height: 50px;
            border: none;
            border-radius: 6px;
            background: #ff5a5f;
            color: #fff;
            font-size: 16px;
            font-weight: 600;
            margin-top: 25px;
            transition: 0.2s;
            cursor: pointer;
        }


        .confirm-btn:hover {
            background: #e94d52;
        }


        .secure-booking {
            text-align: center;
            margin-top: 15px;
            font-size: 12px;
            color: #888;
        }


        .secure-booking i {
            color: #28a745;
            margin-right: 5px;
        }


        /* =====================================================
           ERROR MESSAGE
        ===================================================== */

        .date-error {
            display: none;
            color: #dc3545;
            font-size: 12px;
            margin-top: 7px;
        }


        /* =====================================================
           RESPONSIVE
        ===================================================== */

        @media (max-width: 767px) {

            .booking-page {
                padding: 25px 0 45px;
            }


            .booking-heading h1 {
                font-size: 25px;
            }


            .property-section {
                padding: 20px;
            }


            .property-image {
                height: 200px;
                margin-bottom: 20px;
            }


            .property-info {
                padding-left: 0;
            }


            .property-info h2 {
                font-size: 22px;
            }


            .booking-form-section {
                padding: 20px;
            }


            .date-row {
                flex-direction: column;
                align-items: stretch;
                gap: 15px;
            }


            .guest-columns {
                gap: 25px;
            }


            .summary-card {
                margin-top: 25px;
            }

        }

    </style>

</head>


<body>


<?php

/*
|--------------------------------------------------------------------------
| Existing PGLife Header
|--------------------------------------------------------------------------
*/

require_once "includes/header.php";

?>


<!-- =========================================================
     BOOKING PAGE
========================================================= -->

<div class="booking-page">

    <div class="container">


        <!-- Heading -->

        <div class="booking-heading">

            <h1>
                Complete Your Booking
            </h1>

            <p>
                Reserve your stay with PG Life
            </p>

        </div>


        <div class="booking-card">


            <!-- =================================================
                 PROPERTY INFORMATION
            ================================================== -->

            <div class="property-section">

                <div class="row align-items-center">


                    <div class="col-md-5">

                        <div class="property-image">

                            <i class="fas fa-home"></i>

                        </div>

                    </div>


                    <div class="col-md-7">

                        <div class="property-info">

                            <h2>
                                <?= htmlspecialchars($property['name']) ?>
                            </h2>


                            <div class="property-location">

                                <i class="fas fa-map-marker-alt"></i>

                                <?= htmlspecialchars($property['location']) ?>

                            </div>


                            <span class="property-tag">

                                <i class="fas fa-user"></i>

                                <?= htmlspecialchars($property['gender']) ?>

                            </span>


                            <span class="property-tag">

                                <i class="fas fa-building"></i>

                                PG

                            </span>


                            <div class="property-rent">

                                <span class="amount">

                                    ₹<?= number_format($property['rent']) ?>

                                </span>

                                <span class="period">

                                    / month

                                </span>

                            </div>

                        </div>

                    </div>


                </div>

            </div>



            <!-- =================================================
                 BOOKING DETAILS
            ================================================== -->

            <div class="booking-form-section">

                <div class="row">


                    <!-- LEFT SIDE -->

                    <div class="col-lg-7">


                        <h3 class="section-title">

                            Booking Details

                        </h3>


                        <form
                            method="POST"
                            action=""
                            id="booking-form"
                        >


                            <!-- Property ID -->

                            <input
                                type="hidden"
                                name="property_id"
                                value="<?= $property_id ?>"
                            >

                            <!-- =================================================
                                GUEST INFORMATION
                            ================================================= -->

                            <div class="guest-information">


                                <div class="guest-information-title">

                                    Guest Information

                                </div>


                                <!-- NAME -->

                                <div class="guest-input-row">


                                    <div class="guest-input-group">

                                        <input
                                            type="text"
                                            name="guest_name"
                                            id="guest-name"
                                            class="guest-input"
                                            placeholder="First Name and Last Name"
                                            required
                                        >

                                    </div>


                                </div>



                                <!-- EMAIL + PHONE -->

                                <div class="guest-input-row">


                                    <div class="guest-input-group">

                                        <input
                                            type="email"
                                            name="guest_email"
                                            id="guest-email"
                                            class="guest-input"
                                            placeholder="Email Address"
                                            required
                                        >

                                    </div>



                                    <div class="guest-input-group">

                                        <div class="phone-wrapper">


                                            <div class="country-code">

                                                🇮🇳 +91

                                            </div>


                                            <input
                                                type="tel"
                                                name="guest_phone"
                                                id="guest-phone"
                                                class="phone-input"
                                                placeholder="Enter phone number"
                                                maxlength="10"
                                                required
                                            >


                                        </div>

                                    </div>


                                </div>



                                <!-- SPECIAL REQUEST -->

                                <textarea
                                    name="special_requests"
                                    id="special-requests"
                                    class="special-request"
                                    placeholder="Special Requests"
                                ></textarea>



                                <!-- MESSAGE -->

                                <div class="guest-message">

                                    Read this message to ensure a seamless stay on your selected dates.

                                    <strong>
                                        Secure your stay before the prices change!
                                    </strong>

                                </div>



                                <!-- TERMS -->

                                <div class="terms-box">


                                    <input
                                        type="checkbox"
                                        name="terms_accepted"
                                        id="terms-accepted"
                                        class="terms-checkbox"
                                        value="1"
                                    >


                                    <label
                                        for="terms-accepted"
                                        class="terms-label"
                                    >

                                        By completing this reservation you are accepting our

                                        <a
                                            href="#terms-conditions"
                                            class="terms-link"
                                            id="terms-link"
                                            data-toggle="modal"
                                            data-target="#terms-conditions"
                                            role="button"
                                        >
                                            Terms & Conditions
                                        </a>

                                    </label>


                                </div>


                                <div
                                    class="terms-error"
                                    id="terms-error"
                                >

                                    Please accept the Terms & Conditions before proceeding.

                                </div>


                            </div>

                            <!-- =================================================
                                 FROM / TO DATE
                            ================================================== -->

                            <div class="date-row">


                                <!-- FROM -->

                                <div class="date-box">

                                    <label class="date-label">

                                        From

                                    </label>


                                    <div class="date-input-wrapper">

                                        <input
                                            type="date"
                                            name="from_date"
                                            id="from-date"
                                            class="date-input"
                                            required
                                        >


                                        <i class="far fa-calendar-alt"></i>

                                    </div>

                                </div>



                                <!-- TO -->

                                <div class="date-box">

                                    <label class="date-label">

                                        To

                                    </label>


                                    <div class="date-input-wrapper">

                                        <input
                                            type="date"
                                            name="to_date"
                                            id="to-date"
                                            class="date-input"
                                            required
                                        >


                                        <i class="far fa-calendar-alt"></i>

                                    </div>


                                    <div
                                        class="date-error"
                                        id="date-error"
                                    >

                                        Check-out date must be after
                                        check-in date.

                                    </div>

                                </div>


                            </div>



                            <!-- =================================================
                                 ROOMS
                            ================================================== -->

                            <div class="rooms-container">

                                <div id="rooms-list">


                                    <!-- ROOM 1 -->

                                    <div
                                        class="room-card"
                                        data-room="1"
                                    >


                                        <div class="room-header">

                                            <h4 class="room-title">

                                                Room 1

                                            </h4>


                                            <!-- Room 1 cannot be removed -->

                                        </div>



                                        <div class="guest-columns">


                                            <!-- ADULTS -->

                                            <div class="guest-group">

                                                <span class="guest-label">

                                                    Adults

                                                </span>


                                                <div class="guest-control">


                                                    <button
                                                        type="button"
                                                        class="guest-btn decrease-btn"
                                                        data-type="adults"
                                                    >

                                                        −

                                                    </button>


                                                    <div
                                                        class="guest-number"
                                                        data-value="adults"
                                                    >

                                                        1

                                                    </div>


                                                    <button
                                                        type="button"
                                                        class="guest-btn increase-btn"
                                                        data-type="adults"
                                                    >

                                                        +

                                                    </button>


                                                </div>


                                                <input
                                                    type="hidden"
                                                    name="rooms[1][adults]"
                                                    value="1"
                                                    class="adults-input"
                                                >

                                            </div>



                                            <!-- CHILDREN -->

                                            <div class="guest-group">

                                                <span class="guest-label">

                                                    Children
                                                    <span style="font-size: 10px;">
                                                        (0-10 years)
                                                    </span>

                                                </span>


                                                <div class="guest-control">


                                                    <button
                                                        type="button"
                                                        class="guest-btn decrease-btn"
                                                        data-type="children"
                                                    >

                                                        −

                                                    </button>


                                                    <div
                                                        class="guest-number"
                                                        data-value="children"
                                                    >

                                                        0

                                                    </div>


                                                    <button
                                                        type="button"
                                                        class="guest-btn increase-btn"
                                                        data-type="children"
                                                    >

                                                        +

                                                    </button>


                                                </div>


                                                <input
                                                    type="hidden"
                                                    name="rooms[1][children]"
                                                    value="0"
                                                    class="children-input"
                                                >

                                            </div>


                                        </div>

                                    </div>


                                </div>



                                <!-- ADD ROOM -->

                                <button
                                    type="button"
                                    class="add-room-btn"
                                    id="add-room-btn"
                                >

                                    <i class="fas fa-plus"></i>

                                    Add Another Room

                                </button>


                            </div>


                            <!-- Hidden submit button -->

                            <button
                                type="submit"
                                id="hidden-submit"
                                style="display:none;"
                            >
                            </button>


                        </form>

                    </div>



                    <!-- =================================================
                         SUMMARY
                    ================================================== -->

                    <div class="col-lg-5">


                        <div class="summary-card">


                            <div class="summary-title">

                                Booking Summary

                            </div>


                            <div class="summary-row">

                                <span>
                                    Property
                                </span>

                                <strong>
                                    <?= htmlspecialchars($property['name']) ?>
                                </strong>

                            </div>


                            <div class="summary-row">

                                <span>
                                    From
                                </span>

                                <strong id="summary-from">
                                    —
                                </strong>

                            </div>


                            <div class="summary-row">

                                <span>
                                    To
                                </span>

                                <strong id="summary-to">
                                    —
                                </strong>

                            </div>


                            <div class="summary-row">

                                <span>
                                    Stay
                                </span>

                                <strong id="summary-stay">
                                    —
                                </strong>

                            </div>


                            <div class="summary-row">

                                <span>
                                    Rooms
                                </span>

                                <strong id="summary-rooms">
                                    1 Room
                                </strong>

                            </div>


                            <div class="summary-row">

                                <span>
                                    Guests
                                </span>

                                <strong id="summary-guests">
                                    1 Adult
                                </strong>

                            </div>


                            <div class="summary-divider"></div>


                            <div class="summary-row">

                                <span>
                                    Monthly Rent
                                </span>

                                <strong>
                                    ₹<?= number_format($property['rent']) ?>
                                </strong>

                            </div>


                            <div class="summary-row">

                                <span>
                                    Daily Rate
                                </span>

                                <strong>
                                    ₹<?= number_format($property['daily_rent']) ?> / day
                                </strong>

                            </div>


                            <div class="summary-total">

                                <span>
                                    Total
                                </span>

                                <span id="summary-total">

                                    —

                                </span>

                            </div>


                            <button
                                type="button"
                                class="confirm-btn"
                                id="confirm-booking-btn"
                            >
                                Proceed to Payment
                            </button>

                            <div class="secure-booking">

                                <i class="fas fa-lock"></i>

                                Secure booking

                            </div>


                        </div>

                    </div>


                </div>

            </div>


        </div>

    </div>

</div>

                        <!-- =========================================================
                            TERMS & CONDITIONS MODAL
                        ========================================================= -->

                        <div
                            class="modal fade"
                            id="terms-conditions"
                            tabindex="-1"
                            role="dialog"
                            aria-hidden="true"
                        >

                            <div
                                class="modal-dialog modal-dialog-centered"
                                role="document"
                            >

                                <div class="modal-content">


                                    <div class="modal-header">

                                        <h5 class="modal-title">

                                            Terms & Conditions

                                        </h5>


                                        <button
                                            type="button"
                                            class="close"
                                            data-dismiss="modal"
                                            aria-label="Close"
                                        >

                                            <span aria-hidden="true">
                                                &times;
                                            </span>

                                        </button>

                                    </div>



                                    <div class="modal-body">


                                        <p>
                                            <strong>a)</strong>
                                            You are making a booking with the hotel directly.
                                        </p>


                                        <p>
                                            <strong>b)</strong>
                                            Please review the booking and cancellation
                                            policies for the bookings. In case you make
                                            a change or cancel the booking, the cancellation
                                            penalties specified may apply.
                                        </p>


                                        <p>
                                            <strong>c)</strong>
                                            You may be asked to furnish the form of payment
                                            and identification proofs during check-in.
                                        </p>


                                        <p>
                                            <strong>d)</strong>
                                            Other inclusions not listed as a part of this
                                            booking may be chargeable.
                                        </p>


                                        <p>
                                            <strong>e)</strong>
                                            If the booking requires payment by a certain date,
                                            non payment by that date will cause the booking
                                            to be automatically canceled.
                                        </p>


                                    </div>


                                    <div class="modal-footer">

                                        <button
                                            type="button"
                                            class="btn btn-secondary"
                                            data-dismiss="modal"
                                        >

                                            Close

                                        </button>

                                    </div>


                                </div>

                            </div>

                        </div>

<!-- =========================================================
     FOOTER
========================================================= -->

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

            © 2020 Copyright PG Life

        </div>

    </div>

</div>



<!-- =========================================================
     JAVASCRIPT
========================================================= -->

<script
    type="text/javascript"
    src="js/jquery.js"
></script>


<script
    type="text/javascript"
    src="js/bootstrap.min.js"
></script>


<script
    type="text/javascript"
    src="/PGLife/js/common.js"
></script>



<script>

document.addEventListener("DOMContentLoaded", function () {


    /*
    |--------------------------------------------------------------------------
    | Variables
    |--------------------------------------------------------------------------
    */

    var roomsList =
        document.getElementById("rooms-list");

    var addRoomBtn =
        document.getElementById("add-room-btn");

    var fromDate =
        document.getElementById("from-date");

    var toDate =
        document.getElementById("to-date");

    var dateError =
        document.getElementById("date-error");

    var confirmBookingBtn =
        document.getElementById("confirm-booking-btn");


    var summaryFrom =
        document.getElementById("summary-from");

    var summaryTo =
        document.getElementById("summary-to");

    var summaryStay =
        document.getElementById("summary-stay");

    var summaryRooms =
        document.getElementById("summary-rooms");

    var summaryGuests =
        document.getElementById("summary-guests");

    var summaryTotal =
        document.getElementById("summary-total");


    var rent =
        <?= (int) $property['rent'] ?>;

    var dailyRent =
        <?= (int) $property['daily_rent'] ?>;


    var roomCount = 1;



    /*
    |--------------------------------------------------------------------------
    | Today's Date
    |--------------------------------------------------------------------------
    */

    var today =
        new Date();

    var year =
        today.getFullYear();

    var month =
        String(today.getMonth() + 1).padStart(2, "0");

    var day =
        String(today.getDate()).padStart(2, "0");

    var todayString =
        year + "-" + month + "-" + day;


    fromDate.min = todayString;

    toDate.min = todayString;



    /*
    |--------------------------------------------------------------------------
    | Format Date
    |--------------------------------------------------------------------------
    */

    function formatDate(dateString) {

        if (!dateString) {
            return "—";
        }


        var date =
            new Date(dateString + "T00:00:00");


        var options = {
            day: "2-digit",
            month: "short",
            year: "numeric"
        };


        return date.toLocaleDateString(
            "en-IN",
            options
        );

    }



    /*
    |--------------------------------------------------------------------------
    | Calculate Stay
    |--------------------------------------------------------------------------
    */

    function calculateStay() {

        if (!fromDate.value || !toDate.value) {

            summaryStay.innerText = "—";

            return;

        }


        var start =
            new Date(
                fromDate.value + "T00:00:00"
            );

        var end =
            new Date(
                toDate.value + "T00:00:00"
            );


        var difference =
            end - start;


        var days =
            Math.round(
                difference /
                (1000 * 60 * 60 * 24)
            );


        if (days <= 0) {

            dateError.style.display =
                "block";

            summaryStay.innerText =
                "Invalid dates";

            return false;

        }


        dateError.style.display =
            "none";


        summaryStay.innerText =
            days +
            (days === 1 ? " Day" : " Days");


        return true;

    }



    /*
    |--------------------------------------------------------------------------
    | Update Date Summary
    |--------------------------------------------------------------------------
    */

    function updateDateSummary() {

        summaryFrom.innerText =
            formatDate(fromDate.value);


        summaryTo.innerText =
            formatDate(toDate.value);


        calculateStay();

    }



    /*
    |--------------------------------------------------------------------------
    | From Date Change
    |--------------------------------------------------------------------------
    */

    fromDate.addEventListener(
        "change",
        function () {

            if (this.value) {

                toDate.min =
                    this.value;

            }


            updateDateSummary();
            updateSummary();

        }
    );



    /*
    |--------------------------------------------------------------------------
    | To Date Change
    |--------------------------------------------------------------------------
    */

    toDate.addEventListener(
        "change",
        function () {

            updateDateSummary();
            updateSummary();

        }
    );



    /*
    |--------------------------------------------------------------------------
    | Get Room Guest Counts
    |--------------------------------------------------------------------------
    */

    function updateSummary() {

        var roomCards =
            document.querySelectorAll(
                ".room-card"
            );


        var totalAdults = 0;

        var totalChildren = 0;


        roomCards.forEach(
            function (roomCard) {

                var adults =
                    parseInt(
                        roomCard.querySelector(
                            ".adults-input"
                        ).value
                    );


                var children =
                    parseInt(
                        roomCard.querySelector(
                            ".children-input"
                        ).value
                    );


                totalAdults += adults;

                totalChildren += children;

            }
        );


        var totalGuests =
            totalAdults +
            totalChildren;


        summaryRooms.innerText =
            roomCards.length +
            (roomCards.length === 1
                ? " Room"
                : " Rooms");


        var guestText =
            totalAdults +
            (totalAdults === 1
                ? " Adult"
                : " Adults");


        if (totalChildren > 0) {

            guestText +=
                ", " +
                totalChildren +
                (totalChildren === 1
                    ? " Child"
                    : " Children");

        }


        summaryGuests.innerText =
            guestText;


        /*
        |--------------------------------------------------------------------------
        | Daily Booking Total
        |--------------------------------------------------------------------------
        | Daily rate × number of days × number of rooms
        |--------------------------------------------------------------------------
        */

        var totalRent = 0;


        if (fromDate.value && toDate.value) {

            var start =
                new Date(
                    fromDate.value + "T00:00:00"
                );

            var end =
                new Date(
                    toDate.value + "T00:00:00"
                );

            var difference =
                end - start;

            var days =
                Math.round(
                    difference /
                    (1000 * 60 * 60 * 24)
                );

            if (days > 0) {

                totalRent =
                    dailyRent *
                    days *
                    roomCards.length;

            }

        }


        if (totalRent > 0) {

            summaryTotal.innerText =
                "₹" +
                totalRent.toLocaleString(
                    "en-IN"
                );

        } else {

            summaryTotal.innerText =
                "—";

        }

    }



    /*
    |--------------------------------------------------------------------------
    | Create Room
    |--------------------------------------------------------------------------
    */

    function createRoom(roomNumber) {

        var roomCard =
            document.createElement("div");


        roomCard.className =
            "room-card";


        roomCard.setAttribute(
            "data-room",
            roomNumber
        );


        roomCard.innerHTML = `

            <div class="room-header">

                <h4 class="room-title">

                    Room ${roomNumber}

                </h4>


                <button
                    type="button"
                    class="remove-room"
                    title="Remove room"
                >

                    <i class="fas fa-times"></i>

                </button>

            </div>


            <div class="guest-columns">


                <!-- ADULTS -->

                <div class="guest-group">

                    <span class="guest-label">

                        Adults

                    </span>


                    <div class="guest-control">


                        <button
                            type="button"
                            class="guest-btn decrease-btn"
                            data-type="adults"
                        >

                            −

                        </button>


                        <div
                            class="guest-number"
                            data-value="adults"
                        >

                            1

                        </div>


                        <button
                            type="button"
                            class="guest-btn increase-btn"
                            data-type="adults"
                        >

                            +

                        </button>

                    </div>


                    <input
                        type="hidden"
                        name="rooms[${roomNumber}][adults]"
                        value="1"
                        class="adults-input"
                    >

                </div>



                <!-- CHILDREN -->

                <div class="guest-group">

                    <span class="guest-label">

                        Children
                        <span style="font-size: 10px;">
                            (0-10 years)
                        </span>

                    </span>


                    <div class="guest-control">


                        <button
                            type="button"
                            class="guest-btn decrease-btn"
                            data-type="children"
                        >

                            −

                        </button>


                        <div
                            class="guest-number"
                            data-value="children"
                        >

                            0

                        </div>


                        <button
                            type="button"
                            class="guest-btn increase-btn"
                            data-type="children"
                        >

                            +

                        </button>

                    </div>


                    <input
                        type="hidden"
                        name="rooms[${roomNumber}][children]"
                        value="0"
                        class="children-input"
                    >

                </div>


            </div>

        `;


        roomsList.appendChild(
            roomCard
        );

    }



    /*
    |--------------------------------------------------------------------------
    | Add Another Room
    |--------------------------------------------------------------------------
    */

    addRoomBtn.addEventListener(
        "click",
        function () {

            roomCount++;


            createRoom(
                roomCount
            );


            updateSummary();

        }
    );



    /*
    |--------------------------------------------------------------------------
    | Guest + / -
    |--------------------------------------------------------------------------
    */

    document.addEventListener(
        "click",
        function (event) {


            /*
            | Increase
            */

            if (
                event.target.closest(
                    ".increase-btn"
                )
            ) {

                var button =
                    event.target.closest(
                        ".increase-btn"
                    );


                var roomCard =
                    button.closest(
                        ".room-card"
                    );


                var type =
                    button.getAttribute(
                        "data-type"
                    );


                var input =
                    roomCard.querySelector(
                        "." +
                        type +
                        "-input"
                    );


                var number =
                    roomCard.querySelector(
                        '[data-value="' +
                        type +
                        '"]'
                    );


                var currentValue =
                    parseInt(
                        input.value
                    );


                /*
                | Maximum 4 adults
                */

                if (
                    type === "adults" &&
                    currentValue >= 4
                ) {

                    return;

                }


                /*
                | Maximum 4 children
                */

                if (
                    type === "children" &&
                    currentValue >= 4
                ) {

                    return;

                }


                currentValue++;


                input.value =
                    currentValue;


                number.innerText =
                    currentValue;


                updateSummary();

            }



            /*
            | Decrease
            */

            if (
                event.target.closest(
                    ".decrease-btn"
                )
            ) {

                var button =
                    event.target.closest(
                        ".decrease-btn"
                    );


                var roomCard =
                    button.closest(
                        ".room-card"
                    );


                var type =
                    button.getAttribute(
                        "data-type"
                    );


                var input =
                    roomCard.querySelector(
                        "." +
                        type +
                        "-input"
                    );


                var number =
                    roomCard.querySelector(
                        '[data-value="' +
                        type +
                        '"]'
                    );


                var currentValue =
                    parseInt(
                        input.value
                    );


                /*
                | Adults minimum = 1
                */

                if (
                    type === "adults" &&
                    currentValue <= 1
                ) {

                    return;

                }


                /*
                | Children minimum = 0
                */

                if (
                    type === "children" &&
                    currentValue <= 0
                ) {

                    return;

                }


                currentValue--;


                input.value =
                    currentValue;


                number.innerText =
                    currentValue;


                updateSummary();

            }



            /*
            | Remove Room
            */

            if (
                event.target.closest(
                    ".remove-room"
                )
            ) {

                var button =
                    event.target.closest(
                        ".remove-room"
                    );


                var roomCard =
                    button.closest(
                        ".room-card"
                    );


                roomCard.remove();


                renumberRooms();


                updateSummary();

            }

        }
    );



    /*
    |--------------------------------------------------------------------------
    | Renumber Rooms
    |--------------------------------------------------------------------------
    */

    function renumberRooms() {

        var roomCards =
            document.querySelectorAll(
                ".room-card"
            );


        roomCards.forEach(
            function (roomCard, index) {

                var newNumber =
                    index + 1;


                roomCard.setAttribute(
                    "data-room",
                    newNumber
                );


                roomCard.querySelector(
                    ".room-title"
                ).innerText =
                    "Room " +
                    newNumber;


                roomCard.querySelector(
                    ".adults-input"
                ).name =
                    "rooms[" +
                    newNumber +
                    "][adults]";


                roomCard.querySelector(
                    ".children-input"
                ).name =
                    "rooms[" +
                    newNumber +
                    "][children]";

            }
        );


        roomCount =
            roomCards.length;

    }


                    /*
                    |--------------------------------------------------------------------------
                    | Proceed to Payment
                    |--------------------------------------------------------------------------
                    */

                    confirmBookingBtn.addEventListener(
                        "click",
                        function () {


                            /*
                            |--------------------------------------------------------------
                            | Guest Information Validation
                            |--------------------------------------------------------------
                            */

                            var guestName =
                                document.getElementById(
                                    "guest-name"
                                ).value.trim();


                            var guestEmail =
                                document.getElementById(
                                    "guest-email"
                                ).value.trim();


                            var guestPhone =
                                document.getElementById(
                                    "guest-phone"
                                ).value.trim();


                            if (guestName === "") {

                                alert(
                                    "Please enter your name."
                                );

                                document
                                    .getElementById("guest-name")
                                    .focus();

                                return;

                            }


                            if (guestEmail === "") {

                                alert(
                                    "Please enter your email address."
                                );

                                document
                                    .getElementById("guest-email")
                                    .focus();

                                return;

                            }


                            if (guestPhone === "") {

                                alert(
                                    "Please enter your phone number."
                                );

                                document
                                    .getElementById("guest-phone")
                                    .focus();

                                return;

                            }


                            /*
                            |--------------------------------------------------------------
                            | Phone Validation
                            |--------------------------------------------------------------
                            */

                            if (!/^[0-9]{10}$/.test(guestPhone)) {

                                alert(
                                    "Please enter a valid 10-digit phone number."
                                );

                                document
                                    .getElementById("guest-phone")
                                    .focus();

                                return;

                            }


                            /*
                            |--------------------------------------------------------------
                            | Date Validation
                            |--------------------------------------------------------------
                            */

                            var validDates =
                                calculateStay();


                            if (!fromDate.value) {

                                alert(
                                    "Please select From date."
                                );

                                return;

                            }


                            if (!toDate.value) {

                                alert(
                                    "Please select To date."
                                );

                                return;

                            }


                            if (!validDates) {

                                alert(
                                    "Please select a valid date range."
                                );

                                return;

                            }


                            /*
                            |--------------------------------------------------------------
                            | Terms & Conditions
                            |--------------------------------------------------------------
                            */

                            var termsAccepted =
                                document.getElementById(
                                    "terms-accepted"
                                );


                            var termsError =
                                document.getElementById(
                                    "terms-error"
                                );


                            if (!termsAccepted.checked) {

                                termsError.style.display =
                                    "block";

                                termsAccepted.focus();

                                return;

                            }


                            termsError.style.display =
                                "none";


                            /*
                            |--------------------------------------------------------------
                            | Submit
                            |--------------------------------------------------------------
                            */

                            document
                                .getElementById(
                                    "booking-form"
                                )
                                .requestSubmit();

                        }
                    );

    /*
    |--------------------------------------------------------------------------
    | Initial Summary
    |--------------------------------------------------------------------------
    */

    updateSummary();

});

</script>


</body>

</html>