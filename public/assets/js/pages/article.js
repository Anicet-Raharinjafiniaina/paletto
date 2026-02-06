$(function () {
    initialiseSelect2Modal("palette_id", "modal_ajout_article")
});

$("#btn-add-article").click(function () {
    loaderContent('main')
    $("#modal_ajout_article").modal("show");
    $('.add-article-content')
        .find('input[type="text"], input[type="hidden"], textarea, select')
        .val('')
        .trigger('change');
    $('#quantite').val(0);
    $(".validation-error-label").html("");
    loadCodeArticle()
    loadClient("client")
    inputDateForm('dluo')
    stopLoaderContent('main')
});

function loadCodeArticle() {
    $("#code").typeahead({
        minLength: 2,
        items: 20,
        source: function (query, process) {
            return $.post(
                "Article/getArticleTypeahead",
                {
                    code: query,
                },
                function (data) {
                    data = $.parseJSON(data);
                    return process(data);
                }
            );
        },
    });
}

function getClientForPalette(idPalette, idClient, idmodal = null) {
    let palette_id = $("#" + idPalette).val();
    if (palette_id != null && palette_id != "") {
        loaderContent(idmodal)
        $.ajax({
            url: urlProject + "Article/getClientForPalette",
            type: "POST",
            data: {
                palette_id: palette_id
            },
            success: function (res) {
                stopLoaderContent(idmodal)
                res = $.parseJSON(res);
                if (res != null && res != "" && res.client_code != null && res.client_nom != null) {
                    $("#" + idClient).val(res.client_code + " - " + res.client_nom);
                    $('#' + idClient).prop('disabled', true);
                } else {
                    $("#" + idClient).val("");
                    $('#' + idClient).prop('disabled', false);
                }
            }
        });
    }
}

function getDetailArticle(idmodal = null) {
    //loaderContent(idmodal)
    $.ajax({
        url: urlProject + "Article/getDetailArticleByCode",
        type: "POST",
        data: {
            code: $('#code').val()
        },
        success: function (res) {
            // stopLoaderContent(idmodal)
            res = $.parseJSON(res);
            if (res != null) {
                $("#nom").val(res.libelle);
                $("#unite_pcb").val(res.pcb);
                $("#palettisation").val(res.palettisation);
            }
        }
    });
}

function insert() {
    $(".validation-error-label").html("");
    isValid = checkObligatoire(".add-article-content", ".obligatoire")
    if (isValid == true) {
        $("#save").prop("disabled", true);
        let arr_data = getFormDataFromParentClass(".add-article-content")
        loaderContent('modal_ajout_article')
        $.ajax({
            url: urlProject + "Article/insertArticle",
            type: "POST",
            data: { data: arr_data },
            success: function (res) {
                stopLoaderContent('modal_ajout_article')
                if (res == 1) {
                    Swal.fire({
                        title: "Création",
                        html: "L'article a été créé avec succès",
                        icon: "success",
                        showConfirmButton: true
                    }).then(function (result) {
                        if (result.isConfirmed) {
                            $('#modal_ajout_article').modal('hide');
                            loadPage(urlProject + "Article", true)
                        }
                    });
                } else if (res == 2) {
                    Swal.fire({
                        title: "Doublon",
                        html: "La palette " + $('#palette_id option:selected').text() + "est <b>occupée</b>.",
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
                stopLoaderContent('modal_ajout_article')
                $("#save").prop("disabled", false);
            }
        });
    }
}

function view(id, action) {
    var t = $("#l" + id).text();
    $("#content-article").html("");
    loaderContent('main')
    $.ajax({
        url: urlProject + "Article/getArticle",
        type: "POST",
        data: {
            id: id,
            action: action
        },
        success: function (res) {
            stopLoaderContent('main')
            $("#content-article").html(res);
            $("#modal_view_article").modal("show");
            initialiseSelect2Modal("article_statut_id_upd", "modal_view_article")
            // initialiseSelect2Modal("client_upd", "modal_view_article")
            loadClient("client_upd")
            toggleClientFieldUpdate()
            if (action == "voir") {
                $("#div-upd-footer").css("display", "none");
                $("#title").html("Détail de l'article <b>" + t + "</b>");
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
                url: urlProject + "Article/deleteArticle",
                data: { id: id },
                dataType: "json"
            }).then(response => {
                stopLoaderContent('main')
                if (response == 1) {
                    return true;
                } else if (response == 2) {
                    Swal.fire({
                        title: "Information",
                        html: "Impossible de supprimer cet article car son statut est <b>occupé<b>.",
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
                loadPage(urlProject + "Article", true)
            });
        }
    });

}

function maj() {
    isValid = checkObligatoire(".modifier-article-content", ".obligatoire")
    if (isValid == true) {
        let arr_data = getFormDataFromParentClass(".modifier-article-content")
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
                loaderContent('modal_view_article')
                $.ajax({
                    url: urlProject + "Article/majArticle",
                    type: "POST",
                    data: { data: arr_data },
                    success: function (res) {
                        stopLoaderContent('modal_view_article')
                        if (res == 1) {
                            Swal.fire({
                                title: "Modification",
                                html: "Modification faite avec succès.",
                                icon: "success",
                                showConfirmButton: true,
                            }).then(function (result) {
                                if (result.isConfirmed) {
                                    $('#modal_view_article').modal('hide');
                                    loadPage(urlProject + "Article", true)
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
                        stopLoaderContent('modal_view_article')
                        $("#save_upd").prop("disabled", false);
                    }
                });
            }
        });
    }
}