<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TemplateController extends Controller
{
    //
    public function index(){
        return view('admin.template.index');
    }

    public function storeTemplate(Request $request){

        // dd($request->all());
        if($request->has('logo_light')){
            $logo_light = $this->uploadImage($request->file('logo_light'));
            return $logo_light;
        }

        if($request->has('logo_dark')){
            $logo_dark = $this->uploadImage($request->file('logo_dark'));
            return $logo_dark;
        }



    }

    public function uploadImage($image)
    {
        $accessToken = 'In9C2GT6J6vD4aSE14vCRaeLGWaQ511o51jHOn0q258a0M1UBq0KNpmTH3vo7oWzI3md0bWWN09C4piYUIumAq4LJYTf7YmVK2Du5bWzUJD567CXJXKU07iTJ6CpD3ry9YnmQ08X3qixVN5KBNSiGnnDM7yt32HW1pbbG0DiQtq_U09Y0ZaEP10RQLKK7G1SE3XPKXSiCZO-D605BniwC2aeLXOmF1mGUcX3Fduf435K0MO5RmWaFt039L1MNMrXQb0G14PNSXPnUNulM7XhAczW8WboEL4yNZTS7cKO5oqjHW8n1sng0Z4t225sArHFJZvORdK4BK9y6KLsOmOGPYKVPN8J0IzYFY9XNmT_7KD8NdHPUrO3EszFGZrKMazPP6y_HdbXDNH8M7z3LL8TKMTmJKzOMWPEVcmuLW5GGcm';

        $response = Http::attach(
            'file', file_get_contents($image->getRealPath()), $image->getClientOriginalName()
        )->withHeaders([
            'access_token' => $accessToken,
        ])->post('https://business.openapi.zalo.me/upload/image');

        return $response->json();
    }
}
