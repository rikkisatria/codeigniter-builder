<?php

namespace App\Models;

use CodeIgniter\Model;

class Kantor_model extends Model
{
    protected $table = 'kantor';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = false;
    protected $allowedFields = [
        "id",
        "sub_id",
        "id2",
        "inisial",
        "nama",
        "alamat",
        "deskripsi",
        "koordinat",
        "radius",
        "status",
        "no_dev",
    ];
}
