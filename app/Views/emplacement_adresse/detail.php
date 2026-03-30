<form class="form-validate-upd-jquery modifier-emplacement-content printable-emplacement-content text-center">
    <input type="hidden" id="id_upd" name="id_upd" value="<?= $data->emplacement_id ?>">

    <!-- QR CODE -->
    <img src="data:image/png;base64,<?= $data->qr_code_image ?>" alt="QR Code" class="img-fluid mb-2" style="max-width:120px;">

    <!-- CODE -->
    <h5 class="fw-bold mb-3"> <?= $data->qr_code_texte ?> </h5>

    <br><br>
    <!-- INFOS -->
    <div style="margin-left:15%">

        <div class="row mb-1 align-items-center">
            <div class="col-5 fw-semibold text-end">Entrepôt : </div>
            <div class="col-7 text-start">
                <?= $data->entrepot_code . " - " . $data->entrepot_nom ?>
            </div>
        </div>

        <div class="row mb-1 align-items-center">
            <div class="col-5 fw-semibold text-end">Allée : </div>
            <div class="col-7 text-start"><?= $data->allee_code ?></div>
        </div>

        <div class="row mb-1 align-items-center">
            <div class="col-5 fw-semibold text-end">Rangée : </div>
            <div class="col-7 text-start"><?= $data->rangee_code ?></div>
        </div>

        <div class="row mb-1 align-items-center">
            <div class="col-5 fw-semibold text-end">Niveau :</div>
            <div class="col-7 text-start"><?= $data->niveau_code ?></div>
        </div>

        <div class="row mb-1 align-items-center">
            <div class="col-5 fw-semibold text-end">Cage / Alvéole:</div>
            <div class="col-7 text-start"><?= $data->cage_code ?></div>
        </div>

        <div class="row mb-1 align-items-center">
            <div class="col-5 fw-semibold text-end">Emplacement :</div>
            <div class="col-7 text-start"><?= $data->emplacement_code ?></div>
        </div>

        <div class="row mb-1 align-items-center">
            <div class="col-5 fw-semibold text-end">Etat : </div>
            <div class="col-7 text-start"><?= $data->statut ?></div>
        </div>

    </div>
    <br><br>
    <div class="modal-footer d-flex justify-content-end" id="div-upd-footer">
        <button type="button" class="btn btn-danger btn-xs btn-sm" data-bs-dismiss="modal">Annuler</button>
        <button type="button" class="btn btn-primary btn-sm  float-right" id="save_upd" onclick="imprimer('printable-emplacement-content')"
            data-loading-text="<i class='icon-spinner10 spinner'></i> Enregistrer">
            Imprimer</button>
    </div>
</form>