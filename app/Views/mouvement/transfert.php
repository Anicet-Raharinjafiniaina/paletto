<div id="modal_ajout_transfert" class="modal fade">
    <div class="modal-dialog modal-fullscreen modal-transfert-centered modal-transfert-scrollable">
        <div class="modal-content">
            <div class="modal-header d-flex justify-content-between align-items-center">
                <h5 class="modal-title text-center flex-grow-1" id="myModalLabel">Enregistrement Transfert</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="form-validate-jquery add-transfert-content">
                    <div class="card p-4">
                        <div class="row g-4 align-items-end">
                            <!-- Emplacement -->
                            <div class="col-md-6">
                                <label class="form-label">Scan QR Emplacement</label>
                                <div class="input-group">
                                    <input type="text" id="qr_emplacement_transfert" name="qr_emplacement_transfert" class="form-control input-xs obligatoire" placeholder="Scanner ou saisir">
                                    <button class="btn btn-primary" type="button"
                                        onclick="openScanner('qr_emplacement_transfert')">
                                        <i class="fas fa-qrcode fs-5"></i>
                                    </button>
                                </div>
                                <label id="qr_emplacement_transfert-error" class="validation-error-label" for="qr_emplacement_transfert"></label>
                            </div>

                            <!-- Palette -->
                            <div class="col-md-6">
                                <label class="form-label">Scan QR Palette</label>
                                <div class="input-group">
                                    <input type="text" id="qr_palette_transfert" name="qr_palette_transfert" class="form-control input-xs obligatoire" placeholder="Scanner ou saisir">
                                    <button class="btn btn-primary" type="button"
                                        onclick="openScanner('qr_palette_transfert')">
                                        <i class="fas fa-qrcode fs-5"></i>
                                    </button>
                                </div>
                                <label id="qr_palette_transfert-error" class="validation-error-label" for="qr_palette_transfert"></label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer align-items-end">
                        <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal">Annuler</button>
                        <button type="button" class="btn btn-success btn-xs btn-sm" id="save" onclick="validerTransfert()">Valider transfert</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>