<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AutomationBirthday;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AutomationBirthdayController extends Controller
{
    public function automationBirthday(Request $request)
    {
        DB::beginTransaction();
        try {
            $automationBirthday = AutomationBirthday::create([
                'user_id' => $request->input('user_id'),
                'name' => 'Chúc mừng sinh nhật',
                'status' => 0,
                'template_id' => null,
                'start_time' => '09:00:00',
            ]);

            $automationBirthday->save();
            Log::info("Tạo thông báo chúc mừng sinh nhật thành công");
            DB::commit();
            return response()->json(['success', 'Tạo thông báo chúc mừng sinh nhật thành công']);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to create new Automation Birthday: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Tạo thông báo chúc mừng sinh nhật thất bại']);
        }
    }
}
