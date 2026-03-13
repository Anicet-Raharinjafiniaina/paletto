let charts = {};

function animateCounter($el, value) {
    $({ countNum: 0 }).animate({ countNum: value }, {
        duration: 1000,
        easing: 'swing',
        step: function () { $el.text(Math.floor(this.countNum)); },
        complete: function () { $el.text(value); }
    });
}

function updateCard(cardSelector, values, chartLabels, chartColors) {
    const $card = $(cardSelector);

    // Mettre à jour les compteurs
    $card.find('.counter-value').each(function (index) {
        let value = values[index] || 0;
        $(this).data('target', value);
        animateCounter($(this), value);
    });

    // Mettre à jour le graphique
    const canvas = $card.find('canvas')[0];
    const ctx = canvas.getContext('2d');

    if (charts[canvas.id]) charts[canvas.id].destroy();

    charts[canvas.id] = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: chartLabels,
            datasets: [{
                data: values,
                backgroundColor: chartColors
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' },
                datalabels: {
                    color: '#fff',
                    formatter: (value, ctx) => {
                        const total = ctx.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                        const percent = total ? ((value / total) * 100).toFixed(0) : 0;
                        return percent + '%';
                    },
                    font: { weight: 'bold', size: 14 }
                }
            }
        },
        plugins: [ChartDataLabels] // <=== plugin pour afficher les pourcentages
    });
}

function updateBarChart(cardSelector, values, chartLabels, chartColors) {
    const $card = $(cardSelector);

    // Mettre à jour les compteurs si présents
    $card.find('.counter-value').each(function (index) {
        let value = values[index] || 0;
        $(this).data('target', value);
        animateCounter($(this), value);
    });

    // Mettre à jour le graphique
    const canvas = $card.find('canvas')[0];
    const ctx = canvas.getContext('2d');

    if (charts[canvas.id]) charts[canvas.id].destroy();

    charts[canvas.id] = new Chart(ctx, {
        type: 'bar', // graphe vertical
        data: {
            labels: chartLabels,
            datasets: [{
                label: 'Valeurs',
                data: values,
                backgroundColor: chartColors,
                borderColor: chartColors.map(c => c + 'BB'),
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                datalabels: {
                    color: '#000',
                    anchor: 'end',
                    align: 'top',
                    font: { weight: 'bold', size: 12 },
                    formatter: (value) => value
                }
            },
            scales: {
                y: { beginAtZero: true, title: { display: true, text: 'Nombre' } },
                x: { title: { display: true, text: 'Catégorie' } }
            }
        },
        plugins: [ChartDataLabels]
    });
}


$('#periode').on('change', function () {
    let periode = $(this).val();

    $.ajax({
        url: urlProject + 'Dashboard/getDataForChartPie',
        type: 'POST',
        dataType: 'json',
        data: { periode: periode },
        success: function (data) {
            console.log(data);

            updateCard('.card:has(#pie_emplacement)',
                [parseInt(data.emplacement.nb_libre), parseInt(data.emplacement.nb_occupe)],
                ['Libre', 'Occupé'],
                ['#10B981', '#EF4444']);

            updateCard('.card:has(#pie_palette)',
                [parseInt(data.palette.nb_libre), parseInt(data.palette.nb_attribue), parseInt(data.palette.nb_occupe)],
                ['Libre', 'Attribuée', 'Occupée'],
                ['#10B981', '#2563EB', '#EF4444']);

            updateCard('.card:has(#pie_mouvement)',
                [parseInt(data.mouvement.nb_entree), parseInt(data.mouvement.nb_transfert), parseInt(data.mouvement.nb_sortie)],
                ['Entrée', 'Transfert', 'Sortie'],
                ['#10B981', '#2563EB', '#EF4444']);

            updateBarChart('.card:has(#bar_entrepot)',
                [parseInt(data.entrepot.nb_libre), parseInt(data.entrepot.nb_occupe), parseInt(data.entrepot.nb_reserve)],
                ['Libre', 'Occupé', 'Réservé'],
                ['#10B981', '#EF4444', '#2563EB']);
        }
    });
});
