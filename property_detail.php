<?php
session_start();

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

/*<div class="button-container col-6">
|--------------------------------------------------------------------------
| PROPERTY DATA
|--------------------------------------------------------------------------
| Abhi hum properties ko PHP array se manage kar rahe hain.
| Baad me isi structure ko MySQL database se connect kar sakte hain.
|--------------------------------------------------------------------------
*/

$properties = [

    1 => [
        "name" => "Navkar Paying Guest",
        "city" => "Mumbai",
        "address" => "44, Juhu Scheme, Juhu, Mumbai, Maharashtra 400058",
        "gender" => "male",
        "rent" => 9500,
        "interested" => 3,

        "images" => [
            "img/properties/1/1d4f0757fdb86d5f.jpg"
        ],

        "rating_clean" => 4.5,
        "rating_food" => 4.2,
        "rating_safety" => 4.7,

        "description" => "Navkar Paying Guest is a comfortable PG accommodation located in Juhu, Mumbai. The property is suitable for students and working professionals looking for a convenient place to stay with essential facilities and a comfortable living environment.",

        "amenities" => [
            "Building" => [
                ["icon" => "powerbackup", "name" => "Power backup"],
                ["icon" => "lift", "name" => "Lift"]
            ],
            "Common Area" => [
                ["icon" => "wifi", "name" => "Wifi"],
                ["icon" => "tv", "name" => "TV"],
                ["icon" => "rowater", "name" => "Water Purifier"],
                ["icon" => "dining", "name" => "Dining"],
                ["icon" => "washingmachine", "name" => "Washing Machine"]
            ],
            "Bedroom" => [
                ["icon" => "bed", "name" => "Bed with Matress"],
                ["icon" => "ac", "name" => "Air Conditioner"]
            ],
            "Washroom" => [
                ["icon" => "geyser", "name" => "Geyser"]
            ]
        ],

        "testimonials" => [
            [
                "name" => "Ashutosh Gowariker",
                "content" => "You just have to arrive at the place, it's fully furnished and stocked with all basic amenities and services."
            ],
            [
                "name" => "Karan Johar",
                "content" => "A comfortable place to stay with useful facilities and a convenient location."
            ]
        ]
    ],

    2 => [
        "name" => "Ganpati Paying Guest",
        "city" => "Mumbai",
        "address" => "Police Beat, Sainath Complex, Besides, SV Rd, Daulat Nagar, Borivali East, Mumbai - 400066",
        "gender" => "unisex",
        "rent" => 8500,
        "interested" => 6,

        "images" => [
            "img/properties/1/eace7b9114fd6046.jpg"
        ],

        "rating_clean" => 4.3,
        "rating_food" => 3.4,
        "rating_safety" => 4.8,

        "description" => "Furnished studio apartment - share it with close friends! Located in a convenient area of Mumbai, this property is available for comfortable living. Go for a private room or opt for a shared one and make it your own abode. The property provides a comfortable common living environment along with essential facilities.",

        "amenities" => [
            "Building" => [
                ["icon" => "powerbackup", "name" => "Power backup"],
                ["icon" => "lift", "name" => "Lift"]
            ],
            "Common Area" => [
                ["icon" => "wifi", "name" => "Wifi"],
                ["icon" => "tv", "name" => "TV"],
                ["icon" => "rowater", "name" => "Water Purifier"],
                ["icon" => "dining", "name" => "Dining"],
                ["icon" => "washingmachine", "name" => "Washing Machine"]
            ],
            "Bedroom" => [
                ["icon" => "bed", "name" => "Bed with Matress"],
                ["icon" => "ac", "name" => "Air Conditioner"]
            ],
            "Washroom" => [
                ["icon" => "geyser", "name" => "Geyser"]
            ]
        ],

        "testimonials" => [
            [
                "name" => "Ashutosh Gowariker",
                "content" => "You just have to arrive at the place, it's fully furnished and stocked with all basic amenities and services and even your friends are welcome."
            ],
            [
                "name" => "Karan Johar",
                "content" => "A comfortable place with useful facilities and a convenient location for students and working professionals."
            ]
        ]
    ],

    3 => [
        "name" => "PG for Girls Borivali West",
        "city" => "Mumbai",
        "address" => "Plot no.258/D4, Gorai no.2, Borivali West, Mumbai, Maharashtra 400092",
        "gender" => "female",
        "rent" => 8000,
        "interested" => 2,

        "images" => [
            "img/properties/1/46ebbb537aa9fb0a.jpg"
        ],

        "rating_clean" => 3.5,
        "rating_food" => 3.6,
        "rating_safety" => 4.2,

        "description" => "PG for Girls Borivali West offers a comfortable stay for female students and working professionals. The property is located in Borivali West and provides essential amenities for a convenient and comfortable living experience.",

        "amenities" => [
            "Building" => [
                ["icon" => "powerbackup", "name" => "Power backup"],
                ["icon" => "lift", "name" => "Lift"]
            ],
            "Common Area" => [
                ["icon" => "wifi", "name" => "Wifi"],
                ["icon" => "tv", "name" => "TV"],
                ["icon" => "rowater", "name" => "Water Purifier"],
                ["icon" => "dining", "name" => "Dining"],
                ["icon" => "washingmachine", "name" => "Washing Machine"]
            ],
            "Bedroom" => [
                ["icon" => "bed", "name" => "Bed with Matress"],
                ["icon" => "ac", "name" => "Air Conditioner"]
            ],
            "Washroom" => [
                ["icon" => "geyser", "name" => "Geyser"]
            ]
        ],

        "testimonials" => [
            [
                "name" => "Priya Sharma",
                "content" => "A comfortable and convenient place to stay with all the basic amenities required for everyday living."
            ],
            [
                "name" => "Neha Kapoor",
                "content" => "The property provides a peaceful environment and useful facilities for students and working professionals."
            ]
        ]
    ]
];

/*
|----<span class="interested-user-count">----------------------------------------------------------------------
| GET PROPERTY ID
|--------------------------------------------------------------------------
*/
require_once "includes/database_connect.php";

$property_id = isset($_GET['id']) ? (int) $_GET['id'] : 2;

$is_interested = false;

if (isset($_SESSION['user_id'])) {

    $user_id = (int) $_SESSION['user_id'];

    $sql = "SELECT id
            FROM interested_users_properties
            WHERE user_id = $user_id
            AND property_id = $property_id
            LIMIT 1";

    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $is_interested = true;
    }
}

require_once "includes/database_connect.php";

$sql = "SELECT COUNT(*) AS interested_count
        FROM interested_users_properties
        WHERE property_id = $property_id";

$result = mysqli_query($conn, $sql);

$row = mysqli_fetch_assoc($result);

$interested_count = (int) $row['interested_count'];
if (!isset($properties[$property_id])) {
    $property_id = 2;
}

$property = $properties[$property_id];


/*
|--------------------------------------------------------------------------
| HELPER FUNCTION
|--------------------------------------------------------------------------
*/

function e($value)
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}


/*
|--------------------------------------------------------------------------
| TOTAL RATING
|--------------------------------------------------------------------------
*/

$total_rating = (
    $property['rating_clean'] +
    $property['rating_food'] +
    $property['rating_safety']
) / 3;

$total_rating = round($total_rating, 1);


/*
|--------------------------------------------------------------------------
| GENDER IMAGE
|--------------------------------------------------------------------------
*/

$gender_image = "img/unisex.png";

if ($property['gender'] === "male") {
    $gender_image = "img/male.png";
} elseif ($property['gender'] === "female") {
    $gender_image = "img/female.png";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?= e($property['name']) ?> | PG Life</title>

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

    <link href="css/property_detail.css" rel="stylesheet" />

</head>


<body>


<?php
require_once "includes/header.php";
?>


<!-- ============================= -->
<!-- BREADCRUMB -->
<!-- ============================= -->

<nav aria-label="breadcrumb">

    <ol class="breadcrumb py-2">

        <li class="breadcrumb-item">
            <a href="index.php">Home</a>
        </li>

        <li class="breadcrumb-item">
            <a href="property_list.php">
                <?= e($property['city']) ?>
            </a>
        </li>

        <li class="breadcrumb-item active" aria-current="page">
            <?= e($property['name']) ?>
        </li>

    </ol>

</nav>


<!-- ============================= -->
<!-- PROPERTY IMAGES -->
<!-- ============================= -->

<div id="property-images"
     class="carousel slide"
     data-ride="carousel">


    <ol class="carousel-indicators">

        <?php foreach ($property['images'] as $index => $image) { ?>

            <li
                data-target="#property-images"
                data-slide-to="<?= $index ?>"
                class="<?= $index == 0 ? 'active' : '' ?>">
            </li>

        <?php } ?>

    </ol>


    <div class="carousel-inner">

        <?php foreach ($property['images'] as $index => $image) { ?>

            <div class="carousel-item <?= $index == 0 ? 'active' : '' ?>">

                <img
                    class="d-block w-100"
                    src="<?= e($image) ?>"
                    alt="<?= e($property['name']) ?>">

            </div>

        <?php } ?>

    </div>


    <a
        class="carousel-control-prev"
        href="#property-images"
        role="button"
        data-slide="prev">

        <span
            class="carousel-control-prev-icon"
            aria-hidden="true">
        </span>

        <span class="sr-only">
            Previous
        </span>

    </a>


    <a
        class="carousel-control-next"
        href="#property-images"
        role="button"
        data-slide="next">

        <span
            class="carousel-control-next-icon"
            aria-hidden="true">
        </span>

        <span class="sr-only">
            Next
        </span>

    </a>

</div>


<!-- ============================= -->
<!-- PROPERTY SUMMARY -->
<!-- ============================= -->

<div class="property-summary page-container">


    <div class="row no-gutters justify-content-between">


        <!-- RATING -->

        <div
            class="star-container"
            title="<?= $total_rating ?>">

            <?php

            for ($i = 0; $i < 5; $i++) {

                if ($total_rating >= $i + 0.8) {

            ?>

                    <i class="fas fa-star"></i>

            <?php

                } elseif ($total_rating >= $i + 0.3) {

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


        <!-- INTERESTED -->

        <div class="interested-container">
    <i
    class="is-interested-image <?= $is_interested ? 'fas' : 'far' ?> fa-heart"
    property_id="<?= $property_id ?>">
</i>
            <div class="interested-text">

                <span class="interested-user-count">
                    <?= $interested_count ?>
                </span>
                    interested

            </div>

        </div>

    </div>


    <!-- PROPERTY DETAILS -->

    <div class="detail-container">

        <div class="property-name">

            <?= e($property['name']) ?>

        </div>


        <div class="property-address">

            <?= e($property['address']) ?>

        </div>


        <div class="property-gender">

            <img
                src="<?= e($gender_image) ?>"
                alt="<?= e($property['gender']) ?>">

        </div>

    </div>


    <!-- RENT -->

    <div class="row no-gutters">

        <div class="rent-container col-6">

            <div class="rent">

                ₹ <?= number_format($property['rent']) ?>/-

            </div>

            <div class="rent-unit">

                per month

            </div>

        </div>


        <div class="button-container col-6">

            <?php if ($user_id): ?>

                <a
                    href="booking.php?id=<?= $property_id ?>"
                    class="btn btn-primary">

                    Book Now

                </a>

            <?php else: ?>
                <a
                    href="#"
                    class="btn btn-primary"
                    data-toggle="modal"
                    data-target="#login-modal"
                    onclick="sessionStorage.setItem('booking_return_url', window.location.href);">

                    Book Now

                </a>

            <?php endif; ?>

        </div>

    </div>

</div>


<!-- ============================= -->
<!-- AMENITIES -->
<!-- ============================= -->

<div class="property-amenities">

    <div class="page-container">

        <h1>Amenities</h1>


        <div class="row justify-content-between">


            <?php foreach ($property['amenities'] as $type => $amenities) { ?>


                <div class="col-md-auto">

                    <h5>
                        <?= e($type) ?>
                    </h5>


                    <?php foreach ($amenities as $amenity) { ?>

                        <div class="amenity-container">

                            <img
                                src="img/amenities/<?= e($amenity['icon']) ?>.svg"
                                alt="<?= e($amenity['name']) ?>">

                            <span>
                                <?= e($amenity['name']) ?>
                            </span>

                        </div>

                    <?php } ?>


                </div>


            <?php } ?>


        </div>

    </div>

</div>


<!-- ============================= -->
<!-- ABOUT PROPERTY -->
<!-- ============================= -->

<div class="property-about page-container">

    <h1>
        About the Property
    </h1>

    <p>
        <?= e($property['description']) ?>
    </p>

</div>


<!-- ============================= -->
<!-- PROPERTY RATING -->
<!-- ============================= -->

<div class="property-rating">

    <div class="page-container">

        <h1>
            Property Rating
        </h1>


        <div class="row align-items-center justify-content-between">


            <div class="col-md-6">


                <!-- CLEANLINESS -->

                <div class="rating-criteria row">

                    <div class="col-6">

                        <i class="rating-criteria-icon fas fa-broom"></i>

                        <span class="rating-criteria-text">
                            Cleanliness
                        </span>

                    </div>


                    <div
                        class="rating-criteria-star-container col-6"
                        title="<?= $property['rating_clean'] ?>">

                        <?php

                        $rating = $property['rating_clean'];

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

                </div>


                <!-- FOOD -->

                <div class="rating-criteria row">

                    <div class="col-6">

                        <i class="rating-criteria-icon fas fa-utensils"></i>

                        <span class="rating-criteria-text">
                            Food Quality
                        </span>

                    </div>


                    <div
                        class="rating-criteria-star-container col-6"
                        title="<?= $property['rating_food'] ?>">

                        <?php

                        $rating = $property['rating_food'];

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

                </div>


                <!-- SAFETY -->

                <div class="rating-criteria row">

                    <div class="col-6">

                        <i class="rating-criteria-icon fa fa-lock"></i>

                        <span class="rating-criteria-text">
                            Safety
                        </span>

                    </div>


                    <div
                        class="rating-criteria-star-container col-6"
                        title="<?= $property['rating_safety'] ?>">

                        <?php

                        $rating = $property['rating_safety'];

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

                </div>


            </div>


            <!-- TOTAL RATING -->

            <div class="col-md-4">

                <div class="rating-circle">

                    <div class="total-rating">

                        <?= $total_rating ?>

                    </div>


                    <div class="rating-circle-star-container">

                        <?php

                        for ($i = 0; $i < 5; $i++) {

                            if ($total_rating >= $i + 0.8) {

                        ?>

                                <i class="fas fa-star"></i>

                        <?php

                            } elseif ($total_rating >= $i + 0.3) {

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

                </div>

            </div>


        </div>

    </div>

</div>


<!-- ============================= -->
<!-- TESTIMONIALS -->
<!-- ============================= -->

<div class="property-testimonials page-container">

    <h1>
        What people say
    </h1>


    <?php foreach ($property['testimonials'] as $testimonial) { ?>


        <div class="testimonial-block">


            <div class="testimonial-image-container">

                <img
                    class="testimonial-img"
                    src="img/man.png"
                    alt="User">

            </div>


            <div class="testimonial-text">

                <i
                    class="fa fa-quote-left"
                    aria-hidden="true">
                </i>

                <p>
                    <?= e($testimonial['content']) ?>
                </p>

            </div>


            <div class="testimonial-name">

                - <?= e($testimonial['name']) ?>

            </div>


        </div>


    <?php } ?>


</div>


<!-- ============================= -->
<!-- SIGNUP MODAL -->
<!-- ============================= -->

<div
    class="modal fade"
    id="signup-modal"
    tabindex="-1"
    role="dialog"
    aria-labelledby="signup-heading"
    aria-hidden="true">


    <div
        class="modal-dialog"
        role="document">


        <div class="modal-content">


            <div class="modal-header">

                <h5
                    class="modal-title"
                    id="signup-heading">

                    Signup with PGLife

                </h5>


                <button
                    type="button"
                    class="close"
                    data-dismiss="modal"
                    aria-label="Close">

                    <span aria-hidden="true">
                        &times;
                    </span>

                </button>

            </div>


            <div class="modal-body">


                <form
                    id="signup-form"
                    class="form"
                    role="form">


                    <div class="input-group form-group">

                        <div class="input-group-prepend">

                            <span class="input-group-text">
                                <i class="fas fa-user"></i>
                            </span>

                        </div>

                        <input
                            type="text"
                            class="form-control"
                            name="full_name"
                            placeholder="Full Name"
                            maxlength="30"
                            required>

                    </div>


                    <div class="input-group form-group">

                        <div class="input-group-prepend">

                            <span class="input-group-text">
                                <i class="fas fa-phone-alt"></i>
                            </span>

                        </div>

                        <input
                            type="text"
                            class="form-control"
                            name="phone"
                            placeholder="Phone Number"
                            maxlength="10"
                            minlength="10"
                            required>

                    </div>


                    <div class="input-group form-group">

                        <div class="input-group-prepend">

                            <span class="input-group-text">
                                <i class="fas fa-envelope"></i>
                            </span>

                        </div>

                        <input
                            type="email"
                            class="form-control"
                            name="email"
                            placeholder="Email"
                            required>

                    </div>


                    <div class="input-group form-group">

                        <div class="input-group-prepend">

                            <span class="input-group-text">
                                <i class="fas fa-lock"></i>
                            </span>

                        </div>

                        <input
                            type="password"
                            class="form-control"
                            name="password"
                            placeholder="Password"
                            minlength="6"
                            required>

                    </div>


                    <div class="input-group form-group">

                        <div class="input-group-prepend">

                            <span class="input-group-text">
                                <i class="fas fa-university"></i>
                            </span>

                        </div>

                        <input
                            type="text"
                            class="form-control"
                            name="college_name"
                            placeholder="College Name"
                            maxlength="150"
                            required>

                    </div>


                    <div class="form-group">

                        <span>
                            I'm a
                        </span>

                        <input
                            type="radio"
                            class="ml-3"
                            id="gender-male"
                            name="gender"
                            value="male"
                            required>

                        Male


                        <input
                            type="radio"
                            class="ml-3"
                            id="gender-female"
                            name="gender"
                            value="female">

                        Female

                    </div>


                    <div class="form-group">

                        <button
                            type="submit"
                            class="btn btn-block btn-primary">

                            Create Account

                        </button>

                    </div>


                </form>

            </div>


            <div class="modal-footer">

                <span>

                    Already have an account?

                    <a
                        href="#"
                        data-dismiss="modal"
                        data-toggle="modal"
                        data-target="#login-modal">

                        Login

                    </a>

                </span>

            </div>


        </div>

    </div>

</div>


<!-- ============================= -->
<!-- LOGIN MODAL -->
<!-- ============================= -->

<div
    class="modal fade"
    id="login-modal"
    tabindex="-1"
    role="dialog"
    aria-labelledby="login-heading"
    aria-hidden="true">


    <div
        class="modal-dialog modal-dialog-centered"
        role="document">


        <div class="modal-content">


            <div class="modal-header">

                <h5
                    class="modal-title"
                    id="login-heading">

                    Login with PGLife

                </h5>


                <button
                    type="button"
                    class="close"
                    data-dismiss="modal"
                    aria-label="Close">

                    <span aria-hidden="true">
                        &times;
                    </span>

                </button>

            </div>


            <div class="modal-body">


                <form
                    id="login-form"
                    class="form"
                    role="form">


                    <div class="input-group form-group">

                        <div class="input-group-prepend">

                            <span class="input-group-text">
                                <i class="fas fa-user"></i>
                            </span>

                        </div>

                        <input
                            type="email"
                            class="form-control"
                            name="email"
                            placeholder="Email"
                            required>

                    </div>


                    <div class="input-group form-group">

                        <div class="input-group-prepend">

                            <span class="input-group-text">
                                <i class="fas fa-lock"></i>
                            </span>

                        </div>

                        <input
                            type="password"
                            class="form-control"
                            name="password"
                            placeholder="Password"
                            minlength="6"
                            required>

                    </div>


                    <div class="form-group">

                        <button
                            type="submit"
                            class="btn btn-block btn-primary">

                            Login

                        </button>

                    </div>


                </form>

            </div>


            <div class="modal-footer">

                <span>

                    <a
                        href="#"
                        data-dismiss="modal"
                        data-toggle="modal"
                        data-target="#signup-modal">

                        Click here

                    </a>

                    to register a new account

                </span>

            </div>


        </div>

    </div>

</div>


<!-- ============================= -->
<!-- FOOTER -->
<!-- ============================= -->

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


<!-- ============================= -->
<!-- JAVASCRIPT -->
<!-- ============================= -->

<script
    type="text/javascript"
    src="js/jquery.js">
</script>

<script
    type="text/javascript"
    src="js/bootstrap.min.js">
</script>

<script
    type="text/javascript"
    src="js/common.js">
</script>

<script
    type="text/javascript"
    src="js/property_detail.js">
</script>

</body>

</html>