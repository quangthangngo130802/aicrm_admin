<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SuperAdmin;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SuperAdminController extends Controller
{
    public function updateSuperAdmin(Request $request)
    {
        Log::info('Receiving data from SuperAdmin', $request->all());
        Log::info($request->banner);
        try {
            $superAdmin = SuperAdmin::first();

            // Cập nhật thông tin
            $data = $request->except(['banner']);
            $superAdmin->update($data);

            Log::info('Super admin in Admin updated successfully');
            return response()->json(['success' => 'Super admin updated successfully']);
        } catch (\Exception $e) {
            Log::error('Failed to update Super Admin in Admin: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to update Super Admin'], 500);
        }
    }
}
