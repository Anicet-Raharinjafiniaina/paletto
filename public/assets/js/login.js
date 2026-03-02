$(document).ready(function () {
    $(document).on('keypress', function (e) {
        if (e.which === 13) {  // 13 = touche Entrée
            $("#btn_connecter").click();
        }
    });

    $("#btn_connecter").on("click", function (e) {
        if (checkInput() == false) { // validation des champs
            return;
        }
        $("#btn_connecter").prop("disabled", true);
        $.ajax({
            url: urlProject + "Login/loginAuth",
            type: "POST",
            data: {
                login: $("#login").val(),
                mdp: $("#password").val()
            },
            success: function (response) {
                $("#btn_connecter").prop("disabled", false);
                if (response == 0) {
                    flashMessage("error", "Vous n'êtes pas autorisé à accéder à cette application.");
                } else if (response == 1) {
                    flashMessage("error", "Identifiant ou mot de passe incorrect.");
                    $('#password').val('');
                } else if (response == 2) {
                    flashMessage("success", "Connexion réussie !");
                    window.location.href = urlProject + "Profil";
                } else {
                    flashMessage("error", "Erreur lors de la connexion !");
                }
            },
            error: function (xhr, status, error) {
                flashMessage("error", "Erreur lors de la connexion !");
            }
        });
    });

});

function flashMessage(type, message) {
    let el = $("#info");

    el.removeClass("flash-error flash-success");

    if (type === "error") {
        el.addClass("flash-error");
    } else if (type === "success") {
        el.addClass("flash-success");
    }

    el.stop(true, true)
        .text(message)
        .fadeIn(200)
        .delay(5000)
        .fadeOut(400);
}

function checkInput() {
    let login = $("#login").val().trim();
    let password = $("#password").val().trim();
    let valid = true;

    if (login == "") {
        $("#login-error").html("<i>Veuillez saisir votre login.</i>");
        valid = false;
    } else {
        $("#login-error").html("");
    }

    if (password == "") {
        $("#password-error").html("<i>Veuillez saisir votre mot de passe.</i>");
        valid = false;
    } else {
        $("#password-error").html("");
    }
    if (!valid) return false; // on bloque le submit
}

document.addEventListener("DOMContentLoaded", function () {

    feather.replace();

    const password = document.getElementById("password");
    const toggle = document.getElementById("togglePassword");

    toggle.addEventListener("click", function () {

        if (password.type === "password") {
            password.type = "text";
            toggle.innerHTML = '<i data-feather="eye-off"></i>';
        } else {
            password.type = "password";
            toggle.innerHTML = '<i data-feather="eye"></i>';
        }

        feather.replace();
    });

});