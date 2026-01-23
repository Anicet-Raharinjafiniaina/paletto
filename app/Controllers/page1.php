<?php

namespace App\Controllers;

class Page1 extends BaseController
{

    public function index()
    {

        $arr['titre'] = "ma page1";
        $arr['request_ajax'] = 0;
        if ($this->request->isAJAX()) {
            $arr['titre'] = "ma page1 ajax";
            $arr['request_ajax'] = 1;
            sleep(5);
            echo view('page1', $arr);
            return;
        }
        echo view('page1', $arr);
    }
}
