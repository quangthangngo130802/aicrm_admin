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

class StoreService
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
    public function generateCode($phone)
    {
        $lastFourDigits = substr($phone, -4);
        $prefix = Auth::user()->prefix;
        return $prefix . '_' . $lastFourDigits;
    }
    public function addNewStore(array $data)
    {
        DB::beginTransaction();

        try {
            Log::info('Starting process to create new client with data: ', $data);

            // Lấy thông tin user hiện tại
            $user = Auth::user();
            $user_id = $user->id;
            $code = $this->generateCode($data['phone']);

            // Tạo khách hàng mới
            $customer = Customer::create([
                'name' => $data['name'],
                'phone' => $data['phone'],
                'email' => $data['email'] ?? null,
                'address' => $data['address'] ?? null,
                'source' => $data['source'] ?? 'Thêm thủ công',
                'user_id' => $user_id,
                'product_id' => $data['product_id'] ?? null,
                'code' => $code,
                'dob' => !empty($data['dob']) ? Carbon::parse($data['dob']) : null,
            ]);
            $apiUrl = 'http://127.0.0.1:9000/api/customer-create';
            $response = Http::post($apiUrl, $customer);

            $product_name = 'Chưa chọn dịch vụ';

            // Kiểm tra nếu có product_id
            if (!empty($data['product_id'])) {
                $product = Product::find($data['product_id']);
                if ($product) {
                    $product_name = $product->name;
                }
            }
            Log::info('Customer created successfully: ' . json_encode($customer));

            // Lấy token và thông tin cần thiết từ API Zalo
            $accessToken = $this->zaloOaService->getAccessToken();
            $oa_id = ZaloOa::where('user_id', Auth::user()->id)->where('is_active', 1)->first()->id;
            $automationUser = AutomationUser::where('user_id', Auth::user()->id)->first();
            $automationRate = AutomationRate::where('user_id', Auth::user()->id)->first();
            $automationBirthday = AutomationBirthday::where('user_id', Auth::user()->id)->first();
            $automationReminder = AutomationReminder::where('user_id', Auth::user()->id)->first();
            $template_code = $automationUser->template->template_id ?? null;
            $rate_template_code = $automationRate->template->template_id ?? null;
            $birthday_template_code = $automationBirthday->template->template_id ?? null;
            $reminder_template_code = $automationReminder->template->template_id ?? null;
            $user_template_id = $automationUser->template_id ?? null;
            $rate_template_id = $automationRate->template_id ?? null;
            $birthday_template_id = $automationBirthday->template_id ?? null;
            $reminder_template_id = $automationReminder->template_id ?? null;
            $automationUserStatus = $automationUser->status ?? null;
            $automationRateStatus = $automationRate->status ?? null;
            $automationBirthdayStatus = $automationBirthday->status ?? null;
            $automationReminderStatus = $automationReminder->status ?? null;
            $price = $automationUser->template->price ?? null;
            $ratePrice = $automationRate->template->price ?? null;
            $birthdayPrice = $automationBirthday->template->price ?? null;
            $reminderPrice = $automationReminder->template->price ?? null;
            $template_data = $this->templateData($data['name'], $customer->code, $data['phone'], number_format($price), $customer->address, $product_name);
            $reminderCycle = $automationReminder->numbertime ?? null;
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

                            if ($automationRateStatus == 1) {
                                if ($user->sub_wallet >= $ratePrice || $user->wallet >= $ratePrice) {
                                    // Đặt lịch gửi tin nhắn đánh giá sau 5 phút
                                    SendRatingMessageJob::dispatch($data['phone'], $rate_template_code, $template_data, $oa_id, $rate_template_id, $user->id, $ratePrice, $accessToken)->delay(now()->addMinutes(1));
                                    Log::info('Scheduling ZNS rating message to be sent in 5 minutes.');
                                } else {
                                    Log::warning('Not enough funds in both wallets for rating message.');
                                    $this->sendMessage($data['name'], $data['phone'], 0, 'Tài khoản của bạn không đủ tiền để thực hiện gửi tin nhắn', $rate_template_id, $oa_id, $user_id);
                                }
                            } else {
                                Log::warning('Automation Rate is not active');
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
            if ($automationBirthdayStatus == 1) {
                $dob = Carbon::parse($data['dob']);
                $startTime = Carbon::createFromFormat('H:i:s', $automationBirthday->start_time);
                $today = Carbon::now();

                // Tạo ngày sinh nhật năm nay, thêm giờ, phút, giây từ $startTime
                $birthdayThisYear = Carbon::create($today->year, $dob->month, $dob->day, $startTime->hour, $startTime->minute, $startTime->second, $today->timezone);

                if ($birthdayThisYear->isPast()) {
                    $birthdayThisYear->addYear();
                }
                SendZnsBirthdayJob::dispatch($data['phone'], $birthday_template_code, $template_data, $oa_id, $birthday_template_id, $user->id, $birthdayPrice, $accessToken, $startTime, $dob, $automationBirthdayStatus)->delay($birthdayThisYear);
                Log::info('Scheduling sending ZNS birthday message at ' . $automationBirthday->start_time . ', ' . $birthdayThisYear);
            } else {
                Log::warning('Automation Birthday is not activate');
            }
            if ($automationReminderStatus == 1) {
                $sentTime = Carbon::createFromFormat('H:i:s', $automationReminder->sent_time);
                $reminderTime = Carbon::now()->addDays($reminderCycle)->setTimeFromTimeString($sentTime);
                SendZnsReminderJob::dispatch($data['phone'], $reminder_template_code, $template_data, $oa_id, $reminder_template_id, $user->id, $reminderPrice, $accessToken, $sentTime, $reminderCycle, $automationReminderStatus)->delay($reminderTime);
                Log::info('Start sending reminder message after ' . $reminderTime);
            } else {
                Log::warning('Automation Reminder is not active');
            }
            $user->save();
            DB::commit();
            Log::info('Transaction committed successfully');
            return $customer;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to add new client: ' . $e->getMessage());
            throw new Exception('Failed to add new client');
        }
    }

    public function updateCustomer($id, array $data)
    {
        DB::beginTransaction();
        try {
            Log::info('Starting process to update customer with data: ', $data);

            $user = Auth::user();
            $user_id = $user->id;
            $code = $this->generateCode($data['phone']);
            $customer = Customer::where('user_id', Auth::user()->id)->where('id', $id)->first();
            $updateData = [
                'name' => $data['name'] ?? null,
                'phone' => $data['phone'] ?? null,
                'email' => $data['email'] ?? null,
                'address' => $data['address'] ?? null,
                'source' => $data['source'] ?? 'Thêm thủ công',
                'user_id' => $user_id,
                'product_id' => $data['product_id'] ?? null,
                'code' => $code,
                'dob' => isset($data['dob']) ? Carbon::parse($data['dob']) : null,
            ];

            // Lọc bỏ các giá trị null hoặc rỗng
            $updateData = array_filter($updateData, function ($value) {
                return !is_null($value) && $value !== '';
            });

            $dobChanged = isset($data['dob']) && (!isset($customer->dob) || $customer->dob != Carbon::parse($data['dob']));

            $customer->update($updateData);


            $product_name = 'Chưa chọn dịch vụ';
            if (!empty($data['product_id'])) {
                $product = Product::find($data['product_id']);
                $product_name = $product ? $product->name : $product_name;
            }

            $accessToken = $this->zaloOaService->getAccessToken();
            $oa_id = ZaloOa::where('user_id', Auth::user()->id)->where('is_active', 1)->first()->id;
            $automationUser = AutomationUser::where('user_id', Auth::user()->id)->first();
            $automationRate = AutomationRate::where('user_id', Auth::user()->id)->first();
            $automationBirthday = AutomationBirthday::where('user_id', Auth::user()->id)->first();
            $template_code = $automationUser->template->template_id ?? null;
            $rate_template_code = $automationRate->template->template_id ?? null;
            $birthday_template_code = $automationBirthday->template->template_id ?? null;
            $user_template_id = $automationUser->template_id ?? null;
            $rate_template_id = $automationRate->template_id ?? null;
            $birthday_template_id = $automationBirthday->template_id ?? null;
            $automationUserStatus = $automationUser->status ?? null;
            $automationRateStatus = $automationRate->status ?? null;
            $automationBirthdayStatus = $automationBirthday->status ?? null;
            $price = $automationUser->template->price ?? null;
            $ratePrice = $automationRate->template->price ?? null;
            $birthdayPrice = $automationBirthday->template->price ?? null;
            $template_data = $this->templateData($data['name'], $customer->code, $data['phone'], number_format($price), $customer->address, $product_name);

            $template_data = $this->templateData($data['name'], $customer->code, $data['phone'], number_format($price), $customer->address, $product_name);
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

                            if ($automationRateStatus == 1) {
                                if ($user->sub_wallet >= $ratePrice || $user->wallet >= $ratePrice) {
                                    Log::info('Scheduling ZNS rating message to be sent in 5 minutes');

                                    SendRatingMessageJob::dispatch($data['phone'], $rate_template_code, $template_data, $oa_id, $rate_template_id, $user->id, $ratePrice, $accessToken)->delay(now()->addMinutes(1));
                                } else {
                                    Log::warning('Not enough money in both wallets');

                                    $this->sendMessage($data['name'], $data['phone'], 0, 'Tài khoản của bạn không đủ tiền để thực hiện gửi tin nhắn', $rate_template_id, $oa_id, $user_id);
                                }
                            } else {
                                Log::warning('Automation Rate is not active');
                            }
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
            if ($dobChanged) {
                if ($automationBirthdayStatus == 1) {
                    Log::info('Sheduling sending birthday message due to dob change');
                    $dob = Carbon::parse($data['dob']);
                    $startTime = Carbon::createFromFormat('H:i:s', $automationBirthday->start_time);
                    $today = Carbon::now();

                    // Tạo ngày sinh nhật năm nay, thêm giờ, phút, giây từ $startTime
                    $birthdayThisYear = Carbon::create($today->year, $dob->month, $dob->day, $startTime->hour, $startTime->minute, $startTime->second, $today->timezone);

                    if ($birthdayThisYear->isPast()) {
                        $birthdayThisYear->addYear();
                    }
                    SendZnsBirthdayJob::dispatch($data['phone'], $birthday_template_code, $template_data, $oa_id, $birthday_template_id, $user->id, $birthdayPrice, $accessToken, $startTime, $dob, $automationBirthdayStatus)->delay($birthdayThisYear);
                    Log::info('Scheduling sending ZNS birthday message at ' . $automationBirthday->start_time . ', ' . $birthdayThisYear);
                } else {
                    Log::info('Automation Birthday is not active');
                }
            } else {
                Log::info('Not scheduling because dob is not change');
            }

            $user->save();
            DB::commit();
            Log::info('Transaction committed successfully');
            return $customer;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to update client: ' . $e->getMessage());
            throw new Exception('Failed to update client');
        }
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
