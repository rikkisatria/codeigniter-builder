<?php

namespace Modules\Contoh\Controllers;

use App\Controllers\BaseController;
// use Modules\Project1\Models\Project_name;

class Home extends BaseController
{
    public function __construct()
    {
    }

    public function index()
    {
        // die('aaa');
        $page_data = [];
        return view("\Modules\Contoh\Views\home", $page_data);
    }
}
