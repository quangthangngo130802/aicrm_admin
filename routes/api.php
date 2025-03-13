<?php

use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\TransferController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('add-user', [UserController::class, 'addUser'])->name('admin.adduser');
Route::post('confirm-transaction/{id}', [TransactionController::class, 'confirmTransaction']);
Route::post('reject-transaction/{id}', [TransactionController::class, 'rejectTransaction']);
Route::post('transfer', [TransferController::class, 'transfer']);
