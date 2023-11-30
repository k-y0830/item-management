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
});

});
