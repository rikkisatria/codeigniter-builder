<?php

namespace App\Libraries;

use App\Controllers\BaseController;
// use App\Models\Sub_tahap_model;
// use App\Models\Booking_model;

class Modular extends BaseController
{
    public function __construct()
    {
        // $this->booking_model = new Booking_model();
    }

    public function title()
    {
        // return $this->setting->title;
    }


    public function header()
    {
        $data = [];
        return view('layout/header', $data);
    }

    public function footer()
    {
        $data = [];
        return view('layout/footer', $data);
    }

    public function menu()
    {
        // $akses = $this->akses;
        $data = [
            // "akses" => $akses
            // 'jabatan' => !empty(session()->nip) ? session()->nip : session()->nik,
            // 'nama' => session()->nama_lengkap,
            // 'email' => session()->email,
            // 'level' => session()->level,
        ];
        return view('layout/menu', $data);
    }
}
