<?php

namespace App\Providers;

use App\Http\View\Composers\NotificationComposer;
use App\Models\OaTemplate;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\Transfer;
use App\Models\ZaloOa;
use App\Models\ZaloUser;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $id = Auth::id();
                $products = Product::where('user_id', $id)->orderByDesc('created_at')->get();
                $view->with('products', $products);
            }
        });
        // Composer cho admin layout
        View::composer('admin.layout.header', function ($view) {
            if (Auth::check()) {
                $id = Auth::id();
                $adminNotifications = Transaction::orderByDesc('created_at')
                    ->where(function ($query) {
                        $query->where('notification', 0)
                            ->orWhere('notification', 2);
                    })
                    ->where('user_id', $id)
                    ->get();
                $id = Auth::id();
                $templateUser = ZaloOa::where('user_id', $id)->with('template')->first();
                // dd($templateUser);

                // Truyền biến vào view
                $view->with('adminNotifications', $adminNotifications)->with('templateUser', $templateUser);;
            }
        });

        View::composer('admin.layout.header', function ($view) {
            if (Auth::check()) {
                $id = Auth::id();
                $adminTransferNotifications = Transfer::orderByDesc('created_at')
                    ->where('notification', 1)->where('user_id', $id)->get();
                $view->with('adminTransferNotifications', $adminTransferNotifications);
            }
        });

        View::composer('admin.tinnhan.*', function ($view) {
            $this->syncZaloUsers();
        });
        // $this->syncZaloUsers();
    }

    public function syncZaloUsers()
    {
        $offset = 0;
        $count = 50;
        $inserted = 0;
        $accessToken = ZaloOa::where('user_id', Auth::user()->id)->first()->access_token;
        do {
            $response = Http::withHeaders([
                'access_token' => $accessToken,
            ])->get('https://openapi.zalo.me/v3.0/oa/user/getlist', [
                'data' => json_encode([
                    'offset' => $offset,
                    'count' => $count,
                    'is_follower' => 'true'
                ])
            ]);

            if (!$response->successful()) {
                break;
            }

            $data = $response->json();
            $users = $data['data']['users'] ?? [];

            foreach ($users as $user) {
                $exists = ZaloUser::where('user_id', $user['user_id'])->where('admin_id', Auth::user()->id )->exists();
                if (!$exists) {
                    $result = $this->getZaloUserDetail($user['user_id'], $accessToken);
                    $data = $result->getData(true);
                    $userInfo = $data['data'];
                    ZaloUser::create([
                        'user_id' => $user['user_id'],
                        'display_name' => $userInfo['display_name'] ?? null,
                        'admin_id' => Auth::user()->id
                    ]);
                    $inserted++;
                }
            }

            $offset += count($users);
        } while (count($users) === $count);

        return "Đã lưu $inserted người dùng vào cơ sở dữ liệu.";
    }

    public function getZaloUserDetail($userId, $accessToken)
    {

        $data = [
            'data' => json_encode([
                'user_id' => $userId
            ])
        ];

        $response = Http::withHeaders([
            'access_token' => $accessToken,
        ])->get('https://openapi.zalo.me/v3.0/oa/user/detail', $data);

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
