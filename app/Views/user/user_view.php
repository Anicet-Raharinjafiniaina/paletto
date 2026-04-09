<?php if ($request_ajax == 0) : ?>
    <?= $this->extend('layout/main') ?>
    <?= $this->section('content') ?>
<?php endif; ?>
<?php if (isset($request_ajax) && $request_ajax): ?>
    <div id="ajax-title" data-title="<?= esc($titre) ?>"></div>
<?php endif; ?>
<?php
$acces_btn = "";
$style_btn = ($acces_btn == "write" || $acces_btn == "") ? "" : 'style = "display:none;"'; ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-12 d-flex justify-content-end">
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" id="btn-add-user" <?= $style_btn; ?>>
                            <i class="fas fa-plus position-left"></i> Ajouter
                        </button>
                    </div>
                </div>

            </div>
            <div class="card-body">
                <table id="datatable-buttons" class="datatable table table-bordered dt-responsive nowrap w-100">
                    <thead class="text-center">
                        <tr>
                            <th>Action</th>
                            <th>Statut</th>
                            <th>Login</th>
                            <th>Nom</th>
                            <th>Email</th>
                            <th>Profil</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        if (!empty($arr_data_user)):
                            foreach ($arr_data_user as $key => $value) : ?>
                                <tr id="<?= $value->id ?>" class="text-center">
                                    <td class="text-center cursor-pointer td_no_border">
                                        <button href="#" type="button" style="margin-right:0.3em;background:transparent" class="btn btn-icon btn-rounded btn-xs" data-toggle="modal" data-target="#modal_view_user" data-popup="tooltip" title=" Mettre à jour" data-placement="bottom" onclick="view(<?= $value->id ?>,'upd')" <?= $style_btn; ?>><img src="<?= base_url('assets/images/modifier.png') ?>" alt="" style="width: 20px; height: 20px;"></button>
                                        <button type="button" style="margin-right:0.3em;background:transparent" class="btn btn-icon btn-rounded btn-xs" id="del_user" data-popup="tooltip" title="Supprimer" data-placement="bottom" onclick="deleteItem(<?= $value->id ?>)" <?= $style_btn; ?>><img src="<?= base_url('assets/images/supprimer.png') ?>" alt="" style="width: 20px; height: 20px;"></button>
                                    </td>
                                    <td><?= ($value->actif == 1) ? '<span class="badge bg-primary rounded-pill p-2 px-3">ACTIF</span>' : '<span class="badge bg-secondary rounded-pill p-2 px-3">INACTIF</span>' ?> </td>
                                    <td><?= $value->login ?></td>
                                    <td><?= $value->nom ?></td>
                                    <td><?= $value->mail ?></td>
                                    <td><?= $value->profil ?></td>
                                </tr>
                        <?php endforeach;
                        endif;   ?>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- end cardaa -->
    </div> <!-- end col -->
</div>

<!-- Ajout user -->
<div id="modal_ajout_user" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header justify-content-center position-relative">
                <h5 class="modal-title" id="myModalLabel">Ajouter un utilisateur</h5>
                <button type="button" class="btn-close position-absolute end-0 me-3" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form class="form-validate-jquery add-user-content">
                    <div class="form-group">
                        <label>Nom et prénom <span class="fw-bold text-danger">*</span></label>
                        <input type="text" class="form-control input-xs basicAutoComplete obligatoire" placeholder="Nom" name="nom" id="nom" required="required">
                        <label id="nom-error" class="validation-error-label" for="nom"></label>
                    </div>

                    <div class="form-group">
                        <label>Login <span class="fw-bold text-danger">*</span></label>
                        <input type="text" class="form-control input-xs obligatoire" placeholder="Login" name="login" id="login" required="required" disabled>
                        <label id="login-error" class="validation-error-label" for="login"></label>
                    </div>

                    <div class="form-group">
                        <label>Fonction <span class="fw-bold text-danger">*</span></label>
                        <input type="text" class="form-control input-xs obligatoire" placeholder="Fonction" name="fonction" id="fonction" disabled>
                        <label id="fonction-error" class="validation-error-label" for="fonction"></label>
                    </div>

                    <div class="form-group">
                        <label>Email </label>
                        <input type="text" class="form-control input-xs" placeholder="Email" name="mail" id="mail" disabled>
                        <label id="mail-error" class="validation-error-label" for="mail"></label>
                    </div>

                    <div class="form-group">
                        <label>Profil <span class="fw-bold text-danger">*</span></label>
                        <select class="select select-search obligatoire" data-placeholder="Choisir un profil..." name="profil" id="profil" style="width: 100%;">
                            <?php if (!empty($arr_profil)): ?>
                                <?php foreach ($arr_profil as $row_profil): ?>
                                    <option value="<?= $row_profil->id ?>">
                                        <?= $row_profil->text ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <label id="profil-error" class="validation-error-label" for="profil"></label>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger btn-xs btn-sm" data-bs-dismiss="modal">Annuler</button>
                        <button type="button" class="btn btn-primary btn-xs btn-sm" id="save" onclick="insert()">Enregistrer</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
<!-- /Ajout user -->

<!-- Visualiser/Modifier user -->
<div id="modal_view_user" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header justify-content-center position-relative">
                <h5 class="modal-title" id="myModalLabel">Modification d'un utilisateur</h5>
                <button type="button" class="btn-close position-absolute end-0 me-3" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="content-user"></div>
        </div>
    </div>
</div>
<!-- /Visualiser/Modifier user -->

<?php if ($request_ajax == 0) : ?>
    <?= $this->endSection() ?>
    <?= $this->section('script') ?>
<?php endif; ?>
<script type="text/javascript" src="<?= base_url('assets/libs/autocomplete/bootstrap-autocomplete.min.js'); ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/libs/autocomplete/bootstrap3-typeahead.min.js'); ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/js/pages/user.js'); ?>"></script>
<?php if ($request_ajax == 0) : ?>
    <?= $this->endSection() ?>
<?php endif; ?>