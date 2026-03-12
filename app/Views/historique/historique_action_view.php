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
            <div class="card-header"></div>
            <div class="card-body">
                <table id="tbl_historique_action" class="table table-bordered dt-responsive nowrap w-100 text-center" style="width:100%">
                    <thead class="text-center">
                        <tr>
                            <th>Date</th>
                            <th>Action</th>
                            <th>Login</th>
                            <th>Nom</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
        <!-- end cardaa -->
    </div> <!-- end col -->
</div>
<?php if ($request_ajax == 0) : ?>
    <?= $this->endSection() ?>
    <?= $this->section('script') ?>
<?php endif; ?>
<script type="text/javascript" src="<?= base_url('assets/js/pages/historique.js'); ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/js/dataTableServerSide.js'); ?>"></script>
<?php if ($request_ajax == 0) : ?>
    <?= $this->endSection() ?>
<?php endif; ?>