<form class="form-validate-upd-jquery modifier-allee-content">
    <input type="hidden" id="id_upd" name="id_upd" value="<?= $data->id ?>">
    <div class="form-group">
        <label>ENTREPÔT <span class="text-bold text-danger-600" <?= $display; ?>>*</span></label>
        <select class="select select-search obligatoire" data-placeholder="Choisir un entrepôt..." name="entrepot_id_upd" id="entrepot_id_upd" required="required" style="width: 100%;" <?= $disabled; ?>>
            <option value=""></option>
            <?php
            foreach ($arr_data_entrepot as $row_entrepot) :
                $selected_entrepot = (($row_entrepot->id == $data->entrepot_id) ? 'selected' : '');
            ?>
                <option value="<?= $row_entrepot->id ?>" <?= $selected_entrepot ?>>
                    <?= $row_entrepot->code . " - " . $row_entrepot->nom ?>
                </option>
            <?php endforeach ?>
        </select>
        <label id="entrepot_id_upd-error" class="validation-error-label" for="entrepot_id_upd"></label>

        <div class="form-group">
            <label>CODE <span class="text-bold text-danger-600" <?= $display; ?>>*</span></label>
            <input type="text" class="form-control input-xs" placeholder="Code" name="code_upd"
                id="code_upd" required="required" value="<?= $data->code ?>">
            <label id="code_upd-error" class="validation-error-label" for="code_upd"></label>
        </div>

        <?php if ($disabled == ""): ?>
            <div class="modal-footer d-flex justify-content-end" id="div-upd-footer">
                <button type="button" class="btn btn-primary btn-sm  float-right" id="save_upd" onclick="maj()"
                    data-loading-text="<i class='icon-spinner10 spinner'></i> Enregistrer" <?= $disabled; ?>>
                    Enregistrer</button>
            </div>
        <?php endif; ?>
</form>