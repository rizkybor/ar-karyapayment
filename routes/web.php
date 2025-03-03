<?php

use App\Http\Controllers\ContractsController;
use App\Http\Controllers\ManfeeDescriptionsController;
use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DataFeedController;
use App\Http\Controllers\DashboardController;

use App\Http\Controllers\ManfeeDocumentController;
use App\Http\Controllers\ManfeeAttachmentController;
use App\Http\Controllers\ManfeeDocumentDataTableController;
use App\Http\Controllers\ManfeeTaxController;

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
        // management-fee.edit
        Route::get('/{id}/edit', [ManfeeDocumentController::class, 'edit'])->name('edit');

        // Prefix untuk attachments
        Route::prefix('{id}/edit/attachments')->name('attachments.')->group(function () {
            // management-fee.attachments.show
            Route::get('/{attachment_id}', [ManfeeAttachmentController::class, 'show'])->name('show');
            // management-fee.attachments.store
            Route::post('/store', [ManfeeAttachmentController::class, 'store'])->name('store');
            // management-fee.attachments.update
            Route::put('/{attachment_id}/update', [ManfeeAttachmentController::class, 'update'])->name('update');
            // management-fee.attachments.destroy
            Route::delete('/{attachment_id}', [ManfeeAttachmentController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('{id}/edit/descriptions')->name('descriptions.')->group(function () {
            // management-fee.descriptions.show
            Route::get('/{description_id}', [ManfeeDescriptionsController::class, 'show'])->name('show');
            // management-fee.descriptions.store
            Route::post('/store', [ManfeeDescriptionsController::class, 'store'])->name('store');
            // management-fee.descriptions.update
            Route::put('/{description_id}/update', [ManfeeDescriptionsController::class, 'update'])->name('update');
            // management-fee.descriptions.destroy
            Route::delete('/{description_id}', [ManfeeDescriptionsController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('{id}/edit/taxs')->name('taxs.')->group(function () {
            // management-fee.taxs.show
            Route::get('/{tax_id}', [ManfeeTaxController::class, 'show'])->name('show');
            // management-fee.taxs.store
            Route::post('/store', [ManfeeTaxController::class, 'store'])->name('store');
            // management-fee.taxs.update
            Route::put('/{tax_id}/update', [ManfeeTaxController::class, 'update'])->name('update');
            // management-fee.taxs.destroy
            Route::delete('/{tax_id}', [ManfeeTaxController::class, 'destroy'])->name('destroy');
        });
    });

    // ROUTE NON MANAGEMENT FEE
    Route::prefix('non-management-fee')->name('non-management-fee.')->group(function () {
        Route::get('/datatable', [NonManfeeDocumentDataTableController::class, 'index'])->name('datatable');
        Route::get('/export/data', [NonManfeeDocumentController::class, 'export'])->name('export');
        Route::resource('/', NonManfeeDocumentController::class)->except(['show', 'edit'])->parameters(['' => 'id'])->names([
            'index' => 'index',
            'create' => 'create',
            'store' => 'store',
            'update' => 'update',
            'destroy' => 'destroy',
        ]);
        Route::get('{id}/show', [NonManfeeDocumentController::class, 'show'])->name('show');
        Route::put('/process/{id}', [NonManfeeDocumentController::class, 'processApproval'])->name('processApproval');
        Route::put('/revision/{id}', [NonManfeeDocumentController::class, 'processRevision'])->name('processRevision');
        //  Route::put('/revision-reply/{id}', [NonManfeeDocumentController::class, 'processApproval'])->name('processApproval');

        // Edit
        // non-management-fee.edit
        Route::get('/{id}/edit', [NonManfeeDocumentController::class, 'edit'])->name('edit');
        // Prefix untuk accumulated cost
        Route::prefix('{id}/edit/accumulated')->name('accumulated.')->group(function () {
            Route::get('/{accumulated_id}', [NonManfeeAccumulatedCostController::class, 'show'])->name('show');
            Route::put('/{accumulated_id}/update', [NonManfeeAccumulatedCostController::class, 'update'])->name('update'); // Tetap pakai PUT
            Route::delete('/{accumulated_id}', [NonManfeeAccumulatedCostController::class, 'destroy'])->name('destroy');
        });

        // Prefix untuk attachments
        Route::prefix('{id}/edit/attachments')->name('attachments.')->group(function () {
            // non-management-fee.attachments.show
            Route::get('/{attachment_id}', [NonManfeeAttachmentController::class, 'show'])->name('show');
            // non-management-fee.attachments.store
            Route::post('/store', [NonManfeeAttachmentController::class, 'store'])->name('store');
            // non-management-fee.attachments.update
            Route::put('/{attachment_id}/update', [NonManfeeAttachmentController::class, 'update'])->name('update');
            // non-management-fee.attachments.destroy
            Route::delete('/{attachment_id}', [NonManfeeAttachmentController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('{id}/edit/descriptions')->name('descriptions.')->group(function () {
            // non-management-fee.descriptions.show
            Route::get('/{description_id}', [NonManfeeDescriptionController::class, 'show'])->name('show');
            // non-management-fee.descriptions.store
            Route::post('/store', [NonManfeeDescriptionController::class, 'store'])->name('store');
            // non-management-fee.descriptions.update
            Route::put('/{description_id}/update', [NonManfeeDescriptionController::class, 'update'])->name('update');
            // non-management-fee.descriptions.destroy
            Route::delete('/{description_id}', [NonManfeeDescriptionController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('{id}/edit/taxes')->name('taxes.')->group(function () {
            // non-management-fee.taxs.show
            Route::get('/{taxes_id}', [NonManfeeTaxController::class, 'show'])->name('show');
            // non-management-fee.taxs.store
            Route::post('/store', [NonManfeeTaxController::class, 'store'])->name('store');
            // non-management-fee.taxs.update
            Route::put('/{taxes_id}/update', [NonManfeeTaxController::class, 'update'])->name('update');
            // non-management-fee.taxs.destroy
            Route::delete('/{taxes_id}', [NonManfeeTaxController::class, 'destroy'])->name('destroy');
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
