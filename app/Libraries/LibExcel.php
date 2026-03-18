<?php

namespace App\Libraries;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class LibExcel
{
    public function getListColExcel()
    {
        $arr_col = array("1" => "A", "2" => "B", "3" => "C", "4" => "D", "5" => "E", "6" => "F", "7" => "G", "8" => "H", "9" => "I", "10" => "J", "11" => "K", "12" => "L", "13" => "M", "14" => "N", "15" => "O", "16" => "P", "17" => "Q", "18" => "R", "19" => "S", "20" => "T", "21" => "U", "22" => "V", "23" => "W", "24" => "X", "25" => "Y", "26" => "Z", "27" => "AA", "28" => "AB", "29" => "AC", "30" => "AD", "31" => "AE", "32" => "AF", "33" => "AG", "34" => "AH", "35" => "AI", "36" => "AJ", "37" => "AK", "38" => "AL", "39" => "AM", "40" => "AN", "41" => "AO", "42" => "AP", "43" => "AQ", "44" => "AR", "45" => "AS", "46" => "AT", "47" => "AU", "48" => "AV", "49" => "AW", "50" => "AX", "51" => "AY", "52" => "AZ", "53" => "BA", "54" => "BB", "55" => "BC", "56" => "BD", "57" => "BE", "58" => "BF", "59" => "BG", "60" => "BH", "61" => "BI", "62" => "BJ", "63" => "BK", "64" => "BL", "65" => "BM", "66" => "BN", "67" => "BO", "68" => "BP", "69" => "BQ", "70" => "BR", "71" => "BS", "72" => "BT", "73" => "BU", "74" => "BV", "75" => "BW", "76" => "BX", "77" => "BY", "78" => "BZ", "79" => "CA", "80" => "CB", "81" => "CC", "82" => "CD", "83" => "CE", "84" => "CF", "85" => "CG", "86" => "CH", "87" => "CI", "88" => "CJ", "89" => "CK", "90" => "CL", "91" => "CM", "92" => "CN", "93" => "CO", "94" => "CP", "95" => "CQ", "96" => "CR", "97" => "CS", "98" => "CT", "99" => "CU", "100" => "CV", "101" => "CW", "102" => "CX", "103" => "CY", "104" => "CZ", "105" => "DA", "106" => "DB", "107" => "DC", "108" => "DD", "109" => "DE", "110" => "DF", "111" => "DG", "112" => "DH", "113" => "DI");
        return $arr_col;
    }

    public function setTitleOfExcel($sheet, $nb_header_column, $lig, $title)
    {
        $arr_col = $this->getListColExcel();
        $sheet->setCellValue('A' . $lig, $title);
        $sheet->mergeCells('A' . $lig . ':' . $arr_col[$nb_header_column] . $lig);
        $this->setStyleCell(1, $lig, "title", $sheet);
    }

    public function setColumHeader($lig, $arr_columns_title, $sheet)
    {
        $arr_col = $this->getListColExcel();
        for ($col = 0; $col < count($arr_columns_title); $col++) {
            $sheet->setCellValue($arr_col[$col + 1] . $lig, $arr_columns_title[$col]);
            $sheet->getColumnDimension($arr_col[$col + 1])->setAutoSize(true);
            $this->setStyleCell($col + 1, $lig, "header", $sheet);
        }
    }

    public function setDataDetail($arr)
    {
        $arr_data = array();
        foreach ($arr as $key => $val) {
            $i = 1;
            if (is_array($val)) {
                foreach ($val as $k => $v) {
                    if ($i > 0)
                        $arr_data[$key + 1][$i]  = $v;
                    $i++;
                }
            }
        }
        return $arr_data;
    }

    public function fetchAllData_($arr, $nb_header_column, $line_filter, $lig, $lig_detail, $sheet)
    {
        $valeur = "";
        $col = 1;
        //$lig_detail = $lig + 1;
        $arr_data = $this->setDataDetail($arr);
        $arr_col = $this->getListColExcel();
        for ($k = 1; $k <= count($arr_data); $k++) {
            for ($i = 1; $i < ($nb_header_column + 1); $i++) {
                if ($col == ($nb_header_column + 1)) {
                    if ($lig == (count($arr_data) + 1)) {
                        break;
                    } else {
                        $col = 1;
                        $lig++;
                        $lig_detail++;
                    }
                }
                $valeur = $arr_data[$k][$col];
                $sheet->setCellValue($arr_col[$col] . $lig_detail, $valeur);
                $this->setStyleCell($col, $lig_detail, "data", $sheet);
                $col++;
            }
        }
        if (!empty($line_filter)) {
            $sheet->setAutoFilter('A' . $line_filter . ':' . $arr_col[$nb_header_column] . $lig_detail);
        }
        return $lig_detail;
    }

    public function fetchAllData($arr, $nb_header_column, $line_filter, $lig, $lig_detail, $sheet, $colonneTotal = null)
    {
        $valeur = "";
        $col = 1;
        $arr_data = $this->setDataDetail($arr);
        $arr_col = $this->getListColExcel();

        // tableau pour stocker les totaux par colonne
        $totaux = [];

        for ($k = 1; $k <= count($arr_data); $k++) {
            for ($i = 1; $i <= $nb_header_column; $i++) {
                if ($col == ($nb_header_column + 1)) {
                    if ($lig == (count($arr_data) + 1)) {
                        break;
                    } else {
                        $col = 1;
                        $lig++;
                        $lig_detail++;
                    }
                }

                $valeur = $arr_data[$k][$col];
                $sheet->setCellValue($arr_col[$col] . $lig_detail, $valeur);
                $this->setStyleCell($col, $lig_detail, "data", $sheet);

                // 👉 si la colonne demandée est celle-ci, on cumule
                if ($colonneTotal !== null && $col == $colonneTotal && is_numeric($valeur)) {
                    if (!isset($totaux[$col])) {
                        $totaux[$col] = 0;
                    }
                    $totaux[$col] += $valeur;
                }

                $col++;
            }
        }

        // 👉 écrire le total à la fin
        if ($colonneTotal !== null && isset($totaux[$colonneTotal])) {
            $lastRow = $lig_detail + 1; // ligne après les données
            $colLettre = $arr_col[$colonneTotal];
            $sheet->setCellValue($colLettre . $lastRow, $totaux[$colonneTotal]);
            $this->setStyleCell($colonneTotal, $lastRow, "total", $sheet);
        }

        // AutoFilter
        if (!empty($line_filter)) {
            $sheet->setAutoFilter('A' . $line_filter . ':' . $arr_col[$nb_header_column] . $lig_detail);
        }

        return $lig_detail;
    }


    public function setStyleCell($col, $lig, $type, $sheet)
    {
        $arr_style = $this->loadStyle();
        $arr_col   = $this->getListColExcel();
        $sheet->getStyle($arr_col[$col] . $lig)->applyFromArray($arr_style[$type]);
    }

    public function downloadExcel($spreadsheet, $rep, $fileName)
    {
        /*$writer = new Xlsx($spreadsheet);
		$writer->save("upload/" . $fileName);
		header("Content-Type: application/vnd.ms-excel");
		redirect(base_url() . "/upload/" . $fileName);*/

        /*$writer = new Xlsx($spreadsheet);
		$writer->save($rep);

		$spreadsheet->disconnectWorksheets();
		unset($spreadsheet);

		$file = file_get_contents($rep);

		unlink($rep);
		return $this->response->download($fileName, $file);*/
    }

    public function loadStyle()
    {
        $style = array();
        $style["title"] = array(
            'font' => array(
                'bold' => true,
                'size' => 12
            ),
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'wrap'        => true,
                'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'rotation'   => 0
            )
        );
        $style["header"] = array(
            'font' => array(
                'bold' => true,
                'size' => 11
            ),
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'wrap'        => true,
                'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'rotation'   => 0
            ),
            'fill' => array(
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => ['rgb' => 'DDDDDD']
            ),
            'borders' => array(
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => [
                        'rgb' => '000000'
                    ]
                ]
            )
        );
        $style["data"] = array(
            /*'alignment' => array(
				'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
				'wrap'   	 => true,
				'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
				'rotation'   => 0
			),*/
            'borders' => array(
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => [
                        'rgb' => '000000'
                    ]
                ]
            ),
            'font' => array(
                'size' => 10
            ),
        );
        $style["total"] = array(
            /*'alignment' => array(
				'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
				'wrap'   	 => true,
				'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
				'rotation'   => 0
			),*/
            'borders' => array(
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => [
                        'rgb' => '000000'
                    ]
                ]
            ),
            'font' => array(
                'size' => 10,
                'bold' => true
            ),
        );
        $style["bordeur_outline"] = [
            'borders' => [
                'outline' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
        ];
        $style["border"] = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' =>  \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ]
            ]
        ];
        $style["alignement"] = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ];
        $style["border_bold"] = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' =>  \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ]
            ],
            'font' => array(
                'bold' => true
            ),
        ];
        return $style;
    }


    /** Insertion des données 
     * $line : le numéro de ligne pour insérer les données
     * $arrData : tableau de données à insérer (getResult)
     * $sheet : contient l'objet active sheet
     * $border : pour les bordures
     * $arrTotalOfColumn :  tableau pour calculer les valeurs dans une ou plusieurs colonnes
     */
    public function treatData($line, $arrData, $sheet, $arrTotalOfColumn = [], $border = true)
    {
        $arrColumn =  $this->getListColExcel();
        $arr_style = $this->loadStyle();
        $i = 0;
        if (!empty($arrData)) {
            foreach ($arrData as $value) {
                $j = 0;
                foreach ($value as $v) {
                    $sheet->setCellValue($arrColumn[$j + 1] . ($line + $i), $v);
                    $sheet->getColumnDimension($arrColumn[$j + 1])->setAutoSize(true);
                    if ($border == true) {
                        $sheet->getStyle($arrColumn[$j + 1] . ($line + $i))->applyFromArray($arr_style['data']);
                    }
                    $j++;
                }
                $i++;
            }
            if (!empty($arrTotalOfColumn)) {
                foreach ($arrTotalOfColumn as $key => $value) {
                    $this->totalValue($value, $line, $i, $sheet);
                }
            }
            return $line + $i;
        } else {
            return $sheet->setCellValue($arrColumn[1] . ($line), "Le tableau de données (contenu) est vide");
        }
    }

    /** Total des valeurs dans une colonne 
     * $column :  colonnes
     * $line : le numéro de la première ligne pour les données (contenu)
     * $i : itération de la numéro de la ligne
     * $sheet : contient l'objet active sheet
     * $border : pour les bordures
     */
    public function totalValue($column, $line, $i, $sheet, $border = true)
    {
        $arr_style = $this->loadStyle();
        $endLine =  $line + $i - 1;
        $sheet->setCellValue($column . $line + $i, "=SUM(" . $column . $line . ":" . $column . $endLine . ")");
        if ($border == true) {
            $sheet->getStyle($column . ($line + $i))->applyFromArray($arr_style['total']);
        }
    }

    public function simpleMergeCell($sheet, $column1, $column2, $text, $txtAlign, $border = true,  $bold = false, $size = 11, $type = null, $vertical = null)
    {
        $arr_style = $this->loadStyle();
        $sheet->mergeCells($column1 . ':' . $column2);
        $col1 = preg_replace("/[0-9]/", "", $column1);
        $sheet->getColumnDimension($col1)->setAutoSize(true);
        $style = $sheet->getStyle($column1);
        $font = $style->getFont();
        $font->setSize($size);
        if ($border == true) {
            $sheet->getStyle($column1 . ':' . $column2)->applyFromArray($arr_style['data']);
        }
        if ($txtAlign == 1) {
            $sheet->getStyle($column1 . ':' . $column2)->getAlignment()->setHorizontal('left');
        }
        if ($txtAlign == 2) {
            $sheet->getStyle($column1 . ':' . $column2)->getAlignment()->setHorizontal('center');
        }
        if ($txtAlign == 3) {
            $sheet->getStyle($column1 . ':' . $column2)->getAlignment()->setHorizontal('right');
        }
        if ($bold == true) {
            $font->setBold(true);
        }
        if ($type != null) {
            $sheet->getStyle($column1)->applyFromArray($arr_style[$type]);
        }
        if ($vertical != null) {
            $alignment = $style->getAlignment();
            $alignment->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        }
        $sheet->setCellValue($column1, $text);
    }

    public function addValue($sheet, $column1, $text, $size = 11, $bold = false, $border = true, $txtAlign = 1, $autoSize = true, $type = null)
    {
        $arr_style = $this->loadStyle();
        $col1 = preg_replace("/[0-9]/", "", $column1);
        $style = $sheet->getStyle($column1);
        $font = $style->getFont();
        $font->setSize($size);
        if ($autoSize == true) {
            $sheet->getColumnDimension($col1)->setAutoSize(true);
        } else {
            $sheet->getColumnDimension($col1)->setAutoSize(false);
        }
        if ($border == true) {
            $sheet->getStyle($column1)->applyFromArray($arr_style['data']);
        }
        if ($bold == true) {
            $font->setBold(true);
        }
        if ($text != "") {
            $sheet->setCellValue($column1, $text);
        }
        if ($txtAlign == 1) {
            $sheet->getStyle($column1)->getAlignment()->setHorizontal('left');
        }
        if ($txtAlign == 2) {
            $sheet->getStyle($column1)->getAlignment()->setHorizontal('center');
        }
        if ($txtAlign == 3) {
            $sheet->getStyle($column1)->getAlignment()->setHorizontal('right');
        }
        if ($type != null) {
            $sheet->getStyle($column1)->applyFromArray($arr_style[$type]);
        }
    }

    public function alignVertical($sheet, $cellule, $style = null)
    {
        $arr_style = $this->loadStyle();
        $style = $sheet->getStyle($cellule); // Ex : $cellule = A1
        $alignment = $style->getAlignment();
        if ($style != null) {
            $sheet->getStyle($cellule)->applyFromArray($arr_style[$style]);
        }
        return $alignment->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
    }
}
