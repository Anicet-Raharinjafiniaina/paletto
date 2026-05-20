<style>
    .btn-danger-custom {
        background-color: #8a120f;
        border-color: #8a120f;
        color: #fff;
    }

    .btn-danger-custom:hover {
        background-color: #a31612;
        border-color: #a31612;
        color: #fff;
    }

    /* Agrandir les boutons */
    .custom-btn {
        min-height: 180px;
        font-size: 1.5rem;
    }

    .custom-btn i {
        font-size: 3rem !important;
    }
</style>

<div class="container-fluid mt-4 px-0 text-center">
    <div class="row g-2 m-0 text-center">

        <!-- Entrée -->
        <div class="col-md-4 px-1">
            <button class="btn btn-success w-100 fw-bold rounded-4 shadow-sm custom-btn"
                id="btn_entree"
                data-bs-toggle="modal"
                data-bs-target="#modal_ajout_entree">

                <i class="fas fa-sign-in-alt d-block mb-3"></i>
                Entrée
            </button>
        </div>

        <!-- Transfert -->
        <div class="col-md-4 px-1">
            <button class="btn btn-primary w-100 fw-bold rounded-4 shadow-sm custom-btn"
                id="btn_transfert"
                data-bs-toggle="modal"
                data-bs-target="#modal_ajout_transfert">

                <i class="fas fa-exchange-alt d-block mb-3"></i>
                Changement d'emplacement
            </button>
        </div>

        <!-- Sortie -->
        <div class="col-md-4 px-1">
            <button class="btn btn-danger-custom w-100 fw-bold rounded-4 shadow-sm custom-btn"
                id="btn_sortie"
                data-bs-toggle="modal"
                data-bs-target="#modal_ajout_sortie">

                <i class="fas fa-sign-out-alt d-block mb-3"></i>
                Sortie
            </button>
        </div>

    </div>
</div>