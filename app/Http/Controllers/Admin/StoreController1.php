<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Jobs\SendRatingMessageJob;
use App\Jobs\SendZnsBirthdayJob;
use App\Jobs\SendZnsReminderJob;
use App\Models\AutomationBirthday;
use App\Models\AutomationRate;
use App\Models\AutomationReminder;
use App\Models\AutomationUser;
use App\Models\City;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ZaloOa;
use App\Models\ZnsMessage;
use App\Services\SignUpService;
use App\Services\StoreService;
use App\Services\UserService;
use App\Services\ZaloOaService;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Excel;
use Maatwebsite\Excel\Facades\Excel as FacadesExcel;

class StoreController extends Controller
{
    protected $storeService;
    protected $signUpService;
    protected $zaloOaService;
    public function __construct(StoreService $storeService, SignUpService $signUpService, ZaloOaService $zaloOaService)
    {
        $this->storeService = $storeService;
        $this->signUpService = $signUpService;
        $this->zaloOaService = $zaloOaService;
    }

    public function index()
    {
        try {
            $stores = $this->storeService->getAllStore();
            return view('admin.store.index', compact('stores'));
        } catch (Exception $e) {
            Log::error('Failed to find any store' . $e->getMessage());
            return ApiResponse::error('Failed to find any store', 500);
        }
    }

    public function import(Request $request)
    {
        try {
            $filePath = $request->file('import_file')->getRealPath();
            $fileExtension = $request->file('import_file')->getClientOriginalExtension();
            $fileType = $fileExtension === 'xlsx' ? Excel::XLSX : Excel::XLS;

            $user = Auth::user();
            $accessToken = $this->zaloOaService->getAccessToken();
            $oa_id = ZaloOa::where('user_id', $user->id)->where('is_active', 1)->first()->id;
            $automationUser = AutomationUser::where('user_id', $user->id)->first();
            $automationRate = AutomationRate::where('user_id', $user->id)->first();
            $automationReminder = AutomationReminder::where('user_id', $user->id)->first();
            $automationBirthday = AutomationBirthday::where('user_id', $user->id)->first();
            $template_code = $automationUser->template->template_id ?? null;
            $rate_template_code = $automationRate->template->template_id ?? null;
            $birthday_template_code = $automationBirthday->template->template_id ?? null;
            $reminder_template_code = $automationReminder->template->temaplte_id ?? null;
            $user_template_id = $automationUser->template_id ?? null;
            $rate_template_id = $automationRate->template_id ?? null;
            $birthday_template_id = $automationBirthday->template_id ?? null;
            $reminder_template_id = $automationReminder->template_id ?? null;
            $automationUserStatus = $automationUser->status;
            $automationRateStatus = $automationRate->status;
            $automationBirthdayStatus = $automationBirthday->status;
            $automationReminderStatus = $automationReminder->status;
            $price = $automationUser->template->price ?? null;
            $ratePrice = $automationRate->template->price ?? null;
            $birthdayPrice = $automationBirthday->template->price ?? null;
            $reminderPrice = $automationReminder->template->price ?? null;
            $reminderCycle = $automationReminder->numbertime;
            $sentTime = $automationReminder->sent_time;
            // Sử dụng chunk để xử lý dữ liệu
            $rows = FacadesExcel::toArray(new class implements ToArray {
                public function array(array $array)
                {
                    return $array;
                }
            }, $filePath, null, $fileType)[0];

            $chunkSize = 100; // Kích thước chunk
            collect($rows)->skip(1)->chunk($chunkSize)->each(function ($chunk) use ($user, $accessToken, $template_code, $rate_template_code, $user_template_id, $rate_template_id, $price, $ratePrice, $automationUserStatus, $automationRateStatus, $oa_id, $request, $automationBirthdayStatus, $birthday_template_id, $birthday_template_code, $automationBirthday, $birthdayPrice, $reminder_template_code, $reminder_template_id, $reminderPrice, $automationReminderStatus, $reminderCycle, $sentTime) {
                foreach ($chunk as $row) {
                    if (isset($row[0]) && !empty($row[0])) {
                        try {
                            $dob = null;
                            if (is_numeric($row[3])) {
                                // Nếu là giá trị số từ Excel
                                $dob = Carbon::createFromFormat('Y-m-d', Carbon::create(1900, 1, 1)->addDays($row[3] - 2)->format('Y-m-d'));
                            } else {
                                // Kiểm tra và xử lý các định dạng ngày khác nhau
                                $formats = ['d/m/Y', 'm/d/Y']; // Các định dạng cần kiểm tra
                                foreach ($formats as $format) {
                                    try {
                                        $dob = Carbon::createFromFormat($format, $row[3])->format('Y-m-d');
                                        break; // Nếu đúng định dạng thì thoát vòng lặp
                                    } catch (\Exception $e) {
                                        continue; // Thử định dạng tiếp theo
                                    }
                                }

                                if (!$dob) {
                                    throw new \Exception("Invalid date format: " . $row[3]);
                                }
                            }
                        } catch (\Exception $e) {
                            $dob = null;
                            Log::error("Invalid date format: " . $row[3]);
                        }

                        // Lấy số điện thoại từ dữ liệu của từng dòng
                        if (!isset($row[1]) || empty($row[1])) {
                            Log::warning('Số điện thoại không hợp lệ tại dòng: ' . json_encode($row));
                            continue; // Bỏ qua dòng này
                        }
                        $phone = preg_replace('/\D/', '', $row[1]);
                        // Kiểm tra định dạng số điện thoại
                        if (preg_match('/^(84|0084)/', $phone)) {
                            $phone = preg_replace('/^(84|0084)/', '0', $phone);
                        }
                        if (strlen($phone) == 9 && $phone[0] != '0') {
                            $phone = '0' . $phone;
                        }

                        // Kiểm tra xem số điện thoại có hợp lệ không
                        if (empty($phone)) {
                            Log::warning('Số điện thoại không hợp lệ tại dòng: ' . json_encode($row));
                            continue;
                        }

                        $existingUser = Customer::where('user_id', Auth::user()->id)->where('phone', $phone)->first();
                        $city = City::where('name', $row[4])->first();

                        if (!$existingUser) {
                            Log::info('Creating new cusomer');

                            $code = $this->storeService->generateCode($phone);
                            $newUser = Customer::create([
                                'name' => $row[0],
                                'phone' => $phone,
                                'email' => $row[2] ?? null,
                                'city_id' => $city->id ?? null,
                                'address' => $row[5] ?? null,
                                'source' => $request->source,
                                'user_id' => Auth::user()->id,
                                'product_id' => $request->product_id,
                                'code' => $code,
                                'dob' => Carbon::parse($dob),
                            ]);

                            $product_name = 'Chưa chọn dịch vụ'; // Giá trị mặc định nếu không chọn sản phẩm

                            // Kiểm tra nếu có product_id
                            if (!empty($request->product_id)) {
                                $product = Product::find($request->product_id);
                                if ($product) {
                                    $product_name = $product->name; // Lấy tên sản phẩm nếu có
                                }
                            }
                            $template_data = $this->storeService->templateData($newUser->name, $newUser->code, $newUser->phone, $price, $newUser->address, $product_name);
                            if ($newUser) {
                                if ($automationUserStatus == 1) {
                                    if ($user->sub_wallet >= $price || $user->wallet >= $price) {
                                        try {

                                            // Gửi yêu cầu tới API ZALO
                                            $client = new Client();
                                            $response = $client->post('https://business.openapi.zalo.me/message/template', [
                                                'headers' => [
                                                    'access_token' => $accessToken,
                                                    'Content-Type' => 'application/json'
                                                ],
                                                'json' => [
                                                    'phone' => preg_replace('/^0/', '84', $newUser->phone),
                                                    'template_id' => $template_code,
                                                    'template_data' => $template_data,
                                                ]
                                            ]);

                                            $responseBody = $response->getBody()->getContents();
                                            Log::info('Api Response: ' . $responseBody);

                                            $responseData = json_decode($responseBody, true);
                                            $status = $responseData['error'] == 0 ? 1 : 0;
                                            // Lưu thông tin ZNS đã gửi
                                            $this->storeService->sendMessage($newUser->name, $newUser->phone, $status, $responseData['message'], $user_template_id, $oa_id, $user->id);
                                            if ($status == 1) {
                                                Log::info('Gửi ZNS thành công');
                                                $this->storeService->deductMoneyFromAdminWallet($newUser->id, $price);
                                                if ($user->sub_wallet >= $price) {
                                                    $user->sub_wallet -= $price;
                                                } elseif ($user->wallet >= $price) {
                                                    $user->wallet -= $price;
                                                }

                                                if ($automationRateStatus == 1) {
                                                    if ($user->sub_wallet >= $ratePrice || $user->wallet >= $ratePrice) {
                                                        Log::info('Scheduling ZNS rating message to be sent in 5 minutes');

                                                        SendRatingMessageJob::dispatch($newUser->phone, $rate_template_code, $template_data, $oa_id, $rate_template_id, $user->id, $ratePrice, $accessToken)->delay(now()->addMinutes(5));
                                                    } else {
                                                        Log::warning('Dont have enough money to send ZNS Rating Message');
                                                        $this->storeService->sendMessage($newUser->name, $newUser->phone, 0, 'Tài khoản của bạn không đủ tiền để thực hiện gửi tin nhắn đánh giá', $rate_template_id, $oa_id, $user->id);
                                                    }
                                                } else {
                                                    Log::warning('Automation Rate is not activated');
                                                }
                                            } else {
                                                Log::error('Gửi ZNS thất bại: ' . $response->getBody());
                                            }
                                        } catch (Exception $e) {
                                            Log::error('Lỗi khi gửi tin nhắn: ' . $e->getMessage());
                                            $this->storeService->sendMessage($newUser->name, $newUser->phone, 0, $e->getMessage(), $user_template_id, $oa_id, $user->id);
                                        }
                                    } else {
                                        Log::warning('Not enough money to send ZNS Rating Automation');
                                        $this->storeService->sendMessage($newUser->name, $newUser->phone, 0, 'Tài khoản của bạn không đủ để gửi tin nhắn đánh giá', $user_template_id, $oa_id, $user->id);
                                    }
                                } else {
                                    Log::warning('Automation User is not active');
                                    $this->storeService->sendMessage($newUser->name, $newUser->phone, 0, 'Chưa kích hoạt ZNS Automation', $user_template_id, $oa_id, $user->id);
                                }
                                $dob = Carbon::parse($dob);
                                $startTime = Carbon::createFromFormat('H:i:s', $automationBirthday->start_time);
                                $today = Carbon::now();

                                // Tạo ngày sinh nhật năm nay, thêm giờ, phút, giây từ $startTime
                                $birthdayThisYear = Carbon::create($today->year, $dob->month, $dob->day, $startTime->hour, $startTime->minute, $startTime->second, $today->timezone);

                                if ($birthdayThisYear->isPast()) {
                                    $birthdayThisYear->addYear();
                                }
                                if ($automationBirthdayStatus == 1) {
                                    SendZnsBirthdayJob::dispatch($phone, $birthday_template_code, $template_data, $oa_id, $birthday_template_id, $user->id, $birthdayPrice, $accessToken, $startTime, $dob, $automationBirthdayStatus)->delay($birthdayThisYear);
                                    Log::info('Scheduling sending ZNS birthday message at ' . $automationBirthday->start_time . ', ' . $birthdayThisYear);
                                } else {
                                    Log::warning('Automation Birthday is not active');
                                }
                                $sentTime = Carbon::createFromFormat('H:i:s', $sentTime);
                                $reminderTime = Carbon::now()
                                    ->addDays($reminderCycle)    // Thêm số ngày reminderCycle
                                    ->setTimeFromTimeString($sentTime);
                                if ($automationReminderStatus == 1) {
                                    SendZnsReminderJob::dispatch($phone, $reminder_template_code, $template_data, $oa_id, $reminder_template_id, $user->id, $reminderPrice, $accessToken, $sentTime, $reminderCycle, $automationReminderStatus)->delay($reminderTime);
                                    Log::info('Start sending reminder message after ' . $reminderTime);
                                } else {
                                    Log::warning('Automation Reminder is not active');
                                }
                            }
                        } elseif ($existingUser) {
                            Log::info('Customer is already existed, start sending message');
                            $dobChanged = false;

                            if ($dob) {
                                try {
                                    $dobParsed = Carbon::parse($dob);
                                    if ($existingUser->dob) {
                                        $existingUserDob = Carbon::parse($existingUser->dob); // Chuyển đổi dob của user thành Carbon
                                        $dobChanged = !$existingUserDob->isSameDay($dobParsed);
                                    } else {
                                        $dobChanged = true; // Nếu dob chưa tồn tại, coi như thay đổi
                                    }
                                } catch (\Exception $e) {
                                    Log::error("Invalid date format for dob comparison: $dob");
                                }
                            }

                            $product_name = 'Chưa chọn dịch vụ'; // Giá trị mặc định nếu không chọn sản phẩm
                            // Kiểm tra nếu có product_id
                            if (!empty($request->product_id)) {
                                $product = Product::find($request->product_id);
                                if ($product) {
                                    $product_name = $product->name; // Lấy tên sản phẩm nếu có
                                }
                            }
                            $existingUser->update([
                                'name' => $row[0],
                                'address' => $data['address'] ?? null,
                                'email' => $row[2] ?? null,
                                'city_id' => $city->id ?? null,
                                'address' => $row[5] ?? null,
                                'source' => $request->source,
                                'product_id' => $request->product_id,
                                'dob' => Carbon::parse($dob),
                            ]);
                            $template_data2 = $this->storeService->templateData($existingUser->name, $existingUser->code, $existingUser->phone, $price, $existingUser->address, $product_name);
                            if ($automationUserStatus == 1) {
                                if ($user->sub_wallet >= $price || $user->wallet >= $price) {
                                    try {
                                        Log::info('Attemping to send ZNS message via Zalo API');

                                        // Gửi yêu cầu tới API ZALO
                                        $client = new Client();
                                        $response = $client->post('https://business.openapi.zalo.me/message/template', [
                                            'headers' => [
                                                'access_token' => $accessToken,
                                                'Content-Type' => 'application/json'
                                            ],
                                            'json' => [
                                                'phone' => preg_replace('/^0/', '84', $existingUser->phone),
                                                'template_id' => $template_code,
                                                'template_data' => $template_data2,
                                            ]
                                        ]);

                                        $responseBody = $response->getBody()->getContents();
                                        Log::info('Api Response: ' . $responseBody);

                                        $responseData = json_decode($responseBody, true);
                                        $status = $responseData['error'] == 0 ? 1 : 0;

                                        // Lưu thông tin ZNS đã gửi
                                        $this->storeService->sendMessage($existingUser->name, $existingUser->phone, $status, $responseData['message'], $user_template_id, $oa_id, $user->id);

                                        if ($status == 1) {
                                            Log::info('Gửi ZNS thành công');
                                            $this->storeService->deductMoneyFromAdminWallet($user->id, $price);
                                            //Trừ tiền khi tin nhắn gửi thành công
                                            if ($user->sub_wallet >= $price) {
                                                $user->sub_wallet -= $price;
                                            } elseif ($user->wallet >= $price) {
                                                $user->wallet -= $price;
                                            }

                                            if ($automationRateStatus == 1) {
                                                if ($user->sub_wallet >= $ratePrice || $user->wallet >= $ratePrice) {
                                                    Log::info('Scheduling ZNS rating message to be sent in 5 minutes');

                                                    SendRatingMessageJob::dispatch($existingUser->phone, $rate_template_code, $template_data2, $oa_id, $rate_template_id, $user->id, $ratePrice, $accessToken)->delay(now()->addMinutes(5));
                                                } else {
                                                    Log::warning('Not enough money in both wallets for rating message');
                                                    $this->storeService->sendMessage($existingUser->name, $existingUser->phone, 0, 'Tài khoản không đủ tiền để thực hiện gửi tin nhắn', $rate_template_id, $oa_id, $user->id);
                                                }
                                            } else {
                                                Log::warning('Automation Rate is not active');
                                            }
                                        } else {
                                            Log::error('Gửi ZNS thất bại: ' . $response->getBody());
                                        }
                                    } catch (Exception $e) {
                                        Log::error('Lỗi khi gửi tin nhắn: ' . $e->getMessage());
                                        $this->storeService->sendMessage($existingUser->name, $existingUser->phone, 0, $e->getMessage(), $user_template_id, $oa_id, $user->id);
                                    }
                                } else {
                                    Log::warning('not enough funds in both wallets');
                                    $this->storeService->sendMessage($existingUser->name, $existingUser->phone, 0, 'Tài khoản của bạn không đủ để thực hiện gửi tin nhắn đánh giá', $user_template_id, $oa_id, $user->id);
                                }
                            } else {
                                Log::warning('Automation user is not active');
                                $this->storeService->sendMessage($existingUser->name, $existingUser->phone, 0, 'Chưa kích hoạt ZNS Automation', $user_template_id, $oa_id, $user->id);
                            }
                            if ($dobChanged) {
                                Log::info('Sheduling sending birthday message due to dob change');
                                $dob = Carbon::parse($dob);
                                $startTime = Carbon::createFromFormat('H:i:s', $automationBirthday->start_time);
                                $today = Carbon::now();

                                // Tạo ngày sinh nhật năm nay, thêm giờ, phút, giây từ $startTime
                                $birthdayThisYear = Carbon::create($today->year, $dob->month, $dob->day, $startTime->hour, $startTime->minute, $startTime->second, $today->timezone);

                                if ($birthdayThisYear->isPast()) {
                                    $birthdayThisYear->addYear();
                                }
                                SendZnsBirthdayJob::dispatch($phone, $birthday_template_code, $template_data2, $oa_id, $birthday_template_id, $user->id, $birthdayPrice, $accessToken, $startTime, $dob, $automationBirthdayStatus)->delay($birthdayThisYear);
                                Log::info('Scheduling sending ZNS birthday message at ' . $automationBirthday->start_time . ', ' . $birthdayThisYear);
                            } else {
                                Log::info('Not scheduling sending birthday message because customer ' . $existingUser->name . "'s dob did not change");
                            }
                        }
                        $user->save();
                    }
                }
            });

            return response()->json(['success' => true, 'message' => 'Import khách hàng thành công, vui lòng tải lại trang']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Import error: ' . $e->getMessage());
            return response()->json(['message' => 'Import failed: ' . $e->getMessage()], 500);
        }
    }


    public function findByPhone(Request $request)
    {
        try {
            $owner = $this->storeService->findOwnerByPhone($request->input('phone'));
            $stores = new LengthAwarePaginator(
                $owner ? [$owner] : [],
                $owner ? 1 : 0,
                10,
                1,
                ['path' => Paginator::resolveCurrentPath()]
            );
            return view('admin.store.index', compact('stores'));
        } catch (Exception $e) {
            Log::error('Failed to find store owner:' . $e->getMessage());
            return response()->json(['error' => 'Failed to find store owner'], 500);
        }
    }
    public function detail($id)
    {
        try {
            return Customer::find(request()->id); // Sử dụng findOrFail để trả về lỗi nếu không tìm thấy
        } catch (Exception $e) {
            Log::error('Cannot find store infomation: ' . $e->getMessage());
            return ApiResponse::error('Cannot find store info', 500);
        }
    }
    public function delete($id)
    {
        try {
            // dd($id);
            $this->storeService->deleteStore(request()->id);

            $stores = $this->storeService->getAllStore();

            $table = view('admin.store.table', compact('stores'))->render();
            $pagination = $stores->links('vendor.pagination.custom')->render();

            session()->flash('success', 'Xóa thông tin khách hàng thànhc công');
            return response()->json([
                'success' => true,
                'message' => 'Xóa khách hàng thành công',
                'table' => $table,
                'pagination' => $pagination,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to delete store profile: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Xóa khách hàng thất bại'
            ]);
        }
    }
    public function store(Request $request)
    {
        try {
            Log::info('Start validation for adding new client');

            $validated = $request->validate([
                'name' => 'required',
                // 'phone' => [
                //     'required',
                //     Rule::unique('sgo_customers')->where(function ($query) {
                //         return $query->where('user_id', Auth::user()->id);
                //     })
                // ],
                'phone' => 'required',
                'email' => 'nullable|email',
                // 'email' => [
                //     'nullable',
                //     'email',
                //     Rule::unique('sgo_customers')->where(function ($query) {
                //         return $query->where('user_id', Auth::user()->id);
                //     })
                // ],
                'dob' => 'nullable',
                'address' => 'nullable',
                'source' => 'nullable',
                'product_id' => 'nullable|exists:sgo_products,id', // Kiểm tra product_id
            ]);

            Log::info('Validation passed', $validated);

            // Tiến hành thêm khách hàng mới
            $existingUser = Customer::where('user_id', Auth::user()->id)->where('phone', $validated['phone'])->first();
            if (!$existingUser) {
                Log::info("Start creating new customer");
                $client = $this->storeService->addNewStore($validated);
            } elseif ($existingUser) {
                Log::info("Customer is already existed, start sending message");
                $client = $this->storeService->updateCustomer($existingUser->id, $validated);
            }

            return response()->json([
                'success' => true,
                'message' => 'Client added successfully',
            ]);
        } catch (ValidationException $e) {
            Log::error('Validation error: ' . json_encode($e->errors()));

            return response()->json([
                'success' => false,
                'errors' => $e->errors(),  // Trả về lỗi validation chi tiết
            ], 422);
        } catch (Exception $e) {
            Log::error('Error occurred while adding client: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to add new Client',
            ], 500);
        }
    }
}
