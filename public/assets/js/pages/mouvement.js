
$("#btn_entree").click(function () {
    loaderContent('main')
    $("#modal_ajout_entree").modal("show");
    $('.add-entree-content').find('input[type="text"]').val('');
    loadDataTypeAhead("qr_palette", "Mouvement/getPaletteTypeahead", "code")
    loadDataTypeAhead("qr_emplacement", "Mouvement/getEmplacementTypeahead", "code")
    $(".validation-error-label").html("");
    stopLoaderContent('main')
});

function validerEntree() {
    $(".validation-error-label").html("");
    isValid = checkObligatoire(".add-entree-content", ".obligatoire")
    if (isValid == true) {
        $("#save").prop("disabled", true);
        let arr_data = getFormDataFromParentClass(".add-entree-content")
        loaderContent('modal_ajout_entree')
        $.ajax({
            url: urlProject + "Mouvement/validerEntree",
            type: "POST",
            data: { data: arr_data },
            success: function (res) {
                stopLoaderContent('modal_ajout_entree')
                if (res == 1) {
                    Swal.fire({
                        title: "Création",
                        html: "L'entrée a été effectuée avec succès.",
                        icon: "success",
                        showConfirmButton: true
                    }).then(function (result) {
                        if (result.isConfirmed) {
                            $('#modal_ajout_entree').modal('hide');
                            loadPage(urlProject + "Mouvement", true)
                        }
                    });
                } else if (res == 2) {
                    Swal.fire({
                        title: "Données non valides",
                        html: "L'emplacement <b><i>" + $('#qr_emplacement').val() + "</b></i> est <b>invalide</b>.",
                        icon: "warning",
                        showConfirmButton: true,
                    });
                    $("#save").prop("disabled", false);
                }
                else if (res == 3) {
                    Swal.fire({
                        title: "Données non valides",
                        html: "La palette <b><i>" + $('#qr_palette').val() + "</b></i> est <b>invalide</b>.",
                        icon: "warning",
                        showConfirmButton: true,
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
                stopLoaderContent('modal_ajout_article')
                $("#save").prop("disabled", false);
            }
        });
    }
}

$("#btn_sortie").click(function () {
    loaderContent('main')
    $("#modal_ajout_sortie").modal("show");
    $('.add-sortie-content').find('input[type="text"]').val('');
    loadDataTypeAhead("qr_palette", "Mouvement/getPaletteTypeahead", "code")
    loadDataTypeAhead("qr_emplacement", "Mouvement/getEmplacementTypeahead", "code")
    $(".validation-error-label").html("");
    stopLoaderContent('main')
});

function validerSortie() {
    $(".validation-error-label").html("");
    isValid = checkObligatoire(".add-sortie-content", ".obligatoire")
    if (isValid == true) {
        $("#save").prop("disabled", true);
        let arr_data = getFormDataFromParentClass(".add-sortie-content")
        loaderContent('modal_ajout_sortie')
        $.ajax({
            url: urlProject + "Mouvement/validerSortie",
            type: "POST",
            data: { data: arr_data },
            success: function (res) {
                stopLoaderContent('modal_ajout_sortie')
                if (res == 1) {
                    Swal.fire({
                        title: "Création",
                        html: "La sortie a été effectuée avec succès.",
                        icon: "success",
                        showConfirmButton: true
                    }).then(function (result) {
                        if (result.isConfirmed) {
                            $('#modal_ajout_sortie').modal('hide');
                            loadPage(urlProject + "Mouvement", true)
                        }
                    });
                } else if (res == 2) {
                    Swal.fire({
                        title: "Données non valides",
                        html: "L'emplacement <b><i>" + $('#qr_emplacement').val() + "</b></i> est <b>invalide</b>.",
                        icon: "warning",
                        showConfirmButton: true,
                    });
                    $("#save").prop("disabled", false);
                }
                else if (res == 3) {
                    Swal.fire({
                        title: "Données non valides",
                        html: "La palette <b><i>" + $('#qr_palette').val() + "</b></i> est <b>invalide</b>.",
                        icon: "warning",
                        showConfirmButton: true,
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
                stopLoaderContent('modal_ajout_article')
                $("#save").prop("disabled", false);
            }
        });
    }
}