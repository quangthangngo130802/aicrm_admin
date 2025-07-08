<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ZaloOa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TokenController extends Controller
{
    //

    public function index(){
        $user = Auth::user();
        $user_id = $user->id;

        $zaloOa = ZaloOa::where('user_id', $user_id)->get();

        

    }
}
