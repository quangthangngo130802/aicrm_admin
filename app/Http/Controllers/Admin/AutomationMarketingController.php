<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AutomationBirthday;
use App\Models\AutomationReminder;
use App\Models\AutomationUser;
use App\Models\OaTemplate;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AutomationMarketingController extends Controller
{
    public function index()
    {
        $templates = OaTemplate::get();
        $birthday = AutomationBirthday::first();
        $user = AutomationUser::first();
        $reminder = AutomationReminder::first();
        return view('admin.automation_marketing.index', compact('birthday', 'user', 'reminder', 'templates'));
    }

    public function updateUserStatus(Request $request)
    {
        try {
            // Kiểm tra nếu không có chiến dịch nào được tìm thấy sẽ gây lỗi
            $automationUser = AutomationUser::first();
            $automationUser->status = $request->input('status');
            $automationUser->save();
            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Cập nhật trạng thái thất bại', 'error' => $e->getMessage()]);
        }
    }

    public function updateUserTemplate(Request $request)
    {
        try {
            Log::info('Request data:', $request->all()); // Ghi log dữ liệu yêu cầu

            $automationUser = AutomationUser::findOrFail($request->input('user_id'));
            $automationUser->template_id = $request->input('template_id');
            $automationUser->save();

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            Log::error('Error updating template:', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Cập nhật trạng thái thất bại', 'error' => $e->getMessage()]);
        }
    }
}
