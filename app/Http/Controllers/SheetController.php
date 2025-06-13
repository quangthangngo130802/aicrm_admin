<?php

namespace App\Http\Controllers;

use Google_Client;
use Google_Service_Sheets;
use Illuminate\Http\Request;

class SheetController extends Controller
{
    public function index()
    {
        // dd(1);
        $client = new Google_Client();
        $client->setApplicationName('Laravel Google Sheet');
        $client->setScopes([Google_Service_Sheets::SPREADSHEETS_READONLY]);
        $client->setAuthConfig(storage_path('app/google/credentials.json'));

        $service = new Google_Service_Sheets($client);

        $spreadsheetId = '1DrVNKIPmwKNUKjCGYmLbGV0lUlCovtaJccDleCj48kc'; // thay bằng ID thực tế
        $range = "'Trang tính1'!A1:Z1000";

        $response = $service->spreadsheets_values->get($spreadsheetId, $range);
        $values = $response->getValues();
        dd($values);
        // return view('sheet.index', compact('values'));
    }
}
