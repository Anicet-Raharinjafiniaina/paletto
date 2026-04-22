$(function () {
    initializeSelect()
    $('.nb-emplacement').hide()
    $('.plan').hide()
    $('#entrepot').closest('.col-6').removeClass('col-6').addClass('col-12');
});

function getdataByEntrepotId() {
    loaderContent('main')
    $.ajax({
        url: urlProject + "Plan/getDetail",
        type: "POST",
        data: {
            id: $('#entrepot').val()
        },
        success: function (res) {
            $('#entrepot').closest('.col-12').removeClass('col-12').addClass('col-6');
            $('.nb-emplacement').show()
            $('.plan').show()
            stopLoaderContent('main')
            let data = JSON.parse(res);
            $('#libre').text(data.libre)
            $('#occupe').text(data.occupe)
            $('#tableau-emplacement').html(data.tableau).show()
        }
    });
}

function mouvement(qrCode, statutId) {
    loaderContent('main')
    if (statutId == 1) {
        Swal.fire({
            title: 'Type de mouvement',
            text: 'Choisissez le type de mouvement',
            icon: 'question',
            showDenyButton: true,
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-sign-in-alt me-2"></i> Entrée',
            denyButtonText: '<i class="fas fa-exchange-alt me-2"></i> Transfert',
            cancelButtonText: '<i class="fas fa-times me-2"></i> Annuler',
            confirmButtonColor: '#28a745',
            denyButtonColor: '#0d6efd',
        }).then((result) => {
            if (result.isConfirmed) {
                $("#modal_ajout_entree").modal("show");
                $("#qr_emplacement_entree").val(qrCode);
                $("#qr_palette_entree").val("");
                loadDataTypeAhead("qr_emplacement_entree", "Mouvement/getEmplacementTypeahead", 1)
                loadDataTypeAhead("qr_palette_entree", "Mouvement/getPaletteTypeahead", 3, 0)
                $(".validation-error-label").html("");
            } else if (result.isDenied) {
                $("#modal_ajout_transfert").modal("show");
                $("#qr_emplacement_transfert").val(qrCode);
                $("#qr_palette_transfert").val("");
                loadDataTypeAhead("qr_emplacement_transfert", "Mouvement/getEmplacementTypeahead", 1)
                loadDataTypeAhead("qr_palette_transfert", "Mouvement/getPaletteTypeahead", 3, 1)
                $(".validation-error-label").html("");
            }
        });
    } else if (statutId == 2) {
        $("#modal_ajout_sortie").modal("show");
        $("#qr_emplacement_sortie").val(qrCode);
        $("#qr_palette_sortie").val("");
        loadDataTypeAhead("qr_emplacement_sortie", "Mouvement/getEmplacementTypeahead", 2)
        loadDataTypeAhead("qr_palette_sortie", "Mouvement/getPaletteTypeahead", 3, 1)
        $(".validation-error-label").html("");
    } else {

    }
    stopLoaderContent('main')
}