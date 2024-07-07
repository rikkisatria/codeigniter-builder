<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

helper('base_helper');

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {

        if (!empty($arguments) && $arguments[0] == 'survey') {
            if (!session()->login_survey) {
                return redirect()->to(getenv('AKSES_PAGE'));
            }
        } else if (!session()->logged_in) {
            // return redirect()->to(site_url() . getenv('AKSES_PAGE'));
            return redirect()->to(getenv('AKSES_PAGE'));
        }

        if (session()->nama)
            define('session_nama', session()->nama);


        // if (!empty($arguments) && page_access($arguments[0]) == false) {
        //     // dj(page_access($arguments[0], session()->level));
        //     return redirect()->to(site_url() . 'akses/logout');
        // }

        // if (!session()->islogin) {
        //     echo "invalid";
        //     return redirect()->to(base_url('/s'))->with('error', "Invalid Credential");
        // }
        // Do something here
    }

    //--------------------------------------------------------------------

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
    }

    function failed($str = '', $url = '')
    {
        die(json_encode(["status" => 0, "return" => $str, "url" => $url]));
    }
}
