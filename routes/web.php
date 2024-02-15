<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/* Route::get('/', function () {
    return view('welcome');
}); */

Route::get('init-setting', function () {
    Artisan::call('cache:clear');
    Artisan::call('optimize');
    Artisan::call('route:clear');
    Artisan::call('route:cache');
    Artisan::call('view:clear');
    Artisan::call('config:cache');
    Artisan::call('migrate:fresh');
    Artisan::call('db:seed');
    Artisan::call('storage:link');
    return "<h1>Initial setup has been done successfully.</h1>";
});

Route::get('clear-caching', function () {
    Artisan::call('cache:clear');
    Artisan::call('optimize');
    Artisan::call('route:clear');
    Artisan::call('route:cache');
    Artisan::call('view:clear');
    Artisan::call('config:cache');
    return "<h1>Caching has been done successfully.</h1>";
});

//Call storage link command:
Route::get('/storage-link', function () {
    $exitCode = Artisan::call('storage:link');
    return '<h1>storage link has been done successfully.</h1>';
});

//Call queue worker:
Route::get('/queue-work', function () {
    //$exitCode = Artisan::call('queue:work', ['--tries' => 3]);
    $exitCode = Artisan::call('queue:work');
    return '<h1>Queue has been started successfully.</h1>';
});

//Run Seeder:
Route::get('/db-seed', function () {
    $exitCode = Artisan::call('db:seed');
    return '<h1>Seeding data has been done successfully.</h1>';
});

//Run migration:
Route::get('/migrate', function () {
    $exitCode = Artisan::call('migrate');
    return '<h1>Migration has been done successfully.</h1>';
});

//Rollback migration:
Route::get('/migrate-rollback', function () {
    $exitCode = Artisan::call('migrate:rollback');
    return '<h1>Migration has been rollbacked successfully.</h1>';
});

//Clear all Cache:
Route::get('/clear-compiled', function () {
    $exitCode = Artisan::call('clear-compiled');
    return '<h1>Clear all Cache</h1>';
});

//Clear Cache facade value:
Route::get('/cache-clear', function () {
    $exitCode = Artisan::call('cache:clear');
    return '<h1>Cache facade value cleared</h1>';
});

//Reoptimized class loader:
Route::get('/optimize', function () {
    $exitCode = Artisan::call('optimize');
    return '<h1>Reoptimized class loader</h1>';
});

//Clear Route cache:
Route::get('/route-clear', function () {
    $exitCode = Artisan::call('route:clear');
    return '<h1>Route cache cleared</h1>';
});

//Route cache:
Route::get('/route-cache', function () {
    $exitCode = Artisan::call('route:cache');
    return '<h1>Routes cached</h1>';
});

//Clear View cache:
Route::get('/view-clear', function () {
    $exitCode = Artisan::call('view:clear');
    return '<h1>View cache cleared</h1>';
});

//Clear Config cache:
Route::get('/config-cache', function () {
    $exitCode = Artisan::call('config:cache');
    return '<h1>Clear Config cleared</h1>';
});
