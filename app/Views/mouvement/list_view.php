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

<?= $this->include('mouvement/menu'); ?>

<?= $this->include('mouvement/entree'); ?>
<?= $this->include('mouvement/sortie'); ?>
<?= $this->include('mouvement/transfert'); ?>

<?php if ($request_ajax == 0) : ?>
    <?= $this->endSection() ?>
    <?= $this->section('script') ?>
<?php endif; ?>
<script src="<?= base_url('assets/libs/typeahead/typeahead.min.js'); ?>"></script>
<script src="<?= base_url('assets/libs/qr_code/html5_qr_code.js'); ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/js/pages/mouvement.js'); ?>"></script>
<?php if ($request_ajax == 0) : ?>
    <?= $this->endSection() ?>
<?php endif; ?>