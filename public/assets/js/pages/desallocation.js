function view(id, action) {
    var t = $("#l" + id).text();
    loaderContent('main')
    $.ajax({
        url: urlProject + "Article/getArticle",
        type: "POST",
        data: {
            id: id,
            action: action
        },
        success: function (res) {
            stopLoaderContent('main')
            $("#content-article").html(res);
            $("#modal_view_article").modal("show");
            if (action == "voir") {
                $("#div-upd-footer").css("display", "none");
                $("#title").html("Détail de l'article <b>" + t + "</b>");
            }
        }
    });
}

function desallouer(id) {
    Swal.fire({
        title: "Voulez-vous vraiment désallouer la palette ?",
        html: "Aucun client ni aucun article ne sera rattaché à la palette. <b><i>La palette sera libre</i></b>!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#EF5350",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Oui",
        cancelButtonText: "Non",
        showLoaderOnConfirm: true,
        backdrop: true,
        allowOutsideClick: () => !Swal.isLoading(),
        preConfirm: () => {
            loaderContent('main')
            return $.ajax({
                type: "POST",
                url: urlProject + "DesallocationPalette/libererPalette",
                data: { id: id },
                dataType: "json"
            }).then(response => {
                stopLoaderContent('main')
                if (response == 1) {
                    return true;
                } else if (response == 2) {
                    Swal.fire({
                        title: "Désallocation impossible",
                        html: "Impossible de faire la désallocation car la palette avec l'article est déjà affectée à un emplacement. <i>Veuillez d'abord libérer l'emplacement associé à la palette avant de procéder à la désallocation</i>. ",
                        icon: "warning",
                        showConfirmButton: true,
                    });
                } else {
                    throw new Error("Erreur lors de la suppression.");
                }
            }).catch(error => {
                stopLoaderContent('main')
                Swal.showValidationMessage(error.message);
            });
        }
    }).then((result) => {
        if (result.isConfirmed && result.value === true) {
            Swal.fire({
                title: "",
                html: "La palette a été <b>libérée</b>.",
                icon: "success",
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                loadPage(urlProject + "DesallocationPalette", true)
            });
        }
    });

}