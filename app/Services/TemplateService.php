<?php

namespace App\Services;

use App\Models\Automation;
use App\Models\AutomationUser;
use App\Models\OaTemplate;
use App\Models\Template;
use App\Models\ZaloOa;
use App\Models\ZaloUser;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TemplateService
{

    public function zns($user_id, $data)
    {
            $automation = AutomationUser::with('template')->first() ?? [];
            $oatemplate =  $automation->template->template_id;
            $zalo_oa = ZaloOa::where('user_id', $user_id)->first();
            // Log::info($automation);
            // $template = $automation->template->templateId;
            $template_data = $this->templateData($data['name'], $data['phone'], $data['address']);
          
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'access_token' => $zalo_oa->access_token
            ])->post('https://business.openapi.zalo.me/message/template', [
                'phone' => preg_replace('/^0/', '84', $data['phone']),
                'template_id' => 377581,
                'template_data' => $template_data
            ]);

            Log::info( $response->json()); 
            if ($response->json()['error'] != 0) {

                return response()->json(['success' => false, 'message' => 'Request failed'], 500);
            }

            // return $response->json();

    }

    public function templateData($customer_name, $phone_number, $address, )
    {
        $template_data = [
            'name' => $customer_name,
            'date' => Carbon::now()->format('d/m/Y') ?? "",
            'phone_number' => $phone_number,
            'status' => 'Đăng ký thành công',
            'price' => number_format(10000),
            'address' => $address,
            'payment' => 'Chuyển khoản ngân hàng',
            'phone' => $phone_number,
            'payment_status' => 'Chuyển khoản thành công',
            'customer_name' => $customer_name ?? '',
            'time' => Carbon::now()->format('h:i:s d/m/Y') ?? "",
            'order_date' => Carbon::now()->format('d/m/Y') ?? "",
            'product_name' => $customer_name,
        ];

        Log::info($template_data);
        return $template_data;
    }

    public function detailTemplate($templateId)
    {

        $zaloOa = ZaloOa::first() ?? [];
        if ($zaloOa) {
            $response = Http::withHeaders([
                'access_token' => $zaloOa->access_token,
                'Content-Type' => 'application/json',
            ])->get('https://business.openapi.zalo.me/template/info/v2', [
                'template_id' => $templateId,
            ]);


            if ($response->json()['error'] != 0) {
                return response()->json(['success' => false, 'message' => 'Request failed'], 500);
            }
            return  $response->json()['data'];
        }
    }
}
