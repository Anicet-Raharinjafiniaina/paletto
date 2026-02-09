<form class="form-validate-upd-jquery modifier-article-content text-center">
    <input type="hidden" id="id_upd" name="id_upd" value="<?= $data->id ?>">

    <!-- QR CODE -->
    <img src="data:image/png;base64,<?= $data->qr_code_image ?>" alt="QR Code" class="img-fluid mb-2" style="max-width:120px;">

    <!-- CODE -->
    <h6 class="fw-bold mb-3">PAL - <?= $data->palette_code ?> </h6>

    <!-- INFOS -->
    <div class="mx-auto">

        <div class="row mb-1 align-items-center">
            <div class="col-5 fw-semibold text-end">Client : </div>
            <div class="col-7 text-start">
                <?= $data->client_code . " - " . $data->client_nom ?>
            </div>
        </div>

        <div class="row mb-1 align-items-center">
            <div class="col-5 fw-semibold text-end">Article : </div>
            <div class="col-7 text-start"><?= $data->nom ?></div>
        </div>

        <div class="row mb-1 align-items-center">
            <div class="col-5 fw-semibold text-end">Code : </div>
            <div class="col-7 text-start"><?= $data->code ?></div>
        </div>

        <div class="row mb-1 align-items-center">
            <div class="col-5 fw-semibold text-end">Quantité :</div>
            <div class="col-7 text-start"><?= $data->quantite ?></div>
        </div>

        <div class="row mb-1 align-items-center">
            <div class="col-5 fw-semibold text-end">Lot :</div>
            <div class="col-7 text-start"><?= $data->lot ?></div>
        </div>

        <div class="row mb-1 align-items-center">
            <div class="col-5 fw-semibold text-end">DLUO : </div>
            <div class="col-7 text-start"><?= date('d/m/Y', strtotime($data->dluo)) ?></div>
        </div>

        <div class="row mb-1 align-items-center">
            <div class="col-5 fw-semibold text-end">PCB : </div>
            <div class="col-7 text-start"><?= $data->unite_pcb ?></div>
        </div>

        <div class="row align-items-center">
            <div class="col-5 fw-semibold text-end">Palettisation : </div>
            <div class="col-7 text-start"><?= $data->palettisation ?></div>
        </div>

    </div>
    <br><br>
    <div class="modal-footer d-flex justify-content-end" id="div-upd-footer">
        <button type="button" class="btn btn-primary btn-sm  float-right" id="save_upd" onclick="imprimer()"
            data-loading-text="<i class='icon-spinner10 spinner'></i> Enregistrer">
            Imprimer</button>
    </div>
</form>