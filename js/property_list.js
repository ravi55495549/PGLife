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