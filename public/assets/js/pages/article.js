$(function () {
    initialiseSelect2Modal("palette_id", "modal_ajout_article")
    initialiseSelect2Modal("unite_pcb", "modal_ajout_article")
});

$("#btn-add-article").click(function () {
    loaderContent('main')
    $('input[type="checkbox"]').prop('checked', false);
    $('.option-check').removeClass('border-primary bg-light');
    $(".form-article").hide()
    $("#type_article").hide()
    $("#modal_ajout_article").modal("show");
    resetArticleForm()
    getListPalette()
    $('#quantite').val(0);
    $(".validation-error-label").html("");
    inputDateForm('dluo')
    stopLoaderContent('main')
});

function getListPalette() {
    $.ajax({
        url: urlProject + "Article/getAllPaletteNoTOccuped",
        type: "POST",
        dataType: "json",
        success: function (res) {
            setDataSelect("palette_id", res)
        }
    })
}

function resetArticleForm() {
    $('.add-article-content')
        .find('input[type="text"], input[type="hidden"], textarea, select')
        .val('')
        .trigger('change');
    intiSelectUnitePcb();
}

function intiSelectUnitePcb() {    // Réinitialiser tous les selects (Select2 inclus)
    $('#unite_pcb').each(function () {
        $(this).empty();
        $(this).val(null).trigger('change'); // fonctionne pour single et multiple
    });
}

/** pour type d'article */
var typeArticle = null;
$('.article-option').on('change', function () { // toggle choice
    $("#type_article").hide();
    $('.article-option').not(this).prop('checked', false);
    $(".form-article").show()
    $(".validation-error-label").html("");
    typeArticle = $('.article-option:checked').val();
    $('.option-check').removeClass('border-primary bg-light');
    if ($(this).is(':checked')) {
        $(this).closest('.option-check').addClass('border-primary bg-light');
    }
    resetArticleForm()
    getListPalette()
    loadCodeArticle()
    loadClient("client")
});
/** /pour type d'article */

function getAllUnitePCB() {
    var url = "";
    if (typeArticle == "x3") {
        url = urlProject + "Article/getUnitePCB";
    } else {
        url = urlProject + "Article/getUnitePCBHorsX3";
    }
    loaderContent('modal_ajout_article')
    $.ajax({
        url: url,
        type: "POST",
        dataType: "json",
        data: { code: $('#code').val() },
        success: function (res) {
            intiSelectUnitePcb()
            stopLoaderContent('modal_ajout_article')
            setDataSelect("unite_pcb", res)
        }
    })
}

function loadCodeArticle() {
    let url = "";
    if (typeArticle == "x3") {
        url = urlProject + "Article/getArticleTypeahead";
    } else {
        url = urlProject + "Article/getArticleHorsX3Typeahead";
    }
    $("#code").typeahead('destroy');
    $("#code").typeahead({
        minLength: 2,
        items: 20,
        source: function (query, process) {
            return $.post(
                url,
                { code: query },
                function (data) {
                    data = $.parseJSON(data);
                    return process(data);
                }
            );

        }
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
    let url = "";
    if (typeArticle == "x3") {
        url = urlProject + "Article/getDetailArticleByCode";
    } else {
        url = urlProject + "Article/getDetailArticleHorsX3ByCode";
    }
    $.ajax({
        url: url,
        type: "POST",
        data: {
            code: $('#code').val()
        },
        success: function (res) {
            res = $.parseJSON(res);
            if (res != null) {
                $("#nom").val(res.libelle);
                $("#unite_pcb").val(res.pcb);
                $("#unite_stockage").val(res.unite_stockage);
                getAllUnitePCB()
            }
        }
    });
}

function getUnitePCBValue() {
    let unite_pcb = "";
    if ($("#unite_pcb").is(":visible")) {
        unite_pcb = $("#unite_pcb").val();
    }
    else if ($("#unite_pcb_input").is(":visible")) {
        unite_pcb = $("#unite_pcb_input").val();
    }
    return unite_pcb ? unite_pcb.trim() : "";
}

function getPalettisation() {
    let url = "";
    if (typeArticle == "x3") {
        url = urlProject + "Article/getPalettisation";
    } else {
        url = urlProject + "Article/getPalettisationHorsX3";
    }
    $.ajax({
        url: url,
        type: "POST",
        dataType: "json",
        data: {
            code: $('#code').val()
        },
        success: function (res) {
            var unitePcb = $('#unite_pcb').val()
            $('#palettisation').val(res[unitePcb]);
        }
    });
}

function insert() {
    $(".validation-error-label").html("");
    if (typeArticle == undefined && typeArticle == null) {
        $("#type_article").show();
        return;
    }
    $("#type_article").hide();
    isValid = checkObligatoire(".add-article-content", ".obligatoire")
    if (isValid == true) {
        $("#save").prop("disabled", true);
        let arr_data = getFormDataFromParentClass(".add-article-content")
        arr_data['unite_pcb'] = getUnitePCBValue()
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
                        html: "L'article a été attribué à la palette.",
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

// function imprimer() {
//     // Créer un iframe caché
//     var $iframe = $('<iframe>', { name: 'print_frame', style: 'position:absolute; top:-10000px;' });
//     $('body').append($iframe);

//     var doc = $iframe[0].contentWindow.document;

//     // Copier le CSS existant de la page (Bootstrap + styles personnalisés)
//     var styles = '';
//     $('link[rel="stylesheet"], style').each(function () {
//         styles += this.outerHTML;
//     });

//     // Copier le contenu du formulaire
//     var content = $('.form-validate-upd-jquery.modifier-article-content').clone();
//     content.find('#div-upd-footer').remove(); // supprimer le bouton

//     // Écrire le contenu et les styles dans l'iframe
//     doc.open();
//     doc.write('<html><head><title>Impression</title>' + styles + '</head><body style="background-color: #fff;">');
//     doc.write(content.prop('outerHTML'));
//     doc.write('</body></html>');
//     doc.close();

//     // Lancer l'impression
//     $iframe[0].contentWindow.focus();
//     $iframe[0].contentWindow.print();

//     // Supprimer l'iframe après impression
//     setTimeout(function () {
//         $iframe.remove();
//     }, 1000);
// }

// function imprimer() {
//     var $iframe = $('<iframe>', {
//         style: 'position:absolute; top:-10000px; left:-10000px;'
//     });
//     $('body').append($iframe);

//     var doc = $iframe[0].contentWindow.document;

//     // Récupération des styles existants
//     var styles = '';
//     $('link[rel="stylesheet"], style').each(function () {
//         styles += this.outerHTML;
//     });

//     // Cloner le formulaire
//     var content = $('.form-validate-upd-jquery.modifier-article-content').clone();
//     content.find('#div-upd-footer').remove();

//     doc.open();
//     doc.write(`
//         <html>
//         <head>
//             <title>Impression</title>
//             ${styles}
//             <style>
//                 /* Taille page */
//                 @page {
//                     size: A4;
//                     margin: 20mm;
//                 }

//                 body {
//                     background: #fff !important;
//                     margin: 0;
//                     padding: 0;
//                 }

//                 /* Centrage */
//                 .print-wrapper {
//                     width: 100%;
//                     min-height: 100vh;
//                     display: flex;
//                     justify-content: center;
//                     align-items: center;
//                 }

//                 /* CONTENU AGRANDI RÉELLEMENT */
//                 .print-content {
//                     width: 100%;
//                     max-width: 700px;   /* ⬅️ augmente la largeur */
//                     font-size: 18px;    /* ⬅️ taille réelle du texte */
//                     background: #fff;
//                 }

//                 /* QR plus grand */
//                 .print-content img {
//                     max-width: 160px !important;
//                 }

//                 /* Titres plus visibles */
//                 .print-content h6 {
//                     font-size: 22px;
//                 }

//                 @media print {
//                     body {
//                         background: #fff !important;
//                     }
//                 }
//             </style>
//         </head>
//         <body>
//             <div class="print-wrapper">
//                 <div class="print-content">
//                     ${content.prop('outerHTML')}
//                 </div>
//             </div>
//         </body>
//         </html>
//     `);
//     doc.close();

//     $iframe[0].contentWindow.focus();
//     $iframe[0].contentWindow.print();

//     setTimeout(() => {
//         $iframe.remove();
//     }, 1000);
// }