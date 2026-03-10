<?php if ($request_ajax == 0) : ?>
    <?= $this->extend('layout/main') ?>
    <?= $this->section('content') ?>
<?php endif; ?>
<?php if (isset($request_ajax) && $request_ajax): ?>
    <div id="ajax-title" data-title="<?= esc($titre) ?>"></div>
<?php endif; ?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-12 d-flex justify-content-end">
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" id="btn-add-article_hors_x3">
                            <i class="fas fa-plus position-left"></i> Ajouter
                        </button>
                    </div>
                </div>

            </div>
            <div class="card-body">
                <table id="table_article_hors_x3" class="datatable table table-bordered dt-responsive nowrap w-100">
                    <thead class="text-center">
                        <tr>
                            <th>ACTION</th>
                            <th>CODE</th>
                            <th>NOM</th>
                            <th>DESCRIPTION</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        if (!empty($arr_data_article_hors_x3)):
                            foreach ($arr_data_article_hors_x3 as $key => $value) : ?>
                                <tr id="<?= $value->id ?>" class="text-center">
                                    <td class="text-center cursor-pointer td_no_border">
                                        <!-- <button type="button" style="margin-right:0.3em;background:transparent" class="btn btn-icon btn-rounded btn-xs" data-toggle="modal" data-target="#modal_view_article_hors_x3" data-target="Visualiser" data-popup="tooltip" title="Visualiser" data-placement="bottom" onclick="view(<?= $value->id ?>,'voir')"><i class="fas fa-eye"></i></a></button> -->
                                        <button href="#" type="button" style="margin-right:0.3em;background:transparent" class="btn btn-icon btn-rounded btn-xs" data-toggle="modal" data-target="#modal_view_article_hors_x3" data-popup="tooltip" title=" Mettre à jour" data-placement="bottom" onclick="view(<?= $value->id ?>,'upd')"><img src="<?= base_url('assets/images/modifier.png') ?>" alt="" style="width: 20px; height: 20px;"></button>
                                        <button type="button" style="margin-right:0.3em;background:transparent" class="btn btn-icon btn-rounded btn-xs" id="del_article_hors_x3" data-popup="tooltip" title="Supprimer" data-placement="bottom" onclick="deleteItem(<?= $value->id ?>)"><img src="<?= base_url('assets/images/supprimer.png') ?>" alt="" style="width: 20px; height: 20px;"></button>
                                    </td>
                                    <td><?= $value->code ?></td>
                                    <td><?= $value->nom ?></td>
                                    <td><?= $value->description ?></td>
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

<!-- Ajout article_hors_x3 -->
<div id="modal_ajout_article_hors_x3" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header justify-content-center position-relative">
                <h5 class="modal-title" id="myModalLabel">Ajouter un article</h5>
                <button type="button" class="btn-close position-absolute end-0 me-3" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form class="form-validate-jquery add-article_hors_x3-content">
                    <div class="form-group">
                        <label>CODE <span class="text-bold text-danger-600">*</span></label>
                        <input type="text" class="form-control input-xs basicAutoComplete obligatoire" placeholder="Code" name="code" id="code" required="required">
                        <label id="code-error" class="validation-error-label" for="code"></label>
                    </div>

                    <div class="form-group">
                        <label>NOM <span class="text-bold text-danger-600">*</span></label>
                        <input type="text" class="form-control input-xs obligatoire" placeholder="Nom" name="nom" id="nom" required="required">
                        <label id="nom-error" class="validation-error-label" for="nom"></label>
                    </div>

                    <div class="form-group">
                        <label>UNITÉ PCB <span class="text-bold text-danger-600">*</span></label>
                        <div id="container_inputs_add" class="border border-1 p-2">
                            <div class="input-group mb-2 dynamic-row">
                                <input type="text" id="unite_pcb_1" name="unite_pcb_1" class="form-control input-xs obligatoire">
                                <div class="buttons-area ms-2">
                                    <button type="button" class="btn btn-primary btn-xs btn-add">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                            <label id="unite_pcb_1-error" class="validation-error-label" for="unite_pcb_1"></label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>PALETTISATION <span class="text-bold text-danger-600">*</span></label>
                        <input type="text" class="form-control input-xs obligatoire" placeholder="Palettisation" name="palettisation" id="palettisation" required="required">
                        <label id="palettisation-error" class="validation-error-label" for="palettisation"></label>
                    </div>

                    <div class="form-group">
                        <label>UNITÉ DE STOCKAGE <span class="text-bold text-danger-600">*</span></label>
                        <input type="text" class="form-control input-xs obligatoire" placeholder="Unité de stockage" name="unite_stockage" id="unite_stockage" required="required">
                        <label id="unite_stockage-error" class="validation-error-label" for="unite_stockage"></label>
                    </div>

                    <div class="form-group">
                        <label>DESCRIPTION </label>
                        <input type="text" class="form-control input-xs" placeholder="Description" name="description" id="description">
                        <label id="description-error" class="validation-error-label" for="description"></label>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal">Annuler</button>
                        <button type="button" class="btn btn-primary btn-sm" id="save" onclick="insert()">Enregistrer</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
<!-- /Ajout article_hors_x3 -->

<!-- Visualiser/Modifier article_hors_x3 -->
<div id="modal_view_article_hors_x3" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header justify-content-center position-relative">
                <h5 class="modal-title" id="myModalLabel">Modification d'un article</h5>
                <button type="button" class="btn-close position-absolute end-0 me-3" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="content-article_hors_x3"></div>
        </div>
    </div>
</div>
<!-- /Visualiser/Modifier article_hors_x3 -->

<?php if ($request_ajax == 0) : ?>
    <?= $this->endSection() ?>
    <?= $this->section('script') ?>
<?php endif; ?>
<script type="text/javascript" src="<?= base_url('assets/js/pages/article_hors_x3.js'); ?>"></script>
<?php if ($request_ajax == 0) : ?>
    <?= $this->endSection() ?>
<?php endif; ?>