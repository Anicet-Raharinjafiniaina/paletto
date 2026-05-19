<?php

namespace App\Controllers;

use App\Models\CrudModel;
//use TCPDF;


class Impression extends BaseController
{
    public function index()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(12);
        if (!$is_ok) {
            return $this->response->setBody(
                '<script>window.location.href="' . base_url('/') . '";</script>'
            );
        }
        $this->load();
    }

    public function load()
    {
        $arr['titre'] = "Gestion d'impression";
        $crud = new CrudModel(TBL_ENTREPOT);
        $arr['arr_entrepot'] = $crud->getAllData(array('flag_suppression' => 0), [], "*");
        $arr['arr_palette'] =  $this->getAllPallette();
        $arr['request_ajax'] = 0;
        if ($this->request->isAJAX()) {
            $arr['request_ajax'] = 1;
            echo view('impression/create_view', $arr);
            return;
        }
        echo view('impression/create_view', $arr);
    }

    public function getListEmplacementByEntrepotId()
    {
        $id = trim($this->request->getVar('id'));
        $crud = new CrudModel(VIEW_EMPLACEMENT_ADRESSE);
        $arr = $crud->getAllData(array('entrepot_id' => $id), [], "emplacement_adresse_id as id, qr_code_texte as text");
        return json_encode($arr);
    }

    public function getAllPallette()
    {
        $crud = new CrudModel(TBL_ARTICLE);
        $arrJoin = [
            [
                'table' => TBL_PALETTE,
                'type'  => 'LEFT',
                'on'    => TBL_PALETTE . '.id = ' . TBL_ARTICLE . '.palette_id'
            ],
        ];
        $select = TBL_ARTICLE . '.id,' . TBL_ARTICLE . '.qr_code_text';
        return $crud->getAllData([TBL_ARTICLE . '.flag_suppression' => 0, TBL_PALETTE . '.palette_statut_id' => 3, TBL_PALETTE . '.flag_suppression' => 0], $arrJoin, $select);
    }

    public function getDetailPalletteByArticleId()
    {
        $id = trim($this->request->getVar('id'));
        $crud = new CrudModel(TBL_ARTICLE);
        $arrJoin = [
            [
                'table' => TBL_PALETTE,
                'type'  => 'LEFT',
                'on'    => TBL_PALETTE . '.id = ' . TBL_ARTICLE . '.palette_id'
            ],
        ];
        $select = TBL_ARTICLE . '.qr_code_text as qrText,' . TBL_ARTICLE . '.qr_code_image as qrCode';
        return $crud->getDataById([TBL_ARTICLE . '.id' => $id], $arrJoin, $select);
    }

    public function getDetail()
    {
        $id = trim($this->request->getVar('id'));
        $type = trim($this->request->getVar('type'));
        $crud = new CrudModel(VIEW_EMPLACEMENT_ADRESSE);
        $arr = [];
        if ($type == 'emplacement') {
            $arr = $crud->getDataById(array('emplacement_adresse_id' => $id), [], "qr_code_image as qrCode, qr_code_texte as qrText, statut");
        } else if ($type == 'palette') { // palette
            $arr = $this->getDetailPalletteByArticleId();
        } else {
            $arr = [];
        }
        return json_encode($arr);
    }

    public function viewDetail()
    {
        $data = $this->request->getVar('data');
        $arr = $this->traiterTableau($data);

        $map = [];
        $i = 0;
        foreach ($arr as $value) {

            if ($value['type'] == 1) {
                $id = $value['emplacement'];

                $map[$i]['emplacement'] = (array)$this->getDetailEmplacement($id);
            }

            if ($value['type'] == 2) {
                $id = $value['palette'];

                $map[$i]['palette'] = (array)$this->getDetailPalette($id);
            }
            $i++;
        }

        // 🔥 transformation en tableau plat pour PDF
        $arrData = [];

        foreach ($map as $item) {

            $arrData[] = [
                'emplacement' => $item['emplacement'] ?? null,
                'palette'     => $item['palette'] ?? null
            ];
        }

        return $this->viewPdf($arrData);
    }

    public function traiterTableau(array $data): array
    {
        $resultat = [];
        foreach ($data as $key => $value) {
            [$champ, $index] = explode('_', $key);
            $resultat[$index][$champ] = $value;
        }
        ksort($resultat);
        return array_values($resultat);
    }

    public function getDetailEmplacement(int $id)
    {
        $crud = new CrudModel(VIEW_EMPLACEMENT_ADRESSE);
        $arrJoin = array(
            array("table" => TBL_EMPLACEMENT_STATUT, "on" => VIEW_EMPLACEMENT_ADRESSE . ".statut_id = " . TBL_EMPLACEMENT_STATUT . ".id", "type" => "left"),
        );
        return $crud->getDataById(['emplacement_id' => $id], $arrJoin, "*");
    }

    public function getDetailPalette(int $id)
    {
        $crud = new CrudModel(TBL_ARTICLE);
        $arrJoin = [[
            'table' => TBL_PALETTE,
            'type'  => 'LEFT',
            'on'    => TBL_PALETTE . '.id = ' . TBL_ARTICLE . '.palette_id'
        ]];
        $select = TBL_ARTICLE . '.id,' . TBL_ARTICLE . '.code,' . TBL_ARTICLE . '.nom,' . TBL_ARTICLE . '.client_code,' . TBL_ARTICLE . '.client_nom,' . TBL_ARTICLE . '.quantite,' . TBL_ARTICLE . '.lot,' . TBL_ARTICLE . '.dluo,' . TBL_ARTICLE . '.unite_pcb,' . TBL_ARTICLE . '.palettisation,' . TBL_ARTICLE . '.unite_stockage,'  . TBL_ARTICLE . '.observation,' . TBL_ARTICLE . '.qr_code_text,' . TBL_ARTICLE . '.qr_code_image,' . TBL_PALETTE . '.code as palette_code';
        $arrData = $crud->getDataById(array(TBL_ARTICLE . '.id' => intval($id)), $arrJoin, $select);
        return $arrData;
    }

    public function viewPdf(array $dataList)
    {
        $pdf = new \TCPDF();

        $pdf->SetMargins(5, 5, 5);
        $pdf->SetAutoPageBreak(true, 5);
        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->SetFont('helvetica', '', 10);

        $chunks = array_chunk($dataList, 4);

        foreach ($chunks as $pageItems) {

            $pageItems = array_pad($pageItems, 4, null);

            $pdf->AddPage();

            // 🔥 DIMENSIONS PAGE A4 (mm)
            $pageW = 210;
            $pageH = 297;

            $halfW = $pageW / 2;
            $halfH = $pageH / 2;

            // 🔥 HAUT GAUCHE
            $this->renderBlock($pdf, $pageItems[0], 5, 5, $halfW - 10, $halfH - 10);

            // 🔥 HAUT DROITE
            $this->renderBlock($pdf, $pageItems[1], $halfW + 5, 5, $halfW - 10, $halfH - 10);

            // 🔥 BAS GAUCHE
            $this->renderBlock($pdf, $pageItems[2], 5, $halfH + 5, $halfW - 10, $halfH - 10);

            // 🔥 BAS DROITE
            $this->renderBlock($pdf, $pageItems[3], $halfW + 5, $halfH + 5, $halfW - 10, $halfH - 10);
        }

        $fileName = 'palettes_' . date('Ymd_His') . '.pdf';
        return $this->response
            ->setContentType('application/pdf')
            ->setHeader('Content-Disposition', 'inline; filename="' . $fileName . '"')
            ->setBody($pdf->Output($fileName, 'S'));
    }

    public function renderBlock(\TCPDF $pdf, ?array $item, float $x, float $y, float $w, float $h): void
    {
        if (!$item) return;

        $html = view('impression/pdf_view', [
            'item' => $item
        ]);

        $pdf->writeHTMLCell($w, $h, $x, $y, $html, 0, 0, false, true, 'C', true);
    }
}
