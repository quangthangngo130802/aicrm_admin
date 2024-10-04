<?php

namespace App\Services;

use App\Models\Transfer;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransferService
{
    protected $transfer;
    public function __construct(Transfer $transfer)
    {
        $this->transfer = $transfer;
    }

    public function getPaginatedTransfersForAdmin($id, $startDate, $endDate)
    {
        try {
            $queryBuilder = Transfer::with('user')->where('user_id', $id);

            if ($startDate) {
                $queryBuilder->whereDate('created_at', '>=', $startDate);
            }

            if ($endDate) {
                $queryBuilder->whereDate('created_at', '<=', $endDate);
            }

            $transfers = $queryBuilder->orderByDesc('created_at')->paginate(10);
            return $transfers;
        } catch (Exception $e) {
            Log::error('Failed to get paginated transfer for admin: ' . $e->getMessage());
            throw new Exception('Failed to get paginated transfer for admin');
        }
    }

    public function getPaginatedTransfersForSuperAdmin($query, $startDate, $endDate)
    {
        try {
            $queryBuilder = Transfer::with('user');

            // Kiểm tra tên hoặc số điện thoại
            if ($query) {
                $queryBuilder->whereHas('user', function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                        ->orWhere('phone', 'like', "%{$query}%");
                });
            }

            // Kiểm tra ngày bắt đầu
            if ($startDate) {
                $queryBuilder->whereDate('created_at', '>=', $startDate);
            }

            // Kiểm tra ngày kết thúc
            if ($endDate) {
                $queryBuilder->whereDate('created_at', '<=', $endDate);
            }

            $transfers = $queryBuilder->orderByDesc('created_at')->paginate(10);

            return $transfers;
        } catch (Exception $e) {
            Log::error("Failed to get paginated transaction for super admin: " . $e->getMessage());
            throw new Exception('Failed to get paginated transaction for super admin');
        }
    }

    public function transferMoney($id, array $data)
    {
        DB::beginTransaction();
        try {
            // Tìm người dùng
            $user = User::find($id);
            // Cập nhật số dư ví
            $user->sub_wallet += $data['amount'];
            $user->save();

            // Tạo bản ghi giao dịch
            $transfer = $this->transfer->create([
                'amount' => $data['amount'],
                'user_id' => $id,
                'notification' => 1,
            ]);

            // Commit transaction
            DB::commit();

            return (object)[
                'sub_wallet' => $user->sub_wallet,
                'transfer' => $transfer,
            ];
        } catch (Exception $e) {
            Log::error('Failed to transfer money to this user: ' . $e->getMessage());
            DB::rollBack(); // rollback transaction in case of error
            throw new Exception("Failed to transfer money to this user");
        }
    }
}
