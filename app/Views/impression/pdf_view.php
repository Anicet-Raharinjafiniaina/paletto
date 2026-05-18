<style>
    .wrap {
        text-align: center;
        font-family: helvetica;
    }

    .qr img {
        width: 120px;
        height: 120px;
    }

    .title {
        font-size: 14px;
        font-weight: bold;
        margin: 6px 0;
    }

    .info {
        font-size: 12px;
        line-height: 1.3;
    }

    .info div {
        margin: 2px 0;
    }

    .label {
        font-weight: bold;
    }
</style>

<div class="wrap">

    <?php if (!empty($item['emplacement'])): ?>
        <?php $e = $item['emplacement']; ?>

        <div class="qr">
            <?php if (!empty($e['qr_code_image'])): ?>
                <img src="data:image/png;base64,<?= $e['qr_code_image'] ?>">
            <?php endif; ?>
        </div>

        <div class="title"><?= $e['qr_code_texte'] ?? '' ?></div>

        <div class="info">
            <div><span class="label">Entrepôt :</span> <?= $e['entrepot_code'] ?? '' ?></div>
            <div><span class="label">Allée :</span> <?= $e['allee_code'] ?? '' ?></div>
            <div><span class="label">Rangée :</span> <?= $e['rangee_code'] ?? '' ?></div>
            <div><span class="label">Niveau :</span> <?= $e['niveau_code'] ?? '' ?></div>
            <div><span class="label">Cage :</span> <?= $e['cage_code'] ?? '' ?></div>
            <div><span class="label">Emplacement :</span> <?= $e['emplacement_code'] ?? '' ?></div>
        </div>
    <?php endif; ?>

    <br>

    <?php if (!empty($item['palette'])): ?>
        <?php $p = $item['palette']; ?>

        <div class="qr">
            <?php if (!empty($p['qr_code_image'])): ?>
                <img src="data:image/png;base64,<?= $p['qr_code_image'] ?>">
            <?php endif; ?>
        </div>

        <div class="title"><?= $p['qr_code_text'] ?? '' ?></div>

        <div class="info">
            <div><span class="label">Palette :</span> <?= $p['palette_code'] ?? '' ?></div>
            <div><span class="label">Client :</span> <?= ($p['client_code'] ?? '') . ' - ' . ($p['client_nom'] ?? '') ?></div>
            <div><span class="label">Article :</span> <?= $p['nom'] ?? '' ?></div>
            <div><span class="label">Code :</span> <?= $p['code'] ?? '' ?></div>
            <div><span class="label">Quantité :</span> <?= $p['quantite'] ?? '' ?></div>
            <div><span class="label">Lot :</span> <?= $p['lot'] ?? '' ?></div>
        </div>
    <?php endif; ?>

</div>