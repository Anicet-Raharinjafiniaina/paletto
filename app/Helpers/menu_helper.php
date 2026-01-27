<?php

use App\Models\CrudModel;


if (!function_exists('getMenu')) {
    function getMenu(): array
    {
        $crud_section = new CrudModel('section');
        $crud_page = new CrudModel('page');
        $arr_join = [
            array(
                'table' => 'section',
                'on' => 'section.id = page.section_id',
                'type' => 'left'
            )
        ];

        $arr_section = $crud_section->getAllData(array('actif' => 1), [], '*', 'ordre');

        $crud_acces = new CrudModel(TBL_ACCES);
        $profil_id = session()->get('utilisateur')['profil_id'];
        $arr_acces_base = $crud_acces->getDataById(array('profil_id' => $profil_id));
        $arr_acces_base =  array_map('intval', explode(',', str_replace(['{', '}'], '',  $arr_acces_base->page_id)));

        $arr_menu = [];
        if (!empty($arr_section)) {
            foreach ($arr_section as $key => $section) {
                $arr_page = $crud_page->getAllData(array('page.actif' => 1, 'page.show_menu' => 1, 'section_id' => $section->id), $arr_join, 'page.id,page.libelle as page, section.icone as icone_section, page.icone as icone_page, page.lien,image', 'page.ordre');
                if (!empty($arr_page)) {
                    foreach ($arr_page as $key => $page) {
                        if (in_array($page->id,  (array)$arr_acces_base)) {
                            $arr_menu[$section->libelle][] = $page;
                        }
                    }
                }
            }
        }
        return $arr_menu;
    }
}
