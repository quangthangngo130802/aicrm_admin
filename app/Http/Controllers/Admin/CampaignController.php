<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Campaign;
use App\Models\CampaignDetail;
use App\Models\City;
use App\Models\OaTemplate;
use App\Models\User;
use App\Models\ZaloOa;
use App\Services\CampaignService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Maatwebsite\Excel\Excel as ExcelFormat;

class CampaignController extends Controller
{
    protected $campaignService;
    public function __construct(CampaignService $campaignService)
    {
        $this->campaignService = $campaignService;
    }

    public function index()
    {
        try {
            $title = 'Chiến dịch';
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

    public function add()
    {
        try {
            $oa_id = ZaloOa::where('is_active', 1)->first()->id;
            $template = OaTemplate::where('oa_id', $oa_id)->get();
            return view('admin.campaign.add', compact('template'));
        } catch (Exception $e) {
            Log::error('Failed to get OaId or templates: ' . $e->getMessage());
            return ApiResponse::error('Failed to get OaId or templates', 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'sent_time' => 'required|date_format:H:i',
                'sent_date' => 'required|date_format:Y-m-d',
                'import_file' => 'nullable|file|mimes:xlsx,xls'
            ]);

            $campaign = Campaign::create([
                'name' => $request->input('name'),
                'template_id' => $request->input('template_id'),
                'sent_time' => $request->input('sent_time'),
                'sent_date' => $request->input('sent_date'),
                'status' => 1
            ]);

            if ($request->hasFile('import_file') && $request->file('import_file')->isValid()) {
                $filePath = $request->file('import_file')->getRealPath();

                // Determine the file type explicitly
                $fileExtension = $request->file('import_file')->getClientOriginalExtension();
                $fileType = $fileExtension === 'xlsx' ? ExcelFormat::XLSX : ExcelFormat::XLS;

                $rows = Excel::toArray(new class implements ToArray {
                    public function array(array $array)
                    {
                        return $array;
                    }
                }, $filePath, null, $fileType)[0];

                foreach (array_slice($rows, 1) as $row) {
                    if (isset($row[0]) && !empty($row[0])) {
                        $existingUser = User::where('phone', $row[1])->first();
                        $city = City::where('name', $row[4])->first();

                        if ($existingUser) {
                            CampaignDetail::create([
                                'campaign_id' => $campaign->id,
                                'user_id' => $existingUser->id,
                                'data' => json_encode([]),
                            ]);
                        } else {
                            $password = '123456';
                            $hashedPassword = Hash::make($password);

                            try {
                                $dob = Carbon::createFromFormat('d/m/Y', $row[3])->format('Y-m-d');
                            } catch (\Exception $e) {
                                $dob = null;
                            }

                            $newUser = User::create([
                                'name' => $row[0],
                                'phone' => $row[1],
                                'email' => $row[2],
                                'password' => $hashedPassword,
                                'dob' => $dob,
                                'status' => 'active',
                                'role_id' => 2,
                                'city_id' => $city->id ?? null,
                                'address' => $row[5],
                                'source' => 'Import'
                            ]);

                            CampaignDetail::create([
                                'campaign_id' => $campaign->id,
                                'user_id' => $newUser->id,
                                'data' => json_encode([]),
                            ]);
                        }
                    }
                }
            }

            session()->flash('success', 'Thêm chiến dịch thành công');
            return redirect()->route('admin.' . Auth::user()->username . '.campaign.index');
        } catch (Exception $e) {
            Log::error('Failed to create new Campaign:' . $e->getMessage());
            return ApiResponse::error('Failed to create new Campaign', 500);
        }
    }

    public function edit($id)
    {
        try {
            $oa_id = ZaloOa::where('is_active', 1)->first()->id;
            $template = OaTemplate::where('oa_id', $oa_id)->get();
            $campaigns = Campaign::find($id);
            return view('admin.campaign.edit', compact('template', 'campaigns'));
        } catch (Exception $e) {
            Log::error('Failed to find Campaign: ' . $e->getMessage());
            return ApiResponse::error('Failed to find Campaign', 500);
        }
    }

    public function update($id, Request $request)
    {
        try {
            $campaigns = $this->campaignService->updateCampaign($request->all(), $id);
            session()->flash('success', 'Cập nhật thông tin chiến dịch thành công');
            return redirect()->route('admin.' . Auth::user()->username . '.campaign.index');
        } catch (Exception $e) {
            Log::error('Failed to update Campagin Information:' . $e->getMessage());
            return ApiResponse::error('Failed to update Campaign Information', 500);
        }
    }

    public function delete()
    {
        try {
            // dd(request());
            $this->campaignService->deleteCampaign(request('id'));
            $campaigns = $this->campaignService->getPaginateCampaign();
            $table = view('admin.campaign.table', compact('campaigns'))->render();
            $pagination = $campaigns->links('vendor.pagination.custom')->render();

            return response()->json([
                'success' => true,
                'message' => 'Xóa chiến dịch thành công',
                'table' => $table,
                'pagination' => $pagination,
            ]);
        } catch (Exception $e) {
            Log::error('Failed to delete Campaigns: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Xóa chiến dịch thất bại'
            ]);
        }
    }

    public function updateStatus(Request $request)
    {
        try {
            // Kiểm tra nếu không có chiến dịch nào được tìm thấy sẽ gây lỗi
            $campaign = Campaign::findOrFail($request->campaignId);
            $campaign->status = $request->input('status');
            $campaign->save();
            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Cập nhật trạng thái thất bại', 'error' => $e->getMessage()]);
        }
    }

    public function fetch()
    {
        $campaigns = Campaign::orderByDesc('created_at')->paginate(10);

        return view('admin.campaign.table', compact('campaigns'))->render();
    }
}
