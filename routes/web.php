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
use App\Http\Controllers\ManfeeDetailPaymentsController;
use App\Http\Controllers\ManfeeAccumulatedCostController;
use App\Http\Controllers\ManfeeHistoryController;

use App\Http\Controllers\NonManfeeDocumentDataTableController;
use App\Http\Controllers\NonManfeeDocumentController;
use App\Http\Controllers\NonManfeeAccumulatedCostController;
use App\Http\Controllers\NonManfeeAttachmentController;
use App\Http\Controllers\NonManfeeDescriptionController;
use App\Http\Controllers\NonManfeeTaxController;
use App\Http\Controllers\NonManfeeHistoryController;

use App\Http\Controllers\NotificationController;
use Laravel\Fortify\Http\Controllers\NewPasswordController;
use App\Http\Controllers\DropboxController;
use App\Http\Controllers\PDFController;

use App\Actions\Fortify\CreateNewUser;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
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

Route::middleware(['auth', 'role:super_admin'])->group(function () {
    Route::get('/register', function () {
        return view('auth.register', ['roles' => Role::all()]);
    })->name('register');

    Route::post('/register', function (Request $request) {
        $action = new CreateNewUser();
        $action->create($request->all());

        return redirect()->route('register')->with('status', 'User berhasil dibuat.');
    });
});

Route::get('/token', [TestController::class, 'getDataToken'])->name('token');

Route::get('/reset-password', [NewPasswordController::class, 'create'])->name('password.reset');
Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.update');


Route::get('/generate-letter', [PDFController::class, 'generateLetter']);
Route::get('/generate-invoice', [PDFController::class, 'generateInvoice']);
Route::get('/generate-kwitansi', [PDFController::class, 'generateKwitansi']);


Route::middleware(['auth:sanctum', 'verified'])->group(function () {

    // DROPBOX ROUTE
    Route::get('/dropbox/auth', [DropboxController::class, 'redirectToAuthorization'])
        ->name('dropbox.auth');
    Route::get('/dropbox/callback', [DropboxController::class, 'handleAuthorizationCallback'])
        ->name('dropbox.callback');
    Route::get('/test-dropbox', [DropboxController::class, 'index'])->name('dropbox.index');
    Route::post('/dropbox/upload', [DropboxController::class, 'upload'])
        ->name('dropbox.upload');
    Route::get('/dropbox/files', [DropboxController::class, 'listFiles'])->name('dropbox.files');
    Route::get('/dropbox/view/{filePath}', [DropboxController::class, 'viewFile'])
    ->where('filePath', '.*')
    ->name('dropbox.file.view');
    Route::delete('/dropbox/delete/{path}', [DropboxController::class, 'deleteFile'])
    ->where('path', '.*')
    ->name('dropbox.delete');
    // END DROPBOX ROUTE



    // Route for the getting the data feed
    Route::get('/json-data-feed', [DataFeedController::class, 'getDataFeed'])->name('json_data_feed');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/json', [NotificationController::class, 'getNotificationsJson'])->name('notifications.getNotificationsJson');
    Route::get('/notifications/{id}', [NotificationController::class, 'show'])->name('notifications.show');
    Route::post('/notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
    Route::post('/notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead'])
        ->name('notifications.markAsRead');
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

        Route::resource('/', ManfeeDocumentController::class)->except(['show', 'edit'])->parameters(['' => 'id'])->names([
            'index' => 'index',
            'create' => 'create',
            'store' => 'store',
            'update' => 'update',
            'destroy' => 'destroy',
        ]);

        // Details
        Route::get('/{id}/show', [ManfeeDocumentController::class, 'show'])->name('show');

        Route::put('/process/{id}', [ManfeeDocumentController::class, 'processApproval'])->name('processApproval');
        Route::put('/revision/{id}', [ManfeeDocumentController::class, 'processRevision'])->name('processRevision');

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

        Route::prefix('{id}/edit/detail_payments')->name('detail_payments.')->group(function () {
            // management-fee.detail_payments.show
            Route::get('/{detail_payment_id}', [ManfeeDetailPaymentsController::class, 'show'])->name('show');
            // management-fee.detail_payments.store
            Route::post('/store', [ManfeeDetailPaymentsController::class, 'store'])->name('store');
            // management-fee.detail_payments.update
            Route::put('/{detail_payment_id}/update', [ManfeeDetailPaymentsController::class, 'update'])->name('update');
            // management-fee.detail_payments.destroy
            Route::delete('/{detail_payment_id}', [ManfeeDetailPaymentsController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('{id}/edit/accumulated')->name('accumulated.')->group(function () {
            // management-fee.accumulated.show
            Route::get('/{accumulated_id}', [ManfeeAccumulatedCostController::class, 'show'])->name('show');
            // management-fee.accumulated.update
            Route::put('/{accumulated_id}/update', [ManfeeAccumulatedCostController::class, 'update'])->name('update');
            // management-fee.accumulated.destroy 
            Route::delete('/{accumulated_id}', [ManfeeAccumulatedCostController::class, 'destroy'])->name('destroy');
        });

        // Route Print PDF Surat Permohonan, Kwitansi, Invoice
        Route::get('/{id}/print-surat', [PDFController::class, 'generateLetter'])->name('print-surat');;
        Route::get('/{id}/print-kwitansi', [PDFController::class, 'generateInvoice'])->name('print-kwitansi');;
        Route::get('/{id}/print-invoice', [PDFController::class, 'generateKwitansi'])->name('print-invoice');;

        Route::prefix('histories')->name('histories.')->group(function () {
            Route::get('/', [ManfeeHistoryController::class, 'index'])->name('index');
            Route::get('/{history_id}', [ManfeeHistoryController::class, 'show'])->name('show');
            Route::post('/store', [ManfeeHistoryController::class, 'store'])->name('store');
            Route::delete('/{history_id}', [ManfeeHistoryController::class, 'destroy'])->name('destroy');
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

        // Route Print PDF Surat Permohonan, Kwitansi, Invoice
        Route::get('/{id}/print-surat', [PDFController::class, 'generateLetter'])->name('print-surat');;
        Route::get('/{id}/print-kwitansi', [PDFController::class, 'generateInvoice'])->name('print-kwitansi');;
        Route::get('/{id}/print-invoice', [PDFController::class, 'generateKwitansi'])->name('print-invoice');;

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
