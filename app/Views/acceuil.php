<?php if ($request_ajax == 0) : ?>
    <?= $this->extend('layout/main') ?>
    <?= $this->section('link') ?>
<?php endif; ?>
<style>
    .card-flex-wrapper {
        display: flex;
        justify-content: center;
        /* centre le bloc interne */
    }

    .card-flex-container {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        justify-content: center;
        /* max-width: 1000px; */
        /* optionnel : limite la largeur totale */
    }

    .card-flex-container .card {
        flex: 0 0 300px;
        /* taille fixe */
        max-width: 300px;
    }

    .fixed-card {
        width: 300px;
    }

    .card-title {
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
    }

    .card-hover {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card-hover:hover {
        transform: scale(1.05);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
    }
</style>
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