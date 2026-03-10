$("#btn_entree").click(function () {
    loaderContent('main')
    $("#modal_ajout_entree").modal("show");
    $('.add-entree-content').find('input[type="text"]').val('');
    loadDataTypeAhead("qr_emplacement_entree", "Mouvement/getEmplacementTypeahead", 1)
    loadDataTypeAhead("qr_palette_entree", "Mouvement/getPaletteTypeahead", 3, 0)
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
                        title: "Entrée effectuée",
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
                        html: "L'emplacement <b><i>" + $('#qr_emplacement_entree').val() + "</b></i> est <b>invalide</b>.",
                        icon: "warning",
                        showConfirmButton: true,
                    });
                    $("#save").prop("disabled", false);
                }
                else if (res == 3) {
                    Swal.fire({
                        title: "Données non valides",
                        html: "La palette <b><i>" + $('#qr_palette_entree').val() + "</b></i> est <b>invalide</b>.",
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
    loadDataTypeAhead("qr_emplacement_sortie", "Mouvement/getEmplacementTypeahead", 2)
    loadDataTypeAhead("qr_palette_sortie", "Mouvement/getPaletteTypeahead", 3, 1)
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
                        title: "Sortie effectuée",
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
                        html: "L'emplacement <b><i>" + $('#qr_emplacement_sortie').val() + "</b></i> est <b>invalide</b>.",
                        icon: "warning",
                        showConfirmButton: true,
                    });
                    $("#save").prop("disabled", false);
                } else if (res == 3) {
                    Swal.fire({
                        title: "Données non valides",
                        html: "La palette <b><i>" + $('#qr_palette_sortie').val() + "</b></i> est <b>invalide</b>.",
                        icon: "warning",
                        showConfirmButton: true,
                    });
                    $("#save").prop("disabled", false);
                } else if (res == 4) {
                    Swal.fire({
                        title: "Données non valides",
                        html: "La palette <b><i>" + $('#qr_palette_sortie').val() + "</b></i> ne correspond pas à l'emplacement <b><i>" + $('#qr_emplacement_sortie').val() + "</b></i>.",
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

$("#btn_transfert").click(function () {
    loaderContent('main')
    $("#modal_ajout_transfert").modal("show");
    $('.add-transfert-content').find('input[type="text"]').val('');
    loadDataTypeAhead("qr_emplacement_transfert", "Mouvement/getEmplacementTypeahead", 1)
    loadDataTypeAhead("qr_palette_transfert", "Mouvement/getPaletteTypeahead", 3, 1)
    $(".validation-error-label").html("");
    stopLoaderContent('main')
});

function validerTransfert() {
    $(".validation-error-label").html("");
    isValid = checkObligatoire(".add-transfert-content", ".obligatoire")
    if (isValid == true) {
        $("#save").prop("disabled", true);
        let arr_data = getFormDataFromParentClass(".add-transfert-content")
        loaderContent('modal_ajout_transfert')
        $.ajax({
            url: urlProject + "Mouvement/validerTransfert",
            type: "POST",
            data: { data: arr_data },
            success: function (res) {
                stopLoaderContent('modal_ajout_transfert')
                if (res == 1) {
                    Swal.fire({
                        title: "Transfert effectué",
                        html: "Le transfert a été effectué avec succès.",
                        icon: "success",
                        showConfirmButton: true
                    }).then(function (result) {
                        if (result.isConfirmed) {
                            $('#modal_ajout_transfert').modal('hide');
                            loadPage(urlProject + "Mouvement", true)
                        }
                    });
                } else if (res == 2) {
                    Swal.fire({
                        title: "Données non valides",
                        html: "L'emplacement <b><i>" + $('#qr_emplacement_transfert').val() + "</b></i> est <b>invalide</b>.",
                        icon: "warning",
                        showConfirmButton: true,
                    });
                    $("#save").prop("disabled", false);
                }
                else if (res == 3) {
                    Swal.fire({
                        title: "Données non valides",
                        html: "La palette <b><i>" + $('#qr_palette_transfert').val() + "</b></i> est <b>invalide</b>.",
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

$(document).ready(function () {
    initDataTableServerSide({
        selector: '#tbl_mouvement',
        ajaxUrl: urlProject + "Mouvement/historiqueMouvement",
        columns: [
            {
                data: 'type',
                render: function (data) {
                    if (data === 'Entrée') {
                        return '<span class="badge rounded-pill p-2 bg-success">Entrée</span>';
                    }
                    if (data === 'Sortie') {
                        return '<span class="badge rounded-pill p-2 bg-danger">Sortie</span>';
                    }
                    if (data === 'Transfert') {
                        return '<span class="badge rounded-pill p-2 bg-primary">Transfert</span>';
                    }
                    return data;
                }
            },
            'emplacement',
            'palette_article',
            'date_mouvement'
        ],
        actions: true
    });

});

$.fn.toggleText = function (t1, t2) {
    return this.each(function () {
        const $this = $(this);
        $this.text($this.text() === t1 ? t2 : t1);
    });
};

function toggleHistorique(e) {
    $('#tbl_content').toggleClass('d-none');
    $('#historique a').toggleText('Afficher l\'historique', 'Masquer l\'historique');

    setTimeout(function () {
        /* réinitialiser les datatables */
        let table = $('.table').DataTable();
        table.destroy();
        $('.table').DataTable({
            responsive: true,
            autoWidth: false
        });
        /* /réinitialiser les datatables */
    }, 50);
}

function view(id, action) {
    var t = $("#l" + id).text();
    $("#content-mouvement").html("");
    loaderContent('main')
    $.ajax({
        url: urlProject + "Mouvement/getDetailMouvement",
        type: "POST",
        data: {
            id: id,
            action: action
        },
        success: function (res) {
            stopLoaderContent('main')
            $("#content-mouvement").html(res);
            $("#modal_view_mouvement").modal("show");
            if (action == "voir") {
                $("#div-upd-footer").css("display", "none");
                $("#title").html("Détail d'un mouvement <b>" + t + "</b>");
            }
        }
    });
}
