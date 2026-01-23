$(function () {
    initAutoCloseSidebar(); // pour fermer le menu en le cliquant sur lui même autre part (mobile)
    $(".datatable").DataTable()
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

        if (!value || String(value).trim() === "") {
            // errorLabel.html('<i class= "fa fa-exclamation-circle"> <span class="text-danger font-italic">Ce champ ' + name + ' est obligatoire.</span>');
            errorLabel.html('<i class= "fa fa-exclamation-circle"> <span class="text-danger font-italic">Ce champ est obligatoire.</span>');
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