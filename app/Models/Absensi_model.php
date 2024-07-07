<?php

namespace App\Models;

use CodeIgniter\Model;

use App\Models\Kantor_model;
use App\Models\Pegawai_model;
use App\Models\Shift_model;
use App\Models\Validator_model;
use App\Models\Level_model;
use App\Models\Status_absensi_model;

class Absensi_model extends Model
{
    protected $table = 'absensi';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = false;
    protected $allowedFields = [
        "id",
        "id_pegawai",
        "id2_pegawai",
        "nama_pegawai",
        "shift",
        "level",
        "opd_id",
        "opd_asal",

        "koodrinat_masuk",
        "jam_masuk",
        "opd_masuk",
        "jarak_masuk",
        "perangkat_masuk",

        "koodrinat_pulang",
        "jam_pulang",
        "opd_pulang",
        "jarak_pulang",
        "perangkat_pulang",

        "total_waktu",
        "status",
        "keterangan",
        "edited_by",
        "edited_at",
        "tanggal_absen"
    ];

    public $kantor_model;
    public $pegawai_model;
    public $shift_model;
    public $validator_model;
    public $level_model;
    public $status_absensi_model;
    public $db;

    public function __construct()
    {
        parent::__construct();
        $this->kantor_model = new Kantor_model();
        $this->pegawai_model = new Pegawai_model();
        $this->shift_model = new Shift_model();
        $this->validator_model = new Validator_model();
        $this->level_model = new Level_model();
        $this->status_absensi_model = new Status_absensi_model();

        $this->db = \Config\Database::connect();
    }

    public function absen($id_pegawai = '', $data = [])
    {
        // dj($data_absen);
        $time = date('H:i', strtotime($data['jam']));
        $absensi = $this->status($id_pegawai, $time); // ubah jam disini
        $data_absen = [
            "id_absensi" => $absensi['id_absensi'],
            "koodrinat" => $data['koodrinat'],
            "jam" => $data['jam'],
            "opd" => $data['opd'],
            "jarak" => $data['jarak'],
            "perangkat" => $data['perangkat'],
            "status" => $absensi['status'],
            "tanggal" => $absensi['tanggal'],
        ];

        if ($absensi['jenis'] == 'masuk') {
            $this->masuk($id_pegawai, $data_absen);
            return success_return('Berhasil absen masuk');
        } else
        if ($absensi['jenis'] == 'pulang') {
            $this->pulang($id_pegawai, $data_absen);
            return success_return('Berhasil absen pulang');
        } else {
            return success_return($absensi['keterangan']);
        }
    }

    public function penyesuaian($id_pegawai = '', $data = [])
    {

        $get_pegawai = $this->pegawai_model->find($id_pegawai);
        if (!$get_pegawai) return failed_return('Pegawai tidak valid');

        /*
            h   : hadir (jam kerja)
            tk  : tk

            c   : cuti
            dl  : dinas luar
            i   : izin
            s   : sakit
            tb  : tugas belajar
        */

        /*
        [
            "penyesuaian" => $penyesuaian,
            "jam_masuk" => $jam_masuk,
            "jam_pulang" => $jam_pulang,
            "keterangan" => $keterangan
        ]
        */

        $status_absensi = '';
        $keterangan_absensi = '';
        $return_ket = '';
        $tgl_masuk = null;
        $tgl_pulang = null;
        $sekarang = date('Y-m-d');
        $kemarin = date('Y-m-d', strtotime('yesterday'));
        $besok = date('Y-m-d', strtotime('tomorrow'));

        $get_validator = $this->validator_model->where('id_pegawai', $id_pegawai)->first();
        $get_shift = $this->shift_model->find($get_validator['shift']);
        $get_status = $this->status_absensi_model->findAll();
        $status_absensi = [];
        foreach ($get_status as $key => $value) {
            $status_absensi[$value['kode']] = $value['nama'];
        }

        $batas_masuk = $get_shift['batas_masuk'];
        $masuk = $get_shift['masuk'];
        $masuk_dispensasi = $get_shift['masuk_dispensasi'];
        $keluar = $get_shift['keluar'];
        $keluar_dispensasi = $get_shift['keluar_dispensasi'];

        $tgl_masuk = waktu_antara('00:00', '>=' . $batas_masuk, '<' . $keluar_dispensasi) &&
            waktu_antara($data['jam_masuk'], '>=00:00', '<' . $keluar_dispensasi) ? $kemarin : $sekarang;

        $tgl_pulang = waktu_antara('00:00', '>=' . $batas_masuk, '<' . $keluar_dispensasi) &&
            waktu_antara($data['jam_pulang'], '>=00:00', '<' . $keluar_dispensasi) ? $besok : $sekarang;

        if ($data['penyesuaian'] == "h") {

            if (empty($data['jam_masuk'])) {
                return failed_return('Jam masuk harus diisi');
            }

            if (!waktu_antara($data['jam_masuk'], '>=' . $batas_masuk, '<' . $keluar)) {
                return failed_return('Jam masuk harus antara ' . $batas_masuk . ' dan ' . $keluar);
            }

            /*
            Masuk Tepat Waktu
            Terlambat
            Sudah Absen Pulang
            Pulang Cepat
            Pulang Tepat Waktu
            Tidak tersedia
            */

            // dj($tgl_masuk);

            $step_masuk1 = waktu_antara($data['jam_masuk'], '>=' . $batas_masuk, '<' . $masuk) ? 1 : 0;
            $step_masuk2 = waktu_antara($data['jam_masuk'], '>=' . $masuk, '<=' . $masuk_dispensasi) ? 1 : 0;
            $step_masuk3 = waktu_antara($data['jam_masuk'], '>' . $masuk_dispensasi, '<=' . $keluar) ? 1 : 0;
            if ($step_masuk1) $keterangan_absensi = 'Masuk Tepat Waktu';
            else if ($step_masuk2) $keterangan_absensi = 'Masuk Tepat Waktu';
            else $keterangan_absensi = 'Terlambat';

            $status_absensi = 'h';

            //jika jam masuk dan jam pulang tidak kosong
            if (!empty($data['jam_masuk']) && !empty($data['jam_pulang'])) {
                if (!waktu_banding($data['jam_masuk'], $data['jam_pulang'], '<')) {
                    return failed_return('Jam masuk harus sebelum jam pulang');
                }

                if (!waktu_antara($data['jam_pulang'], '>=' . $masuk, '<' . $keluar_dispensasi)) {
                    return failed_return('Jam pulang harus antara ' . $masuk . ' dan ' . $keluar_dispensasi);
                }

                $step_pulang2 = waktu_antara($data['jam_pulang'], '>=' . $masuk, '<=' . $masuk_dispensasi) ? 1 : 0;
                $step_pulang3 = waktu_antara($data['jam_pulang'], '>' . $masuk_dispensasi, '<=' . $keluar) ? 1 : 0;
                $step_pulang4 = waktu_antara($data['jam_pulang'], '>' . $keluar, '<=' . $keluar_dispensasi) ? 1 : 0;
                if ($step_pulang2) $keterangan_absensi = 'Pulang Cepat';
                else if ($step_pulang3) $keterangan_absensi = 'Pulang Cepat';
                else $keterangan_absensi = 'Pulang Tepat Waktu';
            }
            $return_ket = "Penyesuaian Jam Kerja";
        } else
        if ($data['penyesuaian'] == "c") {
            $status_absensi = 'c';
            $return_ket = "Penyesuaian Cuti";
        } else
        if ($data['penyesuaian'] == "dl") {
            $status_absensi = 'dl';
            $return_ket = "Penyesuaian Dinas Luar";
        } else
        if ($data['penyesuaian'] == "i") {
            $status_absensi = 'i';
            $return_ket = "Penyesuaian Izin";
        } else
        if ($data['penyesuaian'] == "s") {
            $status_absensi = 's';
            $return_ket = "Penyesuaian Sakit";
        } else
        if ($data['penyesuaian'] == "tb") {
            $status_absensi = 'tb';
            $return_ket = "Penyesuaian Tugas Belajar";
        } else
        if ($data['penyesuaian'] == "tk") {
            $status_absensi = 'tk';
            $return_ket = "Penyesuaian Tanpa Keterangan";
        }

        $get_absensi = $this->db->table($this->table)->where('id_pegawai', $id_pegawai)->where('tanggal_absen', $tgl_masuk)->get()->getRowArray();
        //cek id_pegawai hari ini

        $jadwal_masuk = !empty($data['jam_masuk']) ? $tgl_masuk . ' ' . $data['jam_masuk'] . ':00' : null;
        $jadwal_pulang = !empty($data['jam_pulang']) ? $tgl_pulang . ' ' . $data['jam_pulang'] . ':00' : null;

        if ($get_absensi) {
            //jika sudah absen
            //-- ambil tanggal awal tepat waktu + jam
            //-- ambil tanggal akhir tepat waktu + jam
            // update

            $update = [
                "jam_masuk" => null,
                "jam_pulang" => null,

                "status" => $status_absensi,
                "keterangan" => $data['keterangan'] ?? $keterangan_absensi,
                "edited_by" => session()->nama,
                "edited_at" => date('Y-m-d H:i:s'),
                "total_waktu" => $data['penyesuaian'] == "h" && (!empty($tgl_pulang) && !empty($tgl_masuk)) ? strtotime($jadwal_pulang) - strtotime($jadwal_masuk) : 0,
            ];

            if ($data['penyesuaian'] == "h") {
                $update["jam_masuk"] = $jadwal_masuk;
                $update["jam_pulang"] = $jadwal_pulang;
            }

            if ($data['penyesuaian'] == "tk") {
                $update["jam_masuk"] = null;
                $update["jam_pulang"] = null;
            }

            // dj($update);

            $this->update($get_absensi['id'], $update);
        } else {
            //persiapkan semua 
            //-- ambil tanggal awal tepat waktu + jam
            //tambah

            $get_validator = $this->validator_model->where('id_pegawai', $get_pegawai['id'])->first();
            $get_shift = $this->shift_model->find($get_validator['shift']);
            $get_level = $this->level_model->find($get_validator['level']);
            $get_opd = $this->kantor_model->where('id', $get_pegawai['opd_id'])->first();

            $tambah = [
                "id" => micro_id('md5'),
                "id_pegawai" => $get_pegawai['id'],
                "id2_pegawai" => $get_pegawai['id_pegawai'],
                "nama_pegawai" => $get_pegawai['nama_peg'],
                "shift" => $get_shift['nama'],
                "level" => $get_level['nama'],
                "opd_id" => $get_opd['id'],
                "opd_id2" => $get_opd['id2'],
                "opd_asal" => $get_opd['nama'],

                "jam_masuk" => null,
                "jam_pulang" => null,

                "status" => $status_absensi,
                "keterangan" => $data['keterangan'] ?? $keterangan_absensi,
                "edited_by" => session()->nama,
                "edited_at" => date('Y-m-d H:i:s'),
                "total_waktu" => $data['penyesuaian'] == "h" && (!empty($tgl_pulang) && !empty($tgl_masuk)) ? strtotime($jadwal_pulang) - strtotime($jadwal_masuk) : 0,

                "tanggal_absen" => $tgl_masuk,
            ];

            if ($data['penyesuaian'] == "h") {
                $tambah["jam_masuk"] = $jadwal_masuk;
                $tambah["jam_pulang"] = $jadwal_pulang;
            }


            // dj($tambah);

            $this->insert($tambah);
        }

        return success_return($return_ket);
    }

    private function masuk($id_pegawai = '', $data = [])
    {

        $get_pegawai = $this->pegawai_model->find($id_pegawai);
        // dj($get_pegawai);
        $get_validator = $this->validator_model->where('id_pegawai', $get_pegawai['id'])->first();
        $get_shift = $this->shift_model->find($get_validator['shift']);
        $get_level = $this->level_model->find($get_validator['level']);
        $get_opd = $this->kantor_model->where('id', $get_pegawai['opd_id'])->first();

        //insert
        $tambah = [
            "id" => micro_id('md5'),
            "id_pegawai" => $get_pegawai['id'],
            "id2_pegawai" => $get_pegawai['id_pegawai'],
            "nama_pegawai" => $get_pegawai['nama_peg'],
            "shift" => $get_shift['nama'],
            "level" => $get_level['nama'],
            "opd_id" => $get_opd['id'],
            "opd_id2" => $get_opd['id2'],
            "opd_asal" => $get_opd['nama'],

            "koodrinat_masuk" => $data['koodrinat'],
            "jam_masuk" => $data['jam'],
            "opd_masuk" => $data['opd'],
            "jarak_masuk" => round($data['jarak'], 2),
            "perangkat_masuk" => $data['perangkat'],

            "status" => $data['status'],
            "status" => 'h',
            "keterangan" => $data['status'] ?? '',
            "tanggal_absen" => $data['tanggal'],
        ];

        // dj($tambah);

        //cek sebelum tambah

        $get_absensi = $this->db->table($this->table)->where('id_pegawai', $get_pegawai['id'])->where('tanggal_absen', $data['tanggal'])->get()->getRowArray();
        if (!$get_absensi) $this->insert($tambah);
    }

    private function pulang($id_pegawai = '', $data = [])
    {
        // dj($data['id_absensi']);
        $get_absensi = $this->db->table($this->table)->where('id', $data['id_absensi'])->get()->getRowArray();

        // if (!$get_absensi) {

        //     $get_pegawai = $this->pegawai_model->find($id_pegawai);
        //     // dj($get_pegawai);
        //     $get_validator = $this->validator_model->where('id_pegawai', $get_pegawai['id'])->first();
        //     $get_shift = $this->shift_model->find($get_validator['shift']);
        //     $get_level = $this->level_model->find($get_validator['level']);
        //     $get_opd = $this->kantor_model->where('id', $get_pegawai['opd_id'])->first();

        //     //insert
        //     $tambah = [
        //         "id" => micro_id('md5'),
        //         "id_pegawai" => $get_pegawai['id'],
        //         "id2_pegawai" => $get_pegawai['id_pegawai'],
        //         "nama_pegawai" => $get_pegawai['nama_peg'],
        //         "shift" => $get_shift['nama'],
        //         "level" => $get_level['nama'],
        //         "opd_id" => $get_opd['id'],
        //         "opd_id2" => $get_opd['id2'],
        //         "opd_asal" => $get_opd['nama'],

        //         "koodrinat_masuk" => $data['koodrinat'],
        //         "jam_masuk" => $data['jam'],
        //         "opd_masuk" => $data['opd'],
        //         "jarak_masuk" => round($data['jarak'], 2),
        //         "perangkat_masuk" => $data['perangkat'],

        //         "status" => $data['status'],
        //         "status" => 'h',
        //         "keterangan" => $data['status'] ?? '',
        //         "tanggal_absen" => $data['tanggal'],
        //     ];

        //     // dj($tambah);

        //     //cek sebelum tambah
        //     $this->insert($tambah);
        // }

        // dj($get_absensi);

        // update
        $update = [
            "koodrinat_pulang" => $data['koodrinat'],
            "jam_pulang" => $data['jam'],
            "opd_pulang" => $data['opd'],
            "jarak_pulang" => round($data['jarak'], 2),
            "perangkat_pulang" => $data['perangkat'],

            "keterangan" => $data['status'] ?? '',
            "total_waktu" => !empty($get_absensi['jam_masuk']) ? (strtotime($data['jam']) - strtotime($get_absensi['jam_masuk'])) : 0,
        ];

        // dj($update);

        $this->update($data['id_absensi'], $update);
    }

    public function status($id_pegawai = '', $time = false)
    {
        $time = $time ?? date('H:i');
        // dj(waktu_antara('16:24', '>10:00', '<=17:00') ? 1 : 0);
        $get_validator = $this->validator_model->where('id_pegawai', $id_pegawai)->first();
        $get_shift = $this->shift_model->find($get_validator['shift']);

        $batas_masuk = $get_shift['batas_masuk'];
        $masuk = $get_shift['masuk'];
        $masuk_dispensasi = $get_shift['masuk_dispensasi'];
        $keluar = $get_shift['keluar'];
        $keluar_dispensasi = $get_shift['keluar_dispensasi'];

        /* periksa absen kemarin
        21:00 - 05:00
        
        kasus
        23:00
        02:00
        
            [----- 10 des --------][------- 11 des ---------]
            |---1----|---2----|---3----|---4----|----5------|
        20:00 -- 21:00 -- 22:00 -- 05:00 -- 06:00 >>>>> 20:00

        1. jika 06:00 -> 20:00 (tombol tidak menyala, hanya menampilkan hasil absen)
        2. dapatkan tanggal dengan cara definisi shift jam batas masuk dan jam batas pulang
        3. cari absen berdasarkan tanggal
        4. kemudian persiapkan part
                step 1 >> 10 des 20:00 -- 10 des 21:00 //
                step 2 >> 10 des 21:00 -- 10 des 22:00 //
                step 3 >> 10 des 22:00 -- 11 des 05:00 //
                step 4 >> 11 des 05:00 -- 11 des 06:00 //
                step 5 >> 11 des 06:00 -- 11 des 20:00 //
        5. logic + jika ada atau tidak
                jika absen tidak ada maka 1,2 masuk tepat waktu, 3,4 terlambat
                jika absen ada maka 2,3 pulang cepat, 4 pulang tepat waktu
        */

        $step1 = waktu_antara($time, '>=' . $batas_masuk, '<' . $masuk) ? 1 : 0;
        $step2 = waktu_antara($time, '>=' . $masuk, '<=' . $masuk_dispensasi) ? 1 : 0;
        $step3 = waktu_antara($time, '>' . $masuk_dispensasi, '<=' . $keluar) ? 1 : 0;
        $step4 = waktu_antara($time, '>' . $keluar, '<=' . $keluar_dispensasi) ? 1 : 0;
        $step5 = waktu_antara($time, '>' . $keluar_dispensasi, '<' . $batas_masuk) ? 1 : 0;

        $cek = 0;
        // $cek = 1;
        if ($cek) {
            echo "step1 : $time :: >=$batas_masuk --- <$masuk = $step1 <br>";
            echo "step2 : $time :: >=$masuk --- <=$masuk_dispensasi = $step2 <br>";
            echo "step3 : $time :: >$masuk_dispensasi --- <=$keluar = $step3 <br>";
            echo "step4 : $time :: >$keluar --- <=$keluar_dispensasi = $step4 <br>";
            echo "step5 : $time :: >$keluar_dispensasi --- <$batas_masuk = $step5 <br>";
            die();
        }

        //jika absen antara batas masuk dan batas pulang 
        $date = date('Y-m-d'); //default hari ini
        if (!$step5) {
            if (
                waktu_antara("00:00", '>=' . $batas_masuk, '<=' . $keluar_dispensasi) &&
                waktu_antara($time, '>00:00', '<=keluar_dispensasi')
            ) {
                //jika terdapat 00:00 pada absensi maka jenis absen malam dan
                $date = date('Y-m-d', strtotime('yesterday'));
            }

            $get_absensi = $this->db->table($this->table)->where('id_pegawai', $id_pegawai)->where('tanggal_absen', $date)->get()->getRowArray();
            // dj($get_absensi);

            if (!$get_absensi) {
                if ($step1 || $step2) {
                    return [
                        'id_absensi' => null,
                        'absensi' => [],
                        'shift' => $get_shift,
                        'waktu' => $time,
                        'tanggal' => $date,
                        'jenis' => 'masuk',               //untuk fungsi absen
                        'keterangan' => 'Absen Masuk',   //untuk status di mobile
                        'status' => 'Masuk Tepat Waktu',        //untuk status db
                    ];
                } else
                if ($step3) {
                    return [
                        'id_absensi' => null,
                        'absensi' => [],
                        'shift' => $get_shift,
                        'waktu' => $time,
                        'tanggal' => $date,
                        'jenis' => 'masuk',               //untuk fungsi absen
                        'keterangan' => 'Absen Masuk',   //untuk status di mobile
                        'status' => 'Terlambat',          //untuk status db
                    ];
                } else
                if ($step4) {
                    return [
                        'id_absensi' => null,
                        'absensi' => [],
                        'shift' => $get_shift,
                        'waktu' => $time,
                        'tanggal' => $date,
                        'jenis' => 'pulang',                //untuk fungsi absen
                        'keterangan' => 'Absen Pulang',     //untuk status di mobile
                        'status' => 'Pulang Tepat Waktu',   //untuk status db
                    ];
                }
            } else {
                if (!empty($get_absensi['jam_pulang'])) {
                    return [
                        'id_absensi' => null,
                        'absensi' => $get_absensi,
                        'shift' => $get_shift,
                        'waktu' => $time,
                        'tanggal' => $date,
                        'jenis' => false,                         //untuk fungsi absen
                        'keterangan' => 'Sudah Absen Pulang',     //untuk status di mobile
                        'status' => 'Sudah Absen Pulang',         //untuk status db
                    ];
                } else
                if ($step1) {
                    return [
                        'id_absensi' => null,
                        'absensi' => $get_absensi,
                        'shift' => $get_shift,
                        'waktu' => $time,
                        'tanggal' => $date,
                        'jenis' => false,                        //untuk fungsi absen
                        'keterangan' => 'Sudah Absen Masuk',     //untuk status di mobile
                        'status' => 'Sudah Absen Masuk',         //untuk status db
                    ];
                } else
                if ($step2 || $step3) {
                    // return [
                    //     'id_absensi' => $get_absensi['id'],
                    //     'absensi' => $get_absensi,
                    //     'shift' => $get_shift,
                    //     'waktu' => $time,
                    //     'tanggal' => $date,
                    //     'jenis' => 'pulang',              //untuk fungsi absen
                    //     'keterangan' => 'Absen Pulang',  //untuk status di mobile
                    //     'status' => 'Pulang Cepat',       //untuk status db
                    // ];

                    return [
                        'id_absensi' => $get_absensi['id'],
                        'absensi' => $get_absensi,
                        'shift' => $get_shift,
                        'waktu' => $time,
                        'tanggal' => $date,
                        'jenis' => false,                       //untuk fungsi absen
                        'keterangan' => 'Belum Waktu Pulang',   //untuk status di mobile
                        'status' => 'Belum Waktu Pulang',       //untuk status db
                    ];
                } else
                if ($step4) {
                    return [
                        'id_absensi' => $get_absensi['id'],
                        'absensi' => $get_absensi,
                        'shift' => $get_shift,
                        'waktu' => $time,
                        'tanggal' => $date,
                        'jenis' => 'pulang',              //untuk fungsi absen
                        'keterangan' => 'Absen Pulang',  //untuk status di mobile
                        'status' => 'Pulang Tepat Waktu', //untuk status db
                    ];
                }
            }
        }

        return [
            'id_absensi' => null,
            'absensi' => [],
            'shift' => $get_shift,
            'waktu' => $time,
            'tanggal' => $date,
            'jenis' => false,                         //untuk fungsi absen
            'keterangan' => 'Absen belum tersedia',  //untuk status di mobile
            'status' => 'Tidak tersedia',             //untuk status db
        ];
    }
}
