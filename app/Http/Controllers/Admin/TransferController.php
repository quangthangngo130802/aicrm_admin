<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Transfer;
use App\Services\TransferService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TransferController extends Controller
{
    protected $transferService;
    public function __construct(TransferService $transferService)
    {
        $this->transferService = $transferService;
    }

    public function index(Request $request)
    {
        try {
            $title = "Giao dịch nhận tiền";
            $user_id = Auth::user()->id;
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $transfers = $this->transferService->getPaginatedTransfersForAdmin($user_id, $startDate, $endDate);
            if ($request->ajax()) {
                return response()->json([
                    'html' => view('admin.transfer.table', compact('transfers'))->render(),
                    'pagination' => $transfers->links('pagination::custom')->render(),
                ]);
            }
            return view('admin.transfer.index', compact('transfers', 'title'));
        } catch (Exception $e) {
            Log::error('Failed to get Paginated transfer list for admin: ' . $e->getMessage());
            throw new Exception('Failed to get Paginated transfer list for admin');
        }
    }

    public function search(Request $request)
    {
        try {
            $user_id = Auth::user()->id;
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $transfers = $this->transferService->getPaginatedTransfersForAdmin($user_id, $startDate, $endDate);

            if ($request->ajax()) {
                $html = view('admin.transfer.table', compact('transfers'))->render();
                $pagination = $transfers->appends(['start_date' => $startDate, 'end_date' => $endDate])->links('pagination::custom')->render();
                return response()->json(['html' => $html, 'pagination' => $pagination]);
            }
            return view('admin.transfer.index', compact('transfers'));
        } catch (Exception $e) {
            Log::error('Failed to search transfer: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to search transfers: ' . $e->getMessage()], 500);
        }
    }

    public function updateNotification($id)
    {
        try {
            $transfer = Transfer::find(request()->id);
            $transfer->notification = 0;
            $transfer->save();
            return to_route('admin.{username}.transfer.index', ['username' => Auth::user()->id]);
        } catch (Exception $e) {
            Log::error('Failed to change this transfer notification: ' . $e->getMessage());
            return ApiResponse::error('Failed to change this transfer notification', 500);
        }
    }
}
