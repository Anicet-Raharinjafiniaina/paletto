//var charts = {};
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

function updateEntrepotChart(canvasId, data) {
    const labels = data.map(e => e.entrepot_code);    // Labels → codes des entrepôts

    // Convertir les valeurs en nombres
    const libreData = data.map(e => parseFloat(e.libre) || 0);
    const occupeData = data.map(e => parseFloat(e.occupe) || 0);

    const canvas = document.getElementById(canvasId);
    const ctx = canvas.getContext('2d');

    const oldChart = Chart.getChart(canvas); // récupère l'instance existante
    if (oldChart) oldChart.destroy();

    charts[canvasId] = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Libre',
                    data: libreData,
                    backgroundColor: '#10B981'
                },
                {
                    label: 'Occupé',
                    data: occupeData,
                    backgroundColor: '#EF4444'
                }
            ]
        },
        options: {
            layout: {
                padding: {
                    top: 30   // espace entre le canvas et le haut (légende)
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        font: { size: 14, weight: 'bold' }
                    }
                },
                datalabels: {
                    color: '#ffffff',       // couleur blanche
                    anchor: 'center',       // au centre de la barre
                    align: 'center',        // centré
                    font: { weight: 'bold', size: 12 },
                    formatter: value => value + '%'   // ajoute le %
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    title: { display: true, text: 'Pourcentage (%)' }
                },
                x: {
                    title: { display: true, text: 'Entrepôt' }
                }
            }
        },
        plugins: [ChartDataLabels]
    });
}

function loadMouvementFlux(fluxChart, data) {

    // const labels = data.map(e => e.periode);
    // const entree = data.map(e => e.entree);
    // const sortie = data.map(e => e.sortie);
    // const transfert = data.map(e => e.transfert);

    const labels = data.map(e => e.periode);
    const entree = data.map(e => Number(e.entree));
    const sortie = data.map(e => Number(e.sortie));
    const transfert = data.map(e => Number(e.transfert));

    const ctx = document.getElementById(fluxChart);

    if (window.fluxChart) {
        window.fluxChart.destroy();
    }

    window.fluxChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Entrée',
                    data: entree,
                    borderColor: '#4CAF50',
                    backgroundColor: '#4CAF50',
                    tension: 0.3
                },
                {
                    label: 'Sortie',
                    data: sortie,
                    borderColor: '#F44336',
                    backgroundColor: '#F44336',
                    tension: 0.3
                },
                {
                    label: 'Transfert',
                    data: transfert,
                    borderColor: '#2196F3',
                    backgroundColor: '#2196F3',
                    tension: 0.3
                }
            ]
        },
        options: {
            responsive: true,
            interaction: {
                mode: 'index',
                intersect: false
            },
            plugins: {
                legend: {
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

$(document).ready(function () {
    setTimeout(function () {
        $('#periode').val('mensuel').trigger('change');
    }, 500);
});

$('#periode').on('change', function () {
    let periode = $(this).val();
    $.ajax({
        url: urlProject + 'Dashboard/getDataForChartPie',
        type: 'POST',
        dataType: 'json',
        data: { periode: periode },
        success: function (data) {
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

            updateEntrepotChart('bar_entrepot', data.entrepot);

            loadMouvementFlux('flux_chart', data.fluxMouvement)
        }
    });
});
