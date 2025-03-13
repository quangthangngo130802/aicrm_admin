<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;


class UserService
{
    protected $user;
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function getPaginatedUser()
    {
        try {
            return $this->user->orderByDesc('created_at')->paginate(10);
        } catch (Exception $e) {
            Log::error('Failed to get paginated user list: ' . $e->getMessage());
            throw new Exception('Failed to get paginated user list');
        }
    }

    public function getAllUser($role)
    {
        try {
            $user = $this->user->orderByDesc('created_at')->get();
            return $user;
        } catch (Exception $e) {
            Log::error("Failed to search products: {$e->getMessage()}");
            throw new Exception('Failed to search products');
        }
    }

    public function  addNewUser(array $data)
    {
        DB::beginTransaction();
        $password = '123456';
        $hashedPassword = Hash::make($password);
        try {
            Log::info('Creating new user');
            $user = $this->user->create([
                'name' => $data['name'],
                'phone' => $data['phone'],
                'email' => $data['email'],
                'company_name' => $data['company_name'],
                'tax_code' => $data['tax_code'],
                'address' => $data['address'],
                'expired_at' => Carbon::now()->addMonths(6), // Cộng 6 tháng vào thời gian hiện tại
                'field' => $data['field'],
                'username' => $data['username'],
            ]);
            DB::commit();
            return $user;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to add new user: ' . $e->getMessage());
            throw new Exception('Failed to add new user');
        }
    }

    public function getUserByPhone($phone)
    {
        try {
            return $this->user->where('phone', 'LIKE', '%' . $phone . '%')->paginate(10);
        } catch (Exception $e) {
            Log::error('Failed to find this client by phone: ' . $e->getMessage());
            throw new Exception('Failed to find this client by phone');
        }
    }

    public function getUserByName($name)
    {
        try {
            return $this->user->where('name', 'LIKE', '%' . $name . '%')->paginate(10);
        } catch (Exception $e) {
            Log::error('Failed to find this client by name: ' . $e->getMessage());
            throw new Exception('Failed to find this client by name');
        }
    }
    public function authenticateUser($credentials, $remember)
    {
        // Tìm user qua email hoặc phone
        $user = User::where('email', $credentials['email'])
            ->orWhere('phone', $credentials['email'])
            ->first();

        if (!$user) {
            toastr()->error('Người dùng không tồn tại');
            return null;
        }

        // Kiểm tra mật khẩu
        if (!Hash::check($credentials['password'], $user->password)) {
            toastr()->error('Tài khoản hoặc mật khẩu không đúng');
            return null;
        }

        // Thực hiện đăng nhập thủ công
        Auth::login($user, $remember);

        return ['user' => $user];
    }


    public function logout(Request $request)
    {
        Auth::logout();
        Session::flush();
        return redirect()->route('login');
    }

    public function getQualifiedUsers()
    {
        try {
            return $this->user->where('wallet', '>', 0)
                ->orderBy('name', 'asc') // Sắp xếp theo trường name theo thứ tự tăng dần
                ->paginate(10);
        } catch (Exception $e) {
            Log::error('Failed to get qualified users: ' . $e->getMessage());
            throw new Exception('Failed to get qualified users');
        }
    }
}
