<?php
function no_reg($kategori = 0, $no = 0)
{
    return sprintf('%02d%05d', $kategori, $no);
}

function micro_id($encrypt = '', $utimestamp = null)
{
    if (is_null($utimestamp))
        $utimestamp = microtime(true);

    $timestamp = floor($utimestamp);
    // die($utimestamp . ' ## ' . $timestamp);
    $milliseconds = round(($utimestamp - $timestamp) * 1000000);

    $d = date(preg_replace('`(?<!\\\\)u`', $milliseconds, 'YmdHis'), $timestamp) . str_pad(date(preg_replace('`(?<!\\\\)u`', $milliseconds, 'u'), $timestamp), 6, '0', STR_PAD_LEFT);
    // die($milliseconds . ' ## ' . $timestamp . ' ## ' . str_pad(date(preg_replace('`(?<!\\\\)u`', $milliseconds, 'u'), $timestamp), 6, '0', STR_PAD_LEFT));

    $length = 5;
    $characters = ' 0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);

    if ($encrypt == 'md5') {
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        $d .= '_' . $randomString;
        $d = md5($d);
    }

    return $d;
    //2015-07-13 18:36:40:266099
    //thun[4],bulan[2],tgl[2],jam[2],menit[2],detik[2],millisecond
}

function cek_file($img = '')
{
    // echo(site_url() . 'files/product/' . $img);
    if (!empty($img) && file_exists('files/product/' . $img)) {
        return site_url() . 'files/product/' . $img;
    } else {
        return site_url() . 'assets/admin/img/produk.jpg';
    }
}

function cek_json($string = "[]")
{
    //echo 'Decoding: ' . $string;
    json_decode($string, true);

    switch (json_last_error()) {
        case JSON_ERROR_NONE:
            return false;
            break;
        case JSON_ERROR_DEPTH:
            return ' - Maximum stack depth exceeded';
            break;
        case JSON_ERROR_STATE_MISMATCH:
            return ' - Underflow or the modes mismatch';
            break;
        case JSON_ERROR_CTRL_CHAR:
            return ' - Unexpected control character found';
            break;
        case JSON_ERROR_SYNTAX:
            return ' - Syntax error, malformed JSON';
            break;
        case JSON_ERROR_UTF8:
            return ' - Malformed UTF-8 characters, possibly incorrectly encoded';
            break;
        default:
            return ' - Unknown error';
            break;
    }

    return PHP_EOL;
}

function is_json($string)
{
    return is_string($string) && is_array(json_decode($string, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
}

function stopwatch($status = 'start', $datetime2 = '')
{
    $datetime1 = new DateTime();

    if ($status == 'start') return $datetime1;
    if (empty($datetime2)) $datetime2 = $datetime1;

    $interval = $datetime1->diff($datetime2);
    return $interval->format('%h jam %i menit %s detik');
}

function timing($time, $toTime = '', $ket = 'lalu')
{
    /*
        $time = strtotime('2010-04-28 17:25:43');
        echo 'event happened '.humanTiming($time).' ago';
        */
    $time = (!empty($toTime) ? $toTime : time()) - $time; // to get the time since that moment
    $time = ($time < 1) ? 1 : $time;
    $tokens = array(
        31536000 => 'tahun',
        2592000 => 'bulan',
        604800 => 'minggu',
        86400 => 'hari',
        3600 => 'jam',
        60 => 'menit',
        1 => 'detik'
    );

    foreach ($tokens as $unit => $text) {
        if ($time < $unit) continue;
        $numberOfUnits = floor($time / $unit);
        return $numberOfUnits . ' ' . $text . (($numberOfUnits > 1) ? ' ' . $ket : ' ' . $ket);
    }
}

function rangeJam($time, $toTime = '')
{
    $time = (!empty($toTime) ? $toTime : time()) - $time; // to get the time since that moment
    $time = ($time < 1) ? 1 : $time;
    $range = $time / 3600;
    return round($range);
}

function hitung_hari($date1, $date2 = '')
{
    $date1 = strtotime($date1);
    $date2 = !empty($date2) ? strtotime($date2) : time();
    $diff = $date2 - $date1;
    $days = floor($diff / (60 * 60 * 24));
    return $days;
}

function date_save($tgl = '')
{
    return !empty($tgl) ? date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $tgl))) : null;
}


//extend:::::::::::::::::::::::::::::::::::::::::::::::::::::::::
function extend($default, $options)
{
    foreach ($options as $key => $value) {
        if (is_array($value)) {
            if (empty($default[$key])) $default[$key] = [];
            $default[$key] = extend($default[$key], $value);
        } else {
            $default[$key] = $value;
        }
    }
    return $default;
}

function failed($str = '', $url = '')
{
    header('Content-Type: application/json; charset=utf-8');
    die(json_encode(["status" => 0, "return" => $str, "url" => $url]));
}

function success($str = '', $url = '')
{
    header('Content-Type: application/json; charset=utf-8');
    die(json_encode(["status" => 1, "return" => $str, "url" => $url]));
}

function failed_return($str = '', $url = '')
{
    return (["status" => 0, "return" => $str, "url" => $url]);
}

function success_return($str = '', $url = '')
{
    return (["status" => 1, "return" => $str, "url" => $url]);
}

function api_failed($str = '', $url = '')
{
    header('Content-Type: application/json; charset=utf-8');
    die(json_encode(["status" => 0, "return" => $str, "url" => $url]));
}

function api_success($str = '', $url = '')
{
    header('Content-Type: application/json; charset=utf-8');
    die(json_encode(["status" => 1, "return" => $str, "url" => $url]));
}

function time2second($waktu = '')
{
    $hari = explode(' days ', $waktu)[0];
    $jam = explode(' days ', $waktu)[1];
    list($h, $m, $s) = explode(':', $jam);

    return ($hari * 86400) + ($h * 3600) + ($m * 60) + $s;
}

function get_percentage($total = 0, $number = 0)
{
    if (!empty($total)) {
        $percent = $number / $total;
        return number_format($percent * 100, 2);
    } else
        return 0;
}

function dj($json = [])
{
    die(json_encode($json, JSON_PRETTY_PRINT));
}

function colorRange($d = 0)
{
    if ($d > 4)
        return 'danger';
    else if ($d > 2)
        return 'warning';
    else if ($d > 1)
        return 'secondary';
    else
        return 'light';
}

function colorPercent($d = 0)
{
    if ($d > 75)
        return 'success';
    else if ($d > 50)
        return 'secondary';
    else if ($d > 25)
        return 'warning';
    else
        return 'danger';
}

function random_pic($size = '1920x1080', $category = 'computer')
{
    return 'https://source.unsplash.com/random/' . $size . '?' . $category;
}

function nominal($var)
{
    return filter_var($var, FILTER_SANITIZE_NUMBER_INT);
}

function uang($var = 0, $pemisah = ',')
{
    return number_format($var, 0, ',', $pemisah);
}

function nominal_percent($str)
{
    $angka  = nominal($str);
    return $angka . (substr($str, -1) == '%' ? '%' : '');
}

function nominal_percent_format($str)
{
    if (!empty($str) && substr($str, -1) != '%') return uang($str);
    return $str;
}

function titik_titik($str, $lenght)
{
    return strlen($str) > $lenght ? substr($str, 0, $lenght) . ".." : $str;
}

function tanggal($tanggal = '', $type = '', $sort = 0)
{
    $tanggal = !empty($tanggal) ? $tanggal : date('Y-m-d');

    $bulan = array(
        1 =>   'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember'
    );

    $bulan_sort = array(
        1 =>   'Jan',
        'Feb',
        'Mar',
        'Apr',
        'Mei',
        'Jun',
        'Jul',
        'Agu',
        'Sep',
        'Okt',
        'Nov',
        'Des'
    );
    // $pecahkan = explode('-', $tanggal);

    // variabel pecahkan 0 = tanggal
    // variabel pecahkan 1 = bulan
    // variabel pecahkan 2 = tahun

    $bln = $bulan[(int) date('m', strtotime($tanggal))];
    if ($sort)
        $bln = $bulan_sort[(int) date('m', strtotime($tanggal))];

    return ($type == 'full' ? hari($tanggal) . ', ' : '') . date('d', strtotime($tanggal)) . ' ' . $bln . ' ' . date('Y', strtotime($tanggal));
}

function bulan($date = '')
{
    $bulan = array(
        1 =>   'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember'
    );
    return $bulan[intval(date('m', strtotime($date)))];
}

function hari($date = '')
{
    $daftar_hari = array(
        'Sunday' => 'Minggu',
        'Monday' => 'Senin',
        'Tuesday' => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday' => 'Kamis',
        'Friday' => 'Jumat',
        'Saturday' => 'Sabtu'
    );
    $namahari = date('l', strtotime($date));

    return $daftar_hari[$namahari];
}

function tgl_hijriah($tanggal)
{
    require 'vendor/autoload.php';
    include_once APPPATH . 'Libraries/ar-php/src/Arabic.php';
    $Arabic = new \ArPHP\I18N\Arabic();
    $correction = $Arabic->dateCorrection(strtotime($tanggal));
    $Arabic->setDateMode(1); //8
    return [
        'date' => $Arabic->date('j F', strtotime($tanggal), $correction),
        'year' => $Arabic->date('Y', strtotime($tanggal), $correction)
    ];
}

function percentage($price = 0, $diskon = 0, $just_diskon = 0)
{
    if (preg_match('/^\d+(?:\.\d+)?%$/', $diskon)) {
        $diskon = ($price * (intval($diskon) / 100));
    } else {
        $diskon = intval($diskon);
    }
    return !empty($just_diskon) ? $diskon : ($price - $diskon);
}

function curl($url = '', $post = [], $show = false)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_TIMEOUT, 100000);
    curl_setopt($ch, CURLOPT_ENCODING, "UTF-8");

    curl_setopt($ch, CURLOPT_POST, 1);
    // $post['api'] = $registered;

    //die(json_encode($post));
    foreach ($post as $key => $value) {
        $post[$key] = is_array($value) ? json_encode($value) : $value;
    }
    $post = http_build_query($post);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

    if ($errno = curl_errno($ch)) {
        $error_message = curl_strerror($errno);
        $return = json_encode(['return' => 0, 'content' => "cURL error ({$errno}): {$error_message}"]);
    } else {
        $content = curl_exec($ch);
        if ($show) die($content);


        $return = json_decode($content, true);

        // if ($content && !empty($content['status']))
        //     $return = ['status' => 1, 'return' => $content['return']];
        // else
        //     $return = ['status' => 0, 'return' => !empty($content['return']) ? $content['return'] : ''];
    }

    curl_close($ch);
    return $return;
}

function js_notif($judul, $isi, $type = "success")
{
    if (!$isi) return false;
    if ($type == "success") {
        return 'notif_success("' . $judul . '", "' . $isi . '");';
    } else {
        return 'notif_error("' . $judul . '", "' . $isi . '");';
    }
}

function leading_zero($str, $digit)
{
    return str_pad($str, $digit, '0', STR_PAD_LEFT);
}

function page_access($page = '')
{
    $level = session()->level;
    // dj($page . ' ' . $level);
    $db = \Config\Database::connect();
    $setting = $db->table('setting')->get()->getRow();
    $access = json_decode($setting->access ?? '[]', 1);
    foreach ($access as $key_p => $parent) {
        if ($parent['id'] == $page && in_array($level, $parent['access'])) return true;

        if (!empty($parent['children']))
            foreach ($parent['children'] as $key_c => $child) {
                if ($child['id'] == $page && in_array($level, $child['access'])) return true;
            }
    }

    return false;
}

function menu_access($page)
{
    return !page_access($page) ? 'd-none' : '';
}

function last_query()
{
    $db = \Config\Database::connect();
    die($db->getLastQuery());
}

function no_hp($no = '')
{
    if (preg_match('/^08/', $no)) {
        $no = preg_replace('/^08/', '628',  $no);
    } else
    if (preg_match('/^\+62 0/', $no)) {
        $no = preg_replace('/^\+62 0/', '62',  $no);
    } else
    if (preg_match('/^\+62 /', $no)) {
        $no = preg_replace('/^\+62 /', '62',  $no);
    }

    return $no;
}

function hari_kerja($date = null, $count = 0)
{
    $hari_libur = ['6', '7']; //sabtu dan minggu
    $tgl_libur = [];

    if (!$count) {
        //apakah hari libur atau next mencari hari tidak libur
        while (
            in_array(date('N', strtotime($date)), $hari_libur) ||
            in_array($date, $tgl_libur)
        ) {
            $date = date('Y-m-d', strtotime('+1 day', strtotime($date)));
        }
    } else {
        for ($i = 1; $i <= $count; $i++) {
            if (
                in_array(date('N', strtotime($date)), $hari_libur) ||
                in_array($date, $tgl_libur)
            )
                $i--;
            $date = date('Y-m-d', strtotime('+1 day', strtotime($date)));
        }
    }

    return $date;
}

function log_($id_perkara, $text)
{
    $db = \Config\Database::connect();
    $perkara = $db->table('perkara')->where('id', $id_perkara)->get()->getRowArray();
    $perkara_log = json_decode($perkara['log'], 1);
    $perkara_log[micro_id()] = [
        'time' => date('Y-m-d H:i:s'),
        'text' => $text,
        'user' => 'amonim'
    ];

    // dj($perkara_log);
    $db->table('perkara')->where('id', $id_perkara)->set('log', json_encode($perkara_log))->update();
}

function join_text($arr = [])
{
    $last  = array_slice($arr, -1);
    $first = join(', ', array_slice($arr, 0, -1));
    $both  = array_filter(array_merge(array($first), $last), 'strlen');
    return join(' & ', $both);
}

function kondisi()
{
    $time = date("H");

    if ($time < "12")
        return "Selamat Pagi";

    if ($time >= "12" && $time < "15")
        return "Selamat Siang";

    if ($time >= "15" && $time < "19")
        return "Selamat Sore";

    if ($time >= "19")
        return "Selamat Malam";
}

function huruf($number)
{
    if ($number >= 1 && $number <= 26) {
        return chr(96 + $number);
    } else {
        return "Invalid number";
    }
}

function full_tahun($year)
{
    //$year 10 = 2010
    if (is_numeric($year)) {
        $year = intval($year);
        if ($year >= 0 && $year <= 99) {
            if ($year >= 0 && $year <= 20) {
                // 00-20 will become 2000-2020
                return 2000 + $year;
            } else {
                // 21-99 will become 1921-1999
                return 1900 + $year;
            }
        } else {
            return false;
        }
    } else {
        return false;
    }
}


function isBase64Image($base64Data = '')
{
    // Remove the data URI scheme (e.g., 'data:image/jpeg;base64,') from the Base64 string
    $base64Data = preg_replace('#^data:image/[^;]+;base64,#', '', $base64Data);

    // Check if the remaining data is a valid image
    return (bool)getimagesizefromstring(base64_decode($base64Data));
}


function isTime24($time)
{
    // Regular expression to match a valid 24-hour time format
    $pattern = '/^([01][0-9]|2[0-3]):[0-5][0-9]$/';

    if (preg_match($pattern, $time)) {
        // Check if the time is valid using strtotime
        $timestamp = strtotime($time);
        if ($timestamp !== false) {
            return true;
        }
    }

    return false;
}

function convertFileSize($bytes, $unit = 'MB', $decimal = 2)
{
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $unitIndex = array_search(strtoupper($unit), $units);

    if ($unitIndex === false) {
        return 'Unsupported unit';
    }

    $hasil  = number_format($bytes / pow(1024, $unitIndex), $decimal);

    return (substr($hasil, -3) === '.00' ?  substr($hasil, 0, -3) : $hasil) . ' ' . $units[$unitIndex];
}

function rmdir_force($dir)
{
    $it = new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS);
    $it = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
    foreach ($it as $file) {
        if ($file->isDir()) rmdir($file->getPathname());
        else unlink($file->getPathname());
    }
    rmdir($dir);
}

function jarak($c1, $c2)
{
    $c1 = explode(',', $c1);
    $lat1 = trim($c1[0]);
    $lon1 = trim($c1[1]);

    $c2 = explode(',', $c2);
    $lat2 = trim($c2[0]);
    $lon2 = trim($c2[1]);

    // Radius of the Earth in meters
    $earthRadius = 6371000;

    // Convert latitude and longitude from degrees to radians
    $lat1Rad = deg2rad($lat1);
    $lon1Rad = deg2rad($lon1);
    $lat2Rad = deg2rad($lat2);
    $lon2Rad = deg2rad($lon2);

    // Calculate differences
    $latDiff = $lat2Rad - $lat1Rad;
    $lonDiff = $lon2Rad - $lon1Rad;

    // Haversine formula
    $a = sin($latDiff / 2) * sin($latDiff / 2) +
        cos($lat1Rad) * cos($lat2Rad) *
        sin($lonDiff / 2) * sin($lonDiff / 2);

    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

    // Distance in meters
    $distance = $earthRadius * $c;

    return round($distance, 2);
}

function isPointInPolygon($point, $polygon)
{
    $x = $point[0];
    $y = $point[1];

    $isInside = false;

    foreach ($polygon['coordinates'] as $ring) {
        $isInside = !$isInside ? isPointInRing($x, $y, $ring) : $isInside;
    }

    return $isInside;
}

function isPointInRing($x, $y, $ring)
{
    $isInside = false;
    $count = count($ring);

    for ($i = 0, $j = $count - 1; $i < $count; $j = $i++) {
        $xi = $ring[$i][0];
        $yi = $ring[$i][1];
        $xj = $ring[$j][0];
        $yj = $ring[$j][1];

        $intersect = (($yi > $y) != ($yj > $y)) &&
            ($x < ($xj - $xi) * ($y - $yi) / ($yj - $yi) + $xi);

        if ($intersect) {
            $isInside = !$isInside;
        }
    }

    return $isInside;
}

function waktu_banding($time1, $time2, $logic = '>')
{
    $format = 'H:i'; // Define the time format

    // Create DateTime objects from the time strings
    $dateTime1 = DateTime::createFromFormat($format, $time1);
    $dateTime2 = DateTime::createFromFormat($format, $time2);

    // Compare the DateTime objects
    switch ($logic) {
        case '>':
            return $dateTime1 > $dateTime2;
            break;
        case '>=':
            return $dateTime1 >= $dateTime2;
            break;
        case '<':
            return $dateTime1 < $dateTime2;
            break;
        case '<=':
            return $dateTime1 <= $dateTime2;
            break;
    }
}

function waktu_antara($now, $first, $second)
{
    $nowTime = new DateTime($now);

    // Extract operator and time value from the input
    $firstOperator = preg_replace('/[0-9]/', '', $first);
    $firstTime = new DateTime(preg_replace('/[^0-9:]/', '', $first));

    $secondOperator = preg_replace('/[0-9]/', '', $second);
    $secondTime = new DateTime(preg_replace('/[^0-9:]/', '', $second));

    // Check if $now is between $first and $second based on the specified operators
    switch ($firstOperator) {
        case '>:':
            $firstCondition = $nowTime > $firstTime;
            break;
        case '>=:':
            $firstCondition = $nowTime >= $firstTime;
            break;
        case '<:':
            $firstCondition = $nowTime < $firstTime;
            break;
        case '<=:':
            $firstCondition = $nowTime <= $firstTime;
            break;
        default:
            $firstCondition = false;
    }

    switch ($secondOperator) {
        case '>:':
            $secondCondition = $nowTime > $secondTime;
            break;
        case '>=:':
            $secondCondition = $nowTime >= $secondTime;
            break;
        case '<:':
            $secondCondition = $nowTime < $secondTime;
            break;
        case '<=:':
            $secondCondition = $nowTime <= $secondTime;
            break;
        default:
            $secondCondition = false;
    }

    // Handle the case where the specified time range can cross midnight
    if ($firstTime > $secondTime) {
        return $firstCondition || $secondCondition;
    } else {
        return $firstCondition && $secondCondition;
    }
}

function isImage($filePath)
{
    $imageInfo = @getimagesize($filePath);

    // Check if getimagesize was successful and the mime type starts with "image/"
    return $imageInfo !== false && strpos($imageInfo['mime'], 'image/') === 0;
}


function download_file($path, $name)
{   //$this->_push_file($filePath, $get_arsip['real_name']);
    // make sure it's a file before doing anything!
    if (is_file($path)) {
        // required for IE
        if (ini_get('zlib.output_compression')) {
            ini_set('zlib.output_compression', 'Off');
        }

        // get the file mime type using the file extension

        $mime = mime_content_type($path);

        // Build the headers to push out the file properly.
        header('Pragma: public');     // required
        header('Expires: 0');         // no cache
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($path)) . ' GMT');
        header('Cache-Control: private', false);
        header('Content-Type: ' . $mime);  // Add the mime type from Code igniter.
        header('Content-Disposition: attachment; filename="' . basename($name) . '"');  // Add the file name
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . filesize($path)); // provide file size
        header('Connection: close');
        readfile($path); // push it out
        exit();
    }
}

function is_date($dateString, $format = 'Y-m-d')
{
    $dateTime = DateTime::createFromFormat($format, $dateString);

    // Check if the date is valid and matches the specified format
    return $dateTime && $dateTime->format($format) === $dateString;
}

function clear_session()
{
    $directory = "./writable/session/";
    // dj($directory);
    $filecount = 0;
    $files2 = glob($directory . "*");
    if ($files2) {
        $filecount = count($files2);
    }

    echo uang($filecount) . " files";
    session_gc();
}
