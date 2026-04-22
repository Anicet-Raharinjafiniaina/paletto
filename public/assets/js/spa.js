/*** Pour gérer le SPA */
$(document).ready(function () {
    function loadPage(url, addToHistory = true) { // fonction pour charger une page via AJAX
        $('#ajax-title').text(""); // réinialiser le titre via AJAX
        $('#titre_page').text(""); // réinialiser le titre via controleur
        $('#content-page').fadeOut(100, function () { // le contenu de la page disparait (réinitalisation)
            $('#content-page').html(loaderContentPage()).fadeIn(100);
        });
        $.ajax({
            url: url,
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function (data) {
                $('#content-page').fadeOut(100, function () { // charger le contenu de la page via la variabmle data
                    $('.daterangepicker.ltr.show-ranges.opensright').hide(); // masquer le résidu daterange
                    $('#content-page').html(data).fadeIn(100, function () {
                        // Ici le fadeIn est terminé, le DOM est prêt et visible
                        $('#content-page').find('script').each(function () { // exécuter les scripts (code JS dans les autres pages) inclus dans la réponse AJAX
                            $.globalEval(this.text || this.textContent || this.innerHTML || '');
                        });

                        setTimeout(function () {
                            initGraphs()
                            feather.replace(); // réinitialisation des icons Feather
                            activateMenuByUrl();// activer le menu après un court délai pour s'assurer que le DOM est prêt (c'est pour marquer le menu actif correctement)
                            document.body.click(); // fermer le menu sidebar sur mobile après le chargement de la page
                            /* réinitialiser les datatables */
                            let table = $('.datatable').DataTable();
                            table.destroy();
                            $('.datatable').DataTable({
                                responsive: true,
                                autoWidth: false
                            });
                            /* /réinitialiser les datatables */
                            $('.daterangepicker.ltr.show-ranges.opensright').hide(); // masquer le résidu daterange
                        }, 50);
                    });

                    // Mettre à jour le titre si présent
                    var newTitle = $('#ajax-title').data('title');
                    if (newTitle) {
                        $('.page-title-box h4').text(newTitle);
                    }
                });

                //1️⃣ Supprimer les anciens calendriers Flatpickr
                document.querySelectorAll('.flatpickr-calendar').forEach(cal => cal.remove());
                if (addToHistory) {
                    history.pushState({
                        url: url
                    }, '', url);
                }
            },
            error: function () {
                $('#content-page').html('<div class="alert alert-danger text-center mt-3">Erreur de chargement</div>');
            }
        });
    }

    // expose globalement
    window.loadPage = loadPage;

    $(document).on('click', 'a.nav-link, a.menu-link', function (e) {
        e.preventDefault();
        const url = $(this).attr('href');
        if (url && !url.startsWith('#')) {
            loadPage(url);
        }
    });

    window.onpopstate = function (event) {
        if (event.state && event.state.url) {
            loadPage(event.state.url, false);
        }
    };
});

function loaderContentPage() { // contenu de chargement pendant le chargement AJAX
    return `
    <div style="
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center; /* centre verticalement */
        align-items: center;     /* centre horizontalement */
        height: 60vh;           /* prend toute la hauteur de l'écran */
        width: 100%;             /* prend toute la largeur */
        text-align: center;
        background-color: transparent;
    ">
        <div style="
            display: inline-block;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            border: 6px solid #A9A9A9 ;
            border-color: #A9A9A9  transparent #A9A9A9  transparent;
            animation: spin 1.2s linear infinite;
            margin-bottom: 12px;
            background-color: transparent;
        "></div>
        <div style="font-size:16px; color:#A9A9A9 ;">Chargement en cours...</div>
    </div>
    <style>
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
    `;
}
/*** /Pour gérer le SPA */


function activateMenuByUrl() { // Fonction pour activer le menu en fonction de l'URL actuelle
    let currentUrl = window.location.origin + window.location.pathname;
    // ou window.location.href si tu veux inclure les paramètres GET

    // 1. Supprimer toutes les classes d’activation
    $('#side-menu a').removeClass('active');
    $('#side-menu li').removeClass('mm-active');
    $('#side-menu ul').removeClass('mm-show');

    // 2. Trouver le lien correspondant
    let $activeLink = $('#side-menu a[href="' + currentUrl + '"]');

    if ($activeLink.length) {

        // Ajouter la classe active sur le lien
        $activeLink.addClass('active');

        // 3. Activer les <li> parents
        $activeLink.closest('li').addClass('mm-active');

        // 4. Ouvrir sa section parente
        $activeLink.closest('ul').addClass('mm-show');

        // 5. Activer aussi le parent supérieur (le menu principal)
        $activeLink.closest('ul').closest('li').addClass('mm-active');
    }
}

// Objet global pour stocker les charts
const charts = {};

function initGraphs() {
    const graphs = [
        { id: 'pie_emplacement', data: [0, 0], labels: ['Libre', 'Occupé'] },
        { id: 'pie_palette', data: [0, 0, 0], labels: ['Libre', 'Attribuée', 'Occupée'] },
        { id: 'pie_entrepot', data: [0, 0, 0], labels: ['ENT1', 'ENT2', 'ENT3'] },
        { id: 'pie_mouvement', data: [0, 0, 0], labels: ['Entrée', 'Transfert', 'Sortie'] }
    ];

    graphs.forEach(g => {
        const canvas = document.getElementById(g.id);
        if (!canvas) return;

        // Si un chart existait déjà, le détruire
        if (charts[g.id]) {
            charts[g.id].destroy();
        }

        const ctx = canvas.getContext('2d');

        // Créer un nouveau chart et le stocker
        charts[g.id] = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: g.labels,
                datasets: [{
                    data: g.data,
                    backgroundColor: ['#10B981', '#2563EB', '#EF4444'],
                    borderWidth: 1
                }]
            },
            plugins: [ChartDataLabels],
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' },
                    datalabels: {
                        color: '#fff',
                        font: { weight: 'bold', size: 14 },
                        formatter: function (value, context) {
                            let data = context.chart.data.datasets[0].data;
                            let total = data.reduce((a, b) => a + b, 0);
                            return total ? (value / total * 100).toFixed(1) + "%" : "0%";
                        }
                    }
                }
            }
        });
    });
}