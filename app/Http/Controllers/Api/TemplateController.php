<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OaTemplate;
use App\Models\ZaloOa;
use Illuminate\Http\Request;

class TemplateController extends Controller
{
    //
    public function listtemplate(){
        $listtemplates = OaTemplate::with('zaloOa')->get();
        $zaloOas = ZaloOa::get();

        return response()->json([
            'status' => 200,
            'data' => $listtemplates,
            'zaoOa' => $zaloOas
        ]);
    }
}
