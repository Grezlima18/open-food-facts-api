<?php

use App\Http\Controllers\ProductController;
use App\Models\ImportHistory;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/', function () {
    $lastImport = ImportHistory::latest('imported_at')->first();

    return response()->json([
        'status' => 'API is running',
        'last_cron_execution' => $lastImport->imported_at ?? 'Never',
        'last_import_status' => $lastImport->status ?? 'No history',
        'products_imported' => $lastImport->products_imported ?? 0,
    ]);
});

Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{code}', [ProductController::class, 'show']);
Route::put('/products/{code}', [ProductController::class, 'update']);
Route::delete('/products/{code}', [ProductController::class, 'delete']);

