<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AutomationUser;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AutomationUserController extends Controller
{
    public function automationUser(Request $request)
    {
        DB::beginTransaction();
        try {
            $automationUser = AutomationUser::create([
                'user_id' => $request->input('user_id'),
                'name' => 'Thông báo xác nhận',
                'status' => 0,
                'template_id' => null,
            ]);

            $automationUser->save();
            Log::info('Tạo thông báo xác nhận thành công');
            DB::commit();
            return response()->json(['success', 'Tạo thông báo xác nhận thành công']);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to create new Automation User: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Tạo thông báo xác nhận thất bại']);
        }
    }
}
