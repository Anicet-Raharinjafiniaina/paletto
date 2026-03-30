<?php if ($request_ajax == 0) : ?>
    <?= $this->extend('layout/main') ?>
    <?= $this->section('link') ?>
<?php endif; ?>
<link href="<?= base_url('assets/css/accueil.css') ?>" rel="stylesheet" type="text/css" />
<?php if ($request_ajax == 0) : ?>
    <?= $this->endSection() ?>
    <?= $this->section('content') ?>
<?php endif; ?>
<?php if (isset($request_ajax) && $request_ajax): ?>
    <div id="ajax-title" data-title="<?= esc($titre) ?>"></div>
<?php endif; ?>
<?php
helper('menu');
$arr_menu = getMenu();
?>
<div class="container mt-5">
    <div class="card-flex-container">
        <?php if (!empty($arr_menu)) :
            foreach ($arr_menu as $key_section => $section) :
                $lien = (isset($section[0]->lien) ? $section[0]->lien : "#"); ?>
                <a href='<?= base_url($lien) ?>' class="text-decoration-none text-dark nav-link">
                    <div class="card fixed-card text-center card-hover">
                        <div class="card-body">
                            <h5 class="card-title"><?= $key_section ?></h5>
                            <img src="data:image/png;base64,<?= $section[0]->image ?>" style="width:100px;" class="d-block mx-auto">
                        </div>
                    </div>
                </a>
        <?php endforeach;
        endif; ?>
    </div>
</div>
<?php if ($request_ajax == 0) : ?>
    <?= $this->endSection() ?>
<?php endif; ?>