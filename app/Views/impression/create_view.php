<?php

/** @var int $request_ajax */
/** @var string $titre */
/** @var array $arr_entrepot */
/** @var array $arr_palette */
?>

<?php if ($request_ajax == 0) : ?>
    <?= $this->extend('layout/main') ?>
    <?= $this->section('content') ?>
<?php endif; ?>
<?php if (isset($request_ajax) && $request_ajax): ?>
    <div id="ajax-title" data-title="<?= esc($titre) ?>"></div>
<?php endif; ?>

<div class="row mb-3">
    <div class="col-md-6 d-flex justify-content-start">
    </div>
    <div class="col-md-6 d-flex justify-content-end">
        <button type="button" class="btn btn-primary btn-sm" id="btn-add-part">
            <i class="fas fa-plus position-left"></i> Ajouter
        </button>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="parts-container" id="parts-container">

            <!-- Ligne initiale avec le premier part (rendu par PHP) -->
            <div class="row mb-3 part-row">
                <div class="col-6" id="slot_1">
                    <?= $this->include('impression/part_view'); ?>
                </div>
                <div class="col-6" id="slot_empty"></div>
            </div>

        </div>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-6 d-flex justify-content-start">
    </div>
    <div class="col-md-6 d-flex justify-content-end">
        <button type="button" class="btn btn-primary btn-sm" id="btn-view" onclick="sendData()">
            <i class="fas fa-eye position-left"></i> Visualiser
        </button>
    </div>
</div>

<script>
    const entrepots = <?= json_encode($arr_entrepot) ?>;
    const palettes = <?= json_encode($arr_palette) ?>;
</script>

<?php if ($request_ajax == 0) : ?>
    <?= $this->endSection() ?>
    <?= $this->section('script') ?>
<?php endif; ?>
<script type="text/javascript" src="<?= base_url('assets/js/pages/impression.js'); ?>"></script>
<?php if ($request_ajax == 0) : ?>
    <?= $this->endSection() ?>
<?php endif; ?>