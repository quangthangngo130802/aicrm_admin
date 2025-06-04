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

        // dd($username);
            $loginField = filter_var($request->input('email'), FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
            $credentials = [
                $loginField => $request->input('email'),
                'password'  => $request->input('password'),
            ];
            $remember = $request->boolean('remember');

            $user = User::where($loginField, $credentials[$loginField])->first();

            // Kiểm tra nếu tài khoản không tồn tại
            if (!$user) {
                toastr()->error('Tài khoản hoặc mật khẩu không chính xác!');
                return back();
            }

            logger('Username mismatch: entered = ' . $username . ', user = ' . $user->username);
            if($user->username != $username){
                logger('Username mismatch: entered = ' . $username . ', user = ' . $user->username);
                toastr()->error('Username không chính xác!');
                return back();
            }


            // Thực hiện đăng nhập
            if (auth()->attempt($credentials, $remember)) {
                // dd( $username);

                toastr()->success('Đăng nhập thành công.');
                return  redirect()->route('admin.dashboard');
            } else {
                toastr()->error('Tài khoản hoặc mật khẩu không chính xác!');
                return back();
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
