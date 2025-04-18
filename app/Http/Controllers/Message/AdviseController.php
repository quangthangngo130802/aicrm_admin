<?php

namespace App\Http\Controllers\Message;

use App\Http\Controllers\Controller;
use App\Models\ZaloOa;
use App\Models\ZaloUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AdviseController extends Controller
{

    public function text()
    {
        $title = "Tin nhắn văn bản";
        $users = ZaloUser::where('admin_id', Auth::user()->id)->get();
        return view('admin.tinnhan.text', compact('users', 'title'));
    }

    public function images()
    {
        $title = "Tin nhắn đình kèm hình ảnh";
        $users = ZaloUser::where('admin_id', Auth::user()->id)->get();
        return view('admin.tinnhan.image', compact('users', 'title'));
    }
    //
    public function messageDocument()
    {
        $title = "Tin nhắn truyền thông";
        // dd($title);
        $result = $this->fetchAllArticles();

        // dd($result);
        $accessToken = ZaloOa::where('user_id', Auth::user()->id)->first()->access_token;
        $data = $result->getData(true);
        $articles = $data['articles'];
        return view('admin.tinnhan.sdvise', compact('articles', 'title'));
    }


    public function sendZaloBroadcast(Request $request)
    {
        // dd($request->toArray());
        $accessToken = ZaloOa::where('user_id', Auth::user()->id)->first()->access_token;
        $target = [];

        if (!empty($request['cities'])) {
            $target['cities'] = $request['cities'];
        }

        if (!empty($request['ages'])) {
            $target['ages'] = $request['ages'];
        }

        if (!empty($request['locations'])) {
            $target['locations'] = $request['locations'];
        }


        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'access_token' => $accessToken,
        ])->post('https://openapi.zalo.me/v2.0/oa/message', [
            "recipient" => [
                "target" => [
                    "gender" => "0",
                    "platform" => "1,2,3",
                    $target
                ]
            ],
            "message" => [
                "attachment" => [
                    "type" => "template",
                    "payload" => [
                        "template_type" => "media",
                        "elements" => [
                            [
                                "media_type" => "article",
                                "attachment_id" => $request['attachment_id']
                            ]
                        ]
                    ]
                ]
            ]
        ]);

        logger($response->body()); // log kết quả để debug

        if ($response->successful()) {
            toastr()->success('Gửi tin nhắn thành công !');
            return back();
        }
        toastr()->error('Gửi tin nhắn thất bại!');
        return back();
    }


    public function fetchAllArticles()
    {
        $accessToken = ZaloOa::where('user_id', Auth::user()->id)->first()->access_token;
        $offset = 0;
        $limit = 10;
        $allArticles = [];
        do {
            $response = Http::withHeaders([
                'access_token' => $accessToken,
            ])->get('https://openapi.zalo.me/v2.0/article/getslice', [
                'offset' => $offset,
                'limit' => $limit,
                'type' => 'normal',
            ]);

            if (!$response->successful()) {
                Log::error('API lỗi: ' . $response->body());
                break;
            }

            $data = $response->json();

            $articles = $data['data']['medias'] ?? [];

            // Nếu không phải mảng => dừng
            if (!is_array($articles)) {
                break;
            }

            // Nếu rỗng => kết thúc
            if (count($articles) === 0) {
                break;
            }

            $allArticles = array_merge($allArticles, $articles);

            $offset += count($articles);
        } while (count($articles) === $limit);

        return response()->json([
            'total' => count($allArticles),
            'articles' => $allArticles,
        ]);

    }


    public function sendMessage(Request $request)
    {

        $accessToken = ZaloOa::where('user_id', Auth::user()->id)->first()->access_token;
        $userId = '319119771891059276';

        $response = Http::withHeaders([
            'access_token' => $accessToken,
        ])->post('https://openapi.zalo.me/v3.0/oa/message/cs', [
            'recipient' => [
                'user_id' => $request->userId,
            ],
            'message' => [
                'text' => $request->message,
            ],
        ]);
        if ($response->successful()) {
            toastr()->success('Gửi tin nhắn thành công !');
            return back();
        }
        toastr()->error('Gửi tin nhắn thất bại!');
        return back();
    }

    public function sendImageMessage(Request $request)
    {
        // dd($request->toArray());
        $accessToken = ZaloOa::where('user_id', Auth::user()->id)->first()->access_token;
        $userId = $request->userId;

        $response = Http::withHeaders([
            'access_token' => $accessToken,
        ])->post('https://openapi.zalo.me/v3.0/oa/message/cs', [
            'recipient' => [
                'user_id' => $userId,
            ],
            'message' => [
                'text' => $request->text,
                'attachment' => [
                    'type' => 'template',
                    'payload' => [
                        'template_type' => 'media',
                        'elements' => [
                            [
                                'media_type' => 'image',
                                'url' => $request->image_url,
                            ]
                        ]
                    ]
                ],
            ],
        ]);

        // if ($response->successful()) {
        //     return response()->json([
        //         'success' => true,
        //         'data' => $response->json()
        //     ]);
        // }

        // return response()->json([
        //     'success' => false,
        //     'error' => $response->body()
        // ], $response->status());
        if ($response->successful()) {
            toastr()->success('Gửi tin nhắn thành công !');
            return back();
        }
        toastr()->error('Gửi tin nhắn thất bại!');
        return back();
    }
}
