<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AutomationReminder;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AutomationReminderController extends Controller
{
    public function automationReminder(Request $request)
    {
        DB::beginTransaction();
        try {
            $automationReminder = AutomationReminder::create([
                'user_id' => $request->input('user_id'),
                'name' => 'Nhắc nhở',
                'status' => 0,
                'template_id' => null,
                'sent_time' => '09:00:00',
                'numbertime' => 0,
            ]);

            $automationReminder->save();
            Log::info('Automation Reminder created successfully');
            DB::commit();
            return response()->json(['success', 'Tạo thông báo nhắc lại thành công']);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to create automation reminder: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Tạo thông báo nhắc lại thất bại']);
        }
    }
}
