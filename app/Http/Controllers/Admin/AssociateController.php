<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\User;
use App\Services\AssociateService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AssociateController extends Controller
{
    protected $associateService;

    public function __construct(AssociateService $associateService)
    {
        $this->associateService = $associateService;
    }

    public function index()
    {
        try {
            $title = "Danh sách cộng sự";
            $associates = $this->associateService->getPaginatedAssociate();
            if (request()->ajax()) {
                $view = view('admin.associate.table', compact('associates'))->render();
                return response()->json(['success' => true, 'table' => $view]);
            }
            return view('admin.associate.index', compact('associates', 'title'));
        } catch (Exception $e) {
            Log::error("Failed to get Associates: " . $e->getMessage());
            return ApiResponse::error('Failed to get Associates' . $e->getMessage(), 500);
        }
    }

    public function detail($id)
    {
        try {
            $associate = User::find(request()->id);
            return response()->json($associate);
        } catch (Exception $e) {
            Log::error("Failed to find this Associate: " . $e->getMessage());
            return ApiResponse::error('Failed to find this Associate: ' . $e->getMessage(), 500);
        }
    }

    public function search(Request $request)
    {
        try {
            $query = $request->input('query');
            if (preg_match('/\d/', $query)) {
                $associates = $this->associateService->getAssociateByPhone($query);
            } else {
                $associates = $this->associateService->getAssociateByName($query);
            }

            if ($request->ajax()) {
                $html = view('admin.associate.table', compact('associates'))->render();
                $pagination = $associates->appends(['query' => $query])->links('pagination::custom')->render();
                return response()->json(['html' => $html, 'pagination' => $pagination]);
            }

            return view('admin.associate.index', compact('associates'));
        } catch (Exception $e) {
            Log::error('Failed to find these associates: ' . $e->getMessage());
            return ApiResponse::error('Failed to find these associates due to: ' . $e->getMessage(), 500);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $request->id,
            'phone' => 'required|numeric|digits:10|unique:users,phone,' . $request->id,
            'address' => 'nullable|string|max:255',
            'field' => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'tax_code' => 'nullable|numeric',
            'username' => 'required|unique:users,username',
            'sub_wallet' => 'nullable',
        ], [
            'name.required' => 'Vui lòng nhập tên người dùng',
            'phone.required' => 'Vui lòng nhập số điện thoại người dùng',
            'username.required' => 'Vui lòng nhập tên đăng nhập',
            'username.unique' => 'Tên đăng nhập đã tồn tại',
            'phone.numeric' => 'Số điện thoại phải là số',
            'phone.digits' => 'Số điện thoại phải đủ 10 ký tự',
            'phone.unique' => 'Số điện thoại đã tồn tại',
            'email.email' => 'Email không đúng định dạng',
            'email.unique' => 'Email này đã tồn tại',
            'tax_code.numeric' => 'Mã số thuế phải là số',
        ]);
        try {
            $newAssociate = $this->associateService->addNewAssociate($validated);
            $paginatedAssociates = $this->associateService->getPaginatedAssociate();

            return response()->json([
                'success' => "Thêm cộng sự thành công",
                'html' => view('admin.associate.table', ['associates' => $paginatedAssociates])->render(),
                'pagination' => $paginatedAssociates->links('pagination::custom')->render()
            ]);
        } catch (Exception $e) {
            Log::error('Failed to add new Associate: ' . $e->getMessage(), [
                'request_data' => $request->all()
            ]);

            return response()->json([
                'error' => 'Thêm cộng sự thất bại'
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . request()->id,
            'phone' => 'required|numeric|digits:10|unique:users,phone,' . request()->id,
            'address' => 'nullable|string|max:255',
            'field' => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'tax_code' => 'nullable|numeric',
            'username' => 'required|unique:users,username,' . $request->id,
            'sub_wallet' => 'nullable',
        ], [
            'name.required' => 'Vui lòng nhập tên người dùng',
            'username.required' => 'Vui lòng nhập tên đăng nhập',
            'username.unique' => 'Tên đăng nhập đã tồn tại',
            'phone.numeric' => 'Số điện thoại phải là số',
            'phone.digits' => 'Số điện thoại phải đủ 10 ký tự',
            'phone.unique' => 'Số điện thoại đã tồn tại',
            'email.unique' => 'Email này đã tồn tại',
            'tax_code.numeric' => 'Mã số thuế phải là số',
            'phone.required' => 'Vui lòng nhập số điện thoại người dùng',
        ]);

        try {
            $associate = $this->associateService->updateAssociate($validated, request()->id);
            $paginatedAssociates = $this->associateService->getPaginatedAssociate();

            return response()->json([
                'success' => "Sửa cộng sự thành công",
                'html' => view('admin.associate.table', ['associates' => $paginatedAssociates])->render(),
                'pagination' => $paginatedAssociates->links('pagination::custom')->render()
            ]);
        } catch (Exception $e) {
            Log::error('Failed to update this associate: ' . $e->getMessage());
            return response()->json([
                'error' => 'Sửa cộng sự thất bại'
            ], 500);
        }
    }

    public function delete()
    {
        try {
            $this->associateService->deleteAssociate(request()->id);

            // Fetch the updated list of associates
            $associates = $this->associateService->getPaginatedAssociate();

            return response()->json([
                'success' => 'Xóa cộng sự thành công',
                'html' => view('admin.associate.table', ['associates' => $associates])->render(),
                'pagination' => $associates->links('pagination::custom')->render(),
            ]);
        } catch (Exception $e) {
            Log::error('Failed to delete this associate: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Xóa cộng sự thất bại'
            ]);
        }
    }


    public function updateAssociateStatus(Request $request)
    {
        try {
            $associate = User::where('id', $request->associate_id)->first();
            $associate->status = $request->input('status');
            $associate->save();
            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cập nhật trạng thái thất bại',
                'error' => $e->getMessage()
            ]);
        }
    }
}
