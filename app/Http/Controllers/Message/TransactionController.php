<?php

namespace App\Http\Controllers\Message;

use App\Http\Controllers\Controller;
use App\Models\ZaloOa;
use App\Models\ZaloUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class TransactionController extends Controller
{
    //

    public function transaction()
    {
        $users = ZaloUser::where('admin_id', Auth::user()->id)->get();
        return view('admin.tinnhan.transaction', compact('users'));
    }


    public function sendTransactionMessage(Request $request)
    {
        // dd($request->toArray());
        $accessToken = ZaloOa::where('user_id', Auth::user()->id)->first()->access_token;
        $response = Http::withHeaders([
            'access_token' => $accessToken,
            'Content-Type' => 'application/json',
        ])
            ->post('https://openapi.zalo.me/v3.0/oa/message/transaction', [
                "recipient" => [
                    "user_id" => $request['user_id'] 
                ],
                "message" => [
                    "attachment" => [
                        "type" => "template",
                        "payload" => [
                            "template_type" => "transaction_order",
                            "language" => "VI",
                            "elements" => [
                                [
                                    "image_url" => $request['banner'],
                                    "type" => "banner"
                                ],
                                [
                                    "type" => "header",
                                    "content" => $request['header'],
                                    "align" => "left"
                                ],
                                [
                                    "type" => "text",
                                    "align" => "left",
                                    "content" =>  $request['intro']
                                ],
                                [
                                    "type" => "table",
                                    "content" => [
                                        [
                                            "key" => "Mã khách hàng",
                                            "value" => $request['customer_code']
                                        ],
                                        [
                                            "key" => "Trạng thái",
                                            "value" => $request['status'],
                                            "style" => "yellow"
                                        ],
                                        [
                                            "key" => "Giá tiền",
                                            "value" =>  number_format($request['price'])
                                        ]
                                    ]
                                ],
                                [
                                    "type" => "text",
                                    "align" => "center",
                                    "content" =>  $request['note']
                                ]
                            ],
                            "buttons" => [
                                [
                                    "title" => "Kiểm tra lộ trình ",
                                    "image_icon" => "",
                                    "type" => "oa.open.url",
                                    "payload" => [
                                        "url" =>  $request['tracking_url']
                                    ]
                                ],
                                [
                                    "title" => "Xem lại giỏ hàng",
                                    "image_icon" => "",
                                    "type" => "oa.query.show",
                                    "payload" => "kiểm tra giỏ hàng"
                                ],
                                [
                                    "title" => "Liên hệ tổng đài",
                                    "image_icon" => "",
                                    "type" => "oa.open.phone",
                                    "payload" => [
                                        "phone_code" => $request['phone_transaction']
                                    ]
                                ]
                            ]
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
    }
}
