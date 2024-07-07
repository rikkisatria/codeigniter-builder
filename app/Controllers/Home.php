<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        // die(site_url());
        return view('welcome_message');
    }
}
