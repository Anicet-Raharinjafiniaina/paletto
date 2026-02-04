<style>
    .glass-menu {
        display: flex;
        flex-wrap: wrap;
        /* permet le retour à la ligne */
        gap: 8px;
        padding: 10px;
        font-family: sans-serif;
        background: rgba(255, 255, 255, 0.5);
        backdrop-filter: blur(6px);
        border-radius: 8px;
    }

    .glass-item {
        padding: 10px 18px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 14px;
        color: #222;
        transition: 0.2s;
        background: rgba(255, 255, 255, 0.4);
        text-align: center;
        flex: 1 0 auto;
        /* permet de s'adapter */
    }

    .glass-item:hover {
        background: rgba(255, 255, 255, 0.7);
    }

    .glass-item.active {
        background: #3b82f6;
        color: white;
    }

    /* Adaptation mobile */
    @media (max-width: 600px) {
        .glass-item {
            padding: 6px 10px;
            font-size: 12px;
            flex: 1 1 45%;
            /* 2 items par ligne */
        }
    }

    @media (max-width: 400px) {
        .glass-item {
            flex: 1 1 100%;
            /* 1 item par ligne si écran très petit */
        }
    }
</style>
<?php
/** menus dans palettes */
$controllers = [
    'liste palette'   => 'Palette',
    'attribution palette'       => 'AttributionPalette',
];; ?>
<nav class="glass-menu">
    <?php foreach ($controllers as $key => $label): ?>
        <a onclick="loadPage(urlProject + '<?= $controllers[$key] ?>', true)"
            class="glass-item <?= ($key === $menu_palette) ? 'active' : '' ?>">
            <?= $label ?>
        </a>
    <?php endforeach; ?>
</nav>
<br><br>