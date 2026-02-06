<?php if ($request_ajax == 0) : ?>
    <?= $this->extend('layout/main') ?>
    <?= $this->section('content') ?>
<?php endif; ?>
<?php if (isset($request_ajax) && $request_ajax): ?>
    <div id="ajax-title" data-title="<?= esc($titre) ?>"></div>
<?php endif; ?>
<link rel="stylesheet" href="assets/libs/flatpickr/flatpickr.min.css">
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
                        <h5>Liste des palettes attribuées</h5>
                    </div>
                    <div class="col-md-12 d-flex justify-content-end">
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" id="btn-add-article" <?= $style_btn; ?>>
                            <i class="fas fa-plus position-left"></i> Ajouter
                        </button>
                    </div>
                </div>

            </div>
            <div class="card-body">
                <table id="table_article" class="datatable table table-bordered dt-responsive nowrap w-100">
                    <thead class="text-center">
                        <tr>
                            <th>ACTION</th>
                            <th>QR CODE</th>
                            <th>CODE PALETTE</th>
                            <th>CODE ARTICLE</th>
                            <th>CLIENT</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        if (!empty($arr_article)):
                            foreach ($arr_article as $key => $value) : ?>
                                <tr id="<?= $value->id ?>" class="text-center">
                                    <td class="text-center cursor-pointer td_no_border">
                                        <!-- <button type="button" style="margin-right:0.3em;background:transparent" class="btn btn-icon btn-rounded btn-xs" data-toggle="modal" data-target="#modal_view_niveau" data-target="Visualiser" data-popup="tooltip" title="Visualiser" data-placement="bottom" onclick="view(<?= $value->id ?>,'voir')"><i class="fas fa-eye"></i></a></button> -->
                                        <button href="#" type="button" style="margin-right:0.3em;background:transparent" class="btn btn-icon btn-rounded btn-xs" data-toggle="modal" data-target="#modal_view_niveau" data-popup="tooltip" title=" Mettre à jour" data-placement="bottom" onclick="view(<?= $value->id ?>,'upd')" <?= $style_btn; ?>><img src="<?= base_url('assets/images/modifier.png') ?>" alt="" style="width: 20px; height: 20px;"></button>
                                        <button type="button" style="margin-right:0.3em;background:transparent" class="btn btn-icon btn-rounded btn-xs" id="del_niveau" data-popup="tooltip" title="Supprimer" data-placement="bottom" onclick="deleteItem(<?= $value->id ?>)" <?= $style_btn; ?>><img src="<?= base_url('assets/images/supprimer.png') ?>" alt="" style="width: 20px; height: 20px;"></button>
                                    </td>
                                    <td><?= $value->qr_code_text ?></td>
                                    <td><?= $value->code ?></td>
                                    <td><?= $value->nom ?></td>
                                    <td><?= $value->client_nom ?></td>
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

<!-- Ajout article -->
<div id="modal_ajout_article" class="modal fade">
    <div class="modal-dialog modal-xl modal-article-centered modal-article-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">Attribuer une palette à un article</h5>
                <button type="button" class="btn-close float-right" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="form-validate-jquery add-article-content">
                    <div class="row g-3">
                        <!-- Ligne 1 -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>PALETTE <span class="text-bold text-danger-600">*</span></label>
                                <select class="select select-search obligatoire" data-placeholder="Choisir une palette..." name="palette_id" id="palette_id" onchange="getClientForPalette('palette_id','client','modal_ajout_article')" style="width: 100%;">
                                    <option value=""></option>
                                    <?php if (!empty($arr_palette)): ?>
                                        <?php foreach ($arr_palette as $row): ?>
                                            <option value="<?= $row->id ?>">
                                                <?= $row->code ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <label id="palette_id-error" class="validation-error-label" for="palette_id"></label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>CLIENT <span class="text-bold text-danger-600">*</span></label>
                                <!-- <select class="select select-search obligatoire" data-placeholder="Choisir un client..." name="client" id="client" style="width: 100%;">
                                    <option value=""></option>
                                    <?php /*if (!empty($arr_client)): ?>
                                        <?php foreach ($arr_client as $row): ?>
                                            <option value="<?= $row->code . " - " . $row->nom ?>">
                                                <?= $row->code . " - " . $row->nom ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; */ ?>
                                </select> -->
                                <input type="text" class="form-control input-xs obligatoire" placeholder="Code ou/et Nom du client" name="client" id="client" required="required">
                                <label id="client-error" class="validation-error-label" for="client"></label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>CODE ARTICLE <span class="text-bold text-danger-600">*</span></label>
                                <input type="text" class="form-control input-xs obligatoire" placeholder="Code" name="code" id="code" required="required" onblur="getDetailArticle()">
                                <label id="code-error" class="validation-error-label" for="code"></label>
                            </div>
                        </div>

                        <!-- Ligne 2 -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>NOM ARTICLE <span class="text-bold text-danger-600">*</span></label>
                                <input type="text" class="form-control input-xs obligatoire" placeholder="Nom article" name="nom" id="nom" required="required" disabled>
                                <label id="nom-error" class="validation-error-label" for="nom"></label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>DLUO <span class="text-bold text-danger-600">*</span></label>
                                <input type="text" id="dluo" name="dluo" class="form-control input-xs text-end obligatoire" value="" placeholder="DD/MM/YYYY" required="required">
                                <label id="dluo-error" class="validation-error-label" for="dluo"></label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>UNITÉ PCB <span class="text-bold text-danger-600">*</span></label>
                                <input type="text" class="form-control input-xs obligatoire" placeholder="Unité PCB" name="unite_pcb" id="unite_pcb" required="required" disabled>
                                <label id="unite_pcb-error" class="validation-error-label" for="unite_pcb"></label>
                            </div>
                        </div>

                        <!-- Ligne 3 -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>QUANTITTÉ <span class="text-bold text-danger-600">*</span></label>
                                <input type="text" class="form-control input-xs obligatoire" placeholder="Quantité" name="quantite" id="quantite" required="required" value="0" onkeyup="numberDecimal(this)">
                                <label id="quantite-error" class="validation-error-label" for="quantite"></label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>LOT <span class="text-bold text-danger-600">*</span></label>
                                <input type="text" class="form-control input-xs obligatoire" placeholder="Lot" name="lot" id="lot" required="required">
                                <label id="lot-error" class="validation-error-label" for="lot"></label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>PALETTISATION <span class="text-bold text-danger-600">*</span></label>
                                <input type="text" class="form-control input-xs obligatoire" placeholder="Palettisation" name="palettisation" id="palettisation" required="required" disabled>
                                <label id="palettisation-error" class="validation-error-label" for="palettisation"></label>
                            </div>
                        </div>

                        <!-- Observation -->
                        <div class="col-12">
                            <label for="observation" class="form-label">OBSERVATION</label>
                            <textarea id="observation" name="observation" class="form-control" rows="3" placeholder="Remarques..."></textarea>
                        </div>
                    </div>

                    <div class="modal-footer">
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
            <div class="modal-header">
                <h5 class="modal-title text-center" id="title">Modification d'une palette</h5>
                <button type="button" class="btn-close float-right" data-bs-dismiss="modal" aria-label="Close"></button>
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
<script src="assets/libs/flatpickr/flatpickr.min.js"></script>
<script src="assets/libs/typeahead/typeahead.min.js"></script>
<script type="text/javascript" src="<?= base_url('assets/js/pages/article.js'); ?>"></script>
<?php if ($request_ajax == 0) : ?>
    <?= $this->endSection() ?>
<?php endif; ?>