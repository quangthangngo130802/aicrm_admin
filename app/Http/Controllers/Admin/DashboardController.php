<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rate;
use App\Models\SuperAdmin;
use App\Models\Transaction;
use App\Models\ZaloOa;
use App\Models\ZnsMessage;
use Aws\Token\Token;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function default()
    {
        return view('web_default');
    }
    //
    public function index()
    {
        $title = 'Bảng thống kê hệ thống gửi tin nhắn Zalo ZNS';
        // $toleprice = Transaction::sum('amount');

        $toleprice = ZnsMessage::where('user_id', Auth::user()->id)->where('status', 1)
            ->with('template')
            ->get()
            ->sum(function ($message) {
                return $message->template ? $message->template->price : 0;
            });
        $success = ZnsMessage::where('user_id', Auth::user()->id)->where('status', 1)->count();
        $fail = ZnsMessage::where('user_id', Auth::user()->id)->where('status', 0)->count();
        $oa = ZaloOa::where('user_id', Auth::user()->id)->count();
        $client = new Client();
        $getBannerApiUrl = config('app.api_url') . '/api/get-banners';
        $response = $client->get($getBannerApiUrl);
        $responseBody =  json_decode($response->getBody()->getContents());
        $banners = json_decode($responseBody->banner);

        $rate =  Rate::where('user_id', Auth::user()->id)->count();

        $ratingCounts = Rate::where('user_id', Auth::user()->id)->select('rate', DB::raw('count(*) as total'))
            ->groupBy('rate')
            ->pluck('total', 'rate') // kết quả: [1 => 5, 2 => 8, ...]
            ->toArray();

        // Đảm bảo đủ từ 1 đến 5 sao, nếu thiếu thì thêm 0
        $chartData = [];
        for ($i = 1; $i <= 5; $i++) {
            $chartData[] = $ratingCounts[$i] ?? 0;
        }
        return view("admin.dashboard.index", compact('title', 'toleprice', 'success', 'fail', 'oa', 'banners', 'rate', 'chartData'));
    }
}
