<?php

use App\Http\Controllers\ContractsController;
use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DataFeedController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ManfeeDocumentController;
use App\Http\Controllers\NonManfeeDocumentController;
use App\Http\Controllers\NotificationController;

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

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{id}', [NotificationController::class, 'show'])->name('notifications.show');
    Route::post('/notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::delete('/notifications/clear-all', [NotificationController::class, 'clearAll'])->name('notifications.clearAll');


    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('/management-fee', ManfeeDocumentController::class);
    Route::resource('/contracts', ContractsController::class);


    // MANAGEMENT NON FEE
    Route::get('/management-non-fee/export/data', [NonManfeeDocumentController::class, 'export'])
        ->name('management-non-fee.export');

    // Route untuk Lampiran (Attachments)
    Route::get('/management-non-fee/{id}/attachments', [NonManfeeDocumentController::class, 'attachments'])
        ->name('attachments.index'); // Menampilkan daftar lampiran
    Route::get('/management-non-fee/attachments/view/{id}', [NonManfeeDocumentController::class, 'viewAttachment'])
        ->name('attachments.view'); // Melihat file lampiran
    Route::get('/management-non-fee/attachments/edit/{id}', [NonManfeeDocumentController::class, 'editAttachment'])
        ->name('attachments.edit');
    Route::delete('/management-non-fee/attachments/{id}', [NonManfeeDocumentController::class, 'destroyAttachment'])
        ->name('attachments.destroy'); // Menghapus lampiran

    // ✅ resource tidak menangani `show`
    Route::resource('/management-non-fee', NonManfeeDocumentController::class)->except(['show']);

    // ✅ route show eksplisit untuk mencegah bentrok
    Route::get('/management-non-fee/{id}', [NonManfeeDocumentController::class, 'show'])
        ->name('management-non-fee.show');


    Route::fallback(function () {
        return view('pages/utility/404');
    });
});
