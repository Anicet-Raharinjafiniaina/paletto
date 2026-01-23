$("#btn-add-profil").click(function () {
    loaderContent('main')
    $("#modal_ajout_profil").modal("show");
    initializeDuallistBox("page_id")
    /** pour dualListBox */
    $('.icon-last').removeClass('icon-last').addClass('fas fa-angle-double-right');
    $('.icon-first').removeClass('icon-first').addClass('fas fa-angle-double-left');
    /** /pour dualListBox */
    $("#page_id").val("");
    $("#profil").val("");
    $(".validation-error-label").html("");
    stopLoaderContent('main')
});

function insert() {
    $(".validation-error-label").html("");
    isValid = true;
    if ($('#page_id').val().length === 0) {
        $('#page_id-error').html('<i class= "fa fa-exclamation-circle"> <span class="text-danger font-italic">Séléctionné au moins une page..</span>');
        isValid = false;
    }
    if ($('#profil').val() == "") {
        $('#profil-error').html('<i class= "fa fa-exclamation-circle"> <span class="text-danger font-italic">Ce champ libellé de profil est obligatoire.</span>');
        isValid = false;
    }
    if (isValid) {
        $("#save").prop("disabled", true);
        loaderContent('modal_ajout_profil')
        $.ajax({
            url: urlProject + "Profil/insertProfil",
            type: "POST",
            data: {
                profil: $('#profil').val().trim(),
                arr_page_id: $('#page_id').val()
            },
            success: function (res) {
                stopLoaderContent('modal_ajout_profil')
                if (res == 1) {
                    Swal.fire({
                        title: "Création",
                        html: "Le profil <b>" + $("#profil").val() + "</b> a été créé avec succès",
                        icon: "success",
                        showConfirmButton: true
                    }).then(function (result) {
                        if (result.isConfirmed) {
                            $('#modal_ajout_profil').modal('hide');
                            loadPage(urlProject + "Profil", true)
                        }
                    });
                } else if (res == 2) {
                    Swal.fire({
                        title: "Doublon",
                        html: "Le libellé  <b>" + $("#profil").val() + "</b> existe déjà",
                        icon: "warning",
                        timer: 4000,
                        showConfirmButton: false,
                    });
                    $("#save").prop("disabled", false);
                } else {
                    Swal.fire({
                        title: "Erreur",
                        html: "Erreur dans la base de données. Merci de réessayer plus tard.",
                        icon: "error",
                        timer: 3000,
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
                stopLoaderContent('modal_ajout_profil')
                $("#save").prop("disabled", false);
            }
        });
    }

}

function view(id, action) {
    var t = $("#l" + id).text();
    $("#content-profil").html("");
    loaderContent('main')
    $.ajax({
        url: urlProject + "Profil/getDetail",
        type: "POST",
        data: {
            id: id,
            action: action
        },
        success: function (res) {
            stopLoaderContent('main')
            $("#content-profil").html(res);
            $("#modal_view_profil").modal("show");
            if (action == "voir") {
                $("#div-upd-footer").css("display", "none");
                $("#title").html("Détail du profil <b>" + t + "</b>");
            } else if (action == "upd") {
                $("#div-upd-footer").css("display", "block");
                $("#title").text("Modification d'un profil");
            }
        }
    });
}

function maj() {
    $("#page_id_upd-error").text("");
    $("#profil_upd-error").text("");
    isValid = true;
    if ($('#profil_upd').val().length === 0) {
        $('#profil_upd-error').html('<i class= "fa fa-exclamation-circle"> <span class="text-danger font-italic"> Ce champ libellé de profil est obligatoire</span>');
        isValid = false;
    }
    if ($('#page_id_upd').val() == "[]") {
        $('#page_id_upd-error').html('<i class= "fa fa-exclamation-circle"> <span class="text-danger font-italic"> Séléctionné au moins une page...</span>');
        isValid = false;
    }
    if (isValid == true) {
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
                loaderContent('modal_view_profil')
                $.ajax({
                    url: urlProject + "Profil/majProfil",
                    type: "POST",
                    data: {
                        id: $('#id_upd').val(),
                        profil: $('#profil_upd').val().trim(),
                        arr_page_id: $('#page_id_upd').val(),
                        actif: $('#actif').val()
                    },
                    success: function (res) {
                        stopLoaderContent('modal_view_profil')
                        if (res == 1) {
                            Swal.fire({
                                title: "Modification",
                                html: "Modification faite avec succès.",
                                icon: "success",
                                showConfirmButton: true,
                            }).then(function (result) {
                                if (result.isConfirmed) {
                                    $('#modal_view_profil').modal('hide');
                                    loadPage(urlProject + "Profil", true)
                                }
                            });
                        } else if (res == 2) {
                            Swal.fire({
                                title: "Doublon",
                                html: "Le libellé  <b>" + $("#profil_upd").val() + "</b> existe déjà.",
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
                        stopLoaderContent('modal_view_profil')
                        $("#save_upd").prop("disabled", false);
                    }
                });
            }
        });
    }
}


function deleteItem(id) {
    Swal.fire({
        title: "Voulez-vous vraiment supprimer ?",
        text: "La suppression de ce profil est irréversible !",
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
                url: urlProject + "Profil/deleteProfil",
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
                text: "Le profil a été supprimé.",
                icon: "success",
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                loadPage(urlProject + "Profil", true)
            });
        }
    });
}

function initializeDuallistBox(id) {
    if (!$('#' + id).data('dualListBoxInitialized')) {
        $('#' + id).bootstrapDualListbox({
            selectorMinimalHeight: 150,
            filterPlaceHolder: "Rechercher",
            nonSelectedListLabel: "Disponibles",
            selectedListLabel: "Sélectionnées",
            infoTextEmpty: "Liste vide",
            infoText: "Afficher tout {0}",
            filterTextClear: "Afficher tout",
            infoTextFiltered: '<span class="label label-warning">Filtré</span> {0} à {1}',
            showSelectAll: true
        });
        $('#' + id).data('dualListBoxInitialized', true); // Pour éviter double init
    }
}