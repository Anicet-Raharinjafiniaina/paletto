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

<?= $this->include('palette/menu'); ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6 d-flex justify-content-start">
                        <h5>Liste des palettes</h5>
                    </div>
                    <div class="col-md-12 d-flex justify-content-end">
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" id="btn-add-palette" <?= $style_btn; ?>>
                            <i class="fas fa-plus position-left"></i> Ajouter
                        </button>
                    </div>
                </div>

            </div>
            <div class="card-body">
                <table id="table_palette" class="datatable table table-bordered dt-responsive nowrap w-100">
                    <thead class="text-center">
                        <tr>
                            <th>ACTION</th>
                            <th>ETAT</th>
                            <th>CODE</th>
                            <th>CLIENT</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        if (!empty($arr_data_palette)):
                            foreach ($arr_data_palette as $key => $value) :
                                $disable_btn = ($value->palette_statut_id == 3   ? "disabled" : "") ?>
                                <tr id="<?= $value->id ?>" class="text-center">
                                    <td class="text-center cursor-pointer td_no_border">
                                        <!-- <button type="button" style="margin-right:0.3em;background:transparent" class="btn btn-icon btn-rounded btn-xs" data-toggle="modal" data-target="#modal_view_niveau" data-target="Visualiser" data-popup="tooltip" title="Visualiser" data-placement="bottom" onclick="view(<?= $value->id ?>,'voir')"><i class="fas fa-eye"></i></a></button> -->
                                        <button href="#" type="button" style="margin-right:0.3em;background:transparent" class="btn btn-icon btn-rounded btn-xs" data-toggle="modal" data-target="#modal_view_niveau" data-popup="tooltip" title=" Mettre à jour" data-placement="bottom" onclick="view(<?= $value->id ?>,'upd')" <?= $disable_btn; ?>><img src="<?= base_url('assets/images/modifier.png') ?>" alt="" style="width: 20px; height: 20px;"></button>
                                        <button type="button" style="margin-right:0.3em;background:transparent" class="btn btn-icon btn-rounded btn-xs" id="del_niveau" data-popup="tooltip" title="Supprimer" data-placement="bottom" onclick="deleteItem(<?= $value->id ?>)" <?= $disable_btn; ?>><img src="<?= base_url('assets/images/supprimer.png') ?>" alt="" style="width: 20px; height: 20px;"></button>
                                    </td>
                                    <?php $statutClasses = [
                                        1 => 'bg-success',
                                        2 => 'bg-primary',
                                        3 => 'bg-danger',
                                    ];
                                    ?> <td>
                                        <span class="badge rounded-pill p-2 <?= $statutClasses[$value->palette_statut_id] ?? 'bg-secondary' ?>">
                                            <?= $value->statut ?>
                                        </span>
                                    </td>
                                    <td><?= $value->code ?></td>
                                    <td><?= $value->client_code . " - " . $value->client_nom ?></td>
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

<!-- Ajout palette -->
<div id="modal_ajout_palette" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header justify-content-center position-relative">
                <h5 class="modal-title" id="myModalLabel">Ajouter une palette</h5>
                <button type="button" class="btn-close position-absolute end-0 me-3" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form class="form-validate-jquery add-palette-content">
                    <div class="form-group">
                        <label>CODE <span class="fw-bold text-danger">*</span></label>
                        <input type="text" class="form-control input-xs obligatoire" placeholder="Code" name="code" id="code" required="required">
                        <label id="code-error" class="validation-error-label" for="code"></label>
                    </div>
                    <div class="form-group">
                        <label>ETAT <span class="fw-bold text-danger">*</span></label>
                        <select class="select select-search obligatoire" data-placeholder="Choisir un statut..." name="palette_statut_id" id="palette_statut_id" style="width: 100%;">
                            <option value=""></option>
                            <?php if (!empty($arr_palette_statut)): ?>
                                <?php foreach ($arr_palette_statut as $row): ?>
                                    <?php if ($row->id == 3) continue; ?>
                                    <option value="<?= $row->id ?>">
                                        <?= $row->statut ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <label id="palette_statut_id-error" class="validation-error-label" for="palette_statut_id"></label>
                    </div>
                    <div class="form-group">
                        <label>CLIENT <span id="client-required" class="text-bold text-danger-600">*</span></label>
                        <!-- <select class="select select-search obligatoire" data-placeholder="Choisir un clinet..." name="client" id="client" style="width: 100%;">
                            <option value=""></option>
                            <?php /*if (!empty($arr_client)): ?>
                                <?php foreach ($arr_client as $row): ?>
                                    <option value="<?= $row->code . " - " . $row->nom ?>">
                                        <?= $row->code . " - " . $row->nom ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif;*/ ?>
                        </select> -->
                        <input type="text" class="form-control input-xs obligatoire" placeholder="Code ou/et Nom du client" name="client" id="client" required="required">
                        <label id="client-error" class="validation-error-label" for="client"></label>
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
<!-- /Ajout palette -->

<!-- Visualiser/Modifier palette -->
<div id="modal_view_palette" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header justify-content-center position-relative">
                <h5 class="modal-title" id="myModalLabel">Modification d'une palette</h5>
                <button type="button" class="btn-close position-absolute end-0 me-3" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="content-palette"></div>
        </div>
    </div>
</div>
<!-- /Visualiser/Modifier palette -->

<?php if ($request_ajax == 0) : ?>
    <?= $this->endSection() ?>
    <?= $this->section('script') ?>
<?php endif; ?>
<script src="assets/libs/typeahead/typeahead.min.js"></script>
<script type="text/javascript" src="<?= base_url('assets/js/pages/palette.js'); ?>"></script>
<?php if ($request_ajax == 0) : ?>
    <?= $this->endSection() ?>
<?php endif; ?>