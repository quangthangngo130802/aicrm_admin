<?php

use App\Http\Controllers\Api\AutomationBirthdayController;
use App\Http\Controllers\Api\AutomationRateController;
use App\Http\Controllers\Api\AutomationReminderController;
use App\Http\Controllers\Api\AutomationUserController;
use App\Http\Controllers\Api\SuperAdminController;
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

Route::post('add-user', [UserController::class, 'addUser'])->name('admin.add.user');
Route::put('update-user/{id}', [UserController::class, 'updateUser'])->name('admin.update.user');
Route::post('delete-user/{id}', [UserController::class, 'deleteUser'])->name('admin.user.delete');
Route::post('confirm-transaction/{id}', [TransactionController::class, 'confirmTransaction']);
Route::post('reject-transaction/{id}', [TransactionController::class, 'rejectTransaction']);
Route::post('transfer', [TransferController::class, 'transfer']);
Route::post('automation-user', [AutomationUserController::class, 'automationUser']);
Route::post('automation-rate', [AutomationRateController::class, 'automationRate']);
Route::post('automation-birthday', [AutomationBirthdayController::class, 'automationBirthday']);
Route::post('automation-reminder', [AutomationReminderController::class, 'automationReminder']);
Route::post('/update-super-admin',  [SuperAdminController::class, 'updateSuperAdmin']);
