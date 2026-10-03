<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Best PG's in Mumbai | PG Life</title>

    <link href="css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://use.fontawesome.com/releases/v5.11.2/css/all.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,600;0,700;0,800;1,300;1,400;1,600;1,700;1,800&display=swap" rel="stylesheet" />
    <link href="css/common.css" rel="stylesheet" />
    <link href="css/property_list.css" rel="stylesheet" />
</head>

<body>
    
    <?php
require_once "includes/header.php";
    ?>
    <?php
require_once "includes/database_connect.php";

$interested_properties = array();

$interested_counts = array();

$sql = "SELECT property_id, COUNT(*) AS interested_count
        FROM interested_users_properties
        GROUP BY property_id";

$result = mysqli_query($conn, $sql);

if ($result) {

    while ($row = mysqli_fetch_assoc($result)) {

        $interested_counts[(int) $row['property_id']]
            = (int) $row['interested_count'];

    }

}

if (isset($_SESSION['user_id'])) {

    $user_id = (int) $_SESSION['user_id'];

    $sql = "SELECT property_id
            FROM interested_users_properties
            WHERE user_id = $user_id";

    $result = mysqli_query($conn, $sql);

    if ($result) {

        while ($row = mysqli_fetch_assoc($result)) {

            $interested_properties[] = (int) $row['property_id'];

        }

    }
}
    ?>


    <div id="loading">
    </div>

    <div class="page-container">
        <div class="filter-bar row justify-content-around">
            <div class="col-auto" data-toggle="modal" data-target="#filter-modal">
                <img src="img/filter.png" alt="filter" />
                <span>Filter</span>
            </div>
            <div class="col-auto">
                <img src="img/desc.png" alt="sort-desc" />
                <span>Highest rent first</span>
            </div>
            <div class="col-auto">
                <img src="img/asc.png" alt="sort-asc" />
                <span>Lowest rent first</span>
            </div>
        </div>

        <div class="property-list-container">
                <!-- Navkar -->

        
    <div class="property-card row" data-gender="male" data-rent="9500">
                    <div class="image-container col-md-4">
                    <img src="img/properties/1/1d4f0757fdb86d5f.jpg" />
                </div>
                <div class="content-container col-md-8">
                    <div class="row no-gutters justify-content-between">
                        <div class="star-container" title="4.5">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                        </div>
                        <div class="interested-container">
                            <i
                                class="is-interested-image <?= in_array(1, $interested_properties) ? 'fas' : 'far' ?> fa-heart"
                                property_id="1">
                            </i>

                            <div class="interested-text">
                                <?= $interested_counts[1] ?? 0 ?> interested
                            </div>
                        </div>
                    </div>
                    <div class="detail-container">
                        <div class="property-name">Navkar Paying Guest</div>
                        <div class="property-address">44, Juhu Scheme, Juhu, Mumbai, Maharashtra 400058</div>
                        <div class="property-gender">
                            <img src="img/male.png" />
                        </div>
                    </div>
                    <div class="row no-gutters">
                        <div class="rent-container col-6">
                            <div class="rent">Rs 9,500/-</div>
                            <div class="rent-unit">per month</div>
                        </div>
                        <div class="button-container col-6">
                            <a href="property_detail.php?id=1" class="btn btn-primary">View</a>
                        </div>
                    </div>
                </div>
            </div>
                <div class="property-card row " data-gender="unisex" data-rent="8500">
                    <div class="image-container col-md-4">
                        <img src="img/properties/1/eace7b9114fd6046.jpg" />
                    </div>
                    <div class="content-container col-md-8">
                        <div class="row no-gutters justify-content-between">
                            <div class="star-container" title="4.8">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                            <div class="interested-container">
                                <i
                                    class="is-interested-image <?= in_array(2, $interested_properties) ? 'fas' : 'far' ?> fa-heart"
                                    property_id="2"
                                ></i>

                                <div class="interested-text">
                                    <?= $interested_counts[2] ?? 0 ?> interested
                                </div>

                            </div>
                        </div>    
                        <div class="detail-container">
                            <div class="property-name">Ganpati Paying Guest</div>
                            <div class="property-address">Police Beat, Sainath Complex, Besides, SV Rd, Daulat Nagar, Borivali East, Mumbai - 400066</div>
                            <div class="property-gender">
                                <img src="img/unisex.png" />
                            </div>
                        </div>
                        <div class="row no-gutters">
                            <div class="rent-container col-6">
                                <div class="rent">Rs 8,500/-</div>
                                <div class="rent-unit">per month</div>
                            </div>
                            <div class="button-container col-6">
                                <a href="property_detail.php?id=2" class="btn btn-primary">View</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="property-card row" data-gender="female" data-rent="8000">
                    <div class="image-container col-md-4">
                        <img src="img/properties/1/46ebbb537aa9fb0a.jpg" />
                    </div>
                    <div class="content-container col-md-8">
                        <div class="row no-gutters justify-content-between">
                            <div class="star-container" title="3.5">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star-half-alt"></i>
                                <i class="far fa-star"></i>
                            </div>
                            <div class="interested-container">
                                <i
                                    class="is-interested-image <?= in_array(3, $interested_properties) ? 'fas' : 'far' ?> fa-heart"
                                    property_id="3">
                                </i>

                                <div class="interested-text">
                                    <?= $interested_counts[3] ?? 0 ?> interested
                                </div>
                            </div>
                        </div>
                        <div class="detail-container">
                            <div class="property-name">PG for Girls Borivali West</div>
                            <div class="property-address">Plot no.258/D4, Gorai no.2, Borivali West, Mumbai, Maharashtra 400092</div>
                            <div class="property-gender">
                                <img src="img/female.png" />
                            </div>
                        </div>
                        <div class="row no-gutters">
                            <div class="rent-container col-6">
                                <div class="rent">Rs 8,000/-</div>
                                <div class="rent-unit">per month</div>
                            </div>
                            <div class="button-container col-6">
                                <a href="property_detail.php?id=3" class="btn btn-primary">View</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>
    <div class="modal fade" id="filter-modal" tabindex="-1" role="dialog" aria-labelledby="filter-heading" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title" id="filter-heading">Filters</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <h5>Gender</h5>
                    <hr />
                    <div>
                        <button class="btn btn-outline-dark btn-active" data-filter="all">
                            No Filter
                        </button>

                        <button class="btn btn-outline-dark" data-filter="unisex">
                            <i class="fas fa-venus-mars"></i> Unisex
                        </button>

                        <button class="btn btn-outline-dark" data-filter="male">
                            <i class="fas fa-mars"></i> Male
                        </button>

                        <button class="btn btn-outline-dark" data-filter="female">
                            <i class="fas fa-venus"></i> Female
                        </button>
                    </div>
                </div>

                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-success">Okay</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="signup-modal" tabindex="-1" role="dialog" aria-labelledby="signup-heading" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="signup-heading">Signup with PGLife</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <form id="signup-form" class="form" role="form">
                        <div class="input-group form-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fas fa-user"></i>
                                </span>
                            </div>
                            <input type="text" class="form-control" name="full_name" placeholder="Full Name" maxlength="30" required>
                        </div>

                        <div class="input-group form-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fas fa-phone-alt"></i>
                                </span>
                            </div>
                            <input type="text" class="form-control" name="phone" placeholder="Phone Number" maxlength="10" minlength="10" required>
                        </div>

                        <div class="input-group form-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fas fa-envelope"></i>
                                </span>
                            </div>
                            <input type="email" class="form-control" name="email" placeholder="Email" required>
                        </div>

                        <div class="input-group form-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fas fa-lock"></i>
                                </span>
                            </div>
                            <input type="password" class="form-control" name="password" placeholder="Password" minlength="6" required>
                        </div>

                        <div class="input-group form-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fas fa-university"></i>
                                </span>
                            </div>
                            <input type="text" class="form-control" name="college_name" placeholder="College Name" maxlength="150" required>
                        </div>

                        <div class="form-group">
                            <span>I'm a</span>
                            <input type="radio" class="ml-3" id="gender-male" name="gender" value="male" /> Male
                            <label for="gender-male">
                            </label>
                            <input type="radio" class="ml-3" id="gender-female" name="gender" value="female" />
                            <label for="gender-female">
                                Female
                            </label>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-block btn-primary">Create Account</button>
                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <span>Already have an account?
                        <a href="#" data-dismiss="modal" data-toggle="modal" data-target="#login-modal">Login</a>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="login-modal" tabindex="-1" role="dialog" aria-labelledby="login-heading" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="login-heading">Login with PGLife</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <form id="login-form" class="form" role="form">
                        <div class="input-group form-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fas fa-user"></i>
                                </span>
                            </div>
                            <input type="email" class="form-control" name="email" placeholder="Email" required>
                        </div>

                        <div class="input-group form-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fas fa-lock"></i>
                                </span>
                            </div>
                            <input type="password" class="form-control" name="password" placeholder="Password" minlength="6" required>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-block btn-primary">Login</button>
                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <span>
                        <a href="#" data-dismiss="modal" data-toggle="modal" data-target="#signup-modal">Click here</a>
                        to register a new account
                    </span>
                </div>
            </div>
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
            <div class="footer-copyright">© 2026 Copyright PG Life </div>
        </div>
    </div>

    <script type="text/javascript" src="js/jquery.js"></script>
    <script type="text/javascript" src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/property_list.js"></script>
    <script src="js/common.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {

            const filterButtons = document.querySelectorAll("#filter-modal button[data-filter]");
            const propertyCards = document.querySelectorAll(".property-card");

            filterButtons.forEach(function (button) {

                button.addEventListener("click", function () {

                    const selectedFilter = this.getAttribute("data-filter");

                    // Active button change
                    filterButtons.forEach(function (btn) {
                        btn.classList.remove("btn-active");
                    });

                    this.classList.add("btn-active");

                    // Filter properties
                    propertyCards.forEach(function (card) {

                        const gender = card.getAttribute("data-gender");

                        if (selectedFilter === "all" || gender === selectedFilter) {
                            card.style.display = "";
                        } else {
                            card.style.display = "none";
                        }

                    });

                });

            });

        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {

            const filterButtons = document.querySelectorAll(
                "#filter-modal button[data-filter]"
            );

            const propertyContainer = document.querySelector(
                ".property-list-container"
            );

            let propertyCards = Array.from(
                propertyContainer.querySelectorAll(".property-card")
            );


            /* =========================
            FILTER
            ========================= */

            filterButtons.forEach(function (button) {

                button.addEventListener("click", function () {

                    const selectedFilter = this.getAttribute("data-filter");

                    // Active button
                    filterButtons.forEach(function (btn) {
                        btn.classList.remove("btn-active");
                    });

                    this.classList.add("btn-active");


                    // Show / hide properties
                    propertyCards.forEach(function (card) {

                        const gender = card.getAttribute("data-gender");

                        if (
                            selectedFilter === "all" ||
                            gender === selectedFilter
                        ) {
                            card.style.display = "";
                        } else {
                            card.style.display = "none";
                        }

                    });

                });

            });


            /* =========================
            HIGHEST RENT FIRST
            ========================= */

            document
                .querySelector(".filter-bar .col-auto:nth-child(2)")
                .addEventListener("click", function () {

                    propertyCards.sort(function (a, b) {

                        return (
                            Number(b.getAttribute("data-rent")) -
                            Number(a.getAttribute("data-rent"))
                        );

                    });


                    propertyCards.forEach(function (card) {
                        propertyContainer.appendChild(card);
                    });

                });

            /* =========================
            LOWEST RENT FIRST
            ========================= */

            document
                .querySelector(".filter-bar .col-auto:nth-child(3)")
                .addEventListener("click", function () {

                    propertyCards.sort(function (a, b) {

                        const rentA = parseInt(a.dataset.rent);
                        const rentB = parseInt(b.dataset.rent);

                        return rentA - rentB;

                    });

                    propertyCards.forEach(function (card) {
                        propertyContainer.appendChild(card);
                    });

                });

        });
    </script>
</body>

</html>
