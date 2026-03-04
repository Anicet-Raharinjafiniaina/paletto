<form class="form-validate-upd-jquery modifier-article_hors_x3-content">
    <input type="hidden" id="id_upd" name="id_upd" value="<?= $data->id ?>">

    <div class="form-group">
        <label>CODE <span class="text-bold text-danger-600" <?= $display; ?>>*</span></label>
        <input type="text" class="form-control input-xs basicAutoComplete obligatoire" placeholder="Code" name="code_upd"
            id="code_upd" required="required" value="<?= $data->code ?>" <?= $disabled; ?>>
        <label id="code_upd-error" class="validation-error-label" for="code_upd"></label>
    </div>

    <div class="form-group">
        <label>NOM <span class="text-bold text-danger-600" <?= $display; ?>>*</span></label>
        <input type="text" class="form-control input-xs obligatoire" placeholder="Nom" name="nom_upd"
            id="nom_upd" required="required" value="<?= $data->nom ?>">
        <label id="nom_upd-error" class="validation-error-label" for="nom_upd"></label>
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