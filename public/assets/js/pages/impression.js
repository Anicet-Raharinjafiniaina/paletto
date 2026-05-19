$(function () {
    initializeSelect();
    refreshRemoveButtons();
    $("#entrepotSelect_1, #emplacementSelect_1, #paletteSelect_1, #data_1").hide();
});


function getAllEntrepot(selected = '') {
    let html = `<option value=""></option>`;
    entrepots.forEach(item => {
        let isSelected = item.id == selected ? 'selected' : '';
        html += `
            <option value="${item.id}" ${isSelected}>
                ${item.code} - ${item.nom}
            </option>
        `;
    });
    return html;
}

function getAllPalette(selected = '') {
    let html = `<option value=""></option>`;
    palettes.forEach(item => {
        let isSelected = item.id == selected ? 'selected' : '';
        html += `
            <option value="${item.id}" ${isSelected}>
                ${item.qr_code_text}
            </option>
        `;
    });
    return html;
}


/*** Pour la partie affichage Dynamique */
$(document).ready(function () {
    let partCount = 1;
    $('#entrepot_' + partCount).html(getAllEntrepot());
    $('#palette_' + partCount).html(getAllPalette());

    function getPartHTML(index) {
        return `
        <div class="card part-card h-100" id="part_card_${index}">
            <div class="card-body position-relative">
                <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 btn-remove-part" data-index="${index}">
                    <i class="fas fa-times"></i>
                </button>

                <div class="row" id="type_choix_${index}">
                    <div class="form-group">
                        <label>TYPE <span class="fw-bold text-danger">*</span></label>
                        <select class="select select-search obligatoire" id="type_${index}" name="type_${index}" data-placeholder="Choisir type ..." style="width: 100%;" onchange="changeSelect(this)">
                            <option></option>
                            <option value="1">Emplacement</option>
                            <option value="2">Palette</option>
                        </select>
                        <label id="type_${index}-error" class="validation-error-label" for="type_${index}"></label>
                    </div>
                </div>

                <div class="row" id="entrepotSelect_${index}">
                    <div class="form-group">
                        <label>ENTREPÔT <span class="fw-bold text-danger">*</span></label>
                        <select class="select select-search obligatoire" data-placeholder="Choisir un entrepôt..." name="entrepot_${index}" id="entrepot_${index}" style="width: 100%;" onchange="changeSelect(this)">
                            <option value=""></option>
                            ${$('#entrepot_' + partCount).html(getAllEntrepot())}
                        </select>
                        <label id="entrepot_${index}-error" class="validation-error-label" for="entrepot_${index}"></label>
                    </div>
                </div>

                <div class="row" id="emplacementSelect_${index}">
                    <div class="form-group">
                        <label>EMPLACEMENT ADRESSE <span class="fw-bold text-danger">*</span></label>
                        <select class="select select-search obligatoire" data-placeholder="Choisir un emplacement..." name="emplacement_${index}" id="emplacement_${index}" style="width: 100%;" onchange="changeSelect(this)"></select>
                        <label id="emplacement_${index}-error" class="validation-error-label" for="emplacement_${index}"></label>
                    </div>
                </div>

                <div class="form-group" id="paletteSelect_${index}">
                    <label>PALETTE <span class="fw-bold text-danger">*</span></label>
                    <select class="select select-search obligatoire" data-placeholder="Choisir une palette..." name="palette_${index}" id="palette_${index}" style="width: 100%;" onchange="changeSelect(this)">
                            <option value=""></option>
                            ${$('#entrepot_' + partCount).html(getAllPalette())}
                    </select>
                    <label id="palette_${index}-error" class="validation-error-label" for="palette_${index}"></label>
                </div>

                <div class="row mb-n4" id="data_${index}"></div>
            </div>
        </div>`;
    }

    // Reconstruit toutes les lignes depuis la liste des parts actifs
    function refreshLayout() {
        const activeParts = [];
        $('.part-card').each(function () {
            activeParts.push($(this).attr('id').replace('part_card_', ''));
        });

        // Détache les cards du DOM sans les supprimer
        activeParts.forEach(index => {
            $(`#part_card_${index}`).detach();
        });

        const $container = $('#parts-container');
        $container.empty();

        for (let i = 0; i < activeParts.length; i += 2) {
            const leftIndex = activeParts[i];
            const rightIndex = activeParts[i + 1] || null;

            const $row = $('<div class="row mb-3 part-row"></div>');

            $row.append(`<div class="col-6" id="slot_${leftIndex}"></div>`);
            if (rightIndex) {
                $row.append(`<div class="col-6" id="slot_${rightIndex}"></div>`);
            } else {
                $row.append(`<div class="col-6" id="slot_empty"></div>`);
            }

            $container.append($row);

            // Replace les cards détachées dans leurs nouveaux slots
            $(`#slot_${leftIndex}`).append($(`#part_card_${leftIndex}`));
            if (rightIndex) {
                $(`#slot_${rightIndex}`).append($(`#part_card_${rightIndex}`));
            }
        }
    }

    // Ajoute un nouveau part
    $('#btn-add-part').on('click', function () {
        partCount++;
        // Vérifie s'il y a un slot vide sur la dernière ligne (dernier part était impair)
        const $lastRow = $('#parts-container .part-row').last();
        const $emptySlot = $lastRow.find('#slot_empty');

        if ($emptySlot.length) {
            // Remplace le slot vide par le nouveau part
            $emptySlot
                .attr('id', `slot_${partCount}`)
                .html(getPartHTML(partCount));
        } else {
            // Crée une nouvelle ligne
            const $row = $('<div class="row mb-3 part-row"></div>');
            $row.append(`
                <div class="col-6" id="slot_${partCount}">
                    ${getPartHTML(partCount)}
                </div>
            `);
            $row.append(`<div class="col-6" id="slot_empty"></div>`);
            $('#parts-container').append($row);
        }
        // Attend que le DOM soit mis à jour avant d'initialiser Select2
        setTimeout(() => {
            initSelect2(partCount);
            $(`#entrepotSelect_${partCount}, #emplacementSelect_${partCount}, #paletteSelect_${partCount}, #data_${partCount}`).hide();
            refreshRemoveButtons();
            $('#entrepot_' + partCount).html(getAllEntrepot());
            $('#palette_' + partCount).html(getAllPalette());
        }, 1);
    });

    // Supprime un part et réorganise
    $(document).on('click', '.btn-remove-part', function () {
        const index = $(this).data('index');

        // Stocke les références jQuery des cards à garder
        const partsToKeep = {};
        $('.part-card').each(function () {
            const id = $(this).attr('id').replace('part_card_', '');
            if (id !== String(index)) {
                partsToKeep[id] = $(this).detach();
            } else {
                $(this).remove();
            }
        });

        // console.log('parts à garder:', Object.keys(partsToKeep));

        const $container = $('#parts-container');
        $container.empty();

        const activeIndexes = Object.keys(partsToKeep);

        for (let i = 0; i < activeIndexes.length; i += 2) {
            const leftIndex = activeIndexes[i];
            const rightIndex = activeIndexes[i + 1] || null;

            const $row = $('<div class="row mb-3 part-row"></div>');
            const $leftSlot = $(`<div class="col-6" id="slot_${leftIndex}"></div>`);
            const $rightSlot = rightIndex
                ? $(`<div class="col-6" id="slot_${rightIndex}"></div>`)
                : $(`<div class="col-6" id="slot_empty"></div>`);

            $leftSlot.append(partsToKeep[leftIndex]);
            if (rightIndex) {
                $rightSlot.append(partsToKeep[rightIndex]);
            }

            $row.append($leftSlot).append($rightSlot);
            $container.append($row);
        }
        refreshRemoveButtons()
    });

    function initSelect2(index) {
        $(`#type_${index}`).select2({ placeholder: 'Choisir type ...', allowClear: false });
        $(`#entrepot_${index}`).select2({ placeholder: 'Choisir un entrepôt...', allowClear: false });
        $(`#emplacement_${index}`).select2({ placeholder: 'Choisir un emplacement...', allowClear: false });
        $(`#palette_${index}`).select2({ placeholder: 'Choisir une palette...', allowClear: false });
    }
});

function refreshRemoveButtons() {
    const count = $('.part-card').length;
    if (count <= 1) {
        $('.btn-remove-part').hide();
    } else {
        $('.btn-remove-part').show();
    }
}
/*** Pour la partie affichage Dynamique */

function changeSelect(e) {
    let id = e.id;
    let parts = id.split('_');

    let type = parts[0];      // type / entrepot / emplacement / palette / data
    let index = parts[1];     // 3
    let valeur = $('#' + id).val();

    switch (type) {
        case 'type':
            $('#entrepot_' + index).val(null).trigger('change');
            $('#emplacement_' + index).val(null).trigger('change');
            $('#palette_' + index).val(null).trigger('change');
            $('#data_' + index).empty().hide();
            if (valeur == 1) {
                $('#entrepotSelect_' + index).show();
                $(`#paletteSelect_${index}, #emplacementSelect_${index}, #data_${index}`).hide();
            } else {
                $('#paletteSelect_' + index).show();
                $(`#entrepotSelect_${index}, #emplacementSelect_${index}, #data_${index}`).hide();
            }
            break;

        case 'entrepot':
            $('#emplacementSelect_' + index).show();
            chargeDataSelectImpression('getListEmplacementByEntrepotId', valeur, 'emplacement_' + index)
            break;

        case 'emplacement':
            $('#data_' + index).show();
            showData('getDetail', 'emplacement_' + index, 'data_' + index, type)
            break;

        case 'palette':
            $('#data_' + index).show();
            showData('getDetail', 'palette_' + index, 'data_' + index, type)
            break;
    }

}

function chargeDataSelectImpression(controllerName, valeur, selectToCharge) {
    if (valeur == '' || valeur == undefined) {
        return
    }
    loaderContent('main')
    $.ajax({
        url: urlProject + 'Impression/' + controllerName,
        type: 'POST',
        dataType: 'json',
        data: {
            id: valeur,
        },
        success: function (data) {
            stopLoaderContent('main')
            setDataSelect(selectToCharge, data)
        }
    })
}

function showData(controllerName, selectId, dataId, type) {
    var selectUsed = $('#' + selectId).val();
    if (selectUsed == '' || selectUsed == undefined) {
        return
    }
    loaderContent('main')
    $.ajax({
        url: urlProject + 'Impression/' + controllerName,
        type: 'POST',
        dataType: 'json',
        data: {
            id: selectUsed,
            type: type
        },
        success: function (data) {
            stopLoaderContent('main');
            getData(dataId, data.qrCode, data.qrText, data.statut)
        }
    })
}

function getData(dataId, qrCode, qrText, statut) {
    $('#' + dataId).empty();
    const statutConfig = {
        "Libre": "bg-success",
        "Occupé": "bg-danger",
    };
    statut = (statut || "").trim();
    let imageBase64 = `data:image/png;base64,${qrCode}`;
    let badgeClass = statutConfig[statut];
    let html = `<div class="d-flex justify-content-center">
                            <div class="d-flex align-items-center gap-3 border p-2 mb-2">
                            <img src="${imageBase64}"
                                width="80"
                                height="80"
                                style="object-fit: cover;">                    
                        <div>
                            <div><strong>${qrText}</strong></div>
                            <div>
                                <span class="badge ${badgeClass}">${statut}</span>
                            </div>
                        </div>
                </div>
                `;
    $('#' + dataId).append(html);
}

function sendData() {
    $(".validation-error-label").html("");
    isValid = checkObligatoire(".parts-container", ".obligatoire")
    if (isValid == true) {
        $("#view").prop("disabled", true);
        let arr_data = getFormDataFromParentClass(".parts-container")
        console.log(arr_data);
        loaderContent('main')
        $.ajax({
            url: urlProject + "Impression/viewDetail",
            type: "POST",
            data: { data: arr_data },
            xhrFields: {
                responseType: 'blob'
            },
            success: function (response) {
                stopLoaderContent('main');
                let blob = new Blob([response], { type: 'application/pdf' });
                let url = window.URL.createObjectURL(blob);
                window.open(url); // ouvre le PDF
                Swal.fire({
                    title: "Impression",
                    html: "PDF généré avec succès",
                    icon: "success",
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            }
        });
    }
}