$(function () {
    loadName();
    initialiseSelect2Modal("profil", "modal_ajout_user")
});

$("#btn-add-user").click(function () {
    loaderContent('main')
    $("#modal_ajout_user").modal("show");
    $("#login").val("");
    $("#nom").val("");
    $("#prenom").val("");
    $("#profil").val("");
    $("#profil").trigger("change");
    $(".validation-error-label").html("");
    stopLoaderContent('main')
});

function insert() {
    $(".validation-error-label").html("");
    isValid = checkObligatoire(".add-user-content", ".obligatoire")
    if (isValid == true) {
        $("#save").prop("disabled", true);
        let arr_data = getFormDataFromParentClass(".add-user-content")
        loaderContent('modal_ajout_user')
        $.ajax({
            url: urlProject + "User/insertUser",
            type: "POST",
            data: { data: arr_data },
            success: function (res) {
                stopLoaderContent('modal_ajout_user')
                if (res == 1) {
                    Swal.fire({
                        title: "Création",
                        html: "L'utilisateur a été créé avec succès",
                        icon: "success",
                        showConfirmButton: true
                    }).then(function (result) {
                        if (result.isConfirmed) {
                            $('#modal_ajout_user').modal('hide');
                            loadPage(urlProject + "User", true)
                        }
                    });
                } else if (res == 2) {
                    Swal.fire({
                        title: "Doublon",
                        html: "Le utilisateur <b>" + $("#nom").val() + "</b> existe déjà dans la base.",
                        icon: "warning",
                        timer: 3000,
                        showConfirmButton: false,
                    });
                    $("#save").prop("disabled", false);
                } else {
                    Swal.fire({
                        title: "Erreur",
                        html: "Erreur dans la base de données. Merci de réessayer plus tard.",
                        icon: "error",
                        timer: 2000,
                        showConfirmButton: false,
                    });
                    $("#save").prop("disabled", false);
                }
            },
            error: function (xhr, status, error) {
                Swal.fire({
                    title: "Erreur",
                    html: "Erreur dans la base de données. Merci de réessayer plus tard.",
                    icon: "error",
                    timer: 2000,
                    showConfirmButton: false,
                });
                stopLoaderContent('modal_ajout_user')
                $("#save").prop("disabled", false);
            }
        });
    }
}

function view(id, action) {
    var t = $("#l" + id).text();
    $("#content-user").html("");
    loaderContent('main')
    $.ajax({
        url: urlProject + "User/getUser",
        type: "POST",
        data: {
            id: id,
            action: action
        },
        success: function (res) {
            stopLoaderContent('main')
            $("#content-user").html(res);
            $("#modal_view_user").modal("show");
            initialiseSelect2Modal("profil_id_upd", "modal_view_user")
            $('input[name="actif"]').click(function () {
                if ($(this).is(":checked")) {
                    $(this).val(1);
                } else if ($(this).is(":not(:checked)")) {
                    $(this).val(0);
                }
            });
            loadName("_upd");
            if (action == "voir") {
                $("#div-upd-footer").css("display", "none");
                $("#title").html("Détail du l'utilisateur <b>" + t + "</b>");
            } else if (action == "upd") {
                $("#div-upd-footer").css("display", "block");
                $("#title").text("Modification d'un utilisateur");
            }
        }
    });
}

function deleteItem(id) {
    Swal.fire({
        title: "Voulez-vous vraiment supprimer ?",
        text: "La suppression de cet utilisateur est irréversible !",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#EF5350",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Oui",
        cancelButtonText: "Non",
        showLoaderOnConfirm: true,
        backdrop: true,
        allowOutsideClick: () => !Swal.isLoading(),
        preConfirm: () => {
            loaderContent('main')
            return $.ajax({
                type: "POST",
                url: urlProject + "User/deleteUser",
                data: { id: id },
                dataType: "json" // attend une réponse JSON (1 ou 0)
            }).then(response => {
                stopLoaderContent('main')
                if (response === 1) {
                    return true;
                } else {
                    throw new Error("Erreur lors de la suppression.");
                }
            }).catch(error => {
                stopLoaderContent('main')
                Swal.showValidationMessage(error.message);
            });
        }
    }).then((result) => {
        if (result.isConfirmed && result.value === true) {
            Swal.fire({
                title: "Supprimé !",
                text: "L'utilisateur a été supprimé.",
                icon: "success",
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                loadPage(urlProject + "User", true)
            });
        }
    });

}

function maj() {
    isValid = checkObligatoire(".modifier-user-content", ".obligatoire")
    if (isValid == true) {
        let arr_data = getFormDataFromParentClass(".modifier-user-content")
        Swal.fire({
            title: "Modification",
            html: "Voulez-vous vraiment procéder à la modification?",
            icon: "warning",
            showConfirmButton: true,
            showCancelButton: true, confirmButtonText: 'Oui',
            cancelButtonText: 'Annuler',
        }).then(function (result) {
            if (result.isConfirmed) {
                $("#save_upd").prop("disabled", true);
                loaderContent('modal_view_user')
                $.ajax({
                    url: urlProject + "User/majUser",
                    type: "POST",
                    data: { data: arr_data },
                    success: function (res) {
                        stopLoaderContent('modal_view_user')
                        if (res == 1) {
                            Swal.fire({
                                title: "Modification",
                                html: "Modification faite avec succès.",
                                icon: "success",
                                showConfirmButton: true,
                            }).then(function (result) {
                                if (result.isConfirmed) {
                                    $('#modal_view_user').modal('hide');
                                    loadPage(urlProject + "User", true)
                                }
                            });
                        } else if (res == 2) {
                            Swal.fire({
                                title: "Doublon",
                                html: "Le login  <b>" + $("#login_upd").val() + "</b> existe déjà.",
                                icon: "warning",
                                timer: 2000,
                                showConfirmButton: false,
                            });
                            $("#save_upd").prop("disabled", false);
                        } else if (res == 3) {
                            Swal.fire({
                                title: "Modification",
                                html: "Aucune modification.",
                                icon: "warning",
                                timer: 2000,
                                showConfirmButton: false,
                            });
                            $("#save_upd").prop("disabled", false);
                        } else {
                            Swal.fire({
                                title: "Erreur",
                                html: "Erreur dans la base de données. Merci de réessayer plus tard.",
                                icon: "error",
                                timer: 2000,
                                showConfirmButton: false,
                            });
                            $("#save_upd").prop("disabled", false);
                        }
                    },
                    error: function (xhr, status, error) {
                        Swal.fire({
                            title: "Erreur",
                            html: "Erreur dans la base de données. Merci de réessayer plus tard.",
                            icon: "error",
                            timer: 2000,
                            showConfirmButton: false,
                        });
                        stopLoaderContent('modal_view_user')
                        $("#save_upd").prop("disabled", false);
                    }
                });
            }
        });
    }
}


/*** Auto complète */
function loadName(type = "") {
    $("input.basicAutoComplete").on("input", function () {
        var query = $(this).val();
        var self = this; // Conserver une référence à l'élément input

        // Effacer le délai précédent s'il existe
        clearTimeout($(this).data("timeoutId"));

        // Définir un nouveau délai
        var timeoutId = setTimeout(function () {
            if (query.length > 0) {
                // Vérifier si l'input n'est pas vide
                $.post("User/getInfosUser", { c: query }, function (data) {
                    data = $.parseJSON(data);
                    // 'self' fait référence à l'élément input ici
                    var process = function (items) {
                        $(self).data("typeahead").source = items; // Mettre à jour la source du typeahead
                        $(self).data("typeahead").lookup(); // Déclencher la recherche
                    };
                    return process(data);
                });
            } else {
                // Gérer le cas où l'input est vide (facultatif)
                // Par exemple, effacer les suggestions
                $(self).data("typeahead").source = [];
                $(self).data("typeahead").lookup();
            }
        }, 500); // Délai de 2000 millisecondes (2 secondes)

        // Stocker l'ID du délai pour pouvoir l'effacer
        $(this).data("timeoutId", timeoutId);
    });

    $("input.basicAutoComplete").typeahead({
        source: [], // La source sera mise à jour dynamiquement
        afterSelect: function (obj) {
            getMailByName(obj, type);
            getFonctionByName(obj, type);
            getLoginByName(obj, type);
        },
    });
}

function getMailByName(name, type) {
    console.log(name, type);

    $.ajax({
        url: "User/getMail",
        method: "POST",
        data: { n: name },
        success: function (res) {
            if (res == "not found") {
                Swal.fire({
                    title: "Résultat",
                    text: "Aucune adresse mail trouvée",
                    icon: "warning",
                    timer: 2000,
                    showConfirmButton: false,
                    html: true,
                });
            } else {
                $("#mail" + type).val(res);
            }
        },
        error: function () {
            Swal.fire({
                title: "Erreur",
                text: "Erreur dans la recherche du mail. Merci de réessayer plus tard.",
                icon: "error",
                timer: 2000,
                showConfirmButton: false,
                html: true,
            });
        },
    });
}

function getLoginByName(name, type) {
    $.ajax({
        url: "User/getLogin",
        method: "POST",
        data: { n: name },
        success: function (res) {
            if (res == "not found") {
                Swal.fire({
                    title: "Résultat",
                    text: "Aucun login trouvé",
                    icon: "warning",
                    timer: 2000,
                    showConfirmButton: false,
                    html: true,
                });
            } else {
                $("#login" + type).val(res);
            }
        },
        error: function () {
            Swal.fire({
                title: "Erreur",
                text: "Erreur dans la recherche du mail. Merci de réessayer plus tard.",
                icon: "error",
                timer: 2000,
                showConfirmButton: false,
                html: true,
            });
        },
    });
}

function getFonctionByName(name, type) {
    $.ajax({
        url: "User/getFonction",
        method: "POST",
        data: { n: name },
        success: function (res) {
            if (res == "not found") {
                Swal.fire({
                    title: "Résultat",
                    text: "Aucune fonction trouvée",
                    icon: "warning",
                    timer: 2000,
                    showConfirmButton: false,
                    html: true,
                });
            } else {
                $("#fonction" + type).val(res);
            }
        },
        error: function () {
            Swal.fire({
                title: "Erreur",
                text: "Erreur dans la recherche du mail. Merci de réessayer plus tard.",
                icon: "error",
                timer: 2000,
                showConfirmButton: false,
                html: true,
            });
        },
    });
}
/*** /Auto complète */