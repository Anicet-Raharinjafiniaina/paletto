<?php if ($request_ajax == 0) : ?>
    <?= $this->extend('layout/main') ?>
    <?= $this->section('content') ?>
<?php endif; ?>
<?php if (isset($request_ajax) && $request_ajax): ?>
    <div id="ajax-title" data-title="<?= esc($titre) ?>"></div>
<?php endif; ?>

<link href='<?= base_url("assets/libs/daterangepicker/daterangepicker.css") ?>' rel="stylesheet" type="text/css">
<link href='<?= base_url("assets/css/rapport.css") ?>' rel="stylesheet" type="text/css">

<div class="card card-h-100">
    <div class="card-body">
        <iframe id="downloadFrame" style="display:none;"></iframe>
        <div class="row content-export">
            <div class="col-md-6 mb-3">
                <label class="fw-semibold">Type</label>
                <div class="input-group shadow-sm rounded">
                    <span class="input-group-text bg-primary text-white">
                        <i data-feather="check"></i>
                    </span>
                    <select class="form-select obligatoire" id="type" name="type">
                        <option value="">Veuillez séléctionner ...</option>
                        <option value="1">Liste des mouvements</option>
                        <option value="2">Liste des emplacements</option>
                        <option value="3">Liste des palettes</option>
                        <option value="4">Liste des entrepôts</option>
                        <option value="5">Liste des articles gérés dans l'application</option>
                    </select>
                </div>
                <label id="type-error" class="validation-error-label mt-2" for="type"></label>
            </div>
            <div class="col-md-6 mb-3 periode">
                <label class="fw-semibold">Période</label>
                <div class="input-group shadow-sm rounded">
                    <span class="input-group-text bg-primary text-white">
                        <i data-feather="calendar"></i>
                    </span>
                    <input type="text" id="periode_range" class="form-control obligatoire" placeholder="Veuillez sélectionner la période">
                </div>
                <label id="periode_range-error" class="validation-error-label mt-2" for="periode_range"></label>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6"></div>
            <div class="col-md-6 mt-3">
                <button type="button" class="btn btn-primary btn-sm float-end d-flex align-items-center"
                    id="btn_download" onclick="exporter()"
                    data-loading-text="<i class='icon-spinner10 spinner'></i> Enregistrer">
                    <i data-feather="download" class="me-1" style="width:16px; height:16px;"></i>
                    Télécharger
                </button>
            </div>
        </div>

    </div>
</div>

<?php if ($request_ajax == 0) : ?>
    <?= $this->endSection() ?>
    <?= $this->section('script') ?>
<?php endif; ?>

<!-- Moment.js (nécessaire pour daterangepicker) -->
<script type="text/javascript" src="<?= base_url('assets/libs/moment/moment.min.js'); ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/libs/daterangepicker/daterangepicker.min.js'); ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/js/pages/export.js'); ?>"></script>
<?php if ($request_ajax == 0) : ?>
    <?= $this->endSection() ?>
<?php endif; ?>