<?php

namespace App\Http\Controllers;

use App\Http\Requests\SpreadsheetRequestForm;
use App\Resource\AccessToken;
use App\Resource\ClientContract;
use Google_Service_Sheets;
use Google_Service_Sheets_Spreadsheet;
use Google_Service_Sheets_ValueRange;

class SpreadSheetController extends Controller
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

    public function index()
    {
        dd(auth()->user());
    }

    /**
     * @param string $spreadsheet_id
     * @param string|null $sheet
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(string $spreadsheet_id, string $sheet = null)
    {
        // set token
        $this->client->setAccessToken($this->getAccessToken());
        $sheet_names = $this->getSheetList($spreadsheet_id);
        $records = [];
        if (null === $sheet) {
            $sheet = current($sheet_names);
        }


        if (count($sheet_names)) {
            $records = $this->getRecords($spreadsheet_id, $sheet);
        }

        return view('sheets.edit', compact('sheet_names', 'records', 'sheet', 'spreadsheet_id'));
    }

    /**
     * @param $spreadsheet_id
     * @param $sheet
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($spreadsheet_id, $sheet)
    {
        $this->client->setAccessToken($this->getAccessToken());
        $requestBody = new Google_Service_Sheets_ValueRange();
        $requestBody->setMajorDimension("DIMENSION_UNSPECIFIED");
        $requestBody->setRange($sheet);
        $requestBody->setValues(request('records'));
        $sheets = new Google_Service_Sheets($this->client->getService());
        try {
            $sheets->spreadsheets_values->update($spreadsheet_id, $sheet, $requestBody, [
                'valueInputOption' => 'RAW',
            ]);
            flash()->success('Details updated');
            return redirect()->route('spreadsheet.edit',['spreadsheet_id'=>$spreadsheet_id,'sheet' => $sheet]);
        }
        catch (\Google_Service_Exception $e) {
            logger()->error($e->getMessage());
            $error_message = json_decode($e->getMessage(), true);
            flash()->error($error_message['error']['message']);
            return back();
        }
        catch (\Exception $e) {
            logger()->error($e->getMessage());
            flash()->error('Something went wrong');
            return back();
        }
    }

    /**
     * @param SpreadsheetRequestForm $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(SpreadsheetRequestForm $request)
    {

        if (!request()->ajax()) {
            logger()->alert('Bad Request', ['ip' => request()->ip()]);
            return response()->json(['error' => 'Bad Request'], 400);
        }
        try {
            $this->client->setAccessToken($this->getAccessToken());

            $sheet = new Google_Service_Sheets_Spreadsheet([
                'properties' => [
                    'title' => $request->get('spreadsheet'),
                ],
            ]);
            $spreadsheet = $this->client->getSheet()->spreadsheets->create($sheet, [
                'fields' => 'spreadsheetId',
            ]);
            logger()->info('created', ['sheet' => $spreadsheet]);
            return response()->json(['message' => 'Spreadsheet created Successfully.'], 200);
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
        if (!request()->ajax()) {
            logger()->alert('Bad Request', ['ip' => request()->ip()]);
            return response()->json(['error' => 'Bad Request'], 400);
        }
        try {
            $this->client->setAccessToken($this->getAccessToken());

            $this->client->getDrive()->files->delete(request('spreadsheet'));
            return response()->json(['message' => 'Spreadsheet Removed Successfully.'], 200);
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
     * @param $spreadsheet_id
     * @return array
     */
    private function getSheetList($spreadsheet_id)
    {
        $sheets = $this->spreadsheet->spreadsheets->get($spreadsheet_id)->getSheets();

        if (count($sheets) === 0) {
            return [];
        }

        $results = [];

        foreach ($sheets as $sheet) {
            $results[$sheet->getProperties()->getSheetId()] = $sheet->getProperties()->getTitle();
        }
        return $results;
    }

    /**
     * @param $spreadsheet_id
     * @param $sheet_id
     * @return mixed
     */
    private function getRecords($spreadsheet_id, $sheet_id)
    {
        return $this->spreadsheet->spreadsheets_values->batchGet($spreadsheet_id, [
            'ranges' => $sheet_id,
        ])->getValueRanges()[0]->getValues();
    }
}
