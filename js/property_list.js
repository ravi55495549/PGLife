window.addEventListener("load", function () {

    var interested_buttons =
        document.querySelectorAll(".is-interested-image");

    interested_buttons.forEach(function (interested_button) {

        interested_button.addEventListener("click", function (event) {

            event.preventDefault();

            var property_id =
                interested_button.getAttribute("property_id");

            if (!property_id) {
                alert("Property ID missing.");
                return;
            }

            var XHR = new XMLHttpRequest();

            var formData = new FormData();

            formData.append("property_id", property_id);

            XHR.addEventListener("load", function () {

                console.log(
                    "PROPERTY LIST INTEREST RESPONSE:",
                    XHR.responseText
                );

                try {

                    var response =
                        JSON.parse(XHR.responseText);
                    // User is not logged in
                    if (response.login_required) {

                        sessionStorage.setItem(
                            "interest_return_url",
                            window.location.href
                        );

                        sessionStorage.setItem(
                            "auto_interest_after_login",
                            "1"
                        );

                        sessionStorage.setItem(
                            "interest_property_id",
                            property_id
                        );

                        $("#login-modal").modal("show");

                        return;
                    }
                    // Some other error
                    if (!response.success) {
                        alert(response.message);
                        return;
                    }

                    // Property is now interested
                    if (response.is_interested) {

                        interested_button.classList.remove("far");
                        interested_button.classList.add("fas");

                    } else {

                        interested_button.classList.remove("fas");
                        interested_button.classList.add("far");

                    }
                    var count_element =
                        interested_button.parentElement.querySelector(".interested-text");

                    if (count_element) {

                        count_element.textContent =
                            response.interested_count + " interested";

                    }

                } catch (error) {

                    console.error("JSON ERROR:", error);
                    console.error(
                        "SERVER RESPONSE:",
                        XHR.responseText
                    );

                    alert(
                        "Server se unexpected response aa raha hai."
                    );
                }

            });

            XHR.addEventListener("error", function () {

                alert("Something went wrong!");

            });

            XHR.open(
                "POST",
                "/PGLife/api/interested_submit.php"
            );

            XHR.send(formData);

        });

    });

});
// Automatically activate heart after login
window.addEventListener("load", function () {

    var autoInterest =
        sessionStorage.getItem("auto_interest_after_login");

    if (autoInterest === "1") {

        sessionStorage.removeItem(
            "auto_interest_after_login"
        );

        // Small delay so the page and heart buttons are fully loaded
        setTimeout(function () {

            var property_id =
                sessionStorage.getItem("interest_property_id");

            if (!property_id) {
                return;
            }

            var interested_buttons =
                document.querySelectorAll(".is-interested-image");

            interested_buttons.forEach(function (button) {

                if (
                    button.getAttribute("property_id") ===
                    property_id
                ) {
                    button.click();
                }

            });

            sessionStorage.removeItem(
                "interest_property_id"
            );

        }, 300);
    }

});