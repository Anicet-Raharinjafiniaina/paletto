
// /** datatable server side */
function initDataTableServerSide(tableConfig) {
    //  let dtColumns = tableConfig.columns.map(col => ({ data: col }));

    let dtColumns = tableConfig.columns.map(col => {

        // Si c'est déjà un objet (avec render etc.)
        if (typeof col === 'object') {
            return col;
        }

        // Sinon c'est un string
        return { data: col };
    })

    if (tableConfig.actions) {
        dtColumns.unshift({   //  push() : dernier élément, unshift() : premier élément
            data: 'id',
            orderable: false,
            searchable: false,
            className: 'text-center cursor-pointer',
            render: function (data, type, row) {
                let html = '';

                if (Array.isArray(row.actions)) {
                    if (row.actions.includes('voir')) {
                        html += `<button type="button" class="btn btn-xs" onclick="view(${data},'voir')" data-bs-toggle="tooltip" title="Voir détails"><i class="fas fa-eye"></i></button>`;
                    }
                    if (row.actions.includes('modifier')) {
                        html += `<button type="button" class="btn btn-xs" onclick="view(${data},'upd')" data-bs-toggle="tooltip" title="Modifier"><img src="${urlProject}assets/images/modifier.png" style="width:20px;"></button>`;
                    }
                    if (row.actions.includes('supprimer')) {
                        html += `<button type="button" class="btn btn-xs" onclick="deleteItem(${data})" data-bs-toggle="tooltip" title="Supprimer"><img src="${urlProject}assets/images/supprimer.png" style="width:20px;"></button>`;
                    }
                }

                return html;
            }
        });
    }

    $(tableConfig.selector).DataTable({
        processing: true,
        serverSide: true,
        ordering: false, // désactiver le tri sur la table ↑↓
        ajax: {
            url: tableConfig.ajaxUrl,
            type: 'POST'
        },
        columns: dtColumns,
        // language: { url: "//cdn.datatables.net/plug-ins/1.13.8/i18n/fr-FR.json" }
    });
}