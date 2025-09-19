<?php

use App\Http\Controllers\Master\RolesController;
use Illuminate\Support\Facades\Route;
// use Opcodes\LogViewer\Facades\LogViewer;

Route::get('/', function () {
    // return view('welcome');
    return view('template.app');
});
// Route::get('roles',[RolesController::class,'index']);
// Route::post('/datatable', [App\Http\Controllers\DataTableController::class, 'fetch']);
// Route::get('/datatable/export/{type}', [App\Http\Controllers\DataTableController::class, 'export']);
Route::prefix('roles')->name('roles.')->controller(RolesController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/add', 'add')->name('add');
    Route::post('/fetch', 'fetch')->name('fetch');
    Route::get('/export', 'exportCsv')->name('export');
});

// Route::middleware(['auth']) // kasih auth biar aman
//     ->prefix('log-viewer')
//     ->group(function () {
//         LogViewer::routes();
//     });