<?php

namespace App\Http\Controllers;

use App\Events\CustomerLogin;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use App\Models\OTP;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    protected $userService;
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
    public function login(LoginRequest $request, $username)
    {
        try {
            Log::info('Bắt đầu quá trình đăng nhập', ['username' => $username]);

            // Lấy thông tin đăng nhập từ request
            $credentials = $request->only(['email', 'password']);
            // dd($credentials);
            Log::info('Lấy thông tin đăng nhập', ['credentials' => $credentials]);

            // Gọi tới service để xác thực
            $remember = $request->has('remember');
            $result = $this->userService->authenticateUser($credentials, $remember);
            Log::info('Kết quả xác thực từ service', ['result' => $result]);

            // So sánh username
            if ($result['user']['username'] === $username) {
                Log::info('Username hợp lệ', ['username' => $username]);

                // return redirect()->route('admin.{username}.dashboard', ['username' => $username]);
                // Kiểm tra role_id
                if ($result['user']->role_id == 1 || $result['user']->role_id == 2) {
                    session()->put('authUser', $result['user']);
                    // session()->put('userName', Auth::user()->username);
                    session()->save();
                    Log::info('Đăng nhập thành công với role admin', ['username' => $username]);
                    return redirect()->route('admin.{username}.dashboard', ['username' => $username]);
                }
                // elseif ($result['user']->role_id == 2) {
                //     session()->put('authUser', $result['user']);
                //     session()->save();
                //     Log::info('Đăng nhập thành công với role staff', ['username' => $username]);
                //     return redirect()->route('staff.index');
                // }
                elseif ($result['user']->role_id == 3) {
                    session()->put('authUser', $result['user']);
                    session()->save();
                    Log::info('Đăng nhập thành công với role sa', ['username' => $username]);
                    return redirect()->route('sa.store.index');
                }
            }

            Log::warning('Username không khớp', ['username' => $username, 'resultUsername' => $result['user']['username']]);
            return back();
        } catch (\Exception $e) {
            Log::error('Lỗi trong quá trình đăng nhập', ['error' => $e->getMessage()]);
            return $this->handleLoginError($request, $e);
        }
    }


    public function showLoginForm($username)
    {

        if (Auth::check()) {
            // Chuyển hướng đến trang dashboard hoặc một trang khác
            redirect()->route('admin.{username}.dashboard', ['username' => $username]);
        } else {
            return view('auth.login', compact('username'));
        }
    }

    public function logout(Request $request)
    {
        $username = Auth::user()->username;
        Auth::logout();
        $request->session()->flush();
        return redirect(env('APP_URL_LOGOUT') . '/' . $username);
    }

    protected function handleLoginError($request, \Exception $e)
    {
        return redirect()->back()->withErrors(['error' => $e->getMessage()]);
    }
}
