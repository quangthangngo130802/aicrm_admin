<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\ZaloOa;
use App\Models\ZnsMessage;
use App\Services\OaTemplateService;
use App\Services\ZaloOaService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ZnsMessageController extends Controller
{
    protected $zaloOaService;
    protected $oaTemplateService;

    public function __construct(OaTemplateService $oaTemplateService, ZaloOaService $zaloOaService)
    {
        $this->oaTemplateService = $oaTemplateService;
        $this->zaloOaService = $zaloOaService;
    }
    public function znsMessage(Request $request)
    {
        try {
            $title = 'Hạn mức tin nhắn';
            // Lấy tất cả các OA đang hoạt động
            $activeOas = ZaloOa::where('user_id', Auth::user()->id)->where('is_active', 1)->first()->id;
            // Lấy tất cả các tin nhắn từ các OA đang hoạt động
            $messages = ZnsMessage::where('oa_id', $activeOas)->where('user_id', Auth::user()->id)
                ->orderByDesc('created_at')
                ->paginate(10);
            // dd($messages);
            // Tính tổng phí cho mỗi OA
            $totalFeesByOa = ZnsMessage::where('user_id', Auth::id())
                ->where('status', 1)
                ->whereHas('zaloOa', function ($query) {
                    $query->where('is_active', 1);
                })
                ->join('sgo_oa_templates', 'sgo_zns_messages.template_id', '=', 'sgo_oa_templates.id')
                ->sum('sgo_oa_templates.price');

            // dd($totalFeesByOa);
            if (request()->ajax()) {
                $view = view('admin.message.table', compact('messages'))->render();
                return response()->json(['success' => true, 'table' => $view]);
            }
            return view('admin.message.index', compact('messages', 'totalFeesByOa', 'title'));
        } catch (Exception $e) {
            Log::error('Failed to get Messages: ' . $e->getMessage());
            return ApiResponse::error('Failed to get Messages', 500);
        }
    }



    public function znsQuota()
    {
        $accessToken = $this->zaloOaService->getAccessToken();

        try {
            $client = new Client();
            $response = $client->get('https://business.openapi.zalo.me/message/quota', [
                'headers' => [
                    'access_token' => $accessToken,
                    'Content-Type' => 'application/json',
                ],
            ]);

            $responseBody = $response->getBody()->getContents();
            Log::info('Phản hồi API: ' . $responseBody);
            $responseData = json_decode($responseBody, true)['data'];
            return view('admin.message.quota', compact('responseData'));
        } catch (Exception $e) {
            Log::error('Cannot get ZNS quota: ' . $e->getMessage());
            return ApiResponse::error('Cannot get ZNS quota', 500);
        }
    }

    public function templateIndex()
    {
        $title = 'Thông tin ZNS Template';
        $templates = $this->oaTemplateService->getAllTemplateByOaID();
        $initialTemplateData = null;

        if ($templates->isNotEmpty()) {
            $initialTemplateData = $this->oaTemplateService->getTemplateById($templates->first()->template_id, $templates->first()->oa_id);
        }

        return view('admin.message.template', compact('templates', 'initialTemplateData', 'title'));
    }

    public function getTemplateDetail(Request $request)
    {
        $templateId = $request->input('template_id');
        $accessToken = $this->zaloOaService->getAccessToken();

        try {
            $client = new Client();
            $response = $client->get('https://business.openapi.zalo.me/template/info/v2', [
                'headers' => [
                    'access_token' => $accessToken,
                    'Content-Type' => 'application/json'
                ],
                'query' => [
                    'template_id' => $templateId,
                ],
            ]);
            $responseBody = $response->getBody()->getContents();
            $responseData = json_decode($responseBody, true)['data'];

            // Format response for display
            return response()->json([
                'success' => true,
                'html' => view('admin.message.template_detail', compact('responseData'))->render()
            ]);
        } catch (Exception $e) {
            Log::error('Failed to get template details: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to get template details'], 500);
        }
    }


    public function refreshTemplates()
    {
        try {
            $statusMessage = $this->oaTemplateService->checkTemplate();
            $templates = $this->oaTemplateService->getAllTemplateByOaID();

            // Generate HTML for dropdown
            $options = '';
            foreach ($templates as $template) {
                $options .= '<option value="' . $template->template_id . '">' . $template->template_name . '</option>';
            }

            // Get the details of the first template if it exists
            $initialTemplateData = null;
            if ($templates->isNotEmpty()) {
                $initialTemplateData = $this->oaTemplateService->getTemplateById($templates->first()->template_id, $templates->first()->oa_id);
            }

            return response()->json(['templates' => $options, 'initialTemplateData' => $initialTemplateData]);
        } catch (Exception $e) {
            Log::error('Failed to refresh templates: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to refresh templates'], 500);
        }
    }

    public function status(Request $request)
    {
        // Lấy giá trị trạng thái từ request
        $status = $request->status;

        try {
            // Lấy OA đang hoạt động
            $activeOa = ZaloOa::where('user_id', Auth::user()->id)->where('is_active', 1)->first();
            if (!$activeOa) {
                return back()->with('error', 'Không tìm thấy OA đang hoạt động.');
            }

            // Nếu không có trạng thái được chọn, gọi hàm znsMessage để trả về tất cả tin nhắn
            if ($status === null || $status === '') {
                return $this->znsMessage($request);
            }

            // Lấy tin nhắn theo trạng thái đã chọn
            $messages = ZnsMessage::where('oa_id', $activeOa->id)
                ->where('user_id', Auth::user()->id)
                ->where('status', $status)
                ->orderByDesc('sent_at')
                ->paginate(10)
                ->appends(['status' => $status]); // Thêm status vào các liên kết phân trang

            // Tính tổng phí cho mỗi OA
            $totalFeesByOa = ZnsMessage::where('user_id', Auth::id())
                ->where('status', 1)
                ->whereHas('zaloOa', function ($query) {
                    $query->where('is_active', 1);
                })
                ->join('sgo_oa_templates', 'sgo_zns_messages.template_id', '=', 'sgo_oa_templates.id')
                ->sum('sgo_oa_templates.price');

            return view('admin.message.index', compact('messages', 'totalFeesByOa', 'status'));
        } catch (Exception $e) {
            Log::error('Failed to find Messages: ' . $e->getMessage());
            return redirect()->route('admin.{username}.message.znsMessage', [
                'username' => Auth::user()->username
            ])->with('error', 'Không thể lấy danh sách tin nhắn');
        }
    }

    public function statusDashboard(Request $request)
    {
        $status = $request->get('status');

        if ($status !== null) {
            $messages = ZnsMessage::where('status', $status)->where('user_id', Auth::user()->id)->orderByDesc('created_at')->get();
        }

        return view('admin.message.index', compact('messages'));
    }

    public function params()
    {
        $title = "Định dạng tham số khi tạo mẫu ZNS";
        return view('admin.message.template_parameter', compact('title'));
    }

    public function export(Request $request)
    {
        // Lấy OA đang hoạt động
        $activeOa = ZaloOa::where('user_id', Auth::user()->id)->where('is_active', 1)->first();
        if (!$activeOa) {
            return back()->with('error', 'Không tìm thấy OA đang hoạt động.');
        }

        // Lấy giá trị status từ request
        $status = $request->input('status');

        // Lọc tin nhắn dựa trên OA và trạng thái
        $messagesQuery = ZnsMessage::where('oa_id', $activeOa->id)
            ->where('user_id', Auth::user()->id);

        if ($status !== null) {
            $messagesQuery->where('status', $status);
        }

        $messages = $messagesQuery->orderByDesc('created_at')->get();

        // Tạo file Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Tên OA');
        $sheet->setCellValue('B1', 'Tên người nhận');
        $sheet->setCellValue('C1', 'Số điện thoại');
        $sheet->setCellValue('D1', 'Ngày gửi');
        $sheet->setCellValue('E1', 'Template');
        $sheet->setCellValue('F1', 'Phí');
        $sheet->setCellValue('G1', 'Trạng thái');
        $sheet->setCellValue('H1', 'Thông báo');

        $row = 2;
        foreach ($messages as $message) {
            $sheet->setCellValue('A' . $row, $message->zaloOa->name ?? '');
            $sheet->setCellValue('B' . $row, $message->name ?? '');
            $sheet->setCellValue('C' . $row, $message->phone ?? '');
            $sheet->setCellValue('D' . $row, Carbon::parse($message->sent_at)->format('h:i:s d/m/Y') ?? '');
            $sheet->setCellValue('E' . $row, $message->template->template_name ?? '');
            $sheet->setCellValue('F' . $row, $message->template->price ?? 0);
            $sheet->setCellValue('G' . $row, $message->status == 0 ? 'Thất bại' : 'Thành công');
            $sheet->setCellValue('H' . $row, $message->note ?? '');
            $row++;
        }

        // Định dạng độ rộng các cột
        foreach (['A' => 30, 'B' => 30, 'C' => 20, 'D' => 40, 'E' => 50, 'F' => 10, 'G' => 20, 'H' => 50] as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }

        // Xuất file
        $writer = new Xlsx($spreadsheet);
        $fileName = 'Danh sách tin nhắn ZNS.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);

        $writer->save($temp_file);

        return response()->download($temp_file, $fileName)->deleteFileAfterSend(true);
    }
}
