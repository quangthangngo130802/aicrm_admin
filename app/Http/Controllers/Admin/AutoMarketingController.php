<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AutoMarketingController extends Controller
{
    public function index()
    {
        try {
            $title = 'Automation Marketing';
            $campaigns = $this->campaignService->getPaginateCampaign();
            // dd($campaigns);
            if (request()->ajax()) {
                $view = view('admin.campaign.table', compact('campaigns'))->render();
                return response()->json(['success' => true, 'table' => $view]);
            }
            return view('admin.campaign.index', compact('campaigns', 'title'));
        } catch (Exception $e) {
            Log::error('Failed to fetch all Campaign:' . $e->getMessage());
            return ApiResponse::error('Failed to fetch all Campaign', 500);
        }
    }
}
