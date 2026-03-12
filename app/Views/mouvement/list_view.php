<?php if ($request_ajax == 0) : ?>
    <?= $this->extend('layout/main') ?>
    <?= $this->section('content') ?>
<?php endif; ?>
<?php if (isset($request_ajax) && $request_ajax): ?>
    <div id="ajax-title" data-title="<?= esc($titre) ?>"></div>
<?php endif; ?>
<link rel="stylesheet" href="assets/libs/flatpickr/flatpickr.min.css">

<div class="card">
    <div class="card-body">
        <?= $this->include('mouvement/menu'); ?>
    </div>
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
<script type="text/javascript" src="<?= base_url('assets/js/pages/mouvement.js'); ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/js/dataTableServerSide.js'); ?>"></script>
<?php if ($request_ajax == 0) : ?>
    <?= $this->endSection() ?>
<?php endif; ?>