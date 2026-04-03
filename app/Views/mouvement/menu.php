<style>
    .btn-danger-custom {
        background-color: #8a120f;
        border-color: #8a120f;
        color: #fff;
    }

    .btn-danger-custom:hover {
        background-color: #a31612;
        /* couleur hover */
        border-color: #a31612;
        color: #fff;
    }
</style>

<div class="container mt-4 text-center">
    <div class="row g-3 text-center">

        <!-- Entrée -->
        <div class="col-md-4">
            <button class="btn btn-success w-100 py-4 fw-bold rounded-4 shadow-sm" id="btn_entree" data-bs-toggle="modal" data-bs-target="#modal_ajout_entree">
                <i class="fas fa-sign-in-alt fs-3 d-block mb-2"></i>
                Entrée
            </button>
        </div>

        <!-- Transfert -->
        <div class="col-md-4">
            <button class="btn btn-primary w-100 py-4 fw-bold rounded-4 shadow-sm" id="btn_transfert" data-bs-toggle="modal" data-bs-target="#modal_ajout_transfert">
                <i class="fas fa-exchange-alt fs-3 d-block mb-2"></i>
                Transfert
            </button>
        </div>

        <!-- Sortie -->
        <div class="col-md-4">
            <button class="btn btn-danger-custom w-100 py-4 fw-bold rounded-4 shadow-sm" id="btn_sortie" data-bs-toggle="modal" data-bs-target="#modal_ajout_sortie">
                <i class="fas fa-sign-out-alt fs-3 d-block mb-2"></i>
                Sortie
            </button>
        </div>

    </div>
</div>