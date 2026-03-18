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

<?= $this->include('emplacement_adresse/menu'); ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6 d-flex justify-content-start">
                        <h5>Liste des emplacements</h5>
                    </div>
                </div>

            </div>
            <div class="card-body">
                <table id="table_emplacement" class="datatable table table-bordered dt-responsive nowrap w-100">
                    <thead class="text-center">
                        <tr>
                            <th>ACTION</th>
                            <th>ETAT</th>
                            <th>ADRESSE</th>
                            <th>ENTREPÔT</th>
                            <th>ALLÉE</th>
                            <th>RANGÉE</th>
                            <th>NIVEAU</th>
                            <th>CAGE / ALVÉOLE</th>
                            <th>EMPLACEMENT</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        if (!empty($arr_data_emplacement)):
                            foreach ($arr_data_emplacement as $key => $value) : ?>
                                <tr id="<?= $value->emplacement_id ?>" class="text-center">
                                    <td class="text-center cursor-pointer td_no_border">
                                        <button type="button" style="margin-right:0.3em;background:transparent" class="btn btn-icon btn-rounded btn-xs" data-toggle="modal" data-target="#modal_view_emplacement" data-target="Visualiser" data-popup="tooltip" title="Visualiser" data-placement="bottom" onclick="view(<?= $value->emplacement_id ?>,'voir')"><i class="fas fa-qrcode fa-lg"></i></a></button>
                                    </td>
                                    <?php $statutClasses = [
                                        1 => 'bg-success',
                                        2 => 'bg-danger'
                                    ];
                                    ?> <td>
                                        <span class="badge rounded-pill p-2 <?= $statutClasses[$value->statut_id] ?? 'bg-secondary' ?>">
                                            <?= $value->statut ?>
                                        </span>
                                    </td>
                                    <td><?= $value->qr_code_texte ?></td>
                                    <td><?= $value->entrepot_code ?></td>
                                    <td><?= $value->allee_code ?></td>
                                    <td><?= $value->rangee_code ?></td>
                                    <td><?= $value->niveau_code ?></td>
                                    <td><?= $value->cage_code ?></td>
                                    <td><?= $value->emplacement_code ?></td>
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


<!-- Visualiser/Modifier emplacement -->
<div id="modal_view_emplacement" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header d-flex justify-content-between align-items-center">
                <h5 class="modal-title text-center flex-grow-1" id="myModalLabel">Détail de l'emplacement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="content-emplacement"></div>
        </div>
    </div>
</div>
<!-- /Visualiser emplacement -->


<?php if ($request_ajax == 0) : ?>
    <?= $this->endSection() ?>
    <?= $this->section('script') ?>
<?php endif; ?>
<script type="text/javascript" src="<?= base_url('assets/js/pages/listeEmplacement.js'); ?>"></script>
<?php if ($request_ajax == 0) : ?>
    <?= $this->endSection() ?>
<?php endif; ?>