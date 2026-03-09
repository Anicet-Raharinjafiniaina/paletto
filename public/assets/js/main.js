$(function () {
    initAutoCloseSidebar(); // pour fermer le menu en le cliquant sur lui même autre part (mobile)
    $(".datatable").DataTable()

    // On prépare le menu : visible mais transparent
    $('.vertical-menu').css({
        'background': '#8a120f',
        'opacity': 0
    });

    // Après 300ms, on fait un fade-in
    setTimeout(function () {
        $('.vertical-menu').animate({ opacity: 1 }, 300);
    }, 300);
});

/** Gérer le menu pour la version mobile */
function initAutoCloseSidebar() {
    const verticalMenu = document.querySelector('.vertical-menu');
    const btn = document.getElementById('vertical-menu-btn');

    document.addEventListener('click', function (event) {
        if (window.innerWidth <= 1024) {
            if (!verticalMenu.contains(event.target) && !btn.contains(event.target)) {
                document.body.classList.remove('sidebar-enable', 'vertical-collpsed');
            }
        }
    });
}
/** /Gérer le menu pour la version mobile */

function loaderContent(id) {
    $("#" + id).block({
        message: `
      <div style="padding: 30px 0; text-align:center;">
        <div class="double-ring-spinner"></div>
        <div style="margin-top:12px; font-size:16px; color:#fff;">Chargement en cours...</div>
      </div>
    `,
        overlayCSS: {
            backgroundColor: "#1B2024",
            opacity: 0.3,
            cursor: "wait",
        },
        css: {
            border: 0,
            padding: 0,
            backgroundColor: "transparent",
            color: "#fff",
        },
    });
}


function stopLoaderContent(id) {
    $("#" + id).unblock();
}

function initializeSelect() {
    $(".select-search").select2({
        allowClear: true,
        width: "100%",
        language: {
            searching: function () {
                return "Tapez un texte...";
            },
            noResults: function () {
                return "Aucun résultat trouvé";
            },
        },
    });
}

function initialiseSelect2Modal(id, id_modal) {
    $('#' + id).select2({
        allowClear: false,
        width: "100%",
        dropdownParent: $('#' + id_modal),
        language: {
            searching: function () {
                return "Tapez un texte...";
            },
            noResults: function () {
                return "Aucun résultat trouvé";
            },
        },
    }).on("select2:open", function () {
        // Ajouter la loupe uniquement si elle n'existe pas encore
        let searchBox = $('.select2-container--open .select2-search');
        if (searchBox.find(".fa-search").length === 0) {
            searchBox.css("position", "relative");
            searchBox.prepend(`
                <i class="fa fa-search" 
                   style="position:absolute; left:15px; top:50%; transform:translateY(-50%); color:#888;"></i>
            `);
            searchBox.find("input").css("padding-left", "25px");
        }
    });
}

function checkObligatoire(parentClass, childClass) {
    let isValid = true;
    $(`${parentClass} ${childClass}`).each(function () {
        const input = $(this);
        const value = input.val();
        const id = input.attr("id");
        const errorLabel = $(`#${id}-error`);
        let name = id.replace(/_upd$/, "");
        name = name.replace(/_id$/, "")

        // ✅ Ignore les champs cachés (important ⭐)
        if (!input.is(":visible")) {
            return true;
        }

        if (!value || String(value).trim() === "") {
            errorLabel.html('<i class= "fa fa-exclamation-circle text-danger"> <span class="text-danger font-italic">Ce champ est obligatoire.</span>');
            isValid = false;
        } else {
            errorLabel.text(""); // Clear previous error
        }
    });
    return isValid;
}

function getFormDataFromParentClass(parentClass) {
    let data = {};
    $(`${parentClass} input[name], ${parentClass} select[name], ${parentClass} textarea[name]`).each(function () {
        const name = $(this).attr("name");
        const type = $(this).attr("type");
        const tag = this.tagName.toLowerCase();
        const baseName = name.replace(/_upd$/, "");
        const val = $(this).val();

        if (type === "checkbox") {
            data[baseName] = $(this).is(":checked") ? 1 : 0;
        } else if (type === "radio") {
            if ($(this).is(":checked")) {
                data[baseName] = $(this).is(":checked") ? 1 : 0;
            }
        } else {
            data[baseName] = typeof val === 'string' ? val.trim() : val;
        }
    });
    return data;
}

function setDataSelect(selector, result) {
    $('#' + selector).html('')
    let option = '<option value=""></option>'
    for (let item of result) {
        option += '<option value="' + item.id + '">' + item.text + '</option>'
    }
    $('#' + selector).html(option).trigger('change')
}

function setDataSelected(selector, result, id_to_select) {
    $('#' + selector).html('')
    let m = $('#' + id_to_select).val()
    let selected = ''
    let option = '<option value=""></option>'
    for (let item of result) {
        if (m == item.id) {
            selected = 'selected = "selected"'
        } else {
            selected = ''
        }
        option += '<option value="' + item.id + '" ' + selected + '>' + item.text + '</option>'
    }
    $('#' + selector).html(option).trigger('change')
}

/**
 * 
 * @param {select on click} select_id 
 * @param {select à charger de données venant le controlleur} select_to_charge 
 * @param {lien du controlleur} controller_url 
 * @param {pour le loader durant le chargement du select} content_to_loader 
 * @param {les données à envoyer via AJAX} dataToSend 
 *Appel de la fonction : chargeSelectFromController('entrepot_id', 'allee_id', 'Rangee/getAllAlleeByEntrepot', 'modal_ajout_rangee', { entrepot_id: () => $('#entrepot_id').val() });
 */
function chargeSelectFromController(select_id, select_to_charge, controller_url, content_to_loader, dataToSend) {
    $('#' + select_id).on('change', function () {
        if ($('#' + select_id).val() != '') {
            loaderContent(content_to_loader)
        }
        $.ajax({
            url: urlProject + controller_url,
            type: 'POST',
            dataType: 'json',
            data: dataToSend,
            success: function (data) {
                stopLoaderContent(content_to_loader)
                setDataSelect(select_to_charge, data)
            }
        })
    })
}

/**
 * 
 * @param {select on click} select_id 
 * @param {select à charger de données venant le controlleur} select_to_charge 
 * @param {lien du controlleur} controller_url 
 * @param {pour le loader durant le chargement du select} content_to_loader 
 * @param {les données à envoyer via AJAX} dataToSend 
 * @param {id séléctionné} id_to_compare
 *Appel de la fonction : chargeSelectFromController('entrepot_id', 'allee_id', 'Rangee/getAllAlleeByEntrepot', 'modal_ajout_rangee', { entrepot_id: () => $('#entrepot_id').val() }, 'allee_id_base');
 */
function chargeSelectedFromController(select_id, select_to_charge, controller_url, content_to_loader, dataToSend, id_to_compare) {
    $('#' + select_id).on('change', function () {
        if ($('#' + select_id).val() != '') {
            loaderContent(content_to_loader)
        }
        $.ajax({
            url: urlProject + controller_url,
            type: 'POST',
            dataType: 'json',
            data: dataToSend,
            success: function (data) {
                stopLoaderContent(content_to_loader)
                setDataSelected(select_to_charge, data, id_to_compare)
            }
        })
    })
}

function inputDateForm(id) {
    const input = document.getElementById(id);
    const currentValue = input.value.trim();
    let defaultDate;

    if (currentValue === "") {
        // Si le champ est vide → date du jour
        defaultDate = new Date();
    } else {
        // Si la valeur est au format dd/mm/yyyy → la convertir en Date
        const parts = currentValue.split("/");
        if (parts.length === 3) {
            const jour = parseInt(parts[0], 10);
            const mois = parseInt(parts[1], 10) - 1; // mois commence à 0
            const annee = parseInt(parts[2], 10);
            defaultDate = new Date(annee, mois, jour);
        } else {
            // fallback si format incorrect
            defaultDate = new Date();
        }
    }

    flatpickr("#" + id, {
        dateFormat: "d/m/Y",  // affichage format français
        defaultDate: defaultDate,
    });
}

function loadClient(id) {
    $("#" + id).typeahead({
        minLength: 2,
        items: 20,
        source: function (query, process) {
            return $.post(
                "Palette/getAllClientTypeahead",
                {
                    client: query,
                },
                function (data) {
                    if (data !== "" && data !== null && data !== undefined) {
                        data = $.parseJSON(data);
                        return process(data);
                    } else {
                        return process([]);
                    }
                }
            );
        },
    });
}

function numberOnly(e) {
    var v = $("#" + e.id).val()
    var cleaned = v.replace(/[^0-9]/g, '');
    $("#" + e.id).val(cleaned);
}

function numberDecimal(el) {
    try {
        // position du caret avant modification
        const selStart = el.selectionStart;
        const selEnd = el.selectionEnd;

        const raw = el.value;
        const hadTrailingSeparator = /[.,]$/.test(raw);

        // remplace toutes les virgules par des points (immédiatement)
        let v = raw.replace(/,/g, '.');

        // supprime tout sauf chiffres et points
        v = v.replace(/[^0-9.]/g, '');

        // ne garder qu'un seul point : la première occurrence
        const parts = v.split('.');
        if (parts.length > 1) {
            const intPart = parts.shift();
            // concatène le reste (pour supprimer les autres points)
            let decPart = parts.join('');
            // limite à 2 chiffres après la virgule
            decPart = decPart.substring(0, 2);
            // si l'utilisateur vient de taper '.' en fin et n'a pas encore saisi de décimales,
            // on autorise temporairement le '.' final (pour ne pas gêner la saisie)
            if (decPart.length === 0 && hadTrailingSeparator) {
                v = intPart + '.';
            } else if (decPart.length > 0) {
                v = intPart + '.' + decPart;
            } else {
                v = intPart; // pas de décimales
            }
        } else {
            // pas de point
            v = parts[0];
        }

        // calcule nouvelle position du caret pour la repositionner correctement
        // On prend la différence de longueur entre nouvelle valeur et ancienne (après normalisation des virgules)
        const normalizedRaw = raw.replace(/,/g, '.').replace(/[^0-9.]/g, '');
        const delta = v.length - normalizedRaw.length;

        el.value = v;

        // repositionne le caret (limité aux bornes valides)
        const newPos = Math.max(0, Math.min(v.length, (selEnd != null ? selEnd : selStart) + delta));
        el.setSelectionRange(newPos, newPos);
    } catch (err) {
        // si l'input n'autorise pas selectionStart (ex: certains éléments), on se contente de réaffecter la valeur
        el.value = el.value.replace(/,/g, '.').replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1').replace(/^(\d+)(\.(\d{0,2}))?.*$/, (m, a, b) => a + (b ? b.substring(0, 3) : ''));
    }
}

/**Typeahead */
function loadDataTypeAhead(inputId, controllerUrl, statut1 = null, statut2 = null) {
    $("#" + inputId).typeahead({
        minLength: 2,
        items: 20,
        source: function (query, process) {
            return $.post(
                controllerUrl,
                {
                    code: query,
                    statut_1: statut1,
                    statut_2: statut2
                },
                function (data) {
                    if (data !== "" && data !== null && data !== undefined) {
                        data = $.parseJSON(data);
                        return process(data);
                    } else {
                        return process([]);
                    }

                }
            );
        },
    });
}

/*** Scan QR code */
let html5QrCode;
let currentInputId = null;

function openScanner(inputId) {
    currentInputId = inputId;
    const modal = new bootstrap.Modal(document.getElementById('qrModal'));
    modal.show();

    html5QrCode = new Html5Qrcode("qr-reader");

    html5QrCode.start({
        facingMode: "environment"
    }, // caméra arrière
        {
            fps: 10,
            qrbox: 250
        },
        (decodedText) => {
            document.getElementById(currentInputId).value = decodedText;

            html5QrCode.stop().then(() => {
                modal.hide();
            });
        },
        (errorMessage) => {
            // erreurs ignorées (scan continue)
        }
    );
}

$('#qrModal').on('hidden.bs.modal', function () {
    if (html5QrCode) {
        html5QrCode.stop().catch(() => { });
    }
});

/** pour impression A4 */
// function imprimer(classContent) {
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
//     var content = $('.' + classContent).clone();
//     content.find('#div-upd-footer').remove();

//     doc.open();
//     doc.write(`
//         <html>
//         <head>
//             <title>Impression</title>
//             ${styles}
//             <style>
//                 @page {
//                     size: A4;
//                     margin: 15mm;
//                 }

//                 body {
//                     background: #fff !important;
//                     margin: 0;
//                     padding: 0;
//                 }

//                 /* Wrapper simple (pas de flex en print) */
//                 .print-wrapper {
//                     width: 100%;
//                 }

//                 .print-content {
//                     width: 100%;
//                     background: #fff;
//                     padding-top: 35mm;   /* ⬅️ ajuste ici */
//                 }

//                 /* Neutraliser Bootstrap */
//                 .print-content .container,
//                 .print-content .container-fluid {
//                     max-width: 100% !important;
//                     width: 100% !important;
//                 }

//                 /* QR plus grand */
//                 .print-content img {
//                     margin-bottom : 50px;
//                     max-width: 130px !important;
//                 }

//                 .print-content h6 {
//                     font-size: 22px;
//                     margin-bottom : 150px;
//                 }

//                 /* 🔥 AGRANDISSEMENT RÉEL À L'IMPRESSION */
//                 @media print {
//                     body {
//                         zoom: 175%;
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

/** pour impression A5 */
function imprimer(classContent) {
    var $iframe = $('<iframe>', {
        style: 'position:absolute; top:-10000px; left:-10000px;'
    });

    $('body').append($iframe);

    var doc = $iframe[0].contentWindow.document;

    // Récupération des styles existants
    var styles = '';
    $('link[rel="stylesheet"], style').each(function () {
        styles += this.outerHTML;
    });

    // Cloner le contenu
    var content = $('.' + classContent).clone();
    content.find('#div-upd-footer').remove();

    doc.open();
    doc.write(`
        <html>
        <head>
            <title>Impression A5</title>
            ${styles}

            <style>
                /* FORMAT A5 */
                @page {
                    size: A5 portrait;
                    margin: 10mm;
                }

                body {
                    margin: 0;
                    padding: 0;
                    background: #fff !important;
                    font-family: Arial, sans-serif;
                }

                /* Wrapper centré */
                .print-wrapper {
                    width: 148mm;      /* largeur A5 */
                    min-height: 210mm; /* hauteur A5 */
                    margin: 0 auto;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }

                .print-content {
                    width: 100%;
                    text-align: center;
                }

                /* Neutraliser Bootstrap */
                .print-content .container,
                .print-content .container-fluid {
                    max-width: 100% !important;
                    width: 100% !important;
                }

                /* QR Code */
                .print-content img {
                    max-width: 40mm !important;
                    margin-bottom: 15mm;
                }

                /* Titre */
                .print-content h6 {
                    font-size: 18pt;
                    margin-bottom: 10mm;
                }

                /* Supprimer ombres, borders inutiles */
                * {
                    box-shadow: none !important;
                }
            </style>

        </head>
        <body>
            <div class="print-wrapper">
                <div class="print-content">
                    ${content.prop('outerHTML')}
                </div>
            </div>
        </body>
        </html>
    `);

    doc.close();

    $iframe[0].contentWindow.focus();
    $iframe[0].contentWindow.print();

    setTimeout(() => {
        $iframe.remove();
    }, 1000);
}