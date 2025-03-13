<?php

namespace App\Services;

use App\Models\Associate;
use App\Models\AutomationBirthday;
use App\Models\AutomationRate;
use App\Models\AutomationReminder;
use App\Models\AutomationUser;
use App\Models\User;
use App\Models\ZaloOa;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AssociateService
{
    public function getAllAssociate()
    {
        try {
            return User::where('parent_id', Auth::user()->id)->get();
        } catch (Exception $e) {
            Log::error("Failed to get this user's asscociates: " . $e->getMessage());
            return throw new Exception("Failed to get this user's associates");
        }
    }

    public function getPaginatedAssociate()
    {
        try {
            return User::where('parent_id', Auth::user()->id)->orderByDesc('created_at')->paginate(10);
        } catch (Exception $e) {
            Log::error("Failed to get this user's associates: " . $e->getMessage());
            return throw new Exception("Failed to get this user's associates");
        }
    }

    public function getAssociateByPhone($phone)
    {
        try {
            return User::where('parent_id', Auth::user()->id)->where('phone', 'LIKE', '%' . $phone . '%')->paginate(10);
        } catch (Exception $e) {
            Log::error('Failed to find this associate by phone: ' . $e->getMessage());
            throw new Exception('Failed to find this associatie by phone');
        }
    }

    public function getAssociateByName($name)
    {
        try {
            return User::where('parent_id', Auth::user()->id)->where('name', 'LIKE', '%' . $name . '%')->paginate(10);
        } catch (Exception $e) {
            Log::error('Failed to find this associate by name: ' . $e->getMessage());
            throw new Exception('Failed to find this associate by name');
        }
    }

    public function deleteAssociate($id)
    {
        DB::beginTransaction();
        try {
            Log::info('Deleting associate locally', ['id' => $id]);

            // Tìm associate theo parent_id và id
            $associate = User::where('parent_id', Auth::id())->findOrFail($id);

            // URL API xóa associate
            $superAdminAssociateUrl = config('app.api_url') . '/api/delete-associate';

            // Gọi API để xóa associate ở Super Admin trước
            $client = new Client();
            $response = $client->post($superAdminAssociateUrl, [
                'form_params' => [
                    'id' => $id,
                ],
            ]);

            // Kiểm tra phản hồi từ API
            if ($response->getStatusCode() !== 200) {
                throw new Exception('Failed to delete associate in Super Admin');
            }

            $responseBody = json_decode($response->getBody(), true);
            if (isset($responseBody['error'])) {
                throw new Exception('Error from Super Admin API: ' . $responseBody['error']);
            }

            Log::info('Successfully deleted associate in Super Admin', ['id' => $id]);

            // Xóa associate trong cơ sở dữ liệu
            $associate->delete();

            DB::commit();
            Log::info('Successfully deleted associate locally', ['id' => $id]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete associate', ['id' => $id, 'error' => $e->getMessage()]);
            throw new Exception('Failed to delete associate');
        }
    }


    public function addNewAssociate(array $data)
    {
        DB::beginTransaction();
        Log::info('Creating new associate with data: ', $data);
        $password = '123456';
        $hashedPassword = Hash::make($password);
        $sub_wallet = preg_replace('/[^\d]/', '', $data['sub_wallet']);
        $user = Auth::user();
        if (empty($sub_wallet)) {
            $sub_wallet = 0;
        }
        try {
            Log::info('Creating new associate');
            $associate = User::create([
                'name' => $data['name'],
                'phone' => $data['phone'],
                'email' => $data['email'],
                'company_name' => $data['company_name'],
                'tax_code' => $data['tax_code'],
                'address' => $data['address'],
                'expired_at' => Carbon::now()->addMonths(6),
                'field' => $data['field'],
                'username' => $data['username'],
                'role_id' => 2,
                'status' => 1,
                'password' => $hashedPassword,
                'sub_wallet' => $sub_wallet ?? 0,
                'prefix' => 'A_' . $user->prefix,
                'parent_id' => $user->id,
            ]);

            $data['id'] = $associate->id;
            $data['password'] = $hashedPassword;
            $data['sub_wallet'] = $sub_wallet;
            $data['parent_id'] = $user->id;
            $data['role_id'] = 2;
            $data['prefix'] = 'A-' . $user->prefix;
            $data['expired_at'] = Carbon::now()->addMonths(6);
            $data['status'] = 1;

            $superAdminAssociateUrl = config('app.api_url') . '/api/add-associate';

            $client  = new Client();

            $response = $client->post($superAdminAssociateUrl, [
                'form_params' => $data,
            ]);

            if ($response->getStatusCode() !== 200) {
                throw new Exception('Failed to add associate to super admin');
            } else {
                Log::info('Associate added to super admin successfully');
            }

            // Kiểm tra xem cộng sự này có phải là người dùng cha hay không
            Log::info('Start transfer zalo oa to new associate');
            $isParent = $associate->parent_id === null;  // Kiểm tra cộng sự có parent_id hay không

            // Lặp qua các OA của người dùng cha và tạo OA mới cho cộng sự
            $zaloOas = ZaloOa::where('user_id', $user->id)->get();
            foreach ($zaloOas as $zaloOa) {
                // Nếu cộng sự là người dùng cha (không có parent_id), kiểm tra tính duy nhất của oa_id
                if ($isParent) {
                    $exists = ZaloOa::where('oa_id', $zaloOa->oa_id)
                        ->whereHas('user', function ($query) {
                            $query->whereNull('parent_id'); // Kiểm tra người dùng không có parent_id (người dùng cha)
                        })
                        ->exists();

                    if ($exists) {
                        throw new Exception("OA ID đã tồn tại đối với một người dùng cha.");
                    }
                }

                // Tạo ZaloOa mới cho cộng sự
                ZaloOa::create([
                    'name' => $zaloOa->name,
                    'oa_id' => $zaloOa->oa_id,
                    'access_token' => $zaloOa->access_token,
                    'refresh_token' => $zaloOa->refresh_token,
                    'is_active' => $zaloOa->is_active,
                    'access_token_expiration' => $zaloOa->access_token_expiration,
                    'user_id' => $associate->id, // Gán user_id cho cộng sự mới
                ]);
            }
            Log::info('Zalo oa transfered successfully, start creating automation marketing user');

            AutomationUser::create([
                'name' => 'Thông báo xác nhận',
                'status' => 0,
                'user_id' => $associate->id,
            ]);

            Log::info('Automation User created successfully, start creating automation automation rate');

            AutomationRate::create([
                'name' => 'Đánh giá dịch vụ/sản phẩm',
                'status' => 0,
                'user_id' => $associate->id,
            ]);

            Log::info('Automation Rating created successfully, start creating automation birthday');

            AutomationBirthday::create([
                'name' => 'Chúc mừng sinh nhật',
                'status' => 0,
                'user_id' => $associate->id,
                'start_time' => '9:30:00',
            ]);

            Log::info('Automation Birthday created successfully, start creating automation automation reminder');

            AutomationReminder::create([
                'name' => 'Nhắc nhở',
                'status' => 0,
                'user_id' => $associate->id,
                'sent_time' => '9:30:00',
                'numbertime' => 0,
            ]);

            Log::info('Automation Reminder created successfully, create new associate completed');
            DB::commit();
            return $associate;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Failed to create new associate: " . $e->getMessage());
            throw new Exception('Failed to create new associate: ' . $e->getMessage());
        }
    }

    public function updateAssociate(array $data, $id)
    {
        DB::beginTransaction();
        $user = Auth::user();
        $associate = User::where('id', $id)->first();
        if (!$associate) {
            throw new Exception('Associate not found.');
        }
        try {
            $associate->update([
                'name' => $data['name'],
                'phone' => $data['phone'],
                'email' => $data['email'],
                'company_name' => $data['company_name'],
                'tax_code' => $data['tax_code'],
                'address' => $data['address'],
                'field' => $data['field'],
                'username' => $data['username'],
            ]);
            $data['id'] = $associate->id;
            Log::info('Associate updated successfully, start updating in super admin');

            $superAdminAssociateUrl = config('app.api_url') . '/api/update-associate';

            $client = new Client();

            $response = $client->post($superAdminAssociateUrl, [
                'form_params' => $data,
            ]);

            if ($response->getStatusCode() !== 200) {
                throw new Exception('Failed to update associate to super admin');
            } else {
                Log::info('Associate updated to super admin successfully');
            }
            DB::commit();
            return $associate;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Failed to update this associate: " . $e->getMessage());
            throw new Exception('Failed to update this associate due to: ' . $e->getMessage());
        }
    }
}
