<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    public function confirmTransaction(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $transaction = Transaction::find($id);
            if (!$transaction) {
                throw new Exception('Giao dịch không tồn tại');
            }

            $transaction->status = $request->input('status');
            $transaction->notification = $request->input('notification');
            $transaction->save();

            $user = User::find($request->input('user_id'));
            if (!$user) {
                throw new Exception('Người dùng không tồn tại');
            }

            $user->wallet += $request->input('amount');
            $user->save();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Giao dịch đã được xác nhận']);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to confirm transaction: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Xác nhận giao dịch thất bại'], 500);
        }
    }

    public function rejectTransaction(Request $request, $id)
    {
        DB::beginTransaction();
        try{
            $transaction = Transaction::find($id);
            if(!$transaction)
            {
                throw new Exception('Giao dịch không tồn tại');
            }

            $transaction->status = $request->input('status');
            $transaction->notification = $request->input('notification');
            $transaction->save();
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Từ chối giao dịch thành công']);
        }
        catch(Exception $e)
        {
            DB::rollBack();
            Log::error('Failed to reject transaction: ' .$e->getMessage());
            return response()->json(['success' => false, 'message' => 'Từ chối giao dịch thất bại'], 500);
        }
    }
}
