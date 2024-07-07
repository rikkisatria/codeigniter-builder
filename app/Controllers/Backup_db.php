<?php

namespace App\Controllers;

class Backup_db extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        $dbname = $db->database;
        $path =  './dbs/';            // change path here
        $filename = $dbname . '_' . date('dMY_Hi') . '.sql';   // change file name here
        $prefs = [
            'filename' => $filename
        ];              // I only set the file name, for complete prefs see below 

        $util = (new \CodeIgniter\Database\Database())->loadUtils($db);
        $backup = $util->backup($prefs, $db);

        if (!write_file($path . $filename . '.gz', $backup)) {
            echo 'Unable to write the file';
        } else {
            echo 'File written!';
        }
        // return $this->response->download($path . $filename . '.gz', null);
    }
}
