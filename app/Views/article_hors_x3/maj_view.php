<form class="form-validate-upd-jquery modifier-article_hors_x3-content">
    <input type="hidden" id="id_upd" name="id_upd" value="<?= $data->id ?>">

    <div class="form-group">
        <label>CODE <span class="text-bold text-danger-600" <?= $display; ?>>*</span></label>
        <input type="text" class="form-control input-xs basicAutoComplete obligatoire" placeholder="Code" name="code_upd"
            id="code_upd" required="required" value="<?= $data->code ?>" <?= $disabled; ?>>
        <label id="code_upd-error" class="validation-error-label" for="code_upd"></label>
    </div>

    <div class="form-group">
        <label>UNITÉ PCB <span class="text-bold text-danger-600">*</span></label>
        <div id="container_inputs_upd" class="border border-1 p-2">
            <?php $i = 1;
            foreach ($data->unite_pcb as $key => $value): ?>
                <div class="input-group mb-2 dynamic-row">
                    <input type="text" id="unite_pcb_upd_<?= $i ?>" name="unite_pcb_upd_<?= $i ?>" class="form-control input-xs obligatoire" value="<?= $value ?>">
                    <div class="buttons-area ms-2">
                        <button type="button" class="btn btn-primary btn-xs btn-add">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
                <label id="unite_pcb_upd_<?= $i ?>-error" class="validation-error-label" for="unite_pcb_upd_<?= $i ?>"></label>
            <?php $i++;
            endforeach; ?>
        </div>
    </div>

    <div class="form-group">
        <label>PALETTISATION <span class="text-bold text-danger-600" <?= $display; ?>>*</span></label>
        <input type="text" class="form-control input-xs obligatoire" placeholder="Palettisation" name="palettisation_upd"
            id="palettisation_upd" required="required" value="<?= $data->palettisation ?>">
        <label id="palettisation_upd-error" class="validation-error-label" for="palettisation_upd"></label>
    </div>

    <div class="form-group">
        <label>UNITÉ de STOCKAGE <span class="text-bold text-danger-600" <?= $display; ?>>*</span></label>
        <input type="text" class="form-control input-xs obligatoire" placeholder="Unité de stockage" name="unite_stockage_upd"
            id="unite_stockage_upd" required="required" value="<?= $data->unite_stockage ?>">
        <label id="unite_stockage_upd-error" class="validation-error-label" for="unite_stockage_upd"></label>
    </div>

    <div class="form-group">
        <label>DESCRIPTION </label>
        <input type="text" class="form-control input-xs" placeholder="Description" name="description_upd"
            id="description_upd" required="required" value="<?= $data->description ?>">
        <label id="description_upd-error" class="validation-error-label" for="description_upd"></label>
    </div>

    <?php if ($disabled == ""): ?>
        <div class="modal-footer d-flex justify-content-end" id="div-upd-footer">
            <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal">Annuler</button>
            <button type="button" class="btn btn-primary btn-sm float-right" id="save_upd" onclick="maj()"
                data-loading-text="<i class='icon-spinner10 spinner'></i> Enregistrer" <?= $disabled; ?>>
                Enregistrer</button>
        </div>
    <?php endif; ?>
</form>