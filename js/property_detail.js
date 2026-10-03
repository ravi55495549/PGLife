console.log("PROPERTY DETAIL JS LOADED");

window.addEventListener("load", function () {

    var interested_button =
        document.querySelector(".is-interested-image");


    if (interested_button) {

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
                    "INTEREST RESPONSE:",
                    XHR.responseText
                );


                try {

                    var response =
                        JSON.parse(XHR.responseText);


                    /*
                    |--------------------------------------------------------------------------
                    | LOGIN REQUIRED
                    |--------------------------------------------------------------------------
                    */

                    if (response.login_required) {

                        sessionStorage.setItem(
                            "interest_return_url",
                            window.location.href
                        );

                        $("#login-modal").modal("show");

                        return;
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | ERROR
                    |--------------------------------------------------------------------------
                    */

                    if (!response.success) {

                        alert(response.message);

                        return;
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | UPDATE HEART
                    |--------------------------------------------------------------------------
                    */

                    if (response.is_interested) {

                        interested_button.classList.remove(
                            "far"
                        );

                        interested_button.classList.add(
                            "fas"
                        );

                    } else {

                        interested_button.classList.remove(
                            "fas"
                        );

                        interested_button.classList.add(
                            "far"
                        );
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | UPDATE COUNT
                    |--------------------------------------------------------------------------
                    */

                    var count_element =
                        document.querySelector(
                            ".interested-user-count"
                        );


                    if (count_element) {

                        count_element.textContent =
                            response.interested_count;
                    }


                } catch (error) {

                    console.error(
                        "JSON ERROR:",
                        error
                    );

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

                alert(
                    "Something went wrong!"
                );

            });


            XHR.open(
                "POST",
                "/PGLife/api/interested_submit.php"
            );


            XHR.send(formData);

        });

    }

});
