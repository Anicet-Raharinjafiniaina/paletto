function view(id, action) {
    var t = $("#l" + id).text();
    $("#content-emplacement").html("");
    loaderContent('main')
    $.ajax({
        url: urlProject + "Emplacement/getDetailEmplacement",
        type: "POST",
        data: {
            id: id,
            action: action
        },
        success: function (res) {
            stopLoaderContent('main')
            $("#content-emplacement").html(res);
            $("#modal_view_emplacement").modal("show");
            if (action == "voir") {
                $("#div-upd-footer").css("display", "none");
                $("#title").html("Détail de l'emplacement <b>" + t + "</b>");
            }
        }
    });
}