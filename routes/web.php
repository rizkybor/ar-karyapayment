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


    // ROUTE MANAGEMENT NON FEE
    Route::prefix('management-non-fee')->name('management-non-fee.')->group(function () {

        // Export Data
        Route::get('/export/data', [NonManfeeDocumentController::class, 'export'])->name('export');

        // Create
        Route::get('/create', [NonManfeeDocumentController::class, 'create'])->name('create');
        Route::post('/store', [NonManfeeDocumentController::class, 'store'])->name('store');

        // Read
        Route::get('/', [NonManfeeDocumentController::class, 'index'])->name('index');
        Route::get('/show/{id}', [NonManfeeDocumentController::class, 'show'])->name('show');

        // Update
        Route::get('/edit/{id}', [NonManfeeDocumentController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [NonManfeeDocumentController::class, 'update'])->name('update');

        // Delete
        Route::delete('/destroy/{id}', [NonManfeeDocumentController::class, 'destroy'])->name('destroy');

        // Route untuk Lampiran (Attachments)
        Route::prefix('attachments')->name('attachments.')->group(function () {
            Route::get('/{id}', [NonManfeeDocumentController::class, 'attachments'])->name('index'); // Menampilkan daftar lampiran
            Route::get('/view/{id}', [NonManfeeDocumentController::class, 'viewAttachment'])->name('view'); // Melihat file lampiran
            Route::get('/edit/{id}', [NonManfeeDocumentController::class, 'editAttachment'])->name('edit'); // Edit lampiran
            Route::delete('/{id}', [NonManfeeDocumentController::class, 'destroyAttachment'])->name('destroy'); // Menghapus lampiran
        });
    });

    Route::fallback(function () {
        return view('pages/utility/404');
    });
});
