<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\TransferService;
use App\Services\UserService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TransferController extends Controller
{
    protected $userService, $transferService;
    public function __construct(UserService $userService, TransferService $transferService)
    {
        $this->userService = $userService;
        $this->transferService = $transferService;
    }

    public function index(Request $request)
    {
        try {
            $query = $request->input('query');
            $status = $request->input('status');
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $authUser = Auth::user();
            $transfers = $this->transferService->getPaginatedTransfersForSuperAdmin($query, $startDate, $endDate);
            if ($request->ajax()) {
                return response()->json([
                    'html' => view('superadmin.transfer.table', compact('transfers'))->render(),
                    'pagination' => $transfers->links('pagination::custom')->render(),
                ]);
            }

            return view('superadmin.transfer.index', compact('transfers'));
        } catch (Exception $e) {
            Log::error('Failed to get Paginated transfer list: ' . $e->getMessage());
            throw new Exception('Failed to get paginated transfer list');
        }
    }

    public function search(Request $request)
    {
        try {
            $query = $request->input('query');
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');

            $transfers = $this->transferService->getPaginatedtransfersForSuperAdmin($query, $startDate, $endDate);

            if ($request->ajax()) {
                $html = view('superadmin.transfer.table', compact('transfers'))->render();
                $pagination = $transfers->appends(['query' => $query, 'start_date' => $startDate, 'end_date' => $endDate])->links('pagination::custom')->render();
                return response()->json(['html' => $html, 'pagination' => $pagination]);
            }
            return view('superadmin.transfer.index', compact('transfers'));
        } catch (Exception $e) {
            Log::error('Failed to search transfer: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to search transfers: ' . $e->getMessage()], 500);
        }
    }

    public function list()
    {
        $users = $this->userService->getQualifiedUsers();
        return view('superadmin.transfer.user.index', compact('users'));
    }

    public function store($id, Request $request)
    {
        try {
            // Thực hiện chuyển tiền
            $receipts = $this->transferService->transferMoney($id, $request->all());
            $users = $this->userService->getQualifiedUsers();

            // Trả về phản hồi JSON
            return response()->json([
                'success' => true,
                'sub_wallet' => $receipts->sub_wallet, // Đảm bảo trường subWallet là đúng
                'html' => view('superadmin.transfer.user.table', ['users' => $users])->render(), // Cập nhật lại bảng người dùng
                'pagination' => $users->links('pagination::custom')->render(),
            ]);
        } catch (Exception $e) {
            Log::error('Failed to create new Transfer: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500); // Trả về lỗi từ service
        }
    }
}
