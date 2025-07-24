<?php

namespace App\Http\Controllers\Topcv;

use App\Http\Controllers\Controller;
use App\Models\Webhook;
use App\Models\ZaloOa;
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


        $zaloOas = ZaloOa::all();
        $knownOaIds = ZaloOa::pluck('oa_id')->toArray();


        $id1 = $data['oa_id'] ?? ($data['sender']['id'] ?? null);
        $id2 = $data['recipient']['id'] ?? ($data['user_id'] ?? null);


        if (in_array($id1, $knownOaIds)) {
            $oaId = $id1;
            $userId = $id2;
        } elseif (in_array($id2, $knownOaIds)) {
            $oaId = $id2;
            $userId = $id1;
        } else {
            // Log::warning('Webhook missing valid oa_id or user_id', compact('id1', 'id2'));
            return response()->json(['status' => 'ignored'], 200);
        }

        $zaloOa = $zaloOas->firstWhere('oa_id', $oaId);

        $result = $this->getZaloUserDetail($userId, $zaloOa->access_token);
        $data = $result->getData(true);
        $userInfo = $data['data'] ?? [];

        // Kiểm tra tồn tại theo cả oa_id và user_id
        $exists = Webhook::where('oa_id', $oaId)
            ->where('user_id', $userId)
            ->exists();

        if (!$exists) {
            Webhook::create([
                'oa_id'   => $oaId,
                'user_id' => $userId,
                'name'    => $userInfo['display_name'] ?? ($data['user_alias'] ?? 'unknown'),
            ]);
            Log::info('Webhook created:', compact('oaId', 'userId'));
        } else {
            Log::info('Webhook already exists:', compact('oaId', 'userId'));
        }

        return response()->json(['status' => 'success'], 200);
    }


    public function getZaloUserDetail($userId, $accessToken)
    {
        Log::info($userId.'-'.$accessToken);
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
