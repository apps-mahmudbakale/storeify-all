<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StationController;
use App\Http\Controllers\RequestsController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReturnSaleController;

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

Route::get('/', function () {
    return redirect()->route('app.dashboard');
});
Route::get('syncData', [SaleController::class, 'store']);
Route::get('syncStore', [SaleController::class, 'syncStore']);
Route::post('synced', [SaleController::class, 'synced']);


Auth::routes();

/* Route Dashboards */
Route::group(['prefix' => 'app', 'as' => 'app.', 'middleware' => 'auth'], function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('sync', [DashboardController::class, 'sync'])->name('sync');
    Route::resource('users', UserController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('stations', StationController::class);
    Route::resource('stores', StoreController::class);
    Route::resource('products', ProductController::class);
    Route::get('product/import', [ProductController::class, 'importView'])->name('products.import');
    Route::get('product/export', [ProductController::class, 'export'])->name('products.export');

    /* Department and Staff Management */
    Route::resource('departments', \App\Http\Controllers\DepartmentController::class);
    Route::get('/department/import', [\App\Http\Controllers\DepartmentController::class, 'importView'])
        ->name('departments.import.view');

    Route::post('/department/import', [\App\Http\Controllers\DepartmentController::class, 'import'])
        ->name('departments.import');
    Route::resource('staff', \App\Http\Controllers\StaffController::class);
    Route::post('/staffs/import', [\App\Http\Controllers\StaffController::class, 'import'])
        ->name('staff.import');
    Route::get('/staffs/import', [\App\Http\Controllers\StaffController::class, 'importView'])
        ->name('staff.import.view');
    Route::post('product', [ProductController::class, 'import'])->name('import.products');
    Route::resource('requests', RequestsController::class);
    Route::get('product/history/{product}', [ProductController::class, 'history'])->name('products.history');
    Route::post('requests/approve/{id}', [RequestsController::class, 'approve'])->name('requests.approve');
    Route::get('requests/acknowledge/{id}', [RequestsController::class, 'acknowledge'])->name('requests.acknowledge');
    Route::post('sales/search', [SaleController::class, 'searchItem'])->name('sales.search');
    Route::get('sales/cart/{invoice}', [SaleController::class, 'cart'])->name('sales.cart');
    Route::get('sales/remove/{product}', [SaleController::class, 'removeProduct'])->name('sales.remove');
    Route::get('sales/save/{invoice}', [SaleController::class, 'saveSale']);
    Route::get('sales/print/{invoice}', [SaleController::class, 'saveSalePrint']);
    Route::get('sales/cancel/{invoice}', [SaleController::class, 'cancelSale']);
    Route::get('sales-print/{invoice}', [SaleController::class, 'printInvoice'])->name('sales.print');
    Route::resource('sales', SaleController::class);
    Route::resource('returns', ReturnSaleController::class);
    Route::post('returns/approve', [ReturnSaleController::class, 'approve'])->name('returns.approve');
    Route::get('generalReport', [DashboardController::class, 'generalReport'])->name('general.report');
    Route::get('generalReportExcel', [DashboardController::class, 'exportGeneralReportExcel'])->name('general-report.export-excel');
    Route::get('generalReportPdf', [DashboardController::class, 'exportGeneralReportPDF'])->name('general-report.export-pdf');
    Route::get('endDayReportExcel', [DashboardController::class, 'exportEndOfDayReportExcel'])->name('end-day-report.export-excel');
    Route::get('endDayReportPdf', [DashboardController::class, 'exportEndOfDayReportPdf'])->name('end-day-report.export-pdf');
    Route::get('changePassword', [DashboardController::class, 'showChangePasswordGet'])->name('changePasswordGet');
    Route::post('changePassword', [DashboardController::class, 'changePasswordPost'])->name('changePasswordPost');
    // Route::get('endOfDayReport', [DashboardController::class, 'endOfDayView'])->name('endofDay.view');
    Route::get('endDayReport', [DashboardController::class, 'endOfDayReport'])->name('endofDay.report');
    Route::get('customReport', [DashboardController::class, 'customReportView'])->name('custom.report.view');
    Route::post('customReport', [DashboardController::class, 'customReport'])->name('custom.report');
    Route::get('categoryReport', [DashboardController::class, 'categoryReportView'])->name('category.report.view');
    Route::post('categoryReport', [DashboardController::class, 'categoryReport'])->name('category.report');
    Route::get('categoryReport/excel', [DashboardController::class, 'exportCategoryReportExcel'])->name('category.report.excel');
    Route::get('categoryReport/pdf', [DashboardController::class, 'exportCategoryReportPdf'])->name('category.report.pdf');
    Route::get('invoice/{invoice}', [InvoiceController::class, 'invoice']);
    Route::get('invoice/print/{invoice}', [InvoiceController::class, 'invoicePrint'])->name('invoice.print');
    Route::post('invoice/confirm-payment/{invoice}', [InvoiceController::class, 'confirmPayment'])->name('invoice.confirm-payment');
    Route::resource('invoices', InvoiceController::class);
    Route::resource('settings', SettingsController::class)->except('store', 'update', 'edit', 'show', 'destroy');
    Route::post('settings', [SettingsController::class, 'updateStoreSettings'])->name('update.store.settings');
    Route::post('settings/currency', [SettingsController::class, 'updateStoreCurrency'])->name('update.store.currency');
    Route::post('logout', [LogoutController::class, 'perform'])->name('logout');
});
// if (env('APP_ENV') === 'production') {
    // URL::forceScheme('https');
// }
