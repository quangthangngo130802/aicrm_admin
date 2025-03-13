<?php

namespace App\Jobs;

use App\Models\AutomationReminder;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\ZnsMessage;
use App\Models\Campaign;
use App\Models\OaTemplate;
use App\Models\ZaloOa;
use App\Services\ZaloOaService;
use Carbon\Carbon;

class SendZnsReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $campaignId;
    protected $zaloOaService;
    protected $automationReminder;

    /**
     * Create a new job instance.
     *
     * @param User $user
     * @param int $campaignId
     */
    public function __construct(User $user, $campaignId, ZaloOaService $zaloOaService, AutomationReminder $automationReminder)
    {
        $this->user = $user;
        $this->campaignId = $campaignId;
        $this->zaloOaService = $zaloOaService;
        $this->automationReminder = $automationReminder;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Lấy thời gian gửi là 9h30 sáng của một tuần sau
        $reminder = $this->automationReminder->first();
        $autotime = $reminder->sent_time;
            $hour = $autotime->format('H'); // Lấy giờ
            $c = $autotime->format('i');
        // $sendAt = Carbon::now()->addWeek()->startOfDay()->addHours(9)->addMinutes(30);
        $sendAt = Carbon::now()->addDays($reminder->numbertime)->startOfDay()->addHours($hour)->addMinutes($hour);
        if (now()->lessThan($sendAt)) {
            // Nếu thời gian hiện tại chưa đến thời gian gửi, bỏ qua job
            return;
        }
        if($reminder->status !=0){
            return;
        }

        $user = $this->user;
        $campaignId = $this->campaignId;

        // Lấy thông tin chiến dịch để kiểm tra trạng thái và lấy template_id
        $campaign = Campaign::find($campaignId);

        if (!$campaign) {
            Log::error('Chiến dịch không tìm thấy với ID: ' . $campaignId);
            return;
        }

        // Kiểm tra trạng thái của chiến dịch
        if ($campaign->status != 1) {
            Log::info('Chiến dịch không hoạt động, không gửi ZNS cho người dùng ID: ' . $user->id);
            return;
        }

        $templateId = $campaign->template_id;

        if (!$templateId) {
            Log::error('Template ID không tìm thấy cho chiến dịch ID: ' . $campaignId);
            return;
        }
        $template = OaTemplate::where('id', $templateId)->first()->template_id;
        $accessToken = $this->zaloOaService->getAccessToken(); // Access Token

        // Tạo dữ liệu JSON gửi đến API Zalo
        $payload = [
            'phone' => preg_replace('/^0/', '84', $user->phone),
            'template_id' => $template, // Lấy template_id từ chiến dịch
            'template_data' => [
                'date' => Carbon::now()->format('d/m/Y') ?? "",
                'name' => $user->name ?? "",
                'order_code' => $user->id,
                'phone_number' => $user->phone,
                'status' => 'Đăng ký thành công'
            ]
        ];
        $oa_id = ZaloOa::where('is_active', 1)->first()->id;
        $client = new Client();

        try {
            // Gửi yêu cầu POST đến API Zalo
            $response = $client->post('https://business.openapi.zalo.me/message/template', [
                'headers' => [
                    'access_token' => $accessToken,
                    'Content-Type' => 'application/json'
                ],
                'json' => $payload
            ]);

            $responseBody = $response->getBody()->getContents();
            Log::info("API Response: " . $responseBody);

            $responseData = json_decode($responseBody, true);
            $status = $responseData['error'] == 0 ? 1 : 0;

            // Lưu thông tin tin nhắn vào cơ sở dữ liệu
            ZnsMessage::create([
                'name' => $user->name,
                'phone' => $user->phone,
                'sent_at' => Carbon::now(),
                'status' => $status,
                'note' => $responseData['message'],
                'template_id' => $templateId,
                'oa_id' => $oa_id, // Giả sử storage_id là id của OA
            ]);

            if ($status == 1) {
                Log::info('Gửi ZNS thành công');
            } else {
                Log::error('Gửi ZNS thất bại: ' . $response->getBody());
            }
        } catch (\Exception $e) {
            Log::error('Lỗi khi gửi tin nhắn: ' . $e->getMessage());

            // Lưu thông tin tin nhắn vào cơ sở dữ liệu khi gặp lỗi
            ZnsMessage::create([
                'name' => $user->name,
                'phone' => $user->phone,
                'sent_at' => Carbon::now(),
                'status' => 0,
                'note' => $e->getMessage(),
                'template_id' => $templateId ?? null,
                'oa_id' => $user->storage_id, // Giả sử storage_id là id của OA
            ]);
        }
    }
}
