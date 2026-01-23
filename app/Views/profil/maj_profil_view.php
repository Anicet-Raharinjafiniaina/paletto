    <style>
        .btn {
            margin-top: 10px;
        }
    </style>

    <form class="form-validate-upd-jquery modifier-profil-content">
        <input type="hidden" id="id_upd" name="id_upd" value="<?= $profil->id ?>">
        <div class="form-group">
            <label>Profil <span class="text-bold text-danger-600" <?= $display; ?>>*</span></label>
            <input type="text" class="form-control input-xs obligatoire" placeholder="Login" name="profil_upd"
                id="profil_upd" required="required" value="<?= $profil->libelle ?>" <?= $disabled; ?>>
            <label id="profil_upd-error" class="validation-error-label" for="profil_upd"></label>
        </div>

        <div class="panel panel-flat">
            <div class="panel-heading">
                <h6 class="panel-title">Liste des pages &agrave; associer au profil <span class="text-bold text-danger-600">*</span></h6>
            </div>
            <br><br>
            <div class="panel-body">
                <select multiple="multiple" class="form-control listbox obligatoire" id="page_id_upd" name="page_id_upd[]" <?= $disabled; ?>>
                    <?php
                    foreach ($arr_page as $row_page) :
                        $selected_profil = (in_array($row_page->id, $arr_list_page) ? 'selected' : ''); ?>
                        <option value="<?= $row_page->id ?>" <?= $selected_profil ?>>
                            <?= $row_page->libelle ?>
                        </option>
                    <?php endforeach ?>
                </select>
                <label id="page_id_upds=" validation-error-label" for="page_id_upd"></label>
            </div>
        </div>

        <?php $checked = ($profil->actif == 1) ? 'checked="checked"' : ""; ?>
        <div class="form-group">
            <div class="form-check form-switch switch-label">
                <input type="checkbox" class="form-check-input" name="actif" id="actif" value="<?= $profil->actif; ?>"
                    <?= $checked; ?> <?= $disabled; ?>>
            </div>
        </div>

        <?php if ($disabled == ""): ?>
            <div class="modal-footer d-flex justify-content-end" id="div-upd-footer">
                <button type="button" class="btn btn-success btn-sm  float-right" style="background-color:#21a89f;" id="save_upd" onclick="maj()"
                    data-loading-text="<i class='icon-spinner10 spinner'></i> Enregistrer" <?= $disabled; ?>>
                    Enregistrer</button>
            </div>
        <?php endif; ?>
    </form>

    <script type="text/javascript">
        $(function() {
            initializeDuallistBox("page_id_upd")
            /** pour dualListBox */
            $('.icon-last').removeClass('icon-last').addClass('fas fa-angle-double-right');
            $('.icon-first').removeClass('icon-first').addClass('fas fa-angle-double-left');
            /** /pour dualListBox */
            $('input[name="actif"]').click(function() {
                if ($(this).is(":checked")) {
                    $(this).val(1);
                } else if ($(this).is(":not(:checked)")) {
                    $(this).val(0);
                }
            });
        });
    </script>