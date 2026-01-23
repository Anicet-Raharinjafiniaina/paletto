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
        $.ajax({
            url: urlProject + "Login/loginAuth",
            type: "POST",
            data: {
                login: $("#login").val(),
                mdp: $("#password").val()
            },
            success: function (response) {
                if (response == 0) {
                    flashMessage("error", "Vous n'êtes pas autorisé à accéder à cette application.");
                } else if (response == 1) {
                    flashMessage("error", "Identifiant ou mot de passe incorrect.");
                    $('#password').val('');
                } else if (response == 2) {
                    flashMessage("success", "Connexion réussie !");
                    setTimeout(function () {
                        window.location.href = urlProject + "Profil";
                    }, 800);
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
        .delay(4000)
        .fadeOut(400);
}

function checkInput() {
    let login = $("#login").val().trim();
    let password = $("#password").val().trim();
    let valid = true;

    if (login == "") {
        $("#login-error").text("Veuillez saisir votre login.");
        valid = false;
    } else {
        $("#login-error").text("");
    }

    if (password == "") {
        $("#password-error").text("Veuillez saisir votre mot de passe.");
        valid = false;
    } else {
        $("#password-error").text("");
    }

    if (!valid) return false; // on bloque le submit
}