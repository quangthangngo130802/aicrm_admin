<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Transaction;
use App\Services\TransactionService;
use App\Services\UserService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

use function PHPSTORM_META\map;

class TransactionController extends Controller
{
    protected $transactionService, $userService;
    public function __construct(TransactionService $transactionService, UserService $userService)
    {
        $this->transactionService = $transactionService;
        $this->userService = $userService;
    }

    public function index(Request $request)
    {
        try {
            $status = $request->input('status');
            $query = $request->input('query');
            $status = $request->input('status');
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $authUser = Auth::user();
            $transactions = $this->transactionService->getPaginatedTransactionsForSuperAdmin($query, $startDate, $endDate, $status);
            if ($request->ajax()) {
                return response()->json([
                    'html' => view('superadmin.transaction.table', compact('transactions'))->render(),
                    'pagination' => $transactions->links('pagination::custom')->render(),
                ]);
            }

            return view('superadmin.transaction.index', compact('transactions'));
        } catch (Exception $e) {
            Log::error('Failed to get Paginated Transaction list: ' . $e->getMessage());
            throw new Exception('Failed to get paginated transaction list');
        }
    }

    public function search(Request $request)
    {
        try {
            $query = $request->input('query');
            $status = $request->input('status');
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');

            $transactions = $this->transactionService->getPaginatedTransactionsForSuperAdmin($query, $startDate, $endDate, $status);

            if ($request->ajax()) {
                $html = view('superadmin.transaction.table', compact('transactions'))->render();
                $pagination = $transactions->appends(['query' => $query, 'start_date' => $startDate, 'end_date' => $endDate, 'status' => $status])->links('pagination::custom')->render();
                return response()->json(['html' => $html, 'pagination' => $pagination]);
            }
            return view('superadmin.transaction.index', compact('transactions'));
        } catch (Exception $e) {
            Log::error('Failed to search Transaction: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to search Transactions: ' . $e->getMessage()], 500);
        }
    }


    public function confirmTransaction($id)
    {
        try {
            $transaction = $this->transactionService->confirmTransaction($id);
            // session()->flash('success', 'Đã xác nhận giao dịch');
            return response()->json(['success' => true]);
        } catch (Exception $e) {
            Log::error("Failed to confirm Transaction: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Xác nhận giao dịch thất bại'], 500);
        }
    }

    public function rejectTransaction($id)
    {
        try {
            $transaction = $this->transactionService->rejectTransaction($id);
            // session()->flash('success', 'Đã từ chối xác nhận giao dịch');
            return response()->json(['success' => true]);
        } catch (Exception $e) {
            Log::error('Failed to reject Transaction: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Từ chối xác nhận giao dịch thất bại'], 500);
        }
    }

    public function updateNotification($id)
    {
        try {
            $transaction = Transaction::find($id);
            $transaction->notification = 3;
            $transaction->save();
            return to_route('super.transaction.index');
        } catch (Exception $e) {
            Log::erro('failed to update mark-as-read this transaction: ' . $e->getMessage());
            return ApiResponse::error('Failed to mark-as-read this transaction', 500);
        }
    }
}
