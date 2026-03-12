<?php if ($request_ajax == 0) : ?>
    <?= $this->extend('layout/main') ?>
    <?= $this->section('content') ?>
<?php endif; ?>
<?php if (isset($request_ajax) && $request_ajax): ?>
    <div id="ajax-title" data-title="<?= esc($titre) ?>"></div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <table id="tbl_historique_mouvement" class="table table-bordered dt-responsive nowrap w-100 text-center" style="width:100%">
            <thead>
                <tr>
                    <th>ACTION</th>
                    <th>ETAT</th>
                    <th>EMPLACEMENT</th>
                    <th>PALETTE</th>
                    <th>DATE DU MOUVEMENT</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

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

<?php if ($request_ajax == 0) : ?>
    <?= $this->endSection() ?>
    <?= $this->section('script') ?>
<?php endif; ?>
<script type="text/javascript" src="<?= base_url('assets/js/pages/historique.js'); ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/js/dataTableServerSide.js'); ?>"></script>
<?php if ($request_ajax == 0) : ?>
    <?= $this->endSection() ?>
<?php endif; ?>