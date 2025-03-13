<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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
    public function updateUser(Request $request, $id)
    {
        Log::info('Receiving data from SuperAdmin', $request->all());
        try {
            $user = User::findOrFail($id);
            if (!$user) {
                throw new Exception('User not found');
            }
            $user->update($request->all());
            return response()->json(['success' => 'User updated successfully']);
        } catch (Exception $e) {
            Log::error('Failed to update user in Admin: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to update user'], 500);
        }
    }

    public function deleteUser($id)
    {
        Log::info('Receiving delete request for user with id: ' . $id);
        try {
            $user = User::findOrFail($id);
            $user->delete();

            return response()->json(['success' => 'User deleted successfully']);
        } catch (ModelNotFoundException $e) {
            Log::warning('User not found with id: ' . $id);
            return response()->json(['error' => 'User not found'], 404);
        } catch (\Exception $e) {
            Log::error('Failed to delete user with id: ' . $id . '. Error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to delete user'], 500);
        }
    }
}
