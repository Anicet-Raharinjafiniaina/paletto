<form class="form-validate-upd-jquery modifier-user-content">
    <input type="hidden" id="id_upd" name="id_upd" value="<?= $data->id ?>">

    <div class="form-group">
        <label>Nom et prénom <span class="text-bold text-danger-600" <?= $display; ?>>*</span></label>
        <input type="text" class="form-control input-xs basicAutoComplete obligatoire" placeholder="Nom" name="nom_upd"
            id="nom_upd" required="required" value="<?= $data->nom ?>" <?= $disabled; ?>>
        <label id="nom_upd-error" class="validation-error-label" for="nom_upd"></label>
    </div>

    <div class="form-group">
        <label>Login <span class="text-bold text-danger-600" <?= $display; ?>>*</span></label>
        <input type="text" class="form-control input-xs obligatoire" placeholder="Login" name="login_upd"
            id="login_upd" required="required" value="<?= $data->login ?>" disabled>
        <label id="login_upd-error" class="validation-error-label" for="login_upd"></label>
    </div>

    <div class="form-group">
        <label>Fonction <span class="text-bold text-danger-600" <?= $display; ?>>*</span></label>
        <input type="text" class="form-control input-xs obligatoire" placeholder="Fonction" name="fonction_upd"
            id="fonction_upd" required="required" value="<?= $data->fonction ?>" disabled>
        <label id="fonction_upd-error" class="validation-error-label" for="fonction_upd"></label>
    </div>

    <div class="form-group">
        <label>Email </label>
        <input type="text" class="form-control input-xs" placeholder="Email" name="mail_upd"
            id="mail_upd" required="required" value="<?= $data->mail ?>" disabled>
        <label id="mail_upd-error" class="validation-error-label" for="mail_upd"></label>
    </div>

    <div class="form-group">
        <label>Profil <span class="text-bold text-danger-600" <?= $display; ?>>*</span></label>
        <select class="select select-search obligatoire" data-placeholder="Choisir un profil..." name="profil_id_upd" id="profil_id_upd" required="required" style="width: 100%;" <?= $disabled; ?>>
            <option value=""></option>
            <?php
            foreach ($arr_profil as $row_profil) :
                $selected_profil = (($row_profil->id == $data->profil_id) ? 'selected' : '');
            ?>
                <option value="<?= $row_profil->id ?>" <?= $selected_profil ?>>
                    <?= $row_profil->text ?>
                </option>
            <?php endforeach ?>
        </select>
        <label id="profil_id_upd-error" class="validation-error-label" for="profil_id_upd"></label>
    </div>

    <?php $checked = ($data->actif == 1) ? 'checked="checked"' : ""; ?>
    <div class="form-group">
        <div class="d-flex align-items-center gap-2">
            <span class="mb-n2"><label for="actif">Actif</label></span>
            <div class="form-check form-switch switch-label">
                <input type="checkbox" class="form-check-input" name="actif" id="actif" value="<?= $data->actif; ?>"
                    <?= $checked; ?> <?= $disabled; ?>>
            </div>
        </div>
    </div>

    <?php if ($disabled == ""): ?>
        <div class="modal-footer d-flex justify-content-end" id="div-upd-footer">
            <button type="button" class="btn btn-danger btn-xs btn-sm" data-bs-dismiss="modal">Annuler</button>
            <button type="button" class="btn btn-primary btn-sm  float-right" id="save_upd" onclick="maj()"
                data-loading-text="<i class='icon-spinner10 spinner'></i> Enregistrer" <?= $disabled; ?>>
                Enregistrer</button>
        </div>
    <?php endif; ?>
</form>