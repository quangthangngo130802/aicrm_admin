<?php

namespace App\Services;

use App\Jobs\SendRatingMessageJob;
use App\Jobs\SendZnsBirthday;
use App\Jobs\SendZnsBirthdayJob;
use App\Jobs\SendZnsReminderJob;
use App\Mail\UserRegistered;
use App\Models\AutomationBirthday;
use App\Models\AutomationRate;
use App\Models\AutomationReminder;
use App\Models\AutomationUser;
use App\Models\Config;
use App\Models\Customer;
use App\Models\Product;
use App\Models\SanPham;
use App\Models\User;
use App\Models\ZaloOa;
use App\Models\ZnsMessage;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class BaoHanhService
{
    protected $user;
    protected $automationUser;
    protected $signUpService;
    protected $zaloOaService;
    public function __construct(User $user, AutomationUser $automationUser, SignUpService $signUpService, ZaloOaService $zaloOaService)
    {
        $this->user = $user;
        $this->automationUser = $automationUser;
        $this->signUpService = $signUpService;
        $this->zaloOaService = $zaloOaService;
    }

    public function getAllStore(): LengthAwarePaginator
    {
        try {
            return Customer::where('user_id', Auth::id())->orderByDesc('created_at')->paginate(10);
        } catch (Exception $e) {
            Log::error('Failed to fetch stores: ' . $e->getMessage());
            throw new Exception('Failed to fetch stores');
        }
    }

    public function findStoreByID($id)
    {
        try {
            // dd($id);
            return Customer::where('user_id', Auth::id())->find($id);
        } catch (Exception $e) {
            Log::error('Failed to find store info: ' . $e->getMessage());
            throw new Exception('Failed to find store info');
        }
    }

    public function findOwnerByPhone($phone)
    {
        try {
            $customer = Customer::where('user_id', Auth::id())
                ->where('phone', 'like',  "%{$phone}%")
                ->first();
            return $customer;
        } catch (Exception $e) {
            Log::error('Failed to find client profile: ' . $e->getMessage());
            throw new Exception('Failed to find client profile');
        }
    }

    public function deleteStore($id)
    {
        DB::beginTransaction();
        try {
            // dd($id);
            Log::info("Deleting store");
            $store = Customer::where('user_id', Auth::id())->find($id);
            $store->delete();
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete store profile: ' . $e->getMessage());
            throw new Exception('Failed to delete store profile');
        }
    }
    public function generateCode($phone, $id)
    {
        $lastFourDigits = substr($phone, -4);
        $prefix =  User::find($id)->prefix;
        return $prefix . '_' . $lastFourDigits;
    }
    public function addNewStore(array $data)
    {
        DB::beginTransaction();

        // try {
        //     Log::info('Starting process to create new client with data: ', $data);

            // Lấy thông tin user hiện tại
            $user = User::find($data['user_id']);
            $user_id = $user->id;
            $code = $this->generateCode($data['phone'], $user_id);

            // Tạo khách hàng mới
            $customer = Customer::create([
                'name' => $data['name'],
                'phone' => $data['phone'],
                'email' => $data['email'] ?? null,
                'address' => $data['address'] ?? null,
                'source' => $data['source'] ?? 'Thêm thủ công',
                'user_id' => $user_id,
                'product_id' =>  null,
                'code' => $code,
                'dob' => !empty($data['dob']) ? Carbon::parse($data['dob']) : null,
            ]);
            // $apiUrl = 'http://127.0.0.1:9000/api/customer-create';
            // $response = Http::post($apiUrl, $customer);

            $product_name = $data['product_name'];
            $warranty_period = $data['warranty_period'];
            // Kiểm tra nếu có product_id
            // if (!empty($data['masp'])) {
            //     $product = SanPham::find($data['product_id']);
            //     if ($product) {
            //         $product_name = $product->name;
            //         $warranty_period = $product->warranty_period;
            //     }
            // }
            Log::info('Customer created successfully: ' . json_encode($customer));

            // Lấy token và thông tin cần thiết từ API Zalo
            $accessToken = $this->zaloOaService->getAccessTokenUser($user_id);
            $oa_id = ZaloOa::where('user_id', $user_id)->where('is_active', 1)->first()->id;
            $automationUser = AutomationUser::where('user_id', $user_id)->first();
            $template_code = $automationUser->template->template_id ?? null;

            $user_template_id = $automationUser->template_id ?? null;

            $automationUserStatus = $automationUser->status ?? null;

            $price = $automationUser->template->price ?? null;

            $template_data = $this->templateData($data['name'], $customer->code, $data['phone'], number_format($price), $warranty_period, $product_name);

            // Kiểm tra trạng thái automation
            if ($automationUserStatus == 1) {
                if ($user->sub_wallet >= $price || $user->wallet >= $price) {
                    try {
                        Log::info('Attempting to send ZNS message via Zalo API');

                        // Gửi yêu cầu tới API Zalo
                        $client = new Client();
                        $response = $client->post('https://business.openapi.zalo.me/message/template', [
                            'headers' => [
                                'access_token' => $accessToken,
                                'Content-Type' => 'application/json'
                            ],
                            'json' => [
                                'phone' => preg_replace('/^0/', '84', $data['phone']),
                                'template_id' => $template_code,
                                'template_data' => $template_data,
                            ]
                        ]);

                        $responseBody = $response->getBody()->getContents();
                        Log::info('Zalo API Response: ' . $responseBody);

                        $responseData = json_decode($responseBody, true);
                        $status = $responseData['error'] == 0 ? 1 : 0;

                        $this->sendMessage($data['name'], $data['phone'], $status, $responseData['message'], $user_template_id, $oa_id, $user_id);

                        if ($status == 1) {
                            Log::info('ZNS message sent successfully');
                            $this->deductMoneyFromAdminWallet($user_id, $price);
                            if ($user->sub_wallet >= $price) {
                                $user->sub_wallet -= $price;
                            } elseif ($user->wallet >= $price) {
                                $user->wallet -= $price;
                            }

                        } else {
                            Log::error('ZNS message failed: ' . $responseBody);
                        }
                    } catch (Exception $e) {
                        Log::error('Error occurred while sending ZNS message: ' . $e->getMessage());

                        // Tạo bản ghi khi gặp lỗi
                        $this->sendMessage($data['name'], $data['phone'], 0, $e->getMessage(), $user_template_id, $oa_id, $user_id);
                    }
                } else {
                    Log::warning('Not enough funds in both wallets.');
                    $this->sendMessage($data['name'], $data['phone'], 0, 'Tài khoản của bạn không đủ tiền dể thực hiện gửi tin nhắn', $user_template_id, $oa_id, $user_id);
                }
            } else {
                Log::warning('Automation User is not active');
                $this->sendMessage($data['name'], $data['phone'], 0, 'Chưa kích hoạt ZNS Automation', $user_template_id, $oa_id, $user_id);
            }

            $user->save();
            DB::commit();
            Log::info('Transaction committed successfully');
            return $customer;
        // } catch (Exception $e) {
        //     DB::rollBack();
        //     Log::error('Failed to add new client: ' . $e->getMessage());
        //     throw new Exception('Failed to add new client');
        // }
    }

    public function updateCustomer($id, array $data)
    {
        DB::beginTransaction();
        // try {
            Log::info('Starting process to update customer with data: ', $data);

            $user = User::find($data['user_id']);
            Log::info($user);
            $user_id = $user->id;
            $code = $this->generateCode($data['phone'], $user_id);
            $customer = Customer::where('user_id', $user_id)->where('id', $id)->first();
            // dd($customer);
            $updateData = [
                'name' => $data['name'] ?? null,
                'phone' => $data['phone'] ?? null,
                'email' => $data['email'] ?? null,
                'address' => $data['address'] ?? null,
                'source' => $data['source'] ?? 'Thêm thủ công',
                'user_id' => $data['user_id'],
                'product_id' => null,
                'code' => $code,
                'dob' => isset($data['dob']) ? Carbon::parse($data['dob']) : null,
            ];

            // Lọc bỏ các giá trị null hoặc rỗng
            $updateData = array_filter($updateData, function ($value) {
                return !is_null($value) && $value !== '';
            });

            // $customer->update($updateData);

            $product_name = $data['product_name'];
            $warranty_period = $data['warranty_period'];

            $accessToken = $this->zaloOaService->getAccessTokenUser($user_id);
            $oa_id = ZaloOa::where('user_id', $user_id)->where('is_active', 1)->first()->id;
            $automationUser = AutomationUser::where('user_id', $user_id)->first();
            $template_code = $automationUser->template->template_id ?? null;

            $user_template_id = $automationUser->template_id ?? null;

            $automationUserStatus = $automationUser->status ?? null;

            $price = $automationUser->template->price ?? null;

            $template_data = $this->templateData($data['name'], $customer->code, $data['phone'], number_format($price), $warranty_period, $product_name);

            // $template_data = $this->templateData($data['name'], $customer->code, $data['phone'], number_format($price), $customer->address, $product_name);
            if ($automationUserStatus == 1) {
                if ($user->sub_wallet >= $price || $user->wallet >= $price) {
                    try {
                        Log::info('Attempting to send ZNS message via Zalo API');

                        $client = new Client();
                        $response = $client->post('https://business.openapi.zalo.me/message/template', [
                            'headers' => [
                                'access_token' => $accessToken,
                                'Content-Type' => 'application/json'
                            ],
                            'json' => [
                                'phone' => preg_replace('/^0/', '84', $data['phone']),
                                'template_id' => $template_code,
                                'template_data' => $template_data,
                            ]
                        ]);

                        $responseBody = $response->getBody()->getContents();
                        Log::info('Zalo API Response: ' . $responseBody);

                        $responseData = json_decode($responseBody, true);
                        $status = $responseData['error'] == 0 ? 1 : 0;

                        $this->sendMessage($data['name'], $data['phone'], $status, $responseData['message'], $user_template_id, $oa_id, $user_id);

                        if ($status == 1) {
                            Log::info('ZNS message sent successfully');
                            $this->deductMoneyFromAdminWallet($user_id, $price);

                            if ($user->sub_wallet >= $price) {
                                $user->sub_wallet -= $price;
                            } elseif ($user->wallet >= $price) {
                                $user->wallet -= $price;
                            }

                            $user->save();
                        } else {
                            Log::error('ZNS message failed: ' . $responseBody);
                        }
                    } catch (Exception $e) {
                        Log::error('Error occurred while sending ZNS message: ' . $e->getMessage());
                        $this->sendMessage($data['name'], $data['phone'], 0, $e->getMessage(), $user_template_id, $oa_id, $user_id);
                    }
                } else {
                    Log::warning('Not enough funds in both wallets.');
                    $this->sendMessage($data['name'], $data['phone'], 0, 'Tài khoản của bạn không đủ tiền để thực hiện gửi tin nhắn', $user_template_id, $oa_id, $user_id);
                }
            } else {
                Log::warning('Automation User is not active');
                $this->sendMessage($data['name'], $data['phone'], 0, 'Chưa kích hoạt ZNS Automation', $user_template_id, $oa_id, $user_id);
            }

            DB::commit();
            Log::info('Transaction committed successfully');
            return $customer;
        // } catch (Exception $e) {
        //     DB::rollBack();
        //     Log::error('Failed to update client: ' . $e->getMessage());
        //     throw new Exception('Failed to update client');
        // }
    }
    public function deductMoneyFromAdminWallet($id, $deductionMoney)
    {
        $urlDeductionMoney = config('app.api_url') . "/api/deduct-money-from-user-wallet/{$id}/{$deductionMoney}";
        $response = Http::post($urlDeductionMoney, [
            'id' => $id,
            'deductionMoney' => $deductionMoney,
        ]);
        if (!$response->successful()) {
            return response()->json([
                'error' => 'Trừ tiền bên phía api không thành công',
            ], $response->status());
        }
    }

    public function sendMessage($name, $phone, $status, $note, $template_id, $oa_id, $user_id)
    {
        $data = [
            'name' => $name,
            'phone' => $phone,
            'sent_at' => Carbon::now(),
            'status' => $status,
            'note' => $note,
            'oa_id' => $oa_id,
            'template_id' => $template_id,
            'user_id' => $user_id,
        ];

        // Lưu tin nhắn vào cơ sở dữ liệu
        ZnsMessage::create($data);
        Log::info('Tin nhắn đã được lưu vào cơ sở dữ liệu thành công.');

        // Gửi dữ liệu đến API
        $sendMessageApiUrl = config('app.api_url') . '/api/add-message';

        $client = new Client();
        $response = $client->post($sendMessageApiUrl, [
            'form_params' => $data,
        ]);

        if ($response->getStatusCode() !== 200) {
            Log::error('Gửi tin nhắn đến SuperAdmin thất bại.', [
                'status_code' => $response->getStatusCode(),
                'body' => $response->getBody()->getContents(),
            ]);
            throw new Exception('Không thể thêm tin nhắn vào SuperAdmin.');
        }

        Log::info('Tin nhắn đã được gửi đến SuperAdmin thành công.');
    }

    public function templateData($name, $order_code, $phone, $price, $custom_field, $product_name)
    {
        $template_data = [
            'date' => Carbon::now()->format('d/m/Y') ?? "",
            'name' => $name ?? "",
            'order_code' => $order_code,
            'phone_number' => $phone,
            'status' => 'Đăng ký thành công',
            'price' => number_format($price),
            'custom_field' => $custom_field ?? "",
            'product_name' => $product_name,
            'payment' => 'Chuyển khoản ngân hàng',
            'phone' => $phone,
            'payment_status' => 'Chuyển khoản thành công',
            'customer_name' => $name ?? '',
            'time' => Carbon::now()->format('h:i:s d/m/Y') ?? "",
            'order_date' => Carbon::now()->format('d/m/Y') ?? "",

        ];

        return $template_data;
    }
}
