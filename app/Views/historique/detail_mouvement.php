<form class="form-validate-upd-jquery modifier-mouvement-content text-center">
    <input type="hidden" id="id_upd" name="id_upd" value="<?= $data->id ?>">

    <!-- INFOS -->
    <div class="mx-auto">
        <div class="row mb-1 align-items-center">
            <div class="col-5 fw-semibold text-end">Emplacement : </div>
            <div class="col-7 text-start">
                <?= $data->emplacement ?>
            </div>
        </div>

        <div class="row mb-1 align-items-center">
            <div class="col-5 fw-semibold text-end">Palette : </div>
            <div class="col-7 text-start">
                <?= $data->palette_code ?>
            </div>
        </div>

        <br>

        <div class="row mb-1 align-items-center">
            <div class="col-5 fw-semibold text-end">Code du client : </div>
            <div class="col-7 text-start">
                <?= $data->client_code ?>
            </div>
        </div>

        <div class="row mb-1 align-items-center">
            <div class="col-5 fw-semibold text-end">Nom du client : </div>
            <div class="col-7 text-start">
                <?= $data->client_nom ?>
            </div>
        </div>

        <br>

        <div class="row mb-1 align-items-center">
            <div class="col-5 fw-semibold text-end">Code de l'article : </div>
            <div class="col-7 text-start"><?= $data->article_code ?></div>
        </div>

        <div class="row mb-1 align-items-center">
            <div class="col-5 fw-semibold text-end">Nom de l'article : </div>
            <div class="col-7 text-start"><?= $data->article_nom ?></div>
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

        <div class="row align-items-center">
            <div class="col-5 fw-semibold text-end">Unité de stockage : </div>
            <div class="col-7 text-start"><?= $data->unite_stockage ?></div>
        </div>
        <br>

        <div class="row align-items-center">
            <div class="col-5 fw-semibold text-end">Type de mouvement : </div>
            <div class="col-7 text-start"><?= $data->mouvement_type ?></div>
        </div>

        <div class="row align-items-center">
            <div class="col-5 fw-semibold text-end">Date : </div>
            <div class="col-7 text-start"><?= date('d/m/Y H:i:s', strtotime($data->date_mouvement)) ?></div>
        </div>

        <div class="row align-items-center">
            <div class="col-5 fw-semibold text-end">Auteur : </div>
            <div class="col-7 text-start"><?= $data->auteur ?></div>
        </div>
        <br><br>
    </div>

    <div class="modal-footer align-items-end">
        <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal">Annuler</button>
    </div>
</form>