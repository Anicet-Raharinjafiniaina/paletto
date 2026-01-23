<?php if ($request_ajax == 0) : ?>
    <?= $this->extend('layout/main') ?>
    <?= $this->section('link') ?>
<?php endif; ?>
<!-- duallistbox Css -->
<link href='<?= base_url("assets/libs/duallistbox/duallistbox.min.css") ?>' id="bootstrap-style" rel="stylesheet" type="text/css" />
<?php if ($request_ajax == 0) : ?>
    <?= $this->endSection() ?>
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
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" id="btn-add-profil" <?= $style_btn; ?>>
                            <i class="fas fa-plus position-left"></i> Ajouter
                        </button>
                    </div>
                </div>

            </div>
            <div class="card-body">
                <table id="tbl_profil" class="datatable table table-bordered dt-responsive nowrap w-100">
                    <thead class="text-center">
                        <tr>
                            <th>Action</th>
                            <th>Libellé</th>
                            <th>Accès</th>
                            <th>Statut</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        if (!empty($arr_profil_page)):
                            foreach ($arr_profil_page as $key => $value) : ?>
                                <tr id="<?= $value->id ?>" class="text-center">
                                    <td class="text-center cursor-pointer td_no_border">
                                        <!-- <button type="button" style="margin-right:0.3em;background:transparent" class="btn btn-icon btn-rounded btn-xs" data-toggle="modal" data-target="#modal_view_user" data-target="Visualiser" data-popup="tooltip" title="Visualiser" data-placement="bottom" onclick="view(<?= $value->id ?>,'voir')"><i class="fas fa-eye"></i></a></button> -->
                                        <button href="#" type="button" style="margin-right:0.3em;background:transparent" class="btn btn-icon btn-rounded btn-xs" data-toggle="modal" data-target="#modal_view_user" data-popup="tooltip" title=" Mettre à jour" data-placement="bottom" onclick="view(<?= $value->id ?>,'upd')" <?= $style_btn; ?>><img src="<?= base_url('assets/images/modifier.png') ?>" alt="" style="width: 20px; height: 20px;"></button>
                                        <button type="button" style="margin-right:0.3em;background:transparent" class="btn btn-icon btn-rounded btn-xs" id="del_user" data-popup="tooltip" title="Supprimer" data-placement="bottom" onclick="deleteItem(<?= $value->id ?>)" <?= $style_btn; ?>><img src="<?= base_url('assets/images/supprimer.png') ?>" alt="" style="width: 20px; height: 20px;"></button>
                                    </td>
                                    <td><?= $value->libelle ?></td>
                                    <td><?= $value->pages ?></td>
                                    <td><?= ($value->actif == 1) ? '<span class="badge bg-primary rounded-pill p-2 px-3">ACTIF</span>' : '<span class="badge bg-secondary rounded-pill p-2 px-3">INACTIF</span>' ?> </td>
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

<!-- Ajout profil -->
<div id="modal_ajout_profil" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">Ajouter un profil</h5>
                <button type="button" class="btn-close float-right" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form class="form-validate-jquery add-profil-content">
                    <div class="form-group">
                        <label>Profil <span class="text-bold text-danger-600">*</span></label>
                        <input type="text" class="form-control input-xs obligatoire" placeholder="Libellé du profil" name="profil" id="profil" required="required">
                        <label id="profil-error" class="validation-error-label" for="profil"></label>
                    </div>

                    <div class="panel panel-flat">
                        <div class="panel-heading">
                            <h6 class="panel-title">Liste des pages &agrave; associer au profil <span class="text-bold text-danger-600">*</span></h6>
                        </div>
                        <br><br>
                        <div class="panel-body">
                            <select multiple="multiple" class="form-control listbox obligatoire" id="page_id" name="page_id[]">
                                <?php foreach ($arr_page as $key_p => $val_p) : ?>
                                    <option value="<?= $val_p->id ?>">
                                        <?= $val_p->libelle ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label id="page_id-error" class="validation-error-label" for="page_id"></label>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-success btn-xs" style="background-color:#21a89f;" id="save" onclick="insert()"
                            data-loading-text="<i class='icon-spinner10 spinner'></i> Enregistrer"> Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Ajout profil -->

<!-- Visualiser/Modifier profil -->
<div id="modal_view_profil" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-center" id="title">Modification d'un utilisateur</h5>
                <button type="button" class="btn-close float-right" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="content-profil"></div>
        </div>
    </div>
</div>
<!-- /Visualiser/Modifier profil -->

<?php if ($request_ajax == 0) : ?>
    <?= $this->endSection() ?>
    <?= $this->section('script') ?>
<?php endif; ?>

<script type="text/javascript" src='<?= base_url("assets/libs/duallistbox/duallistbox.min.js") ?>'></script>
<script type="text/javascript" src='<?= base_url("assets/js/pages/profil.js") ?>'></script>
<?php if ($request_ajax == 0) : ?>
    <?= $this->endSection() ?>
<?php endif; ?>