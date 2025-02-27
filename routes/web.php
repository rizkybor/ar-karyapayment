<?php

use App\Http\Controllers\ContractsController;
use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DataFeedController;
use App\Http\Controllers\DashboardController;

use App\Http\Controllers\ManfeeDocumentController;
use App\Http\Controllers\ManfeeAttachmentController;
use App\Http\Controllers\ManfeeDocumentDataTableController;

use App\Http\Controllers\NonManfeeDocumentDataTableController;
use App\Http\Controllers\NonManfeeDocumentController;
use App\Http\Controllers\NonManfeeAccumulatedCostController;
use App\Http\Controllers\NonManfeeAttachmentController;
use App\Http\Controllers\NonManfeeDescriptionController;
use App\Http\Controllers\NonManfeeTaxController;
use App\Http\Controllers\NonManfeeHistoryController;

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
    Route::get('/notifications/unread-count', [NotificationController::class, 'getUnreadNotificationsCount']);


    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');


    // ROUTE CONTRACTS (Super Admin)
    Route::middleware(['role:super_admin'])->group(function () {
        Route::resource('/contracts', ContractsController::class);
    });


    // ROUTE MANAGEMENT FEE
    Route::prefix('management-fee')->name('management-fee.')->group(function () {

        Route::get('/datatable', [ManfeeDocumentDataTableController::class, 'index'])->name('datatable');

        Route::get('/export/data', [ManfeeDocumentController::class, 'export'])->name('export');

        Route::put('/process/{id}', [ManfeeDocumentController::class, 'processApproval'])->name('processApproval');


        Route::resource('/', ManfeeDocumentController::class)->except(['show', 'edit'])->parameters(['' => 'id'])->names([
            'index' => 'index',
            'create' => 'create',
            'store' => 'store',
            'update' => 'update',
            'destroy' => 'destroy',
        ]);

        // Details
        Route::get('/{id}/show', [ManfeeDocumentController::class, 'show'])->name('show');

        // Edit
        Route::get('/{id}/edit', [ManfeeDocumentController::class, 'edit'])->name('edit');

        // Prefix untuk attachments
        Route::prefix('{id}/edit/attachments')->name('attachments.')->group(function () {
            Route::get('/{attachment_id}', [ManfeeAttachmentController::class, 'show'])->name('show');
            Route::post('/{attachment_id}/store', [ManfeeAttachmentController::class, 'store'])->name('store');
            Route::put('/{attachment_id}/update', [ManfeeAttachmentController::class, 'update'])->name('update');
            Route::delete('/{attachment_id}', [ManfeeAttachmentController::class, 'destroy'])->name('destroy');
        });
    });

    // ROUTE MANAGEMENT NON FEE
    Route::prefix('management-non-fee')->name('management-non-fee.')->group(function () {

        // Datatable NonManfee 
        Route::get('/datatable', [NonManfeeDocumentDataTableController::class, 'index'])->name('datatable');

        // Export Data
        Route::get('/export/data', [NonManfeeDocumentController::class, 'export'])->name('export');

        // CRUD utama menggunakan `Route::resource`
        Route::resource('/', NonManfeeDocumentController::class)->except(['show', 'edit'])->parameters(['' => 'id'])->names([
            'index' => 'index',
            'create' => 'create',
            'store' => 'store',
            'update' => 'update',
            'destroy' => 'destroy',
        ]);

        // Details
        Route::get('{document_id}/show', [NonManfeeDocumentController::class, 'show'])->name('show');

        // Route proses persetujuan
        Route::put('/process/{document_id}', [NonManfeeDocumentController::class, 'processApproval'])->name('processApproval');

        // ✨ Prefix Edit Non Fee
        Route::prefix('{document_id}/edit')->name('edit.')->group(function () {

            Route::get('/', [NonManfeeDocumentController::class, 'edit'])->name('index');

            // Route Accumulated Cost
            Route::prefix('accumulated-costs')->name('accumulated-costs.')->group(function () {
                Route::get('/{accumulated_cost_id}', [NonManfeeAccumulatedCostController::class, 'show'])->name('show');
                Route::post('/store', [NonManfeeAccumulatedCostController::class, 'store'])->name('store');
                Route::put('/update/{accumulated_cost_id}', [NonManfeeAccumulatedCostController::class, 'update'])->name('update');
                Route::delete('/{accumulated_cost_id}', [NonManfeeAccumulatedCostController::class, 'destroy'])->name('destroy');
            });

            // Route Lampiran (Attachments)
            Route::prefix('attachments')->name('attachments.')->group(function () {
                Route::get('/{attachment_id}', [NonManfeeAttachmentController::class, 'show'])->name('show');
                Route::post('/store', [NonManfeeAttachmentController::class, 'store'])->name('store');
                Route::put('/update/{attachment_id}', [NonManfeeAttachmentController::class, 'update'])->name('update');
                Route::delete('/{attachment_id}', [NonManfeeAttachmentController::class, 'destroy'])->name('destroy');
            });

            // Route Descriptions
            Route::prefix('descriptions')->name('descriptions.')->group(function () {
                Route::get('/{description_id}', [NonManfeeDescriptionController::class, 'show'])->name('show');
                Route::post('/store', [NonManfeeDescriptionController::class, 'store'])->name('store');
                Route::put('/update/{description_id}', [NonManfeeDescriptionController::class, 'update'])->name('update');
                Route::delete('/{description_id}', [NonManfeeDescriptionController::class, 'destroy'])->name('destroy');
            });

            // Route Taxes
            Route::prefix('taxes')->name('taxes.')->group(function () {
                Route::get('/{tax_id}', [NonManfeeTaxController::class, 'show'])->name('show');
                Route::post('/store', [NonManfeeTaxController::class, 'store'])->name('store');
                Route::put('/update/{tax_id}', [NonManfeeTaxController::class, 'update'])->name('update');
                Route::delete('/{tax_id}', [NonManfeeTaxController::class, 'destroy'])->name('destroy');
            });
        });

        // Route History
        Route::prefix('histories')->name('histories.')->group(function () {
            Route::get('/', [NonManfeeHistoryController::class, 'index'])->name('index');
            Route::get('/{history_id}', [NonManfeeHistoryController::class, 'show'])->name('show');
            Route::post('/store', [NonManfeeHistoryController::class, 'store'])->name('store');
            Route::delete('/{history_id}', [NonManfeeHistoryController::class, 'destroy'])->name('destroy');
        });
    });

    Route::fallback(function () {
        return view('pages/utility/404');
    });
});
