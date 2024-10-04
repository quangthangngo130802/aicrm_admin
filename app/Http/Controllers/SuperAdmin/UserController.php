<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\User;
use App\Services\UserService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    protected $userService;
    public function  __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index()
    {
        try {
            $users = $this->userService->getPaginatedUser();
            if (request()->ajax()) {
                $view = view('superadmin.user.table', compact('users'))->render();
                return response()->json(['success' => true, 'table' => $view]);
            }
            return view('superadmin.user.index', compact('users'));
        } catch (Exception $e) {
            Log::error('Failed to find users: ' . $e->getMessage());
            return ApiResponse::error('Failed to get Users', 500);
        }
    }

    public function detail(Request $request)
    {
        try {
            return User::find($request->id);
        } catch (Exception $e) {
            Log::error('Failed to find this user: ' . $e->getMessage());
            return ApiResponse::error('Failed to find this user', 500);
        }
    }

    public function search(Request $request)
    {
        try {
            $query = $request->input('query');
            // Xử lý tìm kiếm theo tên hoặc số điện thoại
            if (preg_match('/\d/', $query)) {
                $users = $this->userService->getUserByPhone($query);
            } else {
                $users = $this->userService->getUserByName($query);
            }

            if ($request->ajax()) {
                $html = view('superadmin.user.table', compact('users'))->render();
                $pagination = $users->appends(['query' => $query])->links('pagination::custom')->render();
                return response()->json(['html' => $html, 'pagination' => $pagination]);
            }

            return view('superadmin.user.index', compact('users'));
        } catch (Exception $e) {
            Log::error('Failed to search users: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to search users'], 500);
        }
    }

    public function store(Request $request)
    {
        // Validate dữ liệu đầu vào
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|numeric|digits:10|unique:users,phone',
            'address' => 'required|string|max:255',
            'field' => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'tax_code' => 'nullable|numeric',
            'username' => 'required|unique:users,username'
        ], [
            'username.unique' => 'Tên tài khoản đã tồn tại',
            'phone.numeric' => 'Số điện thoại phải là số.',
            'phone.digits' => 'Số điện thoại phải đủ 10 ký tự.',
            'phone.unique' => 'Số điện thoại này đã tồn tại.',
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Email này đã tồn tại.',
            'tax_code.numeric' => 'Mã số thuế phải là số.',
        ]);

        try {
            // Thêm người dùng mới
            $newUser = $this->userService->addNewUser($request->all());

            // Lấy danh sách người dùng đã phân trang
            $paginatedUsers = $this->userService->getPaginatedUser();

            return response()->json([
                'success' => 'Thêm khách hàng thành công',
                'html' => view('superadmin.user.table', ['users' => $paginatedUsers])->render(),
                'pagination' => $paginatedUsers->links('pagination::custom')->render(),
            ]);
        } catch (Exception $e) {
            // Ghi log chi tiết hơn về lỗi
            Log::error('Failed to add new User: ' . $e->getMessage(), [
                'request_data' => $request->all()
            ]);

            return response()->json([
                'error' => 'Thêm khách hàng không thành công'
            ], 500);
        }
    }
}
