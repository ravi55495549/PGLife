<?php

session_start();

/*
|--------------------------------------------------------------------------
| LOGIN CHECK
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

require_once "includes/database_connect.php";

$user_id = (int) $_SESSION['user_id'];


/*
|--------------------------------------------------------------------------
| PENDING BOOKING CHECK
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION['pending_booking'])) {
    header("Location: dashboard.php");
    exit;
}

$pending_booking = $_SESSION['pending_booking'];


/*
|--------------------------------------------------------------------------
| PROPERTY DATA
|--------------------------------------------------------------------------
*/

$properties = array(

    1 => array(
        "name" => "Navkar Paying Guest",
        "gender" => "Male",
        "rent" => 9500,
        "daily_rent" => 350,
        "location" => "Mumbai"
    ),

    2 => array(
        "name" => "Ganpati Paying Guest",
        "gender" => "Unisex",
        "rent" => 8500,
        "daily_rent" => 350,
        "location" => "Mumbai"
    ),

    3 => array(
        "name" => "PG for Girls Borivali West",
        "gender" => "Female",
        "rent" => 8000,
        "daily_rent" => 300,
        "location" => "Mumbai"
    )

);


/*
|--------------------------------------------------------------------------
| PROPERTY
|--------------------------------------------------------------------------
*/

$property_id = isset($pending_booking['property_id'])
    ? (int) $pending_booking['property_id']
    : 0;

if (
    $property_id <= 0 ||
    !isset($properties[$property_id])
) {
    unset($_SESSION['pending_booking']);

    header("Location: dashboard.php");
    exit;
}

$property = $properties[$property_id];


/*
|--------------------------------------------------------------------------
| BOOKING DETAILS
|--------------------------------------------------------------------------
*/

$from_date = $pending_booking['from_date'];
$to_date = $pending_booking['to_date'];

$duration = (int) $pending_booking['duration'];
$rooms = (int) $pending_booking['rooms'];

$adults = (int) $pending_booking['adults'];
$children = (int) $pending_booking['children'];

$total_amount = (int) $pending_booking['total_amount'];

$daily_rent = (int) $property['daily_rent'];

$guest_name = isset($pending_booking['guest_name'])
    ? $pending_booking['guest_name']
    : '';

$guest_email = isset($pending_booking['guest_email'])
    ? $pending_booking['guest_email']
    : '';

$guest_phone = isset($pending_booking['guest_phone'])
    ? $pending_booking['guest_phone']
    : '';

$special_requests = isset($pending_booking['special_requests'])
    ? $pending_booking['special_requests']
    : '';


/*
|--------------------------------------------------------------------------
| PAYMENT PROCESS
|--------------------------------------------------------------------------
*/

$payment_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $payment_method = isset($_POST['payment_method'])
        ? trim($_POST['payment_method'])
        : '';


    /*
    |--------------------------------------------------------------------------
    | BASIC VALIDATION
    |--------------------------------------------------------------------------
    */

    if ($payment_method === '') {

        $payment_error = "Please select a payment method.";

    } else {

        /*
        |--------------------------------------------------------------------------
        | DEMO PAYMENT VALIDATION
        |--------------------------------------------------------------------------
        */

        if ($payment_method === 'card') {

            $card_number = isset($_POST['card_number'])
                ? trim($_POST['card_number'])
                : '';

            $card_name = isset($_POST['card_name'])
                ? trim($_POST['card_name'])
                : '';

            $expiry = isset($_POST['expiry'])
                ? trim($_POST['expiry'])
                : '';

            $cvv = isset($_POST['cvv'])
                ? trim($_POST['cvv'])
                : '';


            if (
                $card_number === '' ||
                $card_name === '' ||
                $expiry === '' ||
                $cvv === ''
            ) {

                $payment_error =
                    "Please fill all card details.";

            }

        } elseif ($payment_method === 'upi') {

            $upi_id = isset($_POST['upi_id'])
                ? trim($_POST['upi_id'])
                : '';

            if ($upi_id === '') {

                $payment_error =
                    "Please enter your UPI ID.";

            }

        } elseif ($payment_method === 'netbanking') {

            $bank = isset($_POST['bank'])
                ? trim($_POST['bank'])
                : '';

            if ($bank === '') {

                $payment_error =
                    "Please select your bank.";

            }

        } elseif ($payment_method === 'emi') {

            $emi_option = isset($_POST['emi_option'])
                ? trim($_POST['emi_option'])
                : '';

            if ($emi_option === '') {

                $payment_error =
                    "Please select an EMI option.";

            }

        } elseif ($payment_method === 'wallet') {

            $wallet = isset($_POST['wallet'])
                ? trim($_POST['wallet'])
                : '';

            if ($wallet === '') {

                $payment_error =
                    "Please select a wallet.";

            }

        } else {

            $payment_error =
                "Invalid payment method.";

        }
    }


    /*
    |--------------------------------------------------------------------------
    | PAYMENT SUCCESS
    |--------------------------------------------------------------------------
    */

    if ($payment_error === '') {

        /*
         * Demo payment successful.
         *
         * No real money is charged.
         */


        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO bookings
            (
                user_id,
                property_id,
                guest_name,
                guest_email,
                guest_phone,
                move_in_date,
                to_date,
                duration,
                rooms,
                adults,
                children,
                total_amount
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );


        if ($stmt) {

            $guest_name = isset($pending_booking['guest_name'])
            ? $pending_booking['guest_name']
            : '';

        $guest_email = isset($pending_booking['guest_email'])
            ? $pending_booking['guest_email']
            : '';

        $guest_phone = isset($pending_booking['guest_phone'])
            ? $pending_booking['guest_phone']
            : '';

            mysqli_stmt_bind_param(
                $stmt,
                'iisssssiiiii',
                $user_id,
                $property_id,
                $guest_name,
                $guest_email,
                $guest_phone,
                $from_date,
                $to_date,
                $duration,
                $rooms,
                $adults,
                $children,
                $total_amount
            );
            if (mysqli_stmt_execute($stmt)) {

                mysqli_stmt_close($stmt);

                /*
                 * Remove pending booking
                 */
                unset($_SESSION['pending_booking']);


                /*
                 * Payment successful
                 */
                echo '<script>

                    alert(
                        "Payment successful! Your booking is confirmed."
                    );

                    window.location.href = "dashboard.php";

                </script>';

                exit;

            } else {

                $payment_error =
                    "Payment could not be completed. Please try again.";

                mysqli_stmt_close($stmt);
            }

        } else {

            $payment_error =
                "Unable to process your booking. Please try again.";
        }
    }
}

?>


<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Payment - PGLife
    </title>


    <style>

        * {
            box-sizing: border-box;
        }


        body {

            margin: 0;

            padding: 0;

            font-family:
                Arial,
                Helvetica,
                sans-serif;

            background: #f5f6f8;

            color: #333;
        }


        /*
        ============================================================
        MAIN CONTAINER
        ============================================================
        */

        .payment-page {

            width: 100%;

            min-height: 100vh;

            padding: 35px 20px;
        }


        .payment-container {

            max-width: 1100px;

            margin: 0 auto;

            background: #fff;

            border: 1px solid #e2e5e8;

            border-radius: 8px;

            overflow: hidden;

            box-shadow:
                0 3px 15px
                rgba(0, 0, 0, 0.08);
        }


        /*
        ============================================================
        HEADER
        ============================================================
        */

        .payment-header {

            display: flex;

            justify-content: space-between;

            align-items: center;

            padding: 18px 25px;

            border-bottom: 1px solid #e5e5e5;

            background: #fff;
        }


        .brand {

            font-size: 25px;

            font-weight: 700;

            color: #333;
        }


        .brand span {

            color: #e83e3e;
        }


        .secure-text {

            font-size: 13px;

            color: #777;
        }


        .secure-text i {

            color: #198754;

            margin-right: 5px;
        }


        /*
        ============================================================
        PAYMENT BODY
        ============================================================
        */

        .payment-body {

            display: flex;

            min-height: 550px;
        }


        /*
        ============================================================
        LEFT SIDE
        ============================================================
        */

        .payment-methods {

            width: 38%;

            border-right: 1px solid #e5e5e5;

            background: #fff;
        }


        .amount-box {

            padding: 20px 25px;

            border-bottom: 1px solid #e5e5e5;
        }


        .amount-label {

            font-size: 14px;

            font-weight: 700;

            color: #0876b9;
        }


        .amount {

            margin-top: 5px;

            font-size: 27px;

            font-weight: 700;

            color: #0876b9;
        }


        .order-id {

            margin-top: 5px;

            font-size: 12px;

            color: #777;
        }


        /*
        ============================================================
        METHOD ITEM
        ============================================================
        */

        .method {

            display: flex;

            align-items: center;

            gap: 15px;

            padding: 18px 25px;

            border-bottom: 1px solid #eeeeee;

            cursor: pointer;

            transition: 0.2s;
        }


        .method:hover {

            background: #f5f9fc;
        }


        .method.active {

            background: #e4f3ff;

            border-left: 4px solid #0876b9;

            padding-left: 21px;
        }


        .method-icon {

            width: 35px;

            height: 35px;

            display: flex;

            align-items: center;

            justify-content: center;

            font-size: 23px;
        }


        .method-content {

            flex: 1;
        }


        .method-title {

            font-size: 15px;

            font-weight: 700;

            color: #333;
        }


        .method-description {

            margin-top: 4px;

            font-size: 12px;

            color: #777;

            white-space: nowrap;

            overflow: hidden;

            text-overflow: ellipsis;
        }


        /*
        ============================================================
        RIGHT SIDE
        ============================================================
        */

        .payment-form-area {

            width: 62%;

            padding: 35px;
        }


        .form-title {

            font-size: 20px;

            font-weight: 600;

            margin-bottom: 25px;

            color: #333;
        }


        /*
        ============================================================
        ERROR
        ============================================================
        */

        .error-message {

            padding: 12px 15px;

            margin-bottom: 20px;

            background: #fff0f0;

            border: 1px solid #ffcccc;

            border-radius: 5px;

            color: #c62828;

            font-size: 14px;
        }


        /*
        ============================================================
        PAYMENT PANELS
        ============================================================
        */

        .payment-panel {

            display: none;
        }


        .payment-panel.active {

            display: block;
        }


        /*
        ============================================================
        FORM
        ============================================================
        */

        .form-group {

            margin-bottom: 20px;
        }


        .form-group label {

            display: block;

            margin-bottom: 8px;

            font-size: 13px;

            font-weight: 600;

            color: #333;
        }


        .form-control {

            width: 100%;

            height: 45px;

            padding: 0 13px;

            border: 1px solid #cfd5da;

            border-radius: 3px;

            font-size: 14px;

            outline: none;

            transition: 0.2s;
        }


        .form-control:focus {

            border-color: #0876b9;

            box-shadow:
                0 0 0 2px
                rgba(8, 118, 185, 0.08);
        }


        .form-row {

            display: flex;

            gap: 25px;
        }


        .form-row .form-group {

            flex: 1;
        }


        /*
        ============================================================
        PAY BUTTON
        ============================================================
        */

        .pay-button {

            width: 100%;

            height: 48px;

            margin-top: 5px;

            border: none;

            border-radius: 3px;

            background: #0876b9;

            color: white;

            font-size: 15px;

            font-weight: 700;

            cursor: pointer;

            transition: 0.2s;
        }


        .pay-button:hover {

            background: #075f94;
        }


        /*
        ============================================================
        DEMO NOTE
        ============================================================
        */

        .demo-note {

            margin-top: 20px;

            padding: 12px 15px;

            background: #fff8e6;

            border: 1px solid #ffe2a8;

            border-radius: 4px;

            font-size: 12px;

            line-height: 1.5;

            color: #775b16;
        }


        /*
        ============================================================
        BACK BUTTON
        ============================================================
        */

        .back-button {

            display: inline-block;

            margin-top: 18px;

            color: #666;

            text-decoration: none;

            font-size: 13px;
        }


        .back-button:hover {

            color: #0876b9;
        }


        /*
        ============================================================
        UPI
        ============================================================
        */

        .upi-info {

            padding: 18px;

            margin-bottom: 20px;

            background: #f7f9fb;

            border-radius: 5px;

            text-align: center;
        }


        .upi-icon {

            font-size: 40px;

            margin-bottom: 10px;
        }


        .upi-info p {

            margin: 5px 0;

            font-size: 13px;

            color: #666;
        }


        /*
        ============================================================
        BANK
        ============================================================
        */

        .bank-grid {

            display: grid;

            grid-template-columns:
                repeat(2, 1fr);

            gap: 12px;

            margin-bottom: 20px;
        }


        .bank-option {

            padding: 13px;

            border: 1px solid #ddd;

            border-radius: 4px;

            cursor: pointer;

            background: #fff;

            font-size: 13px;
        }


        .bank-option:hover {

            border-color: #0876b9;

            background: #f5faff;
        }


        /*
        ============================================================
        BOOKING SUMMARY
        ============================================================
        */

        .booking-summary {

            padding: 24px 25px;

            border-bottom: 1px solid #e5e5e5;

            background: #fbfcfd;
        }


        .section-heading {

            margin: 0 0 16px;

            font-size: 16px;

            font-weight: 700;

            color: #222;
        }


        .property-summary {

            display: flex;

            justify-content: space-between;

            gap: 20px;

            padding-bottom: 18px;

            margin-bottom: 18px;

            border-bottom: 1px solid #e7eaed;
        }


        .property-summary-name {

            font-size: 18px;

            font-weight: 700;

            color: #222;
        }


        .property-summary-location {

            margin-top: 5px;

            font-size: 13px;

            color: #777;
        }


        .property-summary-price {

            text-align: right;

            white-space: nowrap;
        }


        .property-summary-price strong {

            display: block;

            font-size: 18px;

            color: #0876b9;
        }


        .property-summary-price span {

            font-size: 12px;

            color: #777;
        }


        .booking-grid {

            display: grid;

            grid-template-columns: repeat(4, 1fr);

            gap: 12px;
        }


        .booking-detail {

            padding: 12px;

            background: #fff;

            border: 1px solid #e6e9ec;

            border-radius: 6px;
        }


        .booking-detail-label {

            display: block;

            margin-bottom: 5px;

            font-size: 11px;

            font-weight: 600;

            color: #777;

            text-transform: uppercase;

            letter-spacing: .3px;
        }


        .booking-detail-value {

            display: block;

            font-size: 13px;

            font-weight: 600;

            color: #333;

            word-break: break-word;
        }


        /*
        ============================================================
        GUEST DETAILS
        ============================================================
        */

        .guest-details {

            padding: 22px 25px;

            border-bottom: 1px solid #e5e5e5;
        }


        .guest-grid {

            display: grid;

            grid-template-columns: repeat(3, 1fr);

            gap: 14px;
        }


        .guest-item {

            min-width: 0;
        }


        .guest-label {

            display: block;

            margin-bottom: 5px;

            font-size: 11px;

            font-weight: 600;

            color: #777;

            text-transform: uppercase;

            letter-spacing: .3px;
        }


        .guest-value {

            font-size: 14px;

            color: #333;

            word-break: break-word;
        }


        .special-request-box {

            grid-column: 1 / -1;

            padding-top: 4px;
        }


        .special-request-value {

            padding: 10px 12px;

            background: #f7f9fb;

            border: 1px solid #e6e9ec;

            border-radius: 5px;

            font-size: 13px;

            line-height: 1.5;

            color: #555;

        }


        .total-payable-box {

            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 15px;

            margin-top: 25px;

            padding: 16px 18px;

            background: #f4f9fc;

            border: 1px solid #dcecf5;

            border-radius: 6px;

        }


        .total-payable-label {

            font-size: 14px;

            font-weight: 700;

            color: #333;

        }


        .total-payable-value {

            font-size: 22px;

            font-weight: 700;

            color: #0876b9;

            white-space: nowrap;

        }


        /*
        ============================================================
        RESPONSIVE
        ============================================================
        */

        @media (max-width: 768px) {

            .payment-body {

                flex-direction: column;
            }


            .payment-methods,
            .payment-form-area {

                width: 100%;
            }


            .payment-methods {

                border-right: none;

                border-bottom: 1px solid #ddd;
            }


            .payment-form-area {

                padding: 25px;
            }


            .form-row {

                flex-direction: column;

                gap: 0;
            }


            .booking-grid {

                grid-template-columns: repeat(2, 1fr);
            }


            .guest-grid {

                grid-template-columns: 1fr 1fr;
            }


            .property-summary {

                flex-direction: column;
            }


            .property-summary-price {

                text-align: left;
            }


            .total-payable-box {

                align-items: flex-start;

                flex-direction: column;
            }

        }

    </style>

</head>


<body>


<div class="payment-page">


    <div class="payment-container">


        <!-- ======================================================
             HEADER
        ======================================================= -->

        <div class="payment-header">

            <div class="brand">

                PG<span>Life</span>

            </div>


            <div class="secure-text">

                🔒 Secure Checkout

            </div>

        </div>



        <!-- ======================================================
             BOOKING SUMMARY
        ======================================================= -->

        <div class="booking-summary">

            <h2 class="section-heading">
                Booking Summary
            </h2>

            <div class="property-summary">

                <div>

                    <div class="property-summary-name">
                        <?= htmlspecialchars($property['name']) ?>
                    </div>

                    <div class="property-summary-location">
                        📍 <?= htmlspecialchars($property['location']) ?>
                        &nbsp; • &nbsp;
                        <?= htmlspecialchars($property['gender']) ?>
                    </div>

                </div>

                <div class="property-summary-price">
                    <strong>₹<?= number_format($daily_rent) ?></strong>
                    <span>per day</span>
                </div>

            </div>

            <div class="booking-grid">

                <div class="booking-detail">
                    <span class="booking-detail-label">Check-in</span>
                    <span class="booking-detail-value">
                        <?= date('d M Y', strtotime($from_date)) ?>
                    </span>
                </div>

                <div class="booking-detail">
                    <span class="booking-detail-label">Check-out</span>
                    <span class="booking-detail-value">
                        <?= date('d M Y', strtotime($to_date)) ?>
                    </span>
                </div>

                <div class="booking-detail">
                    <span class="booking-detail-label">Duration</span>
                    <span class="booking-detail-value">
                        <?= $duration ?> day<?= $duration == 1 ? '' : 's' ?>
                    </span>
                </div>

                <div class="booking-detail">
                    <span class="booking-detail-label">Rooms</span>
                    <span class="booking-detail-value">
                        <?= $rooms ?> room<?= $rooms == 1 ? '' : 's' ?>
                    </span>
                </div>

                <div class="booking-detail">
                    <span class="booking-detail-label">Adults</span>
                    <span class="booking-detail-value">
                        <?= $adults ?>
                    </span>
                </div>

                <div class="booking-detail">
                    <span class="booking-detail-label">Children</span>
                    <span class="booking-detail-value">
                        <?= $children ?>
                    </span>
                </div>

                <div class="booking-detail">
                    <span class="booking-detail-label">Stay charge</span>
                    <span class="booking-detail-value">
                        ₹<?= number_format($total_amount) ?>
                    </span>
                </div>

                <div class="booking-detail">
                    <span class="booking-detail-label">Payment</span>
                    <span class="booking-detail-value">
                        Pay now
                    </span>
                </div>

            </div>

        </div>


        <!-- ======================================================
             GUEST DETAILS
        ======================================================= -->

        <div class="guest-details">

            <h2 class="section-heading">
                Guest Details
            </h2>

            <div class="guest-grid">

                <div class="guest-item">
                    <span class="guest-label">Guest Name</span>
                    <div class="guest-value">
                        <?= htmlspecialchars($guest_name) ?>
                    </div>
                </div>

                <div class="guest-item">
                    <span class="guest-label">Email Address</span>
                    <div class="guest-value">
                        <?= htmlspecialchars($guest_email) ?>
                    </div>
                </div>

                <div class="guest-item">
                    <span class="guest-label">Phone Number</span>
                    <div class="guest-value">
                        <?= htmlspecialchars($guest_phone) ?>
                    </div>
                </div>

                <?php if ($special_requests !== ''): ?>
                    <div class="guest-item special-request-box">
                        <span class="guest-label">Special Requests</span>
                        <div class="special-request-value">
                            <?= nl2br(htmlspecialchars($special_requests)) ?>
                        </div>
                    </div>
                <?php endif; ?>

            </div>

            <div class="total-payable-box">
                <span class="total-payable-label">
                    Total Payable
                </span>
                <span class="total-payable-value">
                    ₹<?= number_format($total_amount, 2) ?>
                </span>
            </div>

        </div>


        <!-- ======================================================
             PAYMENT BODY
        ======================================================= -->

        <div class="payment-body">


            <!-- ==================================================
                 LEFT SIDE
            =================================================== -->

            <div class="payment-methods">


                <div class="amount-box">

                    <div class="amount-label">
                        Total Payable
                    </div>


                    <div class="amount">

                        ₹<?= number_format($total_amount, 2) ?>

                    </div>


                    <div class="order-id">

                        Order ID:
                        PGL<?= date('YmdHis') ?>

                    </div>

                </div>



                <!-- CARD -->

                <div
                    class="method active"
                    data-method="card"
                >

                    <div class="method-icon">
                        💳
                    </div>


                    <div class="method-content">

                        <div class="method-title">

                            Debit and Credit Card

                        </div>


                        <div class="method-description">

                            Pay using your debit or credit card

                        </div>

                    </div>

                </div>



                <!-- UPI -->

                <div
                    class="method"
                    data-method="upi"
                >

                    <div class="method-icon">
                        📱
                    </div>


                    <div class="method-content">

                        <div class="method-title">

                            Pay via UPI

                        </div>


                        <div class="method-description">

                            Pay instantly using UPI

                        </div>

                    </div>

                </div>



                <!-- NET BANKING -->

                <div
                    class="method"
                    data-method="netbanking"
                >

                    <div class="method-icon">
                        🏦
                    </div>


                    <div class="method-content">

                        <div class="method-title">

                            Net Banking

                        </div>


                        <div class="method-description">

                            Pay directly from your bank account

                        </div>

                    </div>

                </div>



                <!-- EMI -->

                <div
                    class="method"
                    data-method="emi"
                >

                    <div class="method-icon">
                        💰
                    </div>


                    <div class="method-content">

                        <div class="method-title">

                            EMI

                        </div>


                        <div class="method-description">

                            Split your payment into easy installments

                        </div>

                    </div>

                </div>



                <!-- WALLET -->

                <div
                    class="method"
                    data-method="wallet"
                >

                    <div class="method-icon">
                        👛
                    </div>


                    <div class="method-content">

                        <div class="method-title">

                            Wallet

                        </div>


                        <div class="method-description">

                            Pay instantly using your wallet balance

                        </div>

                    </div>

                </div>


            </div>



            <!-- ==================================================
                 RIGHT SIDE
            =================================================== -->

            <div class="payment-form-area">


                <div class="form-title">

                    Enter payment details

                </div>


                <?php if ($payment_error !== ''): ?>

                    <div class="error-message">

                        <?= htmlspecialchars($payment_error) ?>

                    </div>

                <?php endif; ?>



                <form
                    method="POST"
                    action=""
                    id="payment-form"
                >


                    <!-- ==========================================
                         HIDDEN METHOD
                    =========================================== -->

                    <input
                        type="hidden"
                        name="payment_method"
                        id="payment-method"
                        value="card"
                    >



                    <!-- ==========================================
                         CARD PANEL
                    =========================================== -->

                    <div
                        class="payment-panel active"
                        id="panel-card"
                    >

                        <div class="form-group">

                            <label>
                                Card Number
                            </label>

                            <input
                                type="text"
                                name="card_number"
                                class="form-control"
                                placeholder="1234 5678 9012 3456"
                                maxlength="19"
                            >

                        </div>



                        <div class="form-group">

                            <label>
                                Card Holder Name
                            </label>

                            <input
                                type="text"
                                name="card_name"
                                class="form-control"
                                placeholder="Enter card holder name"
                            >

                        </div>



                        <div class="form-row">


                            <div class="form-group">

                                <label>
                                    Expiry Date
                                </label>

                                <input
                                    type="text"
                                    name="expiry"
                                    class="form-control"
                                    placeholder="MM/YY"
                                    maxlength="5"
                                >

                            </div>



                            <div class="form-group">

                                <label>
                                    CVV
                                </label>

                                <input
                                    type="password"
                                    name="cvv"
                                    class="form-control"
                                    placeholder="CVV"
                                    maxlength="3"
                                >

                            </div>


                        </div>


                    </div>



                    <!-- ==========================================
                         UPI PANEL
                    =========================================== -->

                    <div
                        class="payment-panel"
                        id="panel-upi"
                    >

                        <div class="upi-info">

                            <div class="upi-icon">
                                📱
                            </div>

                            <p>
                                Enter your UPI ID to continue
                            </p>

                            <p>
                                Example:
                                name@upi
                            </p>

                        </div>


                        <div class="form-group">

                            <label>
                                UPI ID
                            </label>

                            <input
                                type="text"
                                name="upi_id"
                                class="form-control"
                                placeholder="example@upi"
                            >

                        </div>

                    </div>



                    <!-- ==========================================
                         NET BANKING PANEL
                    =========================================== -->

                    <div
                        class="payment-panel"
                        id="panel-netbanking"
                    >

                        <div class="form-group">

                            <label>
                                Select Your Bank
                            </label>

                            <select
                                name="bank"
                                class="form-control"
                            >

                                <option value="">
                                    Select Bank
                                </option>

                                <option value="SBI">
                                    State Bank of India
                                </option>

                                <option value="HDFC">
                                    HDFC Bank
                                </option>

                                <option value="ICICI">
                                    ICICI Bank
                                </option>

                                <option value="AXIS">
                                    Axis Bank
                                </option>

                                <option value="PNB">
                                    Punjab National Bank
                                </option>

                                <option value="OTHER">
                                    Other Bank
                                </option>

                            </select>

                        </div>

                    </div>



                    <!-- ==========================================
                         EMI PANEL
                    =========================================== -->

                    <div
                        class="payment-panel"
                        id="panel-emi"
                    >

                        <div class="form-group">

                            <label>
                                Select EMI Option
                            </label>

                            <select
                                name="emi_option"
                                class="form-control"
                            >

                                <option value="">
                                    Select EMI
                                </option>

                                <option value="3">
                                    3 Months EMI
                                </option>

                                <option value="6">
                                    6 Months EMI
                                </option>

                                <option value="9">
                                    9 Months EMI
                                </option>

                                <option value="12">
                                    12 Months EMI
                                </option>

                            </select>

                        </div>

                    </div>



                    <!-- ==========================================
                         WALLET PANEL
                    =========================================== -->

                    <div
                        class="payment-panel"
                        id="panel-wallet"
                    >

                        <div class="form-group">

                            <label>
                                Select Wallet
                            </label>

                            <select
                                name="wallet"
                                class="form-control"
                            >

                                <option value="">
                                    Select Wallet
                                </option>

                                <option value="Paytm">
                                    Paytm
                                </option>

                                <option value="PhonePe">
                                    PhonePe
                                </option>

                                <option value="Amazon Pay">
                                    Amazon Pay
                                </option>

                                <option value="Mobikwik">
                                    Mobikwik
                                </option>

                            </select>

                        </div>

                    </div>



                    <!-- ==========================================
                         PAY BUTTON
                    =========================================== -->

                    <button
                        type="submit"
                        class="pay-button"
                        id="pay-button"
                    >

                        Make Payment

                    </button>


                </form>



                <div class="demo-note">

                    <strong>Demo Payment:</strong>

                    This payment page is for your PGLife
                    project demonstration. No real money will
                    be charged.

                </div>



                <a
                    href="booking.php?id=<?= $property_id ?>"
                    class="back-button"
                >

                    ← Back to Booking

                </a>


            </div>


        </div>


    </div>


</div>



<script>


/*
|--------------------------------------------------------------------------
| PAYMENT METHOD SWITCHING
|--------------------------------------------------------------------------
*/

document.addEventListener(
    "DOMContentLoaded",
    function () {


        var methods =
            document.querySelectorAll(".method");


        var panels =
            document.querySelectorAll(".payment-panel");


        var paymentMethod =
            document.getElementById("payment-method");


        var payButton =
            document.getElementById("pay-button");


        methods.forEach(
            function (method) {


                method.addEventListener(
                    "click",
                    function () {


                        /*
                        |--------------------------------------------------
                        | Remove active class
                        |--------------------------------------------------
                        */

                        methods.forEach(
                            function (item) {

                                item.classList.remove(
                                    "active"
                                );

                            }
                        );


                        panels.forEach(
                            function (panel) {

                                panel.classList.remove(
                                    "active"
                                );

                            }
                        );


                        /*
                        |--------------------------------------------------
                        | Current method
                        |--------------------------------------------------
                        */

                        this.classList.add(
                            "active"
                        );


                        var selectedMethod =
                            this.getAttribute(
                                "data-method"
                            );


                        paymentMethod.value =
                            selectedMethod;


                        /*
                        |--------------------------------------------------
                        | Show selected panel
                        |--------------------------------------------------
                        */

                        var selectedPanel =
                            document.getElementById(
                                "panel-" +
                                selectedMethod
                            );


                        if (selectedPanel) {

                            selectedPanel.classList.add(
                                "active"
                            );

                        }


                        /*
                        |--------------------------------------------------
                        | Change button text
                        |--------------------------------------------------
                        */

                        payButton.innerText =
                            "Make Payment";

                    }
                );

            }
        );


        /*
        |--------------------------------------------------------------------------
        | CARD NUMBER FORMATTING
        |--------------------------------------------------------------------------
        */

        var cardInput =
            document.querySelector(
                'input[name="card_number"]'
            );


        if (cardInput) {

            cardInput.addEventListener(
                "input",
                function () {


                    var value =
                        this.value
                            .replace(/\D/g, "")
                            .substring(0, 16);


                    var formatted =
                        value.match(/.{1,4}/g);


                    this.value =
                        formatted
                            ? formatted.join(" ")
                            : "";

                }
            );

        }


        /*
        |--------------------------------------------------------------------------
        | EXPIRY FORMATTING
        |--------------------------------------------------------------------------
        */

        var expiryInput =
            document.querySelector(
                'input[name="expiry"]'
            );


        if (expiryInput) {

            expiryInput.addEventListener(
                "input",
                function () {


                    var value =
                        this.value
                            .replace(/\D/g, "")
                            .substring(0, 4);


                    if (value.length >= 3) {

                        this.value =
                            value.substring(0, 2) +
                            "/" +
                            value.substring(2);

                    } else {

                        this.value =
                            value;

                    }

                }
            );

        }


    }
);

</script>


</body>

</html>