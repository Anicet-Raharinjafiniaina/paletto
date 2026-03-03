<form class="form-validate-upd-jquery modifier-palette-content">
    <input type="hidden" id="id_upd" name="id_upd" value="<?= $data->id ?>">

    <div class="form-group">
        <label>CODE <span class="text-bold text-danger-600" <?= $display; ?>>*</span></label>
        <input type="text" class="form-control input-xs" placeholder="Code" name="code_upd"
            id="code_upd" required="required" value="<?= $data->code ?>">
        <label id="code_upd-error" class="validation-error-label" for="code_upd"></label>
    </div>

    <div class="form-group">
        <label>ETAT <span class="text-bold text-danger-600" <?= $display; ?>>*</span></label>
        <select class="select select-search obligatoire" data-placeholder="Choisir un statut..." name="palette_statut_id_upd" id="palette_statut_id_upd" required="required" style="width: 100%;" <?= $disabled; ?>>
            <option value=""></option>
            <?php
            if (!empty($arr_palette_statut)):
                foreach ($arr_palette_statut as $row) :
                    if ($row->id == 3) continue;
                    $selected = (($row->id == $data->palette_statut_id) ? 'selected' : ''); ?>
                    <option value="<?= $row->id ?>" <?= $selected ?>>
                        <?= $row->statut ?>
                    </option>
            <?php endforeach;
            endif; ?>
        </select>
        <label id="palette_statut_id_upd-error" class="validation-error-label" for="palette_statut_id_upd"></label>
    </div>

    <div class="form-group">
        <label>CLIENT <span id="client_upd-required" class="text-bold text-danger-600" <?= $display; ?>>*</span></label>
        <!-- <select class="select select-search obligatoire" data-placeholder="Choisir un client..." name="client_upd" id="client_upd" required="required" style="width: 100%;" <?= $disabled; ?>>
            <option value=""></option>
            <?php /*
            if (!empty($arr_client)):
                foreach ($arr_client as $row) :
                    $selected = (($row->code == $data->client_code) ? 'selected' : ''); ?>
                    <option value="<?= $row->code . " - " . $row->nom ?>" <?= $selected ?>>
                        <?= $row->code . " - " . $row->nom ?>
                    </option>
            <?php endforeach;
            endif; */ ?>
        </select> -->
        <input type="text" class="form-control input-xs obligatoire" placeholder="Code ou/et Nom du client" name="client_upd" id="client_upd" required="required" value="<?= $data->client_code . " - " . $data->client_nom ?>" <?= $disabled; ?>>
        <label id="client_upd-error" class="validation-error-label" for="client_upd"></label>
    </div>

    <?php if ($disabled == ""): ?>
        <div class="modal-footer d-flex justify-content-end" id="div-upd-footer">
            <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal">Annuler</button>
            <button type="button" class="btn btn-primary btn-sm  float-right" id="save_upd" onclick="maj()"
                data-loading-text="<i class='icon-spinner10 spinner'></i> Enregistrer" <?= $disabled; ?>>
                Enregistrer</button>
        </div>
    <?php endif; ?>
</form>