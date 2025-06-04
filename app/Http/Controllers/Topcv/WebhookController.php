<?php

namespace App\Http\Controllers\Topcv;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    //
    public function handle()
    {
        Log::info(1);
        // // Nhận dữ liệu từ TopCV
        // $data = $request->all();
        // // Ghi log dữ liệu nhận được (có thể thay thế bằng xử lý dữ liệu)
        // Log::info('Received CV data from TopCV:', $data);ss
        // // Xử lý dữ liệu ở đây (lưu vào database, gửi email, v.v.)
        // // Trả về phản hồi cho TopCV
        // // return response()->json(['status' => 'success'], 200);
    }
}
