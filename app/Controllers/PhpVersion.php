<?php

namespace App\Controllers;

class PhpVersion extends BaseController
{
    public function index()
    {
        echo phpinfo();
    }
}
