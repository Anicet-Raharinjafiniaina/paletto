<?php if ($request_ajax == 0) : ?>
    <?= $this->extend('layout/main') ?>
    <?= $this->section('content') ?>
<?php endif; ?>
<?php if (isset($request_ajax) && $request_ajax): ?>
    <div id="ajax-title" data-title="<?= esc($titre) ?>"></div>
<?php endif; ?>

<div class="col-md-12 mb-3">
    <!-- <label class="form-label fw-semibold">Période</label> -->
    <div class="input-group shadow-sm">
        <span class="input-group-text bg-primary text-white">
            <i data-feather="calendar"></i>
        </span>
        <select class="form-select" id="periode" name="periode">
            <option value="quotidien">Quotidien</option>
            <option value="hebdomadaire">Hebdomadaire</option>
            <option value="mensuel">Mensuel</option>
            <option value="annuel">Annuel</option>
        </select>
    </div>
</div>

<div class="row">
    <div class="col-xl-4 col-md-6">
        <!-- card -->
        <div class="card card-h-100">
            <div class="card-header">
                <h5 class="mb-0 text-center">Emplacement</h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <span class="text-muted mb-3 lh-1 d-block">
                            <span class="text-dark counter-value" data-target="0">0</span> libre(s)
                        </span>
                        <span class="text-muted mb-3 lh-1 d-block">
                            <span class="text-dark counter-value" data-target="0">0</span> occupé(s)
                        </span>
                    </div>

                    <div>
                        <canvas id="pie_emplacement" width="215" height="215"></canvas>
                    </div>
                </div>
            </div><!-- end card body -->
        </div><!-- end card -->
    </div><!-- end col -->

    <div class="col-xl-4 col-md-6">
        <!-- card -->
        <div class="card card-h-100">
            <div class="card-header">
                <h5 class="mb-0 text-center">Palette</h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <span class="text-muted mb-3 lh-1 d-block">
                            <span class="text-dark counter-value" data-target="0">0</span> libre(s)
                        </span>
                        <span class="text-muted mb-3 lh-1 d-block">
                            <span class="text-dark counter-value" data-target="0">0</span> attribuée(s)
                        </span>
                        <span class="text-muted mb-3 lh-1 d-block">
                            <span class="text-dark counter-value" data-target="0">0</span> occupée(s)
                        </span>
                    </div>

                    <div>
                        <canvas id="pie_palette" width="215" height="215"></canvas>
                    </div>
                </div>
            </div><!-- end card body -->
        </div><!-- end card -->
    </div><!-- end col -->

    <div class="col-xl-4 col-md-6">
        <!-- card -->
        <div class="card card-h-100">
            <div class="card-header">
                <h5 class="mb-0 text-center">Mouvement</h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <span class="text-muted mb-3 lh-1 d-block">
                            <span class="text-dark counter-value" data-target="0">0</span> entrée
                        </span>
                        <span class="text-muted mb-3 lh-1 d-block">
                            <span class="text-dark counter-value" data-target="0">0</span> transfert
                        </span>
                        <span class="text-muted mb-3 lh-1 d-block">
                            <span class="text-dark counter-value" data-target="0">0</span> sortie
                        </span>
                    </div>

                    <div>
                        <canvas id="pie_mouvement" width="215" height="215"></canvas>
                    </div>
                </div>
            </div><!-- end card body -->
        </div><!-- end card -->
    </div><!-- end col -->
</div>

<div class="row">
    <div class="col-xl-6 col-md-6">
        <div class="card card-h-100">
            <div class="card-header">
                <h5 class="mb-0 text-center">Entrepôt</h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div style="width: 600px; margin: auto;">
                        <canvas id="bar_entrepot"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-6 col-md-6">
        <div class="card card-h-100">
            <div class="card-header">
                <h5 class="mb-0 text-center">Flux mouvement</h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div style="width: 600px; margin: auto;">
                        <canvas id="flux_chart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<?php if ($request_ajax == 0) : ?>
    <?= $this->endSection() ?>
    <?= $this->section('script') ?>
<?php endif; ?>
<script type="text/javascript" src="<?= base_url('assets/libs/chartJs/chart.js'); ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/libs/chartJs/datalabels.js'); ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/js/pages/dashboard.js'); ?>"></script>
<?php if ($request_ajax == 0) : ?>
    <?= $this->endSection() ?>
<?php endif; ?>