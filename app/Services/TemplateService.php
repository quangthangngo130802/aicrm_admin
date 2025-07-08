<?php

namespace App\Services;

use App\Models\Automation;
use App\Models\AutomationUser;
use App\Models\Customer;
use App\Models\OaTemplate;
use App\Models\Template;
use App\Models\User;
use App\Models\ZaloOa;
use App\Models\ZaloUser;
use App\Models\ZnsMessage;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TemplateService
{

    public function zns($user_id, $data)
    {
            $user = User::find($user_id);
            Log::info($user);
            $automation = AutomationUser::with('template')->first() ?? [];
            // $oatemplate =  $automation->template->template_id;
            $zalo_oa = ZaloOa::where('user_id', $user_id)->first();
            $template = $automation->template;
            Log::info($template);
            $template_id = $template->id;
            $template_code = $automation->template->template_id;
            $template_data = $this->templateData($data['name'], $data['phone'], $data['address'],  $data['order_code'],  $data['product_name']);

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'access_token' => $zalo_oa->access_token
            ])->post('https://business.openapi.zalo.me/message/template', [
                'phone' => preg_replace('/^(0|84)?/', '84', $data['phone']),
                'template_id' => $template_code,
                'template_data' => $template_data
            ]);


            $note = $response->json()['message'];
            Log::info( $response->json());
            if ($response->json()['error'] != 0) {
                $status = 0;
            }else{
                $this->update_createClient($data['name'],$data['phone'],'',$data['address'],$user_id,'','');
                $status = 1;
                $user->wallet = $user->wallet - $template->price;
                $user->save();
            }

            $this->sendMessage($data['name'], $data['phone'], $status, $note, $template_id, $zalo_oa->id, $user_id);
            return $response->json();

    }

    public function templateData($customer_name, $phone_number, $address, $order_code , $product_name)
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
            'product_name' => $product_name ?? '',
            'order_code' => $order_code?? ''
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

    public function sendMessage($name, $phone, $status, $note, $template_id, $oa_id, $user_id)
    {
        $data = [
            'name' => $name,
            'phone' => $phone,
            'sent_at' => Carbon::now(),
            'status' => $status,
            'note' => $note,
            'oa_id' => $oa_id,
            'template_id' => $template_id,
            'user_id' => $user_id,
        ];
        // Lưu tin nhắn vào cơ sở dữ liệu
        ZnsMessage::create($data);

        Log::info('Tin nhắn đã được lưu vào cơ sở dữ liệu thành công.');

        $sendMessageApiUrl = config('app.api_url') . '/api/add-message';

        $client = new Client();
        $response = $client->post($sendMessageApiUrl, [
            'form_params' => $data,
        ]);

    }

    public function update_createClient($name, $phone, $email, $address, $user_id, $product_id, $dob)
    {
        // Nếu đã tồn tại khách hàng với số điện thoại này, bỏ qua
        if (Customer::where('phone', $phone)->exists()) {
            return null; // hoặc return 'exists';
        }

        $customer = Customer::create([
            'name'       => $name,
            'phone'      => $phone,
            'email'      => $email ?? null,
            'address'    => $address ?? null,
            'source'     => 'Gg sheet',
            'user_id'    => $user_id,
            'product_id' => !empty($product_id) ? (int) $product_id : null,
            'code'       => $this->generateCode($user_id, $phone),
            'dob'        => !empty($dob) ? Carbon::parse($dob) : null,
        ]);

        return $customer;
    }


    public function generateCode($user_id, $phone)
    {
        $lastFourDigits = substr($phone, -4);
        $prefix = User::find($user_id)->prefix;
        return $prefix . '_' . $lastFourDigits;
    }

}
