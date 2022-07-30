<?php

use App\Http\Controllers\CardController;
use App\Http\Controllers\ColumnController;
use App\Http\Controllers\DBExporter;
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


Route::group(['prefix' => 'columns'], function(){
    Route::get('/', [ColumnController::class, "index"]);
    Route::post('/', [ColumnController::class, "store"]);
    Route::delete('/{id}', [ColumnController::class, "destory"]);

});
Route::get('list-cards', [CardController::class, "filter"]);
Route::post('cards', [CardController::class, "store"]);
Route::post('cards/reorder', [CardController::class, "reorder"]);


Route::get('export', DBExporter::class);
