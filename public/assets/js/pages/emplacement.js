$(function () {
    initialiseSelect2Modal("entrepot_id", "modal_ajout_emplacement")
    initialiseSelect2Modal("allee_id", "modal_ajout_emplacement")
    initialiseSelect2Modal("rangee_id", "modal_ajout_emplacement")
    initialiseSelect2Modal("niveau_id", "modal_ajout_emplacement")
    initialiseSelect2Modal("cage_id", "modal_ajout_emplacement")
});

$("#btn-add-emplacement").click(function () {
    loaderContent('main')
    $("#modal_ajout_emplacement").modal("show");
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
    chargeSelectFromController('entrepot_id', 'allee_id', 'Rangee/getAllAlleeByEntrepot', 'modal_ajout_emplacement', { entrepot_id: () => $('#entrepot_id').val() }); // pour charger les allées en fonction de l'entrepôt
    chargeSelectFromController('allee_id', 'rangee_id', 'Niveau/getAllRangeeByAllee', 'modal_ajout_emplacement', { entrepot_id: () => $('#entrepot_id').val(), allee_id: () => $('#allee_id').val() }); // pour charger les rangées en fonction de l'allée
    chargeSelectFromController('rangee_id', 'niveau_id', 'Cage/getAllNiveauByRangee', 'modal_ajout_emplacement', { entrepot_id: () => $('#entrepot_id').val(), allee_id: () => $('#allee_id').val(), rangee_id: () => $('#rangee_id').val() }); // pour charger les niveaux en fonction de la rangée
    chargeSelectFromController('niveau_id', 'cage_id', 'Emplacement/getAllCageByNiveau', 'modal_ajout_emplacement', { entrepot_id: () => $('#entrepot_id').val(), allee_id: () => $('#allee_id').val(), rangee_id: () => $('#rangee_id').val(), niveau_id: () => $('#niveau_id').val() }); // pour charger les niveaux en fonction de la cage
    stopLoaderContent('main')
});

function insert() {
    $(".validation-error-label").html("");
    isValid = checkObligatoire(".add-emplacement-content", ".obligatoire")
    if (isValid == true) {
        $("#save").prop("disabled", true);
        let arr_data = getFormDataFromParentClass(".add-emplacement-content")
        loaderContent('modal_ajout_emplacement')
        $.ajax({
            url: urlProject + "Emplacement/insertEmplacement",
            type: "POST",
            data: { data: arr_data },
            success: function (res) {
                stopLoaderContent('modal_ajout_emplacement')
                if (res == 1) {
                    Swal.fire({
                        title: "Création",
                        html: "L'emplacement a été créé avec succès",
                        icon: "success",
                        showConfirmButton: true
                    }).then(function (result) {
                        if (result.isConfirmed) {
                            $('#modal_ajout_emplacement').modal('hide');
                            loadPage(urlProject + "Emplacement", true)
                        }
                    });
                } else if (res == 2) {
                    Swal.fire({
                        title: "Doublon",
                        html: "L'emplacement <b>" + $("#code").val() + "</b> existe déjà dans la base.",
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
                stopLoaderContent('modal_ajout_emplacement')
                $("#save").prop("disabled", false);
            }
        });
    }
}

function view(id, action) {
    var t = $("#l" + id).text();
    $("#content-emplacement").html("");
    loaderContent('main')
    $.ajax({
        url: urlProject + "Emplacement/getEmplacement",
        type: "POST",
        data: {
            id: id,
            action: action
        },
        success: function (res) {
            stopLoaderContent('main')
            $("#content-emplacement").html(res);
            $("#modal_view_emplacement").modal("show");
            initialiseSelect2Modal("entrepot_id_upd", "modal_view_emplacement")
            initialiseSelect2Modal("allee_id_upd", "modal_view_emplacement")
            initialiseSelect2Modal("rangee_id_upd", "modal_view_emplacement")
            initialiseSelect2Modal("niveau_id_upd", "modal_view_emplacement")
            initialiseSelect2Modal("cage_id_upd", "modal_view_emplacement")
            chargeSelectedFromController('entrepot_id_upd', 'allee_id_upd', 'Rangee/getAllAlleeByEntrepot', 'modal_view_emplacement', { entrepot_id: () => $('#entrepot_id_upd').val() }, 'allee_id_base'); // pour charger les allées en fonction de l'entrepôt
            chargeSelectedFromController('allee_id_upd', 'rangee_id_upd', 'Niveau/getAllRangeeByAllee', 'modal_view_emplacement', { entrepot_id: () => $('#entrepot_id_upd').val(), allee_id: () => $('#allee_id_upd').val() }, 'rangee_id_base'); // pour charger les rangées en fonction de l'allée
            chargeSelectedFromController('rangee_id_upd', 'niveau_id_upd', 'Cage/getAllNiveauByRangee', 'modal_view_emplacement', { entrepot_id: () => $('#entrepot_id_upd').val(), allee_id: () => $('#allee_id_upd').val(), rangee_id: () => $('#rangee_id_upd').val() }, 'niveau_id_base'); // pour charger les rangée en fonction de la rangée
            chargeSelectedFromController('niveau_id_upd', 'cage_id_upd', 'Emplacement/getAllCageByNiveau', 'modal_view_emplacement', { entrepot_id: () => $('#entrepot_id_upd').val(), allee_id: () => $('#allee_id_upd').val(), rangee_id: () => $('#rangee_id_upd').val(), niveau_id: () => $('#niveau_id_upd').val() }, 'cage_id_base'); // pour charger les cages en fonction du niveau
            $("#entrepot_id_upd").trigger("change");
            $("#allee_id_upd").trigger("change");
            $("#rangee_id_upd").trigger("change");
            $("#niveau_id_upd").trigger("change");
            $("#cage_id_upd").trigger("change");
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
        text: "La suppression de cet emplacement est irréversible !",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#EF5350",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Oui",
        cancelButtonText: "Non",
        showLoaderOnConfirm: true,
        allowOutsideClick: () => !Swal.isLoading(),
        preConfirm: () => {
            loaderContent('main');
            return $.ajax({
                type: "POST",
                url: urlProject + "emplacement/deleteEmplacement",
                data: { id: id },
                dataType: "json"
            }).then(response => {
                stopLoaderContent('main');
                return parseInt(response);
            }).catch(() => {
                stopLoaderContent('main');
                Swal.fire({
                    title: "Erreur !",
                    text: "Erreur serveur ou problème réseau.",
                    icon: "error"
                });
                return false;
            });
        }
    }).then((result) => {
        if (!result.isConfirmed) return;
        let response = result.value;

        if (response === 1) {
            Swal.fire({
                title: "Supprimé !",
                text: "L'emplacement a été supprimé.",
                icon: "success",
                showConfirmButton: true
            }).then(() => {
                loadPage(urlProject + "Emplacement", true);
            });
        } else if (response === 2) {
            Swal.fire({
                title: "Suppression impossible",
                html: "Impossible de faire la suppression car l'emplacement est <b>occupé</b>.",
                icon: "warning"
            });
        } else {
            Swal.fire({
                title: "Erreur",
                text: "Une erreur est survenue lors de la suppression.",
                icon: "error"
            });
        }

    });

}

function maj() {
    isValid = checkObligatoire(".modifier-emplacement-content", ".obligatoire")
    if (isValid == true) {
        let arr_data = getFormDataFromParentClass(".modifier-emplacement-content")
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
                loaderContent('modal_view_emplacement')
                $.ajax({
                    url: urlProject + "emplacement/majEmplacement",
                    type: "POST",
                    data: { data: arr_data },
                    success: function (res) {
                        stopLoaderContent('modal_view_emplacement')
                        if (res == 1) {
                            Swal.fire({
                                title: "Modification",
                                html: "Modification faite avec succès.",
                                icon: "success",
                                showConfirmButton: true,
                            }).then(function (result) {
                                if (result.isConfirmed) {
                                    $('#modal_view_emplacement').modal('hide');
                                    loadPage(urlProject + "Emplacement", true)
                                }
                            });
                        } else if (res == 2) {
                            Swal.fire({
                                title: "Doublon",
                                html: "L'emplacement <b>" + $("#code_upd").val() + "</b> existe déjà.",
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
                                html: "Impossible de faire la modification car l'emplacement est <b>occupé</b>.",
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
                        stopLoaderContent('modal_view_emplacement')
                        $("#save_upd").prop("disabled", false);
                    }
                });
            }
        });
    }
}
