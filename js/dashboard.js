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
                    "DASHBOARD INTEREST RESPONSE:",
                    XHR.responseText
                );

                try {

                    var response =
                        JSON.parse(XHR.responseText);

                    if (response.login_required) {
                        $("#login-modal").modal("show");
                        return;
                    }

                    if (!response.success) {
                        alert(response.message);
                        return;
                    }

                    /*
                     * If user removed the interest,
                     * remove that property card from dashboard.
                     */
                    if (!response.is_interested) {

                        var property_card =
                            document.querySelector(
                                ".property-id-" + property_id
                            );

                        if (property_card) {
                            property_card.remove();
                        }

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