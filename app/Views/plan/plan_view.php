<?php if ($request_ajax == 0) : ?>
    <?= $this->extend('layout/main') ?>
    <?= $this->section('content') ?>
<?php endif; ?>
<?php if (isset($request_ajax) && $request_ajax): ?>
    <div id="ajax-title" data-title="<?= esc($titre) ?>"></div>
<?php endif; ?>

<style>
    .table-black-border td,
    .table-black-border th {
        border: 1px solid #000 !important;
    }
</style>

<div class="row align-items-stretch">
    <div class="col-6">
        <div class="card text-center h-100">
            <div class="card-body py-2">
                <div class="form-group mb-0">
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
        <div class="card nb-emplacement text-center h-100">
            <div class="card-body py-2 d-flex align-items-center justify-content-center gap-3">
                <div class="d-flex flex-column align-items-center">
                    <span class="badge fw-bold fs-1" id="libre" style="background-color: #5cb85c;"></span>
                    <small class="fw-bold mt-1" style="color: #5cb85c;">Libre</small>
                </div>
                <div class="d-flex flex-column align-items-center">
                    <span class="badge fw-bold fs-1" id="occupe" style="background-color: #d9534f;"></span>
                    <small class="fw-bold mt-1" style="color: #d9534f;">Occupé</small>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-5">
    <div class="col-12" id="tableau-emplacement"></div>
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