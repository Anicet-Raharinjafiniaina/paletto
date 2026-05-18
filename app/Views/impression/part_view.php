<div class="card part-card h-100" id="part_card_1">
    <div class="card-body position-relative">
        <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 btn-remove-part" data-index="1">
            <i class="fas fa-times"></i>
        </button>

        <div class="row" id="type_choix_1">
            <div class=" form-group">
                <label>TYPE <span class="fw-bold text-danger">*</span></label>
                <select class="select select-search obligatoire" id="type_1" name="type_1" data-placeholder="Choisir type ..." onchange="changeSelect(this)">
                    <option></option>
                    <option value="1">Emplacement</option>
                    <option value="2">Palette</option>
                </select>
                <label id="type_1-error" class="validation-error-label" for="type_1"></label>
            </div>
        </div>

        <div class="row" id="entrepotSelect_1">
            <div class="form-group">
                <label>ENTREPÔT <span class="fw-bold text-danger">*</span></label>
                <select class="select select-search obligatoire" data-placeholder="Choisir un entrepôt..." name="entrepot_1" id="entrepot_1" style="width: 100%;" onchange="changeSelect(this)"></select>
                <label id="entrepot_1-error" class="validation-error-label" for="entrepot_1"></label>
            </div>
        </div>

        <div class="row" id="emplacementSelect_1">
            <div class="form-group">
                <label>EMPLACEMENT ADRESSE <span class="fw-bold text-danger">*</span></label>
                <select class="select select-search obligatoire" data-placeholder="Choisir un emplacement..." name="emplacement_1" id="emplacement_1" style="width: 100%;" onchange="changeSelect(this)"></select>
                <label id="emplacement_1-error" class="validation-error-label" for="emplacement_1"></label>
            </div>
        </div>

        <div class="form-group" id="paletteSelect_1">
            <label>PALETTE <span class="fw-bold text-danger">*</span></label>
            <select class="select select-search obligatoire" data-placeholder="Choisir une palette..." name="palette_1" id="palette_1" style="width: 100%;" onchange="changeSelect(this)"></select>
            <label id="palette_1-error" class="validation-error-label" for="palette_1"></label>
        </div>

        <div class="row mb-n4" id="data_1"></div>

    </div>
</div>