<?php

namespace App\Jobs;

use App\Models\AutomationBirthday;
use App\Models\User;
use App\Services\StoreService;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SendZnsBirthdayJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $phone, $templateCode, $templateData, $oaId, $templateId, $userId, $birthdayPrice, $accessToken, $startTime, $dob, $automationBirthdayStatus;

    /**
     * Tạo một đối tượng công việc mới.
     *
     * @param string $phone
     * @param string $templateCode
     * @param array $templateData
     * @param int $oaId
     * @param int $templateId
     * @param int $userId
     * @param float $birthdayPrice
     * @param string $accessToken
     * @param string $startTime
     */
    public function __construct($phone, $templateCode, $templateData, $oaId, $templateId, $userId, $birthdayPrice, $accessToken, $startTime, $dob, $automationBirthdayStatus)
    {
        $this->phone = $phone;
        $this->templateCode = $templateCode;
        $this->templateData = $templateData;
        $this->oaId = $oaId;
        $this->templateId = $templateId;
        $this->userId = $userId;
        $this->birthdayPrice = $birthdayPrice;
        $this->accessToken = $accessToken;
        $this->startTime = $startTime;
        $this->dob = $dob;
        $this->automationBirthdayStatus = $automationBirthdayStatus;
    }

    /**
     * Thực thi công việc gửi tin nhắn sinh nhật.
     */
    public function handle(StoreService $storeService)
    {
        if ($this->automationBirthdayStatus == 1) {
            try {
                $user = User::find($this->userId);
                if ($user->sub_wallet >= $this->birthdayPrice || $user->wallet >= $this->birthdayPrice) {
                    Log::info("Bắt đầu gửi tin nhắn chúc mừng sinh nhật");
                    $client = new Client();
                    $response = $client->post('https://business.openapi.zalo.me/message/template', [
                        'headers' => [
                            'access_token' => $this->accessToken,
                            'Content-Type' => 'application/json'
                        ],
                        'json' => [
                            'phone' => preg_replace('/^0/', '84', $this->phone),
                            'template_id' => $this->templateCode,
                            'template_data' => $this->templateData,
                        ]
                    ]);

                    $responseBody = $response->getBody()->getContents();
                    Log::info('Zalo API Response: ' . $responseBody);

                    $responseData = json_decode($responseBody, true);
                    $status = $responseData['error'] == 0 ? 1 : 0;
                    $storeService->sendMessage($this->templateData['name'], $this->phone, $status, $responseData['message'], $this->templateId, $this->oaId, $this->userId);
                    if ($status == 1) {
                        Log::info('Birthday message sent successfully');

                        $storeService->deductMoneyFromAdminWallet($this->userId, $this->birthdayPrice);

                        if ($user->sub_wallet >= $this->birthdayPrice) {
                            $user->sub_wallet -= $this->birthdayPrice;
                        } elseif ($user->wallet >= $this->birthdayPrice) {
                            $user->wallet -= $this->birthdayPrice;
                        }
                        $user->save();
                    } else {
                        Log::error('Failed to send birthday message: ' . $responseBody);
                    }
                    $today = Carbon::now();
                    $startTime = Carbon::parse($this->startTime);
                    $birthdayNextYear = Carbon::create($today->year, $this->dob->month, $this->dob->day, $startTime->hour, $startTime->minute, $startTime->second, $today->timezone)->addYear();
                    SendZnsBirthdayJob::dispatch($this->phone, $this->templateCode, $this->templateData, $this->oaId, $this->templateId, $this->userId, $this->birthdayPrice, $this->accessToken, $this->startTime, $this->dob, $this->automationBirthdayStatus)->delay($birthdayNextYear);
                    Log::info('Scheduling sending ZNS birthday for next year in: ' .  $birthdayNextYear);
                } else {
                    Log::warning('Not enough money in both wallets');
                }
            } catch (Exception $e) {
                Log::error('Failed to send birthday message: ' . $e->getMessage());
                $storeService->sendMessage($this->templateData['name'], $this->phone, 0, $e->getMessage(), $this->templateId, $this->oaId, $this->userId);
            }
        } else {
            Log::info('Automation Birthday is not activate');
            return;
        }
    }
}
