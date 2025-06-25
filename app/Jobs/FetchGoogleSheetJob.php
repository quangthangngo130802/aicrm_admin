<?php

namespace App\Jobs;

use App\Models\Customer;
use App\Models\Ggsheet;
use App\Services\SheetService;
use App\Services\StoreService;
use App\Services\TemplateService;
use Google_Client;
use Google_Service_Sheets;
use Google_Service_Sheets_ValueRange;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use SheetDB\SheetDB;

class FetchGoogleSheetJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $storeService = app()->make(TemplateService::class);
            $ggsheets = Ggsheet::with('user')->get();
            foreach ($ggsheets as $value) {
                $client = new Google_Client();
                $client->setApplicationName('Laravel Google Sheet');
                $client->setScopes([Google_Service_Sheets::SPREADSHEETS]); // quyền ghi
                $client->setAuthConfig(storage_path('app/google/credentials.json'));

                $service = new Google_Service_Sheets($client);

                $spreadsheetId = $value->api_code;
                $sheetName = $value->name_sheet;
                $range = "'$sheetName'!A1:Z1000";

                $response = $service->spreadsheets_values->get($spreadsheetId, $range);
                $values = $response->getValues();

                if (empty($values) || count($values) <= 1) {
                    Log::warning("Không có dữ liệu hoặc chỉ có tiêu đề.");
                    continue;
                }

                $rawHeaders = array_map('trim', $values[0]);
                $headers = array_map(function ($header) {
                    return Str::slug($header, '_'); // "Địa chỉ" => "dia_chi"
                }, $rawHeaders);

                $dataRows = array_slice($values, 1);

                foreach ($dataRows as $index => $row) {
                    $rowIndex = $index + 2; // vì dòng tiêu đề là dòng 1

                    // Tạo mảng ánh xạ header => value
                    $rowData = [];
                    foreach ($headers as $i => $header) {
                        $rowData[$header] = $row[$i] ?? null;
                    }

                    Log::info($rowData);
                    // Kiểm tra nếu các trường cần thiết không tồn tại hoặc rỗng
                    if ( empty($rowData['so_dien_thoai'])) {
                        continue;
                    }

                    // Kiểm tra nếu đã xử lý
                    if (isset($rowData['status']) && $rowData['status'] == 1) {
                        continue;
                    }

                    $data = [
                        'name'    => $rowData['ho_va_ten'],
                        'phone'   => $rowData['so_dien_thoai'],
                        'address' => $rowData['dia_chi'],
                        'order_code' => $rowData['ma_khach_hang'],
                        'product_name' => $rowData['ten_san_pham'],
                    ];

                    // Gửi ZNS hoặc xử lý
                    $storeService->zns($value->user_id, $data);

                    // Cập nhật status = 1
                    $statusColumnIndex = array_search('status', $headers);
                    if ($statusColumnIndex !== false) {
                        $colLetter = chr(65 + $statusColumnIndex); // A=65, B=66...
                        $updateRange = "$sheetName!{$colLetter}{$rowIndex}";

                        $body = new Google_Service_Sheets_ValueRange([
                            'values' => [[1]]
                        ]);

                        $params = ['valueInputOption' => 'RAW'];

                        $service->spreadsheets_values->update(
                            $spreadsheetId,
                            $updateRange,
                            $body,
                            $params
                        );
                    }
                }

            }
        } catch (\Exception $e) {
            Log::error("❌ Lỗi xử lý Google Sheets: " . $e->getMessage());
        }
    }

}
