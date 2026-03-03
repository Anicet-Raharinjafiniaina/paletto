$(function () {
    initialiseSelect2Modal("entrepot_id", "modal_ajout_cage")
    initialiseSelect2Modal("allee_id", "modal_ajout_cage")
    initialiseSelect2Modal("rangee_id", "modal_ajout_cage")
    initialiseSelect2Modal("niveau_id", "modal_ajout_cage")
});

$("#btn-add-cage").click(function () {
    loaderContent('main')
    $("#modal_ajout_cage").modal("show");
    $("#code").val("");
    $("#entrepot_id").val("");
    $("#entrepot_id").trigger("change");
    $("#allee_id").val("");
    $("#allee_id").trigger("change");
    $("#rangee_id").val("");
    $("#rangee_id").trigger("change");
    $("#niveau_id").val("");
    $("#niveau_id").trigger("change");
    $(".validation-error-label").html("");
    chargeSelectFromController('entrepot_id', 'allee_id', 'Rangee/getAllAlleeByEntrepot', 'modal_ajout_cage', { entrepot_id: () => $('#entrepot_id').val() }); // pour charger les allées en fonction de l'entrepôt
    chargeSelectFromController('allee_id', 'rangee_id', 'Niveau/getAllRangeeByAllee', 'modal_ajout_cage', { entrepot_id: () => $('#entrepot_id').val(), allee_id: () => $('#allee_id').val() }); // pour charger les rangées en fonction de l'allée
    chargeSelectFromController('rangee_id', 'niveau_id', 'Cage/getAllNiveauByRangee', 'modal_ajout_cage', { entrepot_id: () => $('#entrepot_id').val(), allee_id: () => $('#allee_id').val(), rangee_id: () => $('#rangee_id').val() }); // pour charger les niveaux en fonction de la rangée
    stopLoaderContent('main')
});

function insert() {
    $(".validation-error-label").html("");
    isValid = checkObligatoire(".add-cage-content", ".obligatoire")
    if (isValid == true) {
        $("#save").prop("disabled", true);
        let arr_data = getFormDataFromParentClass(".add-cage-content")
        loaderContent('modal_ajout_cage')
        $.ajax({
            url: urlProject + "Cage/insertCage",
            type: "POST",
            data: { data: arr_data },
            success: function (res) {
                stopLoaderContent('modal_ajout_cage')
                if (res == 1) {
                    Swal.fire({
                        title: "Création",
                        html: "Le cage a été créé avec succès",
                        icon: "success",
                        showConfirmButton: true
                    }).then(function (result) {
                        if (result.isConfirmed) {
                            $('#modal_ajout_cage').modal('hide');
                            loadPage(urlProject + "Cage", true)
                        }
                    });
                } else if (res == 2) {
                    Swal.fire({
                        title: "Doublon",
                        html: "La cage <b>" + $("#code").val() + "</b> existe déjà dans la base.",
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
                stopLoaderContent('modal_ajout_cage')
                $("#save").prop("disabled", false);
            }
        });
    }
}

function view(id, action) {
    var t = $("#l" + id).text();
    $("#content-cage").html("");
    loaderContent('main')
    $.ajax({
        url: urlProject + "Cage/getCage",
        type: "POST",
        data: {
            id: id,
            action: action
        },
        success: function (res) {
            stopLoaderContent('main')
            $("#content-cage").html(res);
            $("#modal_view_cage").modal("show");
            initialiseSelect2Modal("entrepot_id_upd", "modal_view_cage")
            initialiseSelect2Modal("allee_id_upd", "modal_view_cage")
            initialiseSelect2Modal("rangee_id_upd", "modal_view_cage")
            initialiseSelect2Modal("niveau_id_upd", "modal_view_cage")
            chargeSelectedFromController('entrepot_id_upd', 'allee_id_upd', 'Rangee/getAllAlleeByEntrepot', 'modal_view_cage', { entrepot_id: () => $('#entrepot_id_upd').val() }, 'allee_id_base'); // pour charger les allées en fonction de l'entrepôt
            chargeSelectedFromController('allee_id_upd', 'rangee_id_upd', 'Niveau/getAllRangeeByAllee', 'modal_view_cage', { entrepot_id: () => $('#entrepot_id_upd').val(), allee_id: () => $('#allee_id_upd').val() }, 'rangee_id_base'); // pour charger les rangées en fonction de l'allée
            chargeSelectedFromController('rangee_id_upd', 'niveau_id_upd', 'Cage/getAllNiveauByRangee', 'modal_view_cage', { entrepot_id: () => $('#entrepot_id_upd').val(), allee_id: () => $('#allee_id_upd').val(), rangee_id: () => $('#rangee_id_upd').val() }, 'niveau_id_base'); // pour charger les niveaux en fonction de la rangée
            $("#entrepot_id_upd").trigger("change");
            $("#allee_id_upd").trigger("change");
            $("#rangee_id_upd").trigger("change");
            $("#niveau_id_upd").trigger("change");
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
        text: "La suppression de cette cage est irréversible !",
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
                url: urlProject + "Cage/deleteCage",
                data: { id: id },
                dataType: "json" // attend une réponse JSON (1 ou 0)
            }).then(response => {
                stopLoaderContent('main')
                if (response === 1) {
                    return true;
                } else if (response == 2) {
                    Swal.fire({
                        title: "Suppression impossible",
                        html: "Impossible de faire la suppression car l'emplacement associé à cette cage est <b>occupé</b>.",
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
                text: "La cage a été supprimée.",
                icon: "success",
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                loadPage(urlProject + "Cage", true)
            });
        }
    });

}

function maj() {
    isValid = checkObligatoire(".modifier-cage-content", ".obligatoire")
    if (isValid == true) {
        let arr_data = getFormDataFromParentClass(".modifier-cage-content")
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
                loaderContent('modal_view_cage')
                $.ajax({
                    url: urlProject + "Cage/majCage",
                    type: "POST",
                    data: { data: arr_data },
                    success: function (res) {
                        stopLoaderContent('modal_view_cage')
                        if (res == 1) {
                            Swal.fire({
                                title: "Modification",
                                html: "Modification faite avec succès.",
                                icon: "success",
                                showConfirmButton: true,
                            }).then(function (result) {
                                if (result.isConfirmed) {
                                    $('#modal_view_cage').modal('hide');
                                    loadPage(urlProject + "Cage", true)
                                }
                            });
                        } else if (res == 2) {
                            Swal.fire({
                                title: "Doublon",
                                html: "La cage <b>" + $("#code_upd").val() + "</b> existe déjà.",
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
                                html: "Impossible de faire la modification car l'emplacement associé à cette cage est <b>occupé</b>.",
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
                        stopLoaderContent('modal_view_cage')
                        $("#save_upd").prop("disabled", false);
                    }
                });
            }
        });
    }
}
