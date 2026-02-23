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

<br><br>
<span id="historique" onclick="toggleHistorique()"><a href="#">Afficher l'historique</a></span>
<br><br>

<div id="tbl_content" class="d-none">
    <div class="card">
        <div class="card-body">
            <table id="tbl_mouvement" class="table table-bordered dt-responsive nowrap w-100" style="width:100%">
                <thead>
                    <tr>
                        <th>Action</th>
                        <th>Emplacement</th>
                        <th>Palette</th>
                        <th>Date du mouvement</th>
                        <th>Type</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>


<?= $this->include('mouvement/entree'); ?>
<?= $this->include('mouvement/sortie'); ?>
<?= $this->include('mouvement/transfert'); ?>

<!-- Visualiser/Modifier mouvement -->
<div id="modal_view_mouvement" class="modal fade">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header d-flex justify-content-between align-items-center">
                <h5 class="modal-title text-center flex-grow-1" id="myModalLabel">Détail du mouvement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="content-mouvement"></div>
        </div>
    </div>
</div>
<!-- /Visualiser mouvement -->

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