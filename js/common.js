console.log("COMMON JS LOADED");
window.addEventListener("load", function () {
    var signup_form = document.getElementById("signup-form");
    if (signup_form) {
        signup_form.addEventListener("submit", function (event) {
            event.preventDefault();

            var XHR = new XMLHttpRequest();
            var formData = new FormData(signup_form);

            XHR.addEventListener("load", signup_success);
            XHR.addEventListener("error", on_error);

            XHR.open("POST", "/PGLife/api/signup_submit.php");
            XHR.send(formData);
        });
    }

    var login_form = document.getElementById("login-form");
    if (login_form) {
        login_form.addEventListener("submit", function (event) {
            event.preventDefault(); // Prevents URL query parameter submission

            var XHR = new XMLHttpRequest();
            var formData = new FormData(login_form);

            XHR.addEventListener("load", login_success);
            XHR.addEventListener("error", on_error);

            XHR.open("POST", "/PGLife/api/login_submit.php");
            XHR.send(formData);
        });
    }
});

var signup_success = function (event) {
    var response = JSON.parse(event.target.responseText);
    if (response.success) {
        alert(response.message);
        window.location.href = "dashboard.php";
    } else {
        alert(response.message);
    }
};

var login_success = function (event) {
    console.log("LOGIN RESPONSE:", event.target.responseText);

    try {
        var response = JSON.parse(event.target.responseText);

        if (response.success) {
            alert("Login successful!");
            window.location.href = "/PGLife/dashboard.php";
        } else {
            alert(response.message);
        }
    } catch (error) {
        console.error("JSON ERROR:", error);
        console.error("SERVER RESPONSE:", event.target.responseText);
        alert("Server se unexpected response aa raha hai. Console check karo.");
    }
};

var on_error = function (event) {
    alert('Something went wrong!');
};