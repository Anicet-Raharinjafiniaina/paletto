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

<style>
    .table-black-border td,
    .table-black-border th {
        border: 1px solid #000 !important;
    }
</style>

<div class="row">
    <div class="col-6">
        <div class="card text-center">
            <div class="card-body">
                <div class="form-group">
                    <label>ENTREPÔT <span class="fw-bold text-danger">*</span></label>
                    <select class="select select-search obligatoire" data-placeholder="Choisir un entrepôt..." name="entrepot" id="entrepot" style="width: 100%;" onchange="getdataByEntrepotId()">
                        <option value=""></option>
                        <?php if (!empty($arr_entrepot)): ?>
                            <?php foreach ($arr_entrepot as $row_entrepot): ?>
                                <option value="<?= $row_entrepot->id ?>">
                                    <?= $row_entrepot->code . " - " . $row_entrepot->nom ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <label id="entrepot-error" class="validation-error-label" for="entrepot"></label>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6">
        <div class="card nb-emplacement text-center">
            <div class="card-body" style="margin-bottom : 10px;">
                <label>
                    <span class="badge bg-success p-1">Libre</span> :
                </label>
                <span id="libre"></span>
                <br><br>

                <label>
                    <span class="badge bg-danger p-1">Occupé</span> :
                </label>
                <span id="occupe"></span>
            </div>
        </div>
        <!-- end cardaa -->
    </div> <!-- end col -->
</div>

<div class="row">
    <div id="tableau-emplacement"></div>
</div>

<?= $this->include('mouvement/entree'); ?>
<?= $this->include('mouvement/sortie'); ?>
<?= $this->include('mouvement/transfert'); ?>

<!-- Modal Scanner -->
<?= $this->include('modal/scan_qr_code_modal'); ?>




<?php if ($request_ajax == 0) : ?>
    <?= $this->endSection() ?>
    <?= $this->section('script') ?>
<?php endif; ?>
<script src="<?= base_url('assets/libs/typeahead/typeahead.min.js'); ?>"></script>
<script src="<?= base_url('assets/libs/qr_code/html5_qr_code.js'); ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/js/pages/plan.js'); ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/js/pages/mouvement.js'); ?>"></script>
<?php if ($request_ajax == 0) : ?>
    <?= $this->endSection() ?>
<?php endif; ?>