<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Services\BaoHanhService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BaoHanhController extends Controller
{
    protected $storeService;
    public function __construct(BaoHanhService $storeService)
    {
        $this->storeService = $storeService;
    }

    public function update(Request $request){
        // try {
            Log::info('Start validation for adding new client');
            Log::info( $request->all());

            $existingUser = Customer::where('user_id', $request->user_id)->where('phone', $request->phone)->first();
            if (!$existingUser) {
                Log::info("Start creating new customer");
                $this->storeService->addNewStore($request->all());
            } elseif ($existingUser) {
                Log::info("Customer is already existed, start sending message");
                $this->storeService->updateCustomer($existingUser->id, $request->all());
            }

            return response()->json([
                'success' => true,
                'message' => 'Client added successfully',
            ]);

    }
}
