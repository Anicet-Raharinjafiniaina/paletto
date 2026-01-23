<?php
if ($request_ajax == 0) : ?>
    <?= $this->extend('layout/main') ?>
    <?= $this->section('content') ?>

<?php endif; ?>
<?php

if (isset($request_ajax) && $request_ajax): ?>
    <div id="ajax-title" data-title="<?= esc($titre) ?>"></div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <p>Contenu de la page 1</p>
    </div>
</div>

<?php if ($request_ajax == 0) : ?>
    <?= $this->endSection() ?>
    <?= $this->section('script') ?>
<?php endif; ?>

<?php if ($request_ajax == 0) : ?>
    <?= $this->endSection() ?>
<?php endif; ?>