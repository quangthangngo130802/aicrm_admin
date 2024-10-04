<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use App\Services\OrderService;
use App\Services\TransactionService;
use Illuminate\Support\Facades\Auth;

class NotificationComposer
{
    protected $orderService, $transactionService;

    public function __construct(OrderService $orderService, TransactionService $transactionService)
    {
        // Dependencies are automatically resolved by the service container...
        $this->orderService = $orderService;
        $this->transactionService = $transactionService;
    }

    public function compose(View $view)
    {
        // dd(Auth::user());
        // if (Auth::user()->id == 1) {
        //     $user_id = Auth::user()->id;
        // }

        // $adminNotifications = $this->transactionService->getTransactionNotificationForAdmin($user_id);
        // $superAdminNotifications = $this->transactionService->getTransactionNotificationForSuperAdmin();
        // $view->with('adminNotifications', $adminNotifications);
        // $view->with('superAdminNotifications', $superAdminNotifications);
        // $notifications = $this->orderService->getOrderNotification();
        // $view->with('notifications', $notifications);
    }
}
