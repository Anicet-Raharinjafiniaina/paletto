$(function () {
    initialiseSelect2Modal("entrepot_id", "modal_ajout_niveau")
    initialiseSelect2Modal("allee_id", "modal_ajout_niveau")
    initialiseSelect2Modal("rangee_id", "modal_ajout_niveau")
});

$("#btn-add-niveau").click(function () {
    loaderContent('main')
    $("#modal_ajout_niveau").modal("show");
    $("#code").val("");
    $("#entrepot_id").val("");
    $("#entrepot_id").trigger("change");
    $("#allee_id").val("");
    $("#allee_id").trigger("change");
    $("#rangee_id").val("");
    $("#rangee_id").trigger("change");
    $(".validation-error-label").html("");
    chargeSelectFromController('entrepot_id', 'allee_id', 'Rangee/getAllAlleeByEntrepot', 'modal_ajout_niveau', { entrepot_id: () => $('#entrepot_id').val() }); // pour charger les allées en fonction de l'entrepôt
    chargeSelectFromController('allee_id', 'rangee_id', 'Niveau/getAllRangeeByAllee', 'modal_ajout_rangee', { entrepot_id: () => $('#entrepot_id').val(), allee_id: () => $('#allee_id').val() }); // pour charger les rangées en fonction de l'allée
    stopLoaderContent('main')
});

function insert() {
    $(".validation-error-label").html("");
    isValid = checkObligatoire(".add-niveau-content", ".obligatoire")
    if (isValid == true) {
        $("#save").prop("disabled", true);
        let arr_data = getFormDataFromParentClass(".add-niveau-content")
        loaderContent('modal_ajout_niveau')
        $.ajax({
            url: urlProject + "Niveau/insertNiveau",
            type: "POST",
            data: { data: arr_data },
            success: function (res) {
                stopLoaderContent('modal_ajout_niveau')
                if (res == 1) {
                    Swal.fire({
                        title: "Création",
                        html: "Le niveau a été créé avec succès",
                        icon: "success",
                        showConfirmButton: true
                    }).then(function (result) {
                        if (result.isConfirmed) {
                            $('#modal_ajout_niveau').modal('hide');
                            loadPage(urlProject + "Niveau", true)
                        }
                    });
                } else if (res == 2) {
                    Swal.fire({
                        title: "Doublon",
                        html: "Le niveau <b>" + $("#code").val() + "</b> existe déjà dans la base.",
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
                stopLoaderContent('modal_ajout_niveau')
                $("#save").prop("disabled", false);
            }
        });
    }
}

function view(id, action) {
    var t = $("#l" + id).text();
    $("#content-niveau").html("");
    loaderContent('main')
    $.ajax({
        url: urlProject + "Niveau/getNiveau",
        type: "POST",
        data: {
            id: id,
            action: action
        },
        success: function (res) {
            stopLoaderContent('main')
            $("#content-niveau").html(res);
            $("#modal_view_niveau").modal("show");
            initialiseSelect2Modal("entrepot_id_upd", "modal_view_niveau")
            initialiseSelect2Modal("allee_id_upd", "modal_view_niveau")
            initialiseSelect2Modal("rangee_id_upd", "modal_view_niveau")
            chargeSelectedFromController('entrepot_id_upd', 'allee_id_upd', 'Rangee/getAllAlleeByEntrepot', 'modal_view_niveau', { entrepot_id: () => $('#entrepot_id_upd').val() }, 'allee_id_base'); // pour charger les allées en fonction de l'entrepôt
            chargeSelectedFromController('allee_id_upd', 'rangee_id_upd', 'Niveau/getAllRangeeByAllee', 'modal_view_niveau', { entrepot_id: () => $('#entrepot_id_upd').val(), allee_id: () => $('#allee_id_upd').val() }, 'rangee_id_base'); // pour charger les rangées en fonction de l'allée
            $("#entrepot_id_upd").trigger("change");
            $("#allee_id_upd").trigger("change");
            $("#rangee_id_upd").trigger("change");
            if (action == "voir") {
                $("#div-upd-footer").css("display", "none");
                $("#title").html("Détail d'une allée <b>" + t + "</b>");
            } else if (action == "upd") {
                $("#div-upd-footer").css("display", "block");
                $("#title").text("Modification d'une allée");
            }
        }
    });
}

function deleteItem(id) {
    Swal.fire({
        title: "Voulez-vous vraiment supprimer ?",
        text: "La suppression de ce niveau est irréversible !",
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
                url: urlProject + "Niveau/deleteNiveau",
                data: { id: id },
                dataType: "json" // attend une réponse JSON (1 ou 0)
            }).then(response => {
                stopLoaderContent('main')
                if (response === 1) {
                    return true;
                } else if (response == 2) {
                    Swal.fire({
                        title: "Suppression impossible",
                        html: "Impossible de faire la suppression car l'emplacement associé à ce niveau est <b>occupé</b>.",
                        icon: "warning",
                        showConfirmButton: true,
                    });
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
                text: "Le niveau a été supprimé.",
                icon: "success",
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                loadPage(urlProject + "Niveau", true)
            });
        }
    });

}

function maj() {
    isValid = checkObligatoire(".modifier-niveau-content", ".obligatoire")
    if (isValid == true) {
        let arr_data = getFormDataFromParentClass(".modifier-niveau-content")
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
                loaderContent('modal_view_niveau')
                $.ajax({
                    url: urlProject + "Niveau/majNiveau",
                    type: "POST",
                    data: { data: arr_data },
                    success: function (res) {
                        stopLoaderContent('modal_view_niveau')
                        if (res == 1) {
                            Swal.fire({
                                title: "Modification",
                                html: "Modification faite avec succès.",
                                icon: "success",
                                showConfirmButton: true,
                            }).then(function (result) {
                                if (result.isConfirmed) {
                                    $('#modal_view_niveau').modal('hide');
                                    loadPage(urlProject + "Niveau", true)
                                }
                            });
                        } else if (res == 2) {
                            Swal.fire({
                                title: "Doublon",
                                html: "Le niveau  <b>" + $("#code_upd").val() + "</b> existe déjà.",
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
                        } else if (res == 4) {
                            Swal.fire({
                                title: "Modification impossible",
                                html: "Impossible de faire la modification car l'emplacement associé à ce niveau est <b>occupé</b>.",
                                icon: "warning",
                                showConfirmButton: true,
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
                        stopLoaderContent('modal_view_niveau')
                        $("#save_upd").prop("disabled", false);
                    }
                });
            }
        });
    }
}
