<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\StoreService;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendZnsReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $phone, $templateCode, $templateData, $oaId, $templateId, $userId, $reminderPrice, $accessToken, $sentTime, $numbertime, $automationReminderStatus;
    /**
     * Create a new job instance.
     */
    public function __construct($phone, $templateCode, $templateData, $oaId, $templateId, $userId, $reminderPrice, $accessToken, $sentTime, $numbertime, $automationReminderStatus)
    {
        $this->phone = $phone;
        $this->templateCode = $templateCode;
        $this->templateData = $templateData;
        $this->templateId = $templateId;
        $this->oaId = $oaId;
        $this->userId = $userId;
        $this->accessToken = $accessToken;
        $this->reminderPrice = $reminderPrice;
        $this->sentTime = $sentTime;
        $this->numbertime = $numbertime;
        $this->automationReminderStatus = $automationReminderStatus;
    }

    /**
     * Execute the job.
     */
    public function handle(StoreService $storeService): void
    {
        if ($this->automationReminderStatus == 1) {
            try {
                $user = User::find($this->userId);
                if ($user->sub_wallet >= $this->reminderPrice || $user->wallet >= $this->reminderPrice) {
                    Log::info('Start sending reminder message');
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
                        Log::info('Reminder message sent successfully');

                        $storeService->deductMoneyFromAdminWallet($this->userId, $this->reminderPrice);
                        if ($user->sub_wallet >= $this->reminderPrice) {
                            $user->sub_wallet -= $this->reminderPrice;
                        } elseif ($user->wallet >= $this->reminderPrice) {
                            $user->wallet -= $this->reminderPrice;
                        }
                        $user->save();
                    } else {
                        Log::error('Failed to send reminder message: ' . $responseBody);
                    }
                } else {
                    Log::warning('Not enough money in both wallets');
                }
            } catch (Exception $e) {
                Log::error('Failed to send reminder message: ' . $e->getMessage());
                $storeService->sendMessage($this->templateData['name'], $this->phone, 0, $e->getMessage(), $this->templateId, $this->oaId, $this->userId);
            }
        }
    }
}
