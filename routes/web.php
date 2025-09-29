<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CsvImportController;
use App\Http\Controllers\ImageUploadController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/import', [CsvImportController::class, 'showForm'])->name('import.form');
Route::post('/import', [CsvImportController::class, 'import'])->name('import.data');

Route::get('/upload', [ImageUploadController::class, 'imageUpload'])->name('upload.form');