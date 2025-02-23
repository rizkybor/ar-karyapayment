<?php

use App\Http\Controllers\ContractsController;
use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DataFeedController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ManfeeDocumentController;
use App\Http\Controllers\NonManfeeDocumentController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::redirect('/', 'login');
Route::get('/token', [TestController::class, 'getDataToken'])->name('token');

Route::middleware(['auth:sanctum', 'verified'])->group(function () {

    // Route for the getting the data feed
    Route::get('/json-data-feed', [DataFeedController::class, 'getDataFeed'])->name('json_data_feed');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('/management-fee', ManfeeDocumentController::class);
    Route::resource('/contracts', ContractsController::class);


    Route::resource('/management-non-fee', NonManfeeDocumentController::class);
    Route::get('/management-non-fee/show/{id}', [NonManfeeDocumentController::class, 'show'])
    ->name('management-non-fee.show');
    Route::get('/management-non-fee/edit/{id}', [NonManfeeDocumentController::class, 'edit'])
    ->name('management-non-fee.edit');
   

    // Route untuk Lampiran (Attachments)
    Route::get('/management-non-fee/{id}/attachments', [NonManfeeDocumentController::class, 'attachments'])
    ->name('attachments.index'); // Menampilkan daftar lampiran
    Route::get('/management-non-fee/attachments/view/{id}', [NonManfeeDocumentController::class, 'viewAttachment'])
    ->name('attachments.view'); // Melihat file lampiran
    Route::delete('/management-non-fee/attachments/{id}', [NonManfeeDocumentController::class, 'destroyAttachment'])
    ->name('attachments.destroy'); // Menghapus lampiran


    Route::fallback(function () {
        return view('pages/utility/404');
    });
});
