<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AutomationBirthday;
use App\Models\AutomationRate;
use App\Models\AutomationReminder;
use App\Models\AutomationUser;
use App\Models\OaTemplate;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AutomationMarketingController extends Controller
{
    public function index()
    {
        $title = "Automation Marketing";
        $templates = OaTemplate::whereHas('zaloOa', function ($query) {
            $query->where('user_id', Auth::user()->id);
        })->get();
        $birthday = AutomationBirthday::where('user_id', Auth::user()->id)->first();
        $user = AutomationUser::where('user_id', Auth::user()->id)->first();
        $rate = AutomationRate::where('user_id', Auth::user()->id)->first();
        $reminder = AutomationReminder::where('user_id', Auth::user()->id)->first();
        return view('admin.automation_marketing.index', compact('birthday', 'user', 'reminder', 'templates', 'rate', 'title'));
    }

    public function updateReminderStatus(Request $request)
    {
        try {
            $automationReminder = AutomationReminder::where('user_id', Auth::user()->id)->first();
            $automationReminder->status = $request->input('reminder_status');
            $automationReminder->save();
            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Cập nhật trạng thái thất bại', 'error' => $e->getMessage()]);
        }
    }
    public function updateBirthdayStatus(Request $request)
    {
        try {
            $automationBirthday = AutomationBirthday::where('user_id', Auth::user()->id)->first();
            $automationBirthday->status = $request->input('birthday_status');
            $automationBirthday->save();
            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Cập nhật trạng thái thất bại', 'error' => $e->getMessage()]);
        }
    }

    public function updateRateStatus(Request $request)
    {
        try {
            $automationRate = AutomationRate::where('user_id', Auth::user()->id)->first();
            $automationRate->status = $request->input('rate_status');
            $automationRate->save();
            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Cập nhật trạng thái thất bại', 'error' => $e->getMessage()]);
        }
    }

    public function updateUserStatus(Request $request)
    {
        try {
            // Kiểm tra nếu không có chiến dịch nào được tìm thấy sẽ gây lỗi
            $automationUser = AutomationUser::where('user_id', Auth::user()->id)->first();
            $automationUser->status = $request->input('status');
            $automationUser->save();
            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Cập nhật trạng thái thất bại', 'error' => $e->getMessage()]);
        }
    }

    public function updateReminderTemplate(Request $request)
    {
        try {


            $automationReminder = AutomationReminder::where('user_id', Auth::user()->id)->first();
            $automationReminder->template_id = $request->input('reminder_template_id');
            $automationReminder->save();

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            Log::error('Failed to update rating status: '. $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Cập nhật trạng thái Reminder thất bại', 'error' => $e->getMessage()]);
        }
    }

    public function updateBirthdayTemplate(Request $request)
    {
        try {


            $automationBirthday = AutomationBirthday::where('user_id', Auth::user()->id)->first();
            $automationBirthday->template_id = $request->input('birthday_template_id');
            $automationBirthday->save();

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            Log::error('Failed to update rating status: '. $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Cập nhật trạng thái Rating thất bại', 'error' => $e->getMessage()]);
        }
    }

    public function updateRateTemplate(Request $request)
    {

        try {
            $automationRate = AutomationRate::where('user_id', Auth::user()->id)->first();
            $automationRate->template_id = $request->input('rate_template_id');
            $automationRate->save();

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            Log::error('Failed to update rating status: '. $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Cập nhật trạng thái Rating thất bại', 'error' => $e->getMessage()]);
        }
    }

    public function updateUserTemplate(Request $request)
    {
        try {

            $automationUser = AutomationUser::where('user_id', Auth::user()->id)->first();
            $automationUser->template_id = $request->input('template_id');
            $automationUser->save();

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            Log::error('Error updating template:', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Cập nhật trạng thái thất bại', 'error' => $e->getMessage()]);
        }
    }

    public function updateRateStartTime(Request $request)
    {

        try {
            $automationRate = AutomationRate::where('user_id', Auth::user()->id)->first();
            $automationRate->start_time = $request->input('start_time');
            $automationRate->save();

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            Log::error('Error updating start_time: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Cập nhật giờ gửi thất bại']);
        }
    }

    public function updateReminderStartTime(Request $request)
    {
        try {
            $automationReminder = AutomationReminder::where('user_id', Auth::user()->id)->first();
            $automationReminder->sent_time = $request->input('sent_time');
            $automationReminder->save();

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            Log::error('Error updating start_time: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Cập nhật giờ gửi thất bại']);
        }
    }

    public function updateBirthdayStartTime(Request $request)
    {
        try {
            $automationBirthday = AutomationBirthday::where('user_id', Auth::user()->id)->first();
            $automationBirthday->start_time = $request->input('start_time');
            $automationBirthday->save();

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            Log::error('Error updating start_time: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Cập nhật giờ gửi thất bại']);
        }
    }

    public function updateReminderSendingCycle(Request $request)
    {
        try {
            $automationReminder = AutomationReminder::where('user_id', Auth::user()->id)->first();
            $automationReminder->numbertime = $request->input('numbertime');
            $automationReminder->save();

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            Log::error('Error updating sending cycle: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Cập nhật chu kỳ gửi thất bại']);
        }
    }


    public function updateRateSendingCycle(Request $request)
    {
        try {
            $automationReminder = AutomationRate::where('user_id', Auth::user()->id)->first();
            $automationReminder->numbertime = $request->input('numbertime');
            $automationReminder->save();

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            Log::error('Error updating sending cycle: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Cập nhật chu kỳ gửi thất bại']);
        }
    }

}
