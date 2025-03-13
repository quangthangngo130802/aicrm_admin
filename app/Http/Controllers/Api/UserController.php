<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function addUser(Request $request)
    {
        Log::info('Receiving data from SuperAdmin', $request->all());

        try {
            // Thêm người dùng vào database admin
            User::create($request->all());
            return response()->json(['success' => 'User added successfully']);
        } catch (Exception $e) {
            Log::error('Failed to add user in Admin: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to add user'], 500);
        }
    }
}
