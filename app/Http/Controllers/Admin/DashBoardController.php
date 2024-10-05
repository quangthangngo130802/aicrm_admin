<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\ZaloOa;
use App\Models\ZnsMessage;
use Aws\Token\Token;
use Illuminate\Http\Request;

class DashBoardController extends Controller
{
    //
    public function index()
    {
        $title = 'Dashboard';
        // $toleprice = Transaction::sum('amount');

        $toleprice = ZnsMessage::where('status', 1)
            ->with('template')
            ->get()
            ->sum(function ($message) {
                return $message->template ? $message->template->price : 0;
            });

        $success = ZnsMessage::where('status', 1)->count();
        $fail = ZnsMessage::where('status', 0)->count();
        $oa = ZaloOa::count();
        return view("admin.dashboard.index", compact('title', 'toleprice', 'success', 'fail', 'oa'));
    }
}
