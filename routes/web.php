<?php

use App\Http\Controllers\Master\RolesController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // return view('welcome');
    return view('template.app');
});
// Route::get('roles',[RolesController::class,'index']);
// Route::post('/datatable', [App\Http\Controllers\DataTableController::class, 'fetch']);
// Route::get('/datatable/export/{type}', [App\Http\Controllers\DataTableController::class, 'export']);
Route::prefix('roles')->name('roles.')->controller(RolesController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::post('/fetch', 'fetch')->name('fetch');
    Route::get('/export/csv', 'exportCsv')->name('export.csv');
    Route::get('/export/excel', 'exportExcel')->name('export.excel');
});