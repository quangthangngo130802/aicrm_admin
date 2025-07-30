<?php

namespace App\Jobs;

use App\Models\OaTemplate;
use App\Models\ZaloOa;
use App\Models\ZnsMessage;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class ResherTokenZalo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */


    public function handle(): void
    {
        $client = new Client();
        $appId = config('app.zalo.app_id');
        $secretKey = config('app.zalo.app_secret');

        $zaloOas = ZaloOa::get();

        foreach ($zaloOas as $key => $zaloOa) {
            try {
                $response = $client->post('https://oauth.zaloapp.com/v4/oa/access_token', [
                    'headers' => [
                        'secret_key' => $secretKey,
                    ],
                    'form_params' => [
                        'grant_type' => 'refresh_token',
                        'refresh_token' => $zaloOa->refresh_token,
                        'app_id' => $appId,
                    ]
                ]);

                $body = json_decode($response->getBody()->getContents(), true);



                if (isset($body['access_token'])) {
                    Log::info("OA {$zaloOa->oa_id} - Refresh access token thành công", $body);
                    $zaloOa->access_token = $body['access_token'];
                    $zaloOa->access_token_expiration = now()->addHour(23);

                    if (isset($body['refresh_token'])) {
                        $zaloOa->refresh_token = $body['refresh_token'];
                    }

                    $zaloOa->save();

                    $relatedOas = ZaloOa::where('oa_id', $zaloOa->oa_id)->get();

                    foreach ($relatedOas as $relatedOa) {
                        $relatedOa->access_token = $body['access_token'];
                        $relatedOa->access_token_expiration = now()->addHour(23);

                        if (isset($body['refresh_token'])) {
                            $relatedOa->refresh_token = $body['refresh_token'];
                        }

                        $relatedOa->save();
                    }
                } else {
                    Log::error("OA {$zaloOa->oa_id} - Response không có access_token", $body);
                }
            } catch (Exception $e) {
                Log::error("OA {$zaloOa->oa_id} - Lỗi refresh token: " . $e->getMessage());

            }
        }
    }
}
