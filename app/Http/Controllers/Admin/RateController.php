<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\OaTemplate;
use App\Models\Rate;
use App\Models\ZaloOa;
use App\Services\OaTemplateService;
use App\Services\ZaloOaService;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RateController extends Controller
{
    protected $zaloOaService;
    protected $oaTemplateService;

    public function __construct(OaTemplateService $oaTemplateService, ZaloOaService $zaloOaService)
    {
        $this->oaTemplateService = $oaTemplateService;
        $this->zaloOaService = $zaloOaService;
    }

    public function index(Request $request)
    {
        $this->createRate();
        try {
            $title = 'Đánh giá khách hàng';
            $userId = Auth::id();

            $query = Rate::where('user_id', $userId);

            // Nếu lọc theo số sao
            if ($request->filled('rate')) {
                $query->where('rate', $request->input('rate'));
            }

            // Nếu lọc theo template_id
            if ($request->filled('template_id')) {
                $query->where('template_id', $request->input('template_id'));
            }

            $rates = $query->orderByDesc('created_at')->paginate(10);

            $user = Auth::user();
            $accessToken = $this->zaloOaService->getAccessToken();

            $oa = ZaloOa::where('user_id', $user->id)
                ->where('is_active', 1)
                ->first();

            $templateOptions = OaTemplate::where('oa_id', $oa->id)
                ->get();

            if ($request->ajax()) {
                $view = view('admin.rates.tablde', compact('rates'))->render();
                return response()->json([
                    'success' => true,
                    'table' => $view
                ]);
            }

            return view('admin.rates.index', compact('rates', 'title', 'templateOptions'));
        } catch (\Exception $e) {
            Log::error('Failed to load rates: ' . $e->getMessage());
            return ApiResponse::error('Lỗi khi tải đánh giá', 500);
        }
    }

    public function createRate()
    {
        $user = Auth::user();
        $accessToken = $this->zaloOaService->getAccessToken();

        $oa = ZaloOa::where('user_id', $user->id)
            ->where('is_active', 1)
            ->first();

        $templates = OaTemplate::where('oa_id', $oa->id)
            ->get();

        foreach ($templates as $template) {
            $rate = $this->rate($accessToken, $template->template_id);
            // Log::info($rate);
            if (isset($rate['data'])) {
                foreach ($rate['data'] as $item) {
                    Rate::firstOrCreate(
                        [
                            'user_id' => $user->id,
                            'template_id' => $template->template_id,
                            'msgId' => $item['msgId'],
                        ],
                        [
                            'oaId' => $item['oaId'] ?? null,
                            'note' => $item['note'] ?? null,
                            'rate' => $item['rate'],
                            'feedbacks' => isset($item['feedbacks']) && is_array($item['feedbacks'])
                                ? json_encode($item['feedbacks'])
                                : $item['feedbacks'],
                            'submitDate' => Carbon::createFromTimestampMs($item['submitDate'])->toDateTimeString(),
                        ]
                    );
                }
            }
        }
    }

    public function rate($accessToken, $templateId)
    {
        $from = Carbon::create(2024, 5, 14, 0, 0, 0, 'Asia/Ho_Chi_Minh')->timestamp * 1000;
        $to = round(microtime(true) * 1000);
        $client = new Client();

        $allData = [];
        $offset = 0;
        $limit = 100;

        do {
            $response = $client->get('https://business.openapi.zalo.me/rating/get', [
                'headers' => [
                    'access_token' => $accessToken,
                    'Content-Type' => 'application/json'
                ],
                'query' => [
                    'template_id' => $templateId,
                    'offset' => $offset,
                    'limit' => $limit,
                    'from_time' => $from,
                    'to_time' => $to,
                ],
            ]);

            $responseData = json_decode($response->getBody()->getContents(), true);
            $dataChunk = $responseData['data'] ?? [];

            $allData = array_merge($allData, $dataChunk);
            $offset += $limit;
        } while (count($dataChunk) == $limit); // Nếu ít hơn limit thì là trang cuối

        return $allData;
    }
}
