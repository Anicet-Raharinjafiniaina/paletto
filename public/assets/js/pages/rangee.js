$(function () {
    initialiseSelect2Modal("entrepot_id", "modal_ajout_rangee")
    initialiseSelect2Modal("allee_id", "modal_ajout_rangee")
});

$("#btn-add-rangee").click(function () {
    loaderContent('main')
    $("#modal_ajout_rangee").modal("show");
    $("#code").val("");
    $("#entrepot_id").val("");
    $("#entrepot_id").trigger("change");
    $("#allee_id").val("");
    $("#allee_id").trigger("change");
    $(".validation-error-label").html("");
    chargeSelectFromController('entrepot_id', 'allee_id', 'Rangee/getAllAlleeByEntrepot', 'modal_ajout_rangee', { entrepot_id: () => $('#entrepot_id').val() });
    stopLoaderContent('main')
});

function insert() {
    $(".validation-error-label").html("");
    isValid = checkObligatoire(".add-rangee-content", ".obligatoire")
    if (isValid == true) {
        $("#save").prop("disabled", true);
        let arr_data = getFormDataFromParentClass(".add-rangee-content")
        loaderContent('modal_ajout_rangee')
        $.ajax({
            url: urlProject + "Rangee/insertRangee",
            type: "POST",
            data: { data: arr_data },
            success: function (res) {
                stopLoaderContent('modal_ajout_rangee')
                if (res == 1) {
                    Swal.fire({
                        title: "Création",
                        html: "La rangée a été créée avec succès",
                        icon: "success",
                        showConfirmButton: true
                    }).then(function (result) {
                        if (result.isConfirmed) {
                            $('#modal_ajout_rangee').modal('hide');
                            loadPage(urlProject + "Rangee", true)
                        }
                    });
                } else if (res == 2) {
                    Swal.fire({
                        title: "Doublon",
                        html: "La rangée <b>" + $("#code").val() + "</b> existe déjà dans la base.",
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
                stopLoaderContent('modal_ajout_rangee')
                $("#save").prop("disabled", false);
            }
        });
    }
}

function view(id, action) {
    var t = $("#l" + id).text();
    $("#content-rangee").html("");
    loaderContent('main')
    $.ajax({
        url: urlProject + "Rangee/getRangee",
        type: "POST",
        data: {
            id: id,
            action: action
        },
        success: function (res) {
            stopLoaderContent('main')
            $("#content-rangee").html(res);
            $("#modal_view_rangee").modal("show");
            initialiseSelect2Modal("entrepot_id_upd", "modal_view_rangee")
            initialiseSelect2Modal("allee_id_upd", "modal_view_rangee")
            chargeSelectedFromController('entrepot_id_upd', 'allee_id_upd', 'Rangee/getAllAlleeByEntrepot', 'modal_view_niveau', { entrepot_id: () => $('#entrepot_id_upd').val() }, 'allee_id_base'); // pour charger les allées en fonction de l'entrepôt
            $("#entrepot_id_upd").trigger("change");
            if (action == "voir") {
                $("#div-upd-footer").css("display", "none");
                $("#title").html("Détail d'une rangée <b>" + t + "</b>");
            } else if (action == "upd") {
                $("#div-upd-footer").css("display", "block");
                $("#title").text("Modification d'une rangée");
            }
        }
    });
}

function deleteItem(id) {
    Swal.fire({
        title: "Voulez-vous vraiment supprimer ?",
        text: "La suppression de cette rangée est irréversible !",
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
                url: urlProject + "Rangee/deleteRangee",
                data: { id: id },
                dataType: "json" // attend une réponse JSON (1 ou 0)
            }).then(response => {
                stopLoaderContent('main')
                if (response === 1) {
                    return true;
                } else if (response == 2) {
                    Swal.fire({
                        title: "Suppression impossible",
                        html: "Impossible de faire la suppression car l'emplacement associé à cette rangée est <b>occupé</b>.",
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
                text: "La rangée a été supprimée.",
                icon: "success",
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                loadPage(urlProject + "Rangee", true)
            });
        }
    });

}

function maj() {
    isValid = checkObligatoire(".modifier-rangee-content", ".obligatoire")
    if (isValid == true) {
        let arr_data = getFormDataFromParentClass(".modifier-rangee-content")
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
                loaderContent('modal_view_rangee')
                $.ajax({
                    url: urlProject + "Rangee/majRangee",
                    type: "POST",
                    data: { data: arr_data },
                    success: function (res) {
                        stopLoaderContent('modal_view_rangee')
                        if (res == 1) {
                            Swal.fire({
                                title: "Modification",
                                html: "Modification faite avec succès.",
                                icon: "success",
                                showConfirmButton: true,
                            }).then(function (result) {
                                if (result.isConfirmed) {
                                    $('#modal_view_rangee').modal('hide');
                                    loadPage(urlProject + "Rangee", true)
                                }
                            });
                        } else if (res == 2) {
                            Swal.fire({
                                title: "Doublon",
                                html: "La rangée  <b>" + $("#code_upd").val() + "</b> existe déjà.",
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
                                html: "Impossible de faire la modification car l'emplacement associé à cette rangée est <b>occupé</b>.",
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
                        stopLoaderContent('modal_view_rangee')
                        $("#save_upd").prop("disabled", false);
                    }
                });
            }
        });
    }
}
