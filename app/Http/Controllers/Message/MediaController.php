<?php

namespace App\Http\Controllers\Message;

use App\Http\Controllers\Controller;
use App\Models\ZaloOa;
use App\Models\ZaloUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class MediaController extends Controller
{
    //
    public function media()
    {
        $users = ZaloUser::where('admin_id', Auth::user()->id)->get();
        return view('admin.tinnhan.media', compact('users'));
    }

    public function sendMediaMessage(Request $request)
    {
        // dd($request->toArray());
        $accessToken = ZaloOa::where('user_id', Auth::user()->id)->first()->access_token;

        $userId = $request['user_id'];

        $result = $this->getZaloUserDetail($userId);

        $data = $result->getData(true);

        if ($data['success']) {
            // ✅ Thành công, xử lý tiếp
            $userInfo = $data['data'];
            // dd($userInfo);
            $response = Http::withHeaders([
                'access_token' => $accessToken,
                'Content-Type' => 'application/json',
            ])->post('https://openapi.zalo.me/v3.0/oa/message/promotion', [
                "recipient" => [
                    "user_id" => $userId
                ],
                "message" => [
                    "attachment" => [
                        "type" => "template",
                        "payload" => [
                            "template_type" => "promotion",
                            "language" => "VI",
                            "elements" => [
                                [
                                    "image_url" => $request['image_url'],
                                    "type" => "banner"
                                ],
                                [
                                    "type" => "header",
                                    "content" => $request['header']
                                ],
                                [
                                    "type" => "text",
                                    "align" => "left",
                                    "content" => $request['main_content']
                                ],
                                [
                                    "type" => "table",
                                    "content" => array_map(function ($key, $val) {
                                        return [
                                            'key' => $key,
                                            'value' => $val
                                        ];
                                    }, $request['key'], $request['value'])
                                ],
                                [
                                    "type" => "text",
                                    "align" => "center",
                                    "content" => "Áp dụng tất cả cửa hàng trên toàn quốc"
                                ]
                            ],
                            "buttons" =>
                            array_map(function ($title, $payload) {
                                $isUrl = filter_var($payload, FILTER_VALIDATE_URL);

                                return [
                                    'title' => $title,
                                    'image_icon' => '',
                                    'type' => $isUrl ? 'oa.open.url' : 'oa.query.hide',
                                    'payload' => $isUrl ? ['url' => $payload] : $payload
                                ];
                            }, $request['button_title'] ?? [], $request['payload'] ?? [])


                        ]
                    ]
                ]
            ]);

            if ($response->successful()) {
                toastr()->success('Gửi tin nhắn thành công !');
                return back();

            } else {
               
                toastr()->error('Gửi tin nhắn thất bại!');
                return back();
            }
        } else {
            // ❌ Thất bại, dừng hoặc xử lý lỗi
            return response()->json([
                'success' => false,
                'message' => 'Không thể lấy thông tin người dùng Zalo: ' . $result['error']
            ]);
        }
    }


    public function getZaloUserDetail($userId)
    {
        $accessToken = ZaloOa::where('user_id', Auth::user()->id)->first()->access_token;
        $data = [
            'data' => json_encode([
                'user_id' => $userId
            ])
        ];

        $response = Http::withHeaders([
            'access_token' => $accessToken,
        ])->get('https://openapi.zalo.me/v3.0/oa/user/detail', $data);

        logger($response->body());

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
