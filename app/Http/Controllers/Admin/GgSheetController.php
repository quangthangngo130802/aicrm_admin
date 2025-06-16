<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ggsheet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GgSheetController extends Controller
{
    //
    public function index(){
        $user = Auth::user();
        $user_id = $user->id;
        $ggshet = Ggsheet::where('user_id', $user_id)->first();
        return view('admin.ggsheet.index', compact('ggshet'));
    }

    public function save(Request $request)
    {
        $validated = $request->validate([
            'api_code' => 'required|string|max:255',
            'name_sheet' => [
                'required',
                'string',
                'max:255',
                'regex:/^[A-Za-z0-9_]+$/',
            ],
        ], [
            'api_code.required' => 'Vui lòng nhập mã Google Sheet.',
            'name_sheet.required' => 'Vui lòng nhập tên bảng.',
            'name_sheet.regex' => 'Tên bảng chỉ được chứa chữ cái, số và dấu gạch dưới, không dấu và không khoảng trắng.',
        ]);

        $user = Auth::user();

        Ggsheet::updateOrCreate(
            ['user_id' => $user->id],
            [
                'api_code' => $validated['api_code'],
                'name_sheet' => $validated['name_sheet'],
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Dữ liệu đã được cập nhật thành công!',
        ]);
    }

}
