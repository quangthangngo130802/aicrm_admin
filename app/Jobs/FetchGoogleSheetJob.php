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

                // Bỏ qua dòng tiêu đề
                $dataRows = array_slice($values, 1);
                Log::info($dataRows);
                foreach ($dataRows as $index => $row) {
                    $rowIndex = $index + 2; // vì dòng tiêu đề là dòng 1

                    if (empty($row[1]) || empty($row[2]) || empty($row[3])) {
                        continue;
                    }

                    if (isset($row[4]) && $row[4] == 1) {
                        continue;
                    }

                    $data = [
                        'name' => $row[1],
                        'phone' => $row[2],
                        'address' => $row[3],
                    ];

                    $storeService->zns($value->user_id, $data);
                    // Cập nhật cột E thành 1
                    $updateRange = "$sheetName!E$rowIndex";

                    $body = new Google_Service_Sheets_ValueRange([
                        'values' => [[1]]
                    ]);

                    $params = ['valueInputOption' => 'RAW'];

                    $result = $service->spreadsheets_values->update(
                        $spreadsheetId,
                        $updateRange,
                        $body,
                        $params
                    );

                }

            }
        } catch (\Exception $e) {
            Log::error("❌ Lỗi xử lý Google Sheets: " . $e->getMessage());
        }
    }

}
