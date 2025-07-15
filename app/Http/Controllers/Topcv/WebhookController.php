<?php

namespace App\Http\Controllers\Topcv;

use App\Http\Controllers\Controller;
use App\Models\Webhook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    //
    public function handle(Request $request)
    {
        $data = $request->all();
        Log::info($data);

        $oaId = $data['oa_id'] ?? ($data['sender']['id'] ?? null);

        // user_id từ recipient hoặc user_id
        $userId = $data['recipient']['id'] ?? $data['user_id'] ?? null;

        // Nếu không có userId hoặc oaId thì bỏ qua
        if (!$userId || !$oaId) {
            Log::warning('Webhook missing oa_id or user_id', compact('oaId', 'userId'));
            return response()->json(['status' => 'ignored'], 200);
        }

        // Kiểm tra tồn tại theo cả oa_id và user_id
        $exists = Webhook::where('oa_id', $oaId)
            ->where('user_id', $userId)
            ->exists();

        if (!$exists) {
            Webhook::create([
                'oa_id'   => $oaId,
                'user_id' => $userId,
                'name'    => $data['event_name'] ?? 'unknown',
            ]);
            Log::info('Webhook created:', compact('oaId', 'userId'));
        } else {
            Log::info('Webhook already exists:', compact('oaId', 'userId'));
        }

        // 4. Trả về response
        return response()->json(['status' => 'success'], 200);
    }



    public function getZaloUserDetail($userId, $accessToken)
    {

        $data = [
            'data' => json_encode([
                'user_id' => $userId
            ])
        ];

        $response = Http::withHeaders([
            'access_token' => $accessToken,
        ])->get('https://openapi.zalo.me/v3.0/oa/user/detail', $data);

        if ($response->successful()) {
            return response()->json([
                'success' => true,
                'data' => $response['data'],
            ]);
        }

        return response()->json([
            'success' => false,
            'error' => $response->json(),
        ]);
    }
}
