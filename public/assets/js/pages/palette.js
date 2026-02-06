$(function () {
    initialiseSelect2Modal("palette_statut_id", "modal_ajout_palette")
    // initialiseSelect2Modal("client", "modal_ajout_palette")
});

function toggleClientFieldCreate() {
    let statut = $('#palette_statut_id').val();

    if (statut == 1 || statut === "" || statut === null) {
        $('#client')
            .removeClass('obligatoire')
            .prop('disabled', true)
            .val(null)
            .trigger('change');
        $('#client-required').hide();
    } else {
        $('#client')
            .addClass('obligatoire')
            .prop('disabled', false);
        $('#client-required').show();
    }
}

function toggleClientFieldUpdate() {
    let statut = $('#palette_statut_id_upd').val();
    if (statut == 1 || statut === "" || statut === null) {
        $('#client_upd')
            .removeClass('obligatoire')
            .prop('disabled', true)
            .val(null)
            .trigger('change');
        $('#client_upd-required').hide();
    } else {
        $('#client_upd')
            .addClass('obligatoire')
            .prop('disabled', false);
        $('#client_upd-required').show();
    }
}

$(document).on('change', '#palette_statut_id', toggleClientFieldCreate);
$(document).on('change', '#palette_statut_id_upd', toggleClientFieldUpdate);

$("#btn-add-palette").click(function () {
    loaderContent('main')
    $("#modal_ajout_palette").modal("show");
    $("#code").val("");
    $("#palette_statut_id").val("");
    $("#palette_statut_id").trigger("change");
    $("#client").val("");
    $(".validation-error-label").html("");
    loadClient("client")
    stopLoaderContent('main')
});

function insert() {
    $(".validation-error-label").html("");
    isValid = checkObligatoire(".add-palette-content", ".obligatoire")
    if (isValid == true) {
        $("#save").prop("disabled", true);
        let arr_data = getFormDataFromParentClass(".add-palette-content")
        loaderContent('modal_ajout_palette')
        $.ajax({
            url: urlProject + "Palette/insertPalette",
            type: "POST",
            data: { data: arr_data },
            success: function (res) {
                stopLoaderContent('modal_ajout_palette')
                if (res == 1) {
                    Swal.fire({
                        title: "Création",
                        html: "La palette a été créé avec succès",
                        icon: "success",
                        showConfirmButton: true
                    }).then(function (result) {
                        if (result.isConfirmed) {
                            $('#modal_ajout_palette').modal('hide');
                            loadPage(urlProject + "Palette", true)
                        }
                    });
                } else if (res == 2) {
                    Swal.fire({
                        title: "Doublon",
                        html: "Le code <b>" + $("#code").val() + " </b>existe déjà dans la base.",
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
                stopLoaderContent('modal_ajout_palette')
                $("#save").prop("disabled", false);
            }
        });
    }
}

function view(id, action) {
    var t = $("#l" + id).text();
    $("#content-palette").html("");
    loaderContent('main')
    $.ajax({
        url: urlProject + "Palette/getPalette",
        type: "POST",
        data: {
            id: id,
            action: action
        },
        success: function (res) {
            stopLoaderContent('main')
            $("#content-palette").html(res);
            $("#modal_view_palette").modal("show");
            initialiseSelect2Modal("palette_statut_id_upd", "modal_view_palette")
            // initialiseSelect2Modal("client_upd", "modal_view_palette")
            loadClient("client_upd")
            toggleClientFieldUpdate()
            if (action == "voir") {
                $("#div-upd-footer").css("display", "none");
                $("#title").html("Détail de la palette <b>" + t + "</b>");
            } else if (action == "upd") {
                $("#div-upd-footer").css("display", "block");
                $("#title").text("Modification d'une palette");
            }
        }
    });
}

function deleteItem(id) {
    Swal.fire({
        title: "Voulez-vous vraiment supprimer ?",
        text: "La suppression de cet entrepôt est irréversible !",
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
                url: urlProject + "Palette/deletePalette",
                data: { id: id },
                dataType: "json"
            }).then(response => {
                stopLoaderContent('main')
                if (response == 1) {
                    return true;
                } else if (response == 2) {
                    Swal.fire({
                        title: "Information",
                        html: "Impossible de supprimer cette palette car son statut est <b>occupé<b>.",
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
                text: "La palette a été supprimé.",
                icon: "success",
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                loadPage(urlProject + "Palette", true)
            });
        }
    });

}

function maj() {
    isValid = checkObligatoire(".modifier-palette-content", ".obligatoire")
    if (isValid == true) {
        let arr_data = getFormDataFromParentClass(".modifier-palette-content")
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
                loaderContent('modal_view_palette')
                $.ajax({
                    url: urlProject + "Palette/majPalette",
                    type: "POST",
                    data: { data: arr_data },
                    success: function (res) {
                        stopLoaderContent('modal_view_palette')
                        if (res == 1) {
                            Swal.fire({
                                title: "Modification",
                                html: "Modification faite avec succès.",
                                icon: "success",
                                showConfirmButton: true,
                            }).then(function (result) {
                                if (result.isConfirmed) {
                                    $('#modal_view_palette').modal('hide');
                                    loadPage(urlProject + "Palette", true)
                                }
                            });
                        } else if (res == 2) {
                            Swal.fire({
                                title: "Doublon",
                                html: "Le code  <b>" + $("#code_upd").val() + "</b> existe déjà.",
                                icon: "warning",
                                timer: 3000,
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
                        stopLoaderContent('modal_view_palette')
                        $("#save_upd").prop("disabled", false);
                    }
                });
            }
        });
    }
}