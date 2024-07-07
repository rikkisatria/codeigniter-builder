<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
// $routes->set404Override();
// $routes->set404Override(function () {
// echo view('errors/e404');
// return redirect()->to(getenv('AKSES_PAGE'));
// });

// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
// $routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Home::index', ["namespace" => "\Modules\Contoh\Controllers"]);

// $routes->get('absen/(:any)', "Dashboard::kalender", ["namespace" => "\Modules\Dashboard\Controllers"]);


// $tools = 0;
// $tools = 1;
// if ($tools) {
//     $routes->get('backup_db', "Backup_db::index");
//     $routes->get('test', "Test::index");
//     $routes->get('rock/(:any)', "Rock::buat/$1");
// }
// $routes->get('clear_session', "Akses::clear_session", ["namespace" => "Modules\Akses\Controllers"]);

//akses --------------------------------------------------
// $routes->get(getenv('AKSES_PAGE'), "Akses::login", ["namespace" => "Modules\Akses\Controllers"]);
// $routes->post(getenv('AKSES_PAGE'), "Akses::proses_login", ["namespace" => "Modules\Akses\Controllers"]);
// $routes->get("akses/google", "Akses::google", ["namespace" => "Modules\Akses\Controllers"]);
// $routes->get('logout', "Akses::logout", ["namespace" => "Modules\Akses\Controllers"]);


/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}

// Add this to Footer, Including all module routes

$modules_path = ROOTPATH . 'Modules/';
$modules = scandir($modules_path);

foreach ($modules as $module) {
    if ($module === '.' || $module === '..') {
        continue;
    }

    if (is_dir($modules_path) . '/' . $module) {
        $routes_path = $modules_path . $module . '/Config/Routes.php';
        if (file_exists($routes_path)) {
            require $routes_path;
        } else {
            continue;
        }
    }
}

// if (!session()->logged_in) {
//     $routes->get('admin', "Akses::index", ["namespace" => "\Modules\Admin\Controllers"]);

//     $routes->get('(:any)', "Home::index", ["namespace" => "\Modules\Member\Controllers", 'filter' => 'authfilter_m:login']);
// }

define('node_url', getenv('node_url'));
// define('session_nama', 'Anonim');
