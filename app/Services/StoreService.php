<?php

namespace App\Services;

use App\Mail\UserRegistered;
use App\Models\AutomationUser;
use App\Models\Config;
use App\Models\Customer;
use App\Models\User;
use App\Models\ZaloOa;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class StoreService
{
    protected $user;
    protected $automationUser;
    protected $signUpService;
    public function __construct(User $user, AutomationUser $automationUser, SignUpService $signUpService)
    {
        $this->user = $user;
        $this->automationUser = $automationUser;
        $this->signUpService = $signUpService;
    }

    public function getAllStore(): LengthAwarePaginator
    {
        try {
            return Customer::where('user_id', Auth::id())->orderByDesc('created_at')->paginate(10);
        } catch (Exception $e) {
            Log::error('Failed to fetch stores: ' . $e->getMessage());
            throw new Exception('Failed to fetch stores');
        }
    }

    public function findStoreByID($id)
    {
        try {
            // dd($id);
            return Customer::where('user_id', Auth::id())->find($id);
        } catch (Exception $e) {
            Log::error('Failed to find store info: ' . $e->getMessage());
            throw new Exception('Failed to find store info');
        }
    }

    public function findOwnerByPhone($phone)
    {
        try {
            $customer = Customer::where('user_id', Auth::id())
                ->where('phone', 'like',  "%{$phone}%")
                ->where('role_id', 1)
                ->first();
            return $customer;
        } catch (Exception $e) {
            Log::error('Failed to find client profile: ' . $e->getMessage());
            throw new Exception('Failed to find client profile');
        }
    }

    public function deleteStore($id)
    {
        try {
            // dd($id);
            Log::info("Deleting store");
            $store = Customer::where('user_id', Auth::id())->find($id);
            $store->delete();
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete store profile: ' . $e->getMessage());
            throw new Exception('Failed to delete store profile');
        }
    }

    public function addNewStore(array $data)
    {
        DB::beginTransaction();
        try {
            Log::info('Creating new client');
            // dd(Auth::user()->id);
            $user_id = Auth::user()->id;
            $client = Customer::create([
                'name' => $data['name'],
                'phone' => $data['phone'],
                'email' => $data['email'],
                'address' => $data['address'],
                'source' => $data['source'] ?? 'Thêm thủ công',
                'user_id' => $user_id,
            ]);

            // $user = AutomationUser::first();
            // if ($user->status != 1) {
            //     return;
            // } else {
            //     $accessToken = $this->getAccessToken();
            //     $oa_id = ZaloOa::where('is_active', 1)->first()->id;

            //     try {
            //         // Gửi yêu cầu tới API Zalo
            //         $clientapi = new Client();
            //         $response = $clientapi->post('https://business.openapi.zalo.me/message/template', [
            //             'headers' => [
            //                 'access_token' => $accessToken,
            //                 'Content-Type' => 'application/json'
            //             ],
            //             'json' => [
            //                 'phone' => preg_replace('/^0/', '84', $data['phone']),
            //                 'template_id' => '355330',
            //                 'template_data' => [
            //                     'date' => Carbon::now()->format('d/m/Y') ?? "",
            //                     'name' => $data['name'] ?? "",
            //                     'order_code' => $client->id,
            //                     'phone_number' => $data['phone'],
            //                     'status' => 'Đăng ký thành công'
            //                 ]
            //             ]
            //         ]);
            //     } catch (Exception $e) {
            //         Log::error('Lỗi khi gửi tin nhắn: ' . $e->getMessage());
            //     }
            // }


            DB::commit();
            return $client;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Failed to add new client: " . $e->getMessage());
            throw new Exception('Failed to add new client');
        }
    }

    protected function getAccessToken()
    {
        $oa = ZaloOa::where('is_active', 1)->first();

        if (!$oa) {
            Log::error('Không tìm thấy OA nào có trạng thái is_active = 1');
            throw new Exception('Không tìm thấy OA nào có trạng thái is_active = 1');
        }

        $accessToken = $oa->access_token;
        $refreshToken = $oa->refresh_token;

        if (!$accessToken || Cache::has('access_token_expired')) {
            $secretKey = env('ZALO_APP_SECRET');
            $appId = env('ZALO_APP_ID');
            $accessToken = $this->refreshAccessToken($refreshToken, $secretKey, $appId);

            $oa->update(['access_token' => $accessToken]);
        }

        Log::info('Retrieved access token: ' . $accessToken);
        return $accessToken;
    }

    protected function refreshAccessToken($refreshToken, $secretKey, $appId)
    {
        $client = new Client();
        try {
            $response = $client->post('https://oauth.zaloapp.com/v4/oa/access_token', [
                'headers' => [
                    'secret_key' => $secretKey,
                ],
                'form_params' => [
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $refreshToken,
                    'app_id' => $appId,
                ]
            ]);

            $body = json_decode($response->getBody(), true);
            Log::info("Refresh token response: " . json_encode($body));

            if (isset($body['access_token'])) {
                // Lưu access token vào cache và đặt thời gian hết hạn là 24h
                Cache::put('access_token', $body['access_token'], 86400);
                Cache::forget('access_token_expired');

                if (isset($body['refresh_token'])) {
                    Cache::put('refresh_token', $body['refresh_token'], 7776000);
                }
                return [$body['access_token'], $body['refresh_token']];
            } else {
                throw new Exception('Failed to refresh access token');
            }
        } catch (Exception $e) {
            Log::error('Failed to refresh access token: ' . $e->getMessage());
            throw new Exception('Failed to refresh access token');
        }
    }
}
