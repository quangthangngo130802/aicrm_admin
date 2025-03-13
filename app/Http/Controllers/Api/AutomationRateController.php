<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AutomationRate;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AutomationRateController extends Controller
{
    public function automationRate(Request $request)
    {
        DB::beginTransaction();
        try {
            $automationRate = AutomationRate::create([
                'user_id' => $request->input('user_id'),
                'name' => 'Đánh giá dịch vụ/sản phẩm',
                'status' => 0,
                'template_id' => null,
            ]);

            $automationRate->save();
            Log::info('Tạo Đánh giá dịch vụ/sản phẩm thành công');
            DB::commit();
            return response()->json(['success', 'Tạo Đánh giá dịch vụ/sản phẩm thành công']);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to create new Automation Rate: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Tạo đánh giá dịch vụ/sản phẩm thất bại']);
        }
    }
}
