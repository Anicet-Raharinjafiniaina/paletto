<?php

namespace App\Controllers;

use App\Libraries\LibExcel;
use App\Models\CrudModel;
use App\Models\ExportModel;
use DateTime;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Export extends BaseController
{
    public function index()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(10);
        if (!$is_ok) {
            return $this->response->setBody(
                '<script>window.location.href="' . base_url('/') . '";</script>'
            );
        }
        $this->load();
    }

    public function load()
    {
        $arr['titre'] = "Export des données";
        $arr['request_ajax'] = 0;
        if ($this->request->isAJAX()) {
            $arr['request_ajax'] = 1;
            echo view('export', $arr);
            return;
        }
        echo view('export', $arr);
    }

    public function doExport()
    {
        $acces  = new Acces();
        $is_ok = $acces->is_ok(10);
        if (!$is_ok) {
            return $this->response->setBody(
                '<script>window.location.href="' . base_url('/') . '";</script>'
            );
        }
        $model = new ExportModel();
        $typeExport = trim($this->request->getPost('type') ?? '');
        $periode = trim($this->request->getPost('periode') ?? '');
        $arrDate = $this->parseDateRange($periode);
        switch ($typeExport) {
            case 1:
                $model->historique(39);
                return $this->exportExcel(
                    'Mouvement',
                    'Mouvements.xlsx',
                    ["Date du mouvement", "Type", "Emplacement", "Palette", "Code client", "Nom du client", "Code article", "Nom de l'article", "DLUO", "Unité PCB", "Quantité", "Lot", "Palettisation", "Unité de stockage"],
                    'getAllMouvement',
                    [$arrDate['dateDebut'], $arrDate['dateFin']]
                );
            case 2:
                $model->historique(40);
                return $this->exportExcel(
                    'Emplacement',
                    'Emplacement.xlsx',
                    ["Emplacement", "Statut", "Code de l'entrepôt", "Nom de l'entrepôt", "Localisation de l'entrepôt"],
                    'getEmplacement' // string, pas tableau
                );
            case 3:
                $model->historique(41);
                return $this->exportExcel(
                    'Entrepôt',
                    'Entrepôt.xlsx',
                    ["Emplacement", "Statut", "Code de l'entrepôt", "Nom de l'entrepôt", "Localisation de l'entrepôt", "Code du client", "Nom du client", "Code de l'article", "Nom de l'article"],
                    'getEmplacementClient'
                );
            case 4:
                $model->historique(42);
                return $this->exportExcel(
                    'Palette',
                    'Palette.xlsx',
                    ["Code", "Statut"],
                    'getPalette'
                );
            case 5:
                $model->historique(43);
                return $this->exportExcel(
                    'Entrepot',
                    'Entrepôt.xlsx',
                    ["Code", "Nom", "Localisation"],
                    'getEntrepot'
                );
            case 6:
                $model->historique(44);
                return $this->exportExcel(
                    'Article',
                    'Article.xlsx',
                    ["Code article", "Nom de l'article", "Unité PCB", "Palettisation", "Unité de stockage"],
                    'getArticle'
                );
        }
    }

    private function exportExcel($sheetTitle, $fileName, $columns, $methodName, $methodParams = [])
    {
        $excel = new LibExcel();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $path = URL_FILE;
        if (!file_exists($path)) mkdir($path, 0777, true);

        /** Infos user */
        $session = \Config\Services::session();
        $user = $session->get('utilisateur');
        $sheet->setCellValue('A1', "Exporté(e) par : ");
        $sheet->setCellValue('B1', $user['nom'] . " " . $user['prenom']);
        $sheet->setCellValue('A2', "Profil : ");
        $sheet->setCellValue('B2', $user['profil']);
        $sheet->setCellValue('A3', "Date de l'export : ");
        $sheet->setCellValue('B3', Date('d/m/Y H:i:s'));
        /** /Infos user */

        $nbHeaderColumn = count($columns);
        $excel->setTitleOfExcel($sheet, $nbHeaderColumn, 7, 'Liste des ' . strtolower($sheetTitle));
        $sheet->setTitle($sheetTitle);
        $excel->setColumHeader(9, $columns, $sheet);

        // Appelle la méthode avec paramètres
        $data = call_user_func_array([$this, $methodName], $methodParams);

        if (empty($data)) {
            return $this->response->download("Information.txt", "Aucune donnée correspondante.");
        }

        $excel->fetchAllData($data, $nbHeaderColumn, 9, 1, 10, $sheet);

        $filePath = $path . $fileName;
        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);

        $fileContent = file_get_contents($filePath);
        unlink($filePath);

        return $this->response->download($fileName, $fileContent);
    }

    public function getAllMouvement($dateDebut, $dateFin)
    {
        $crud = new CrudModel(TBL_MOUVEMENT);
        $arrJoin = [
            ['table' => TBL_MOUVEMENT_TYPE, 'type' => 'LEFT', 'on' => TBL_MOUVEMENT_TYPE . '.id=' . TBL_MOUVEMENT . '.mouvement_type_id'],
            ['table' => TBL_EMPLACEMENT_ADRESSE, 'type' => 'LEFT', 'on' => TBL_EMPLACEMENT_ADRESSE . '.id=' . TBL_MOUVEMENT . '.emplacement_id'],
            ['table' => TBL_PALETTE, 'type' => 'LEFT', 'on' => TBL_PALETTE . '.id=' . TBL_MOUVEMENT . '.palette_id'],
            ['table' => TBL_ARTICLE, 'type' => 'LEFT', 'on' => TBL_ARTICLE . '.id=' . TBL_MOUVEMENT . '.article_id'],
        ];

        $select = implode(", ", [
            TBL_MOUVEMENT . ".date_mouvement",
            TBL_MOUVEMENT_TYPE . ".type",
            TBL_EMPLACEMENT_ADRESSE . ".qr_code_texte",
            TBL_ARTICLE . ".qr_code_text",
            TBL_ARTICLE . ".client_code",
            TBL_ARTICLE . ".client_nom",
            TBL_ARTICLE . ".code",
            TBL_ARTICLE . ".nom",
            TBL_ARTICLE . ".dluo",
            TBL_ARTICLE . ".unite_pcb",
            TBL_ARTICLE . ".quantite",
            TBL_ARTICLE . ".lot",
            TBL_ARTICLE . ".palettisation",
            TBL_ARTICLE . ".unite_stockage"
        ]);

        $where = [
            "DATE(" . TBL_MOUVEMENT . ".date_mouvement) >= " => "'" . $dateDebut . "'",
            "DATE(" . TBL_MOUVEMENT . ".date_mouvement) <= " => "'" . $dateFin . "'"
        ];
        return $crud->getAllDataArray($where, $arrJoin, $select);
    }

    public function getEmplacement()
    {
        $crud = new CrudModel(VIEW_EMPLACEMENT_ADRESSE);
        $arrJoin = [
            [
                'table' => TBL_ENTREPOT,
                'type' => 'LEFT',
                'on' => TBL_ENTREPOT . '.id = ' . VIEW_EMPLACEMENT_ADRESSE . '.entrepot_id'
            ]
        ];
        $select = VIEW_EMPLACEMENT_ADRESSE . ".qr_code_texte," . VIEW_EMPLACEMENT_ADRESSE . ".statut," . TBL_ENTREPOT . ". code as code_entrepot," . TBL_ENTREPOT . ". nom as nom_entrepot," . TBL_ENTREPOT . ". localisation as localisation_entrepot";
        return $crud->getAllDataArray([], $arrJoin, $select);
    }

    public function getPalette()
    {
        $crud = new CrudModel(TBL_PALETTE);
        $arrJoin = [
            ['table' => TBL_PALETTE_STATUT, 'type' => 'LEFT', 'on' => TBL_PALETTE_STATUT . '.id=' . TBL_PALETTE . '.palette_statut_id'],
        ];
        $select = TBL_PALETTE . ".code," . TBL_PALETTE_STATUT . ".statut";
        return $crud->getAllDataArray([TBL_PALETTE . ".flag_suppression" => 0], $arrJoin, $select);
    }

    public function getEntrepot()
    {
        $crud = new CrudModel(TBL_ENTREPOT);
        $select = TBL_ENTREPOT . ".code, " . TBL_ENTREPOT . ".nom, " . TBL_ENTREPOT . ".localisation";
        return $crud->getAllDataArray([TBL_ENTREPOT . ".flag_suppression" => 0], [], $select);
    }

    public function getArticle()
    {
        $crud = new CrudModel(TBL_ARTICLE_HORS_X3);
        $select = TBL_ARTICLE_HORS_X3 . ".code, "
            . TBL_ARTICLE_HORS_X3 . ".nom, "
            . TBL_ARTICLE_HORS_X3 . ".unite_pcb, "
            . TBL_ARTICLE_HORS_X3 . ".palettisation, "
            . TBL_ARTICLE_HORS_X3 . ".unite_stockage";
        return $crud->getAllDataArray([TBL_ARTICLE_HORS_X3 . ".flag_suppression" => 0], [], $select);
    }

    public function getEmplacementClient()
    {
        $crud = new CrudModel(VIEW_EMPLACEMENT_ADRESSE);
        $arrJoin = [
            [
                'table' => TBL_MOUVEMENT,
                'type' => 'LEFT',
                'on' => TBL_MOUVEMENT . '.emplacement_id = ' . VIEW_EMPLACEMENT_ADRESSE . '.emplacement_adresse_id'
            ],
            [
                'table' => TBL_ARTICLE,
                'type' => 'LEFT',
                'on' => TBL_ARTICLE . '.id = ' . TBL_MOUVEMENT . '.article_id'
            ],
            [
                'table' => TBL_ENTREPOT,
                'type' => 'LEFT',
                'on' => TBL_ENTREPOT . '.id = ' . VIEW_EMPLACEMENT_ADRESSE . '.entrepot_id'
            ]
        ];
        $select =
            VIEW_EMPLACEMENT_ADRESSE . ".qr_code_texte," .
            VIEW_EMPLACEMENT_ADRESSE . ".statut," .
            TBL_ENTREPOT . ". code as code_entrepot," .
            TBL_ENTREPOT . ". nom as nom_entrepot," .
            TBL_ENTREPOT . ". localisation as localisation_entrepot," .
            TBL_ARTICLE . ".client_code," .
            TBL_ARTICLE . ".client_nom," .
            TBL_ARTICLE . ".code," .
            TBL_ARTICLE . ".nom";
        $where = [
            VIEW_EMPLACEMENT_ADRESSE . '.statut_id' => 2, // occupé
            TBL_ARTICLE . ".flag_suppression" => 0
        ];
        return $crud->getAllDataArray($where, $arrJoin, $select);
    }

    function parseDateRange($range, $delimiter = ' - ')
    {
        $dates = explode($delimiter, $range);
        return [
            'dateDebut' => $this->transformDate($dates[0]),
            'dateFin'   => $this->transformDate($dates[1])
        ];
    }

    function transformDate($date)
    {
        $date = trim($date);
        $formats = ['d/m/Y', 'd-m-Y', 'Y/m/d', 'Y-m-d'];  // Définir les formats possibles (PHP DateTime format)
        foreach ($formats as $format) {
            $dt = DateTime::createFromFormat($format, $date);
            if ($dt && $dt->format($format) === $date) {
                return $dt->format('Y-m-d'); // Retourne toujours YYYY-MM-DD
            }
        }
    }
}
