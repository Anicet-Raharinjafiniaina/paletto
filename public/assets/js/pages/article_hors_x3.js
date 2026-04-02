$("#btn-add-article_hors_x3").click(function () {
    loaderContent('main')
    $("#modal_ajout_article_hors_x3").modal("show");
    $("#code").val("");
    $("#nom").val("");
    $("#localisation").val("");
    $(".validation-error-label").html("");
    stopLoaderContent('main')
});

function insert() {
    $(".validation-error-label").html("");
    isValid = checkObligatoire(".add-article_hors_x3-content", ".obligatoire")
    if (isValid == true) {
        $("#save").prop("disabled", true);
        let arr_data = getFormDataFromParentClass(".add-article_hors_x3-content")
        loaderContent('modal_ajout_article_hors_x3')
        $.ajax({
            url: urlProject + "ArticleHorsX3/insertArticle",
            type: "POST",
            data: { data: arr_data },
            success: function (res) {
                stopLoaderContent('modal_ajout_article_hors_x3')
                if (res == 1) {
                    Swal.fire({
                        title: "Création",
                        html: "L'article a été créé avec succès",
                        icon: "success",
                        showConfirmButton: true
                    }).then(function (result) {
                        if (result.isConfirmed) {
                            $('#modal_ajout_article_hors_x3').modal('hide');
                            loadPage(urlProject + "ArticleHorsX3", true)
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
                stopLoaderContent('modal_ajout_article_hors_x3')
                $("#save").prop("disabled", false);
            }
        });
    }
}

function view(id, action) {
    var t = $("#l" + id).text();
    $("#content-article_hors_x3").html("");
    loaderContent('main')
    $.ajax({
        url: urlProject + "ArticleHorsX3/getArticle",
        type: "POST",
        data: {
            id: id,
            action: action
        },
        success: function (res) {
            stopLoaderContent('main')
            $("#content-article_hors_x3").html(res);
            $("#modal_view_article_hors_x3").modal("show");
            refreshButtons($('#container_inputs_upd'))
            if (action == "voir") {
                $("#div-upd-footer").css("display", "none");
                $("#title").html("Détail du l'article <b>" + t + "</b>");
            } else if (action == "upd") {
                $("#div-upd-footer").css("display", "block");
                $("#title").text("Modification d'un article");
            }
        }
    });
}

function deleteItem(id) {
    Swal.fire({
        title: "Voulez-vous vraiment supprimer ?",
        text: "La suppression de cet article est irréversible !",
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
                url: urlProject + "ArticleHorsX3/deleteArticle",
                data: { id: id },
                dataType: "json" // attend une réponse JSON (1 ou 0)
            }).then(response => {
                stopLoaderContent('main')
                if (response == 1) {
                    return true;
                } else if (response == 2) {
                    Swal.fire({
                        title: "Suppression impossible",
                        html: "Impossible de faire la suppression car un ou plusieurs emplacement(s) / palette(s) sont utilisés par l'article.",
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
                text: "L'article a été supprimé.",
                icon: "success",
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                loadPage(urlProject + "ArticleHorsX3", true)
            });
        }
    });

}

function maj() {
    isValid = checkObligatoire(".modifier-article_hors_x3-content", ".obligatoire")
    if (isValid == true) {
        let arr_data = getFormDataFromParentClass(".modifier-article_hors_x3-content")
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
                loaderContent('modal_view_article_hors_x3')
                $.ajax({
                    url: urlProject + "ArticleHorsX3/majArticle",
                    type: "POST",
                    data: { data: arr_data },
                    success: function (res) {
                        stopLoaderContent('modal_view_article_hors_x3')
                        if (res == 1) {
                            Swal.fire({
                                title: "Modification",
                                html: "Modification faite avec succès.",
                                icon: "success",
                                showConfirmButton: true,
                            }).then(function (result) {
                                if (result.isConfirmed) {
                                    $('#modal_view_article_hors_x3').modal('hide');
                                    loadPage(urlProject + "ArticleHorsX3", true)
                                }
                            });
                        } else if (res == 2) {
                            Swal.fire({
                                title: "Doublon",
                                html: "Le code <b>" + $("#code_upd").val() + "</b> existe déjà.",
                                icon: "warning",
                                timer: 3000,
                                showConfirmButton: false,
                            });
                            $("#save_upd").prop("disabled", false);
                        } else if (res == 4) {
                            Swal.fire({
                                title: "Modification impossible",
                                html: "Impossible d'effectuer la modification, car un ou plusieurs emplacement(s) / palette(s) sont utilisés par l'article.",
                                icon: "warning",
                                showConfirmButton: true,
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
                        stopLoaderContent('modal_view_article_hors_x3')
                        $("#save_upd").prop("disabled", false);
                    }
                });
            }
        });
    }
}

/**
 *  input dynamique unité PCB
*/
/** Input dynamique pour ajout */
$(document).on('click', '#container_inputs_add .btn-add', function () {
    addInput('#container_inputs_add');
});

/** Input dynamique pour modification */
$(document).on('click', '#container_inputs_upd .btn-add', function () {
    addInput('#container_inputs_upd');
});


/** Supprimer dynamiquement */
$(document).on('click', '.btn-remove', function () {
    let row = $(this).closest('.dynamic-row');
    let container = row.closest('.border');

    let uniteId = row.find('input[id^="unite_pcb"]').attr('id');
    let palletId = row.find('input[id^="palettisation"]').attr('id');

    row.next('.error-row').remove(); // supprimer la ligne d'erreur
    row.remove();

    $("#" + uniteId + "-error").remove();
    $("#" + palletId + "-error").remove();
    refreshButtons(container);
});


/** Calcul index */
function getNextIndex(containerSelector) {
    let max = 1;
    $(`${containerSelector} input[id^="unite_pcb_"]`).each(function () {
        let parts = this.id.split('_');
        let num = parseInt(parts[parts.length - 1]);
        if (!isNaN(num) && num > max) max = num;
    });
    return max + 1;
}


/** Ajouter ligne */
function addInput(containerSelector) {
    let index = getNextIndex(containerSelector);
    let uniteId = `unite_pcb_${index}`;
    let palletId = `palettisation_${index}`;
    let html = `
               <!-- Ligne initiale -->
                            <div class="d-flex align-items-center gap-2 dynamic-row mb-2" data-index="1">
                                <input type="text"  id="${uniteId}" name="${uniteId}"
                                    placeholder="Unité PCB" class="form-control obligatoire flex-fill">
                                <input type="text" id="${palletId}" name="${palletId}"
                                    placeholder="Palettisation" class="form-control obligatoire flex-fill">
                                <div class="buttons-area flex-shrink-0"> <button type="button" class="btn btn-primary btn-xs btn-add">
                                        <i class="fas fa-plus"></i>
                                    </button></div>
                            </div>

                            <!-- Ligne erreurs -->
                            <div class="d-flex align-items-center gap-2 mb-2 error-row">
                                <span class="flex-shrink-0" style="width: 50%;">
                                    <label id="${uniteId}-error" class="validation-error-label" for="${uniteId}"></label>
                                </span>
                                <span class="flex-shrink-0" style="width: 50%;">
                                    <label id="${palletId}-error" class="validation-error-label" for="${palletId}"></label>
                                </span>
                            </div>
    `;

    $(containerSelector).append(html);
    refreshButtons($(containerSelector));
}


/** Refresh boutons */
function refreshButtons(container) {
    let rows = container.find('.dynamic-row');
    rows.each(function (i) {
        let btnArea = $(this).find('.buttons-area');
        btnArea.html('');
        if (rows.length === 1) {
            btnArea.append(`
                <button type="button" class="btn btn-primary btn-xs btn-add">
                    <i class="fas fa-plus"></i>
                </button>
            `);
        } else if (i === rows.length - 1) {
            btnArea.append(`
                <button type="button" class="btn btn-primary btn-xs btn-add">
                    <i class="fas fa-plus"></i>
                </button>

                <button type="button" class="btn btn-danger btn-xs btn-remove ms-1">
                    <i class="fas fa-times"></i>
                </button>
            `);

        } else {
            btnArea.append(`
                <button type="button" class="btn btn-danger btn-xs btn-remove">
                    <i class="fas fa-times"></i>
                </button>
            `);
        }

    });
}
/** /input dynamique unité PCB */


$('#modal_ajout_article_hors_x3').on('show.bs.modal', function () {
    let container = $('#container_inputs_add');
    container.empty();     // Vider le container

    // Remettre la ligne initiale
    let html = `
        <div class="d-flex align-items-center gap-2 dynamic-row mb-2" data-index="1">
            <input type="text" id="unite_pcb_1" name="unite_pcb_1" placeholder="Unité PCB" class="form-control obligatoire flex-fill">
            <input type="text" id="palettisation_1" name="palettisation_1" placeholder="Palettisation" class="form-control obligatoire flex-fill">
            <div class="buttons-area flex-shrink-0"></div>
        </div>

        <div class="d-flex align-items-center gap-2 mb-2 error-row">
            <span class="flex-shrink-0" style="width:50%">
                <label id="unite_pcb_1-error" class="validation-error-label" for="unite_pcb_1"></label>
            </span>
            <span class="flex-shrink-0" style="width:50%">
                <label id="palettisation_1-error" class="validation-error-label" for="palettisation_1"></label>
            </span>
        </div>
    `;

    container.append(html);

    // Rafraîchir les boutons
    refreshButtons(container);

});