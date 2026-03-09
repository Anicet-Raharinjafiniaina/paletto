<?php

/** menus dans palettes */
$controllers = [
    'Palette'   => 'Palette',
    'Attribution palette' => 'Article',
    'Désallocation palette' => 'DesallocationPalette',
];; ?>

<div class="bg-white shadow-sm rounded-3 p-4 mb-4">
    <div class="d-flex flex-wrap gap-2 justify-content-center">
        <?php foreach ($controllers as $key => $label): ?>
            <button type="button"
                onclick="loadPage(urlProject + '<?= $controllers[$key] ?>', true)"
                class="btn <?= ($key === $menu_palette) ? 'btn-primary' : 'btn-outline-primary' ?>">
                <?= $key ?>
            </button>
        <?php endforeach; ?>
    </div>
</div>