$(document).ready(function () {
    $('.periode').hide();
    feather.replace();   // Feather Icons
    // Daterangepicker stylé Bootstrap 5
    $('#periode_range').daterangepicker({
        opens: 'right',
        autoUpdateInput: true,
        showDropdowns: true,
        linkedCalendars: true,
        maxDate: moment(), // pas de date future
        startDate: moment().startOf('month'), // mois en cours par défaut
        endDate: moment().endOf('month'),
        locale: {
            format: 'DD/MM/YYYY',
            applyLabel: 'Appliquer',
            cancelLabel: 'Annuler',
            customRangeLabel: 'Plage personnalisée',
            daysOfWeek: ['Di', 'Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa'],
            monthNames: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
            firstDay: 1
        },
        ranges: {
            'Aujourd\'hui': [moment(), moment()],
            'Hier': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            '7 derniers jours': [moment().subtract(6, 'days'), moment()],
            '30 derniers jours': [moment().subtract(29, 'days'), moment()],
            'Ce mois-ci': [moment().startOf('month'), moment().endOf('month')],
            'Mois dernier': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    });

    // Vider l’input si Annuler
    $('#periode_range').on('cancel.daterangepicker', function (ev, picker) {
        $(this).val('');
    });
});

$('#type').on('change', function () {
    if ($('#type').val() == 1) {
        $('.periode').show();
    } else {
        $('.periode').hide();
    }
});

function exporter() {
    const isValid = checkObligatoire(".content-export", ".obligatoire");
    if (isValid) {
        const form = $('<form>', {
            method: 'POST',
            action: urlProject + 'Rapport/doExport',
            target: 'downloadFrame'
        });
        form.append($('<input>', { type: 'hidden', name: 'periode', value: $('#periode_range').val() }));
        form.append($('<input>', { type: 'hidden', name: 'type', value: $('#type').val() }));
        $('body').append(form);
        form.submit();
    }
}