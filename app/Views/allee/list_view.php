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

<?= $this->include('liste_emplacement/menu'); ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6 d-flex justify-content-start">
                        <h5>Liste des allées</h5>
                    </div>
                    <div class="col-md-6 d-flex justify-content-end">
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" id="btn-add-allee" <?= $style_btn; ?>>
                            <i class="fas fa-plus position-left"></i> Ajouter
                        </button>
                    </div>
                </div>

            </div>
            <div class="card-body">
                <table id="table_allee" class="datatable table table-bordered dt-responsive nowrap w-100">
                    <thead class="text-center">
                        <tr>
                            <th>ACTION</th>
                            <th>CODE ALLÉE</th>
                            <th>ENTREPÔT</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        if (!empty($arr_data_allee)):
                            foreach ($arr_data_allee as $key => $value) : ?>
                                <tr id="<?= $value->id ?>" class="text-center">
                                    <td class="text-center cursor-pointer td_no_border">
                                        <!-- <button type="button" style="margin-right:0.3em;background:transparent" class="btn btn-icon btn-rounded btn-xs" data-toggle="modal" data-target="#modal_view_allee" data-target="Visualiser" data-popup="tooltip" title="Visualiser" data-placement="bottom" onclick="view(<?= $value->id ?>,'voir')"><i class="fas fa-eye"></i></a></button> -->
                                        <button href="#" type="button" style="margin-right:0.3em;background:transparent" class="btn btn-icon btn-rounded btn-xs" data-toggle="modal" data-target="#modal_view_allee" data-popup="tooltip" title=" Mettre à jour" data-placement="bottom" onclick="view(<?= $value->id ?>,'upd')" <?= $style_btn; ?>><img src="<?= base_url('assets/images/modifier.png') ?>" alt="" style="width: 20px; height: 20px;"></button>
                                        <button type="button" style="margin-right:0.3em;background:transparent" class="btn btn-icon btn-rounded btn-xs" id="del_allee" data-popup="tooltip" title="Supprimer" data-placement="bottom" onclick="deleteItem(<?= $value->id ?>)" <?= $style_btn; ?>><img src="<?= base_url('assets/images/supprimer.png') ?>" alt="" style="width: 20px; height: 20px;"></button>
                                    </td>
                                    <td><?= $value->code ?></td>
                                    <td><?= $value->entrepot ?></td>
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

<!-- Ajout allee -->
<div id="modal_ajout_allee" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header justify-content-center position-relative">
                <h5 class="modal-title" id="myModalLabel">Ajouter une allée</h5>
                <button type="button" class="btn-close position-absolute end-0 me-3" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form class="form-validate-jquery add-allee-content">

                    <div class="form-group">
                        <label>ENTREPÔT <span class="text-bold text-danger-600">*</span></label>
                        <select class="select select-search obligatoire" data-placeholder="Choisir un entrepôt..." name="entrepot_id" id="entrepot_id" style="width: 100%;">
                            <option value=""></option>
                            <?php if (!empty($arr_data_entrepot)): ?>
                                <?php foreach ($arr_data_entrepot as $row): ?>
                                    <option value="<?= $row->id ?>">
                                        <?= $row->code . " - " . $row->nom ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <label id="entrepot_id-error" class="validation-error-label" for="entrepot_id"></label>
                    </div>
                    <div class="form-group">
                        <label>CODE <span class="text-bold text-danger-600">*</span></label>
                        <input type="text" class="form-control input-xs obligatoire" placeholder="Code" name="code" id="code" required="required">
                        <label id="code-error" class="validation-error-label" for="code"></label>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal">Annuler</button>
                        <button type="button" class="btn btn-primary btn-xs btn-sm" id="save" onclick="insert()">Enregistrer</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
<!-- /Ajout allee -->

<!-- Visualiser/Modifier allee -->
<div id="modal_view_allee" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header justify-content-center position-relative">
                <h5 class="modal-title" id="myModalLabel">Modification d'une allée</h5>
                <button type="button" class="btn-close position-absolute end-0 me-3" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="content-allee"></div>
        </div>
    </div>
</div>
<!-- /Visualiser/Modifier allee -->

<?php if ($request_ajax == 0) : ?>
    <?= $this->endSection() ?>
    <?= $this->section('script') ?>
<?php endif; ?>
<script type="text/javascript" src="<?= base_url('assets/js/pages/allee.js'); ?>"></script>
<?php if ($request_ajax == 0) : ?>
    <?= $this->endSection() ?>
<?php endif; ?>