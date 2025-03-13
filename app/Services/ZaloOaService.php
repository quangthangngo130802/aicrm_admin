<?php

namespace App\Services;

use App\Models\OaTemplate;
use App\Models\User;
use App\Models\ZaloOa;
use App\Models\ZnsMessage;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class ZaloOaService
{
    protected $oaTemplate;
    protected $zaloOa;
    protected $znsMessage;
    protected $client;

    public function __construct(OaTemplate $oaTemplate, ZaloOa $zaloOa, ZnsMessage $znsMessage, Client $client)
    {
        $this->oaTemplate = $oaTemplate;
        $this->zaloOa = $zaloOa;
        $this->znsMessage = $znsMessage;
        $this->client = $client;
    }

    public function addNewOa(array $data)
    {
        DB::beginTransaction();
        try {
            $zaloOa = $this->zaloOa->create([
                'name' => $data['name'],
                'access_token' => $data['access_token'],
                'oa_id' => $data['oa_id'],
                'refresh_token' => $data['refresh_token'],
                'is_active' => 0,
                'user_id' => Auth::user()->id,
                'access_token_expiration' => now()->addHour(23),
            ]);
            $data['is_active'] = 0;
            $data['user_id'] = Auth::user()->id;
            $data['access_token_expiration'] = now()->addHour(23);
            Log::info('Oa added successfully, start transfering data to SuperAdmin');
            $zalOaSuperAdminApiUrl = config('app.api_url') . '/api/add-zalo';

            $client = new Client();

            $response = $client->post($zalOaSuperAdminApiUrl, [
                'form_params' => $data
            ]);

            if ($response->getStatusCode() !== 200) {
                throw new Exception('Failed to add associate to super admin');
            } else {
                Log::info('Associate added to super admin successfully');
            }

            Log::info('Oa added to Super Admin successfully');
            DB::commit();
            return $zaloOa;
        } catch (Exception $e) {
            Log::error('Failed to add new OA to database: ' . $e->getMessage());
            throw new Exception('Failed to add new OA to database');
        }
    }


    public function getAccessToken()
    {
        //Lấy OA từ database
        $oa = ZaloOa::where('user_id', Auth::user()->id)->where('is_active', 1)->first();
        if (!$oa) {
            Log::error('Không tìm thấy OA nào có trạng thái là is_active');
            throw new Exception('Không tìm thấy OA đang hoạt động ');
        }

        $accessToken = $oa->access_token;
        $accessTokenExpiration = $oa->access_token_expiration;

        //Nếu không có access token hoặc access token đã hết hạnn
        if (!$accessToken || now()->greaterThan($accessTokenExpiration)) {
            Log::info('Access token hết hạn hoặc không tồn tại, đang refresh access token');

            //Làm mói access token bằng refresh token từ db
            $accessToken = $this->refreshAccessToken($oa->refresh_token, $oa);
        }

        Log::info('Đang lấy Access Token: ' . $accessToken);
        return $accessToken;
    }

    public function refreshAccessToken($refreshToken, $oa)
    {
        $client = new Client();
        $secretKey = env('ZALO_APP_SECRET');
        $appId = env('ZALO_APP_ID');

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

            $body = json_decode($response->getBody()->getContents(), true);

            Log::info('Refresh access token thành công: ', $body);

            if (isset($body['access_token'])) {
                // Cập nhật access token và thời gian hết hạn cho OA hiện tại
                $oa->access_token = $body['access_token'];
                $oa->access_token_expiration = now()->addHour(23);

                if (isset($body['refresh_token'])) {
                    $oa->refresh_token = $body['refresh_token'];
                }

                $oa->save();

                $relatedOas = ZaloOa::where('oa_id', $oa->oa_id)->get();

                foreach ($relatedOas as $relatedOa) {
                    $relatedOa->access_token = $body['access_token'];
                    $relatedOa->access_token_expiration = now()->addHour(23);

                    if (isset($body['refresh_token'])) {
                        $relatedOa->refresh_token = $body['refresh_token'];
                    }

                    $relatedOa->save();
                }

                return $body['access_token'];
            } else {
                throw new Exception('Failed to refresh access token');
            }
        } catch (Exception $e) {
            Log::error('Failed to refresh access token: ' . $e->getMessage());
            throw new Exception('Failed to refresh access token');
        }
    }
}
