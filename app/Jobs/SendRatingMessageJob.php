<?php

namespace App\Jobs;

use App\Models\ZnsMessage;
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
use Illuminate\Support\Facades\Log;

class SendRatingMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $phone;
    protected $templateCode;
    protected $templateData;
    protected $oaId;
    protected $templateId;
    protected $userId;
    protected $ratePrice;
    protected $accessToken;

    /**
     * Create a new job instance.
     *
     * @param string $phone
     * @param string $templateCode
     * @param array $templateData
     * @param int $oaId
     * @param int $templateId
     * @param int $userId
     * @param float $ratePrice
     */
    public function __construct($phone, $templateCode, $templateData, $oaId, $templateId, $userId, $ratePrice, $accessToken)
    {
        $this->phone = $phone;
        $this->templateCode = $templateCode;
        $this->templateData = $templateData;
        $this->oaId = $oaId;
        $this->templateId = $templateId;
        $this->userId = $userId;
        $this->ratePrice = $ratePrice;
        $this->accessToken = $accessToken;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $storeService = app()->make(StoreService::class);
        try {
            $user = User::find($this->userId);
            Log::info('Thông tin người dùng: ' . $user);
            // Kiểm tra số dư ví của người dùng
            if ($user->sub_wallet < $this->ratePrice && $user->wallet < $this->ratePrice) {
                Log::warning("Not enough funds to send rating message for user ID: {$this->userId}");

                $storeService->sendMessage($this->templateData['name'], $this->phone, 0, 'Tài khoản không đủ tiền để gửi tin nhắn đánh giá', $this->templateId);

                return;
            }

            // Gửi yêu cầu tới API Zalo
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
            $responseData = json_decode($responseBody, true);
            $status = $responseData['error'] == 0 ? 1 : 0;

            $storeService->sendMessage($this->templateData['name'], $this->phone, $status, $responseData['message'], $this->templateId, $this->oaId, $this->userId);

            // Nếu gửi thành công, trừ tiền trong ví của người dùng
            if ($status == 1) {
                $storeService->deductMoneyFromAdminWallet($this->userId, $this->ratePrice);
                if ($user->sub_wallet >= $this->ratePrice) {
                    $user->sub_wallet -= $this->ratePrice;
                } elseif ($user->wallet >= $this->ratePrice) {
                    $user->wallet -= $this->ratePrice;
                }
                $user->save();

                Log::info('Rating message sent successfully and funds deducted');
            } else {
                Log::error('Failed to send rating message: ' . $responseBody);
            }
        } catch (Exception $e) {
            Log::error('Error occurred while sending rating message: ' . $e->getMessage());

            // Tạo bản ghi khi gặp lỗi
            $storeService->sendMessage($this->templateData['name'], $this->phone, 0, $e->getMessage(), $this->templateId, $this->oaId, $this->userId);
        }
    }
}
