$(document).ready(function () {
    initDataTableServerSide({
        selector: '#tbl_historique_mouvement',
        ajaxUrl: urlProject + "HistoriqueMouvement/historiqueMouvement",
        columns: [
            {
                data: 'type',
                render: function (data, type, row) {

                    // 👉 Pour la recherche et le tri → retourner texte brut
                    if (type === 'filter' || type === 'sort') {
                        return data;
                    }
                    // 👉 Pour l'affichage → HTML
                    if (data === 'Entrée') {
                        return '<span class="badge rounded-pill p-2 bg-success">Entrée</span>';
                    }
                    if (data === 'Sortie') {
                        return '<span class="badge rounded-pill p-2 bg-danger">Sortie</span>';
                    }
                    if (data === 'Transfert') {
                        return '<span class="badge rounded-pill p-2 bg-primary">Transfert</span>';
                    }
                    return data;
                }
            },
            {
                data: 'date_mouvement',
                render: function (data) {
                    if (!data) return '';
                    const d = new Date(data.replace(' ', 'T'));
                    return `${String(d.getDate()).padStart(2, '0')}/${String(d.getMonth() + 1).padStart(2, '0')}/${d.getFullYear()} à ${String(d.getHours()).padStart(2, '0')}h${String(d.getMinutes()).padStart(2, '0')}mn${String(d.getSeconds()).padStart(2, '0')}s`;
                }
            },
            'emplacement',
            'palette_article',
            'client_code',
            'client_nom',
            'code_entrepot',
            'nom_entrepot'
        ],
        actions: true
    });

    initDataTableServerSide({
        selector: '#tbl_historique_action',
        ajaxUrl: urlProject + "Historique/historiqueAction",
        columns: [
            'date_creation',
            'libelle',
            'login',
            'nom'
        ],
        actions: false
    });
});

function view(id, action) {
    var t = $("#l" + id).text();
    $("#content-mouvement").html("");
    loaderContent('main')
    $.ajax({
        url: urlProject + "HistoriqueMouvement/getDetailMouvement",
        type: "POST",
        data: {
            id: id,
            action: action
        },
        success: function (res) {
            stopLoaderContent('main')
            $("#content-mouvement").html(res);
            $("#modal_view_mouvement").modal("show");
            if (action == "voir") {
                $("#div-upd-footer").css("display", "none");
                $("#title").html("Détail d'un mouvement <b>" + t + "</b>");
            }
        }
    });
}
