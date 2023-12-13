<?php

use Illuminate\Support\Facades\Route;

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

// Route::get('/', function () {
//     return view('welcome');
// });

Auth::routes();

// ログイン必須のルート
Route::group(['middleware' => ['auth']], function () {
    // Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('/', [App\Http\Controllers\ItemController::class, 'index']);

    // items
    Route::prefix('items')->group(function () {
        Route::get('/', [App\Http\Controllers\ItemController::class, 'index']);
        Route::get('/add', [App\Http\Controllers\ItemController::class, 'add']);
        Route::post('/add', [App\Http\Controllers\ItemController::class, 'add']);
        // 取得したIDの編集ページ表示
        Route::get('/edit{id}', [App\Http\Controllers\ItemController::class, 'edit']);
        // 取得したIDの商品編集
        Route::post('/edit{id}', [App\Http\Controllers\ItemController::class, 'editregister']);
        // 削除
        Route::post('/delete{id}', [App\Http\Controllers\ItemController::class, 'delete']);
        // 複数検索
        Route::get('/search', [App\Http\Controllers\ItemController::class, 'search']);
        // csv
        Route::post('/import', [App\Http\Controllers\ItemController::class, 'import']);
        Route::get('/export', [App\Http\Controllers\ItemController::class, 'export']);
    });

    // type
    Route::prefix('type')->group(function () {
        Route::get('/', [App\Http\Controllers\TypeController::class, 'index']);
        Route::get('/add', [App\Http\Controllers\TypeController::class, 'add']);
        Route::post('/add', [App\Http\Controllers\TypeController::class, 'add']);
        // 取得したIDの編集ページ表示
        Route::get('/edit{id}', [App\Http\Controllers\TypeController::class, 'edit']);
        // 取得したIDの種別編集
        Route::post('/edit{id}', [App\Http\Controllers\TypeController::class, 'editregister']);
        // 削除
        Route::post('/delete{id}', [App\Http\Controllers\TypeController::class, 'delete']);
        // 検索
        Route::get('/search', [App\Http\Controllers\TypeController::class, 'search']);
        // csv
        Route::post('/import', [App\Http\Controllers\TypeController::class, 'import']);
        Route::get('/export', [App\Http\Controllers\TypeController::class, 'export']);
    });

    // company
    Route::prefix('company')->group(function () {
        Route::get('/', [App\Http\Controllers\CompanyController::class, 'index']);
        Route::get('/add', [App\Http\Controllers\CompanyController::class, 'add']);
        Route::post('/add', [App\Http\Controllers\CompanyController::class, 'add']);
        // 取得したIDの編集ページ表示
        Route::get('/edit{id}', [App\Http\Controllers\CompanyController::class, 'edit']);
        // 取得したIDの種別編集
        Route::post('/edit{id}', [App\Http\Controllers\CompanyController::class, 'editregister']);
        // 削除
        Route::post('/delete{id}', [App\Http\Controllers\CompanyController::class, 'delete']);
        // 検索
        Route::get('/search', [App\Http\Controllers\CompanyController::class, 'search']);
        // csv
        Route::post('/import', [App\Http\Controllers\CompanyController::class, 'import']);
        Route::get('/export', [App\Http\Controllers\CompanyController::class, 'export']);
    });

    // orders
    Route::prefix('orders')->group(function () {
        Route::get('/', [App\Http\Controllers\OrderController::class, 'index']);
        Route::get('/add', [App\Http\Controllers\OrderController::class, 'add']);
        Route::post('/add', [App\Http\Controllers\OrderController::class, 'add']);
        // 取得したIDの編集ページ表示
        Route::get('/edit{id}', [App\Http\Controllers\OrderController::class, 'edit']);
        // 取得したIDの商品編集
        Route::post('/edit{id}', [App\Http\Controllers\OrderController::class, 'editregister']);
        // 削除
        Route::post('/delete{id}', [App\Http\Controllers\OrderController::class, 'delete']);
        // 複数検索
        Route::get('/search', [App\Http\Controllers\OrderController::class, 'search']);
        // csv
        Route::post('/import', [App\Http\Controllers\OrderController::class, 'import']);
        Route::get('/export', [App\Http\Controllers\OrderController::class, 'export']);
    });

});
