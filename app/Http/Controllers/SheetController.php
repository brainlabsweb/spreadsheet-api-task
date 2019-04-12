<?php

namespace App\Http\Controllers;

use App\Http\Requests\SheetRequestForm;
use App\Resource\AccessToken;
use App\Resource\ClientContract;
use Google_Service_Sheets;
use Google_Service_Sheets_Spreadsheet;

class SheetController extends Controller
{
    use AccessToken;
    /**
     * @var ClientContract
     */
    private $client;
    /**
     * @var \Google_Service_Sheets;
     */
    private $spreadsheet;

    /**
     * SheetController constructor.
     * @param ClientContract $client
     */
    public function __construct(ClientContract $client)
    {
        $this->client = $client;
        $this->spreadsheet = $client->getSheet();
    }

    /**
     * @param SheetRequestForm $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(SheetRequestForm $request)
    {

        if (!request()->ajax()) {
            logger()->alert('Bad Request', ['ip' => request()->ip()]);
            return response()->json(['error' => 'Bad Request'], 400);
        }
        try {
            $this->client->setAccessToken($this->getAccessToken());
            $sheets = new Google_Service_Sheets($this->client->getService());

            $requestBody = new \Google_Service_Sheets_BatchUpdateSpreadsheetRequest();

            $data = [
                "addSheet" => [
                    "properties" => [
                        "title" => $request->get('sheet'),
                    ],
                ],
            ];
            $requestBody->setRequests($data);

            $sheets->spreadsheets->batchUpdate($request->get('spreadsheet'), $requestBody);
            return response()->json(['message' => 'Sheet created Successfully.'], 200);
        }
        catch (\Google_Service_Exception $e) {
            logger()->error($e->getMessage());
            $error_message = json_decode($e->getMessage(), true);
            return response()->json(['error' => $error_message['error']['message']], 422);
        }
        catch (\Exception $e) {
            logger()->error($e->getMessage());
            return response()->json(['error' => 'Something went wrong'], 422);
        }

    }

    /**
     * Remove spreadsheet from drive
     * @ajax
     * @DELETE
     * @param $spreadsheet_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete()
    {
        $this->validate(request(), ['sheet' => 'required']);

        if (!request()->ajax()) {
            logger()->alert('Bad Request', ['ip' => request()->ip()]);
            return response()->json(['error' => 'Bad Request'], 400);
        }
        try {
            $this->client->setAccessToken($this->getAccessToken());
            $sheets = new Google_Service_Sheets($this->client->getService());

            $requestBody = new \Google_Service_Sheets_BatchUpdateSpreadsheetRequest();

            $data = [
                "deleteSheet" => [
                    "sheetId" => request('sheet'),
                ],
            ];
            $requestBody->setRequests($data);

            $sheets->spreadsheets->batchUpdate(request('spreadsheet'), $requestBody);
            return response()->json(['message' => 'Sheet Removed Successfully.'], 200);
        }
        catch (\Google_Service_Exception $e) {
            logger()->error($e->getMessage());
            $error_message = json_decode($e->getMessage(), true);
            return response()->json(['error' => $error_message['error']['message']], 422);
        }
        catch (\Exception $e) {
            logger()->error($e->getMessage());
            return response()->json(['error' => 'Something went wrong'], 422);

        }
    }

}
