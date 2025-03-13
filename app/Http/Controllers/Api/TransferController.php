<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transfer;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransferController extends Controller
{
    public function transfer(Request $request)
    {
        DB::beginTransaction();
        try {
            $transfer = Transfer::create([
                'user_id' => $request->input('user_id'),
                'amount' => $request->input('amount'),
                'notification' => $request->input('notification'),
            ]);

            $transfer->save();
            $user = User::find($request->input('user_id'));
            $user->sub_wallet += $request->input('amount');
            $user->save();
            DB::commit();
            return response()->json(['success', 'Chuyển tiền thành công']);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to transfer: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Chuyển tiền thất bại'], 500);
        }
    }
}
