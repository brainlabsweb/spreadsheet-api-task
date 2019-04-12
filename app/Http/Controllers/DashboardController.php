<?php

namespace App\Http\Controllers;

use App\Resource\AccessToken;
use App\Resource\ClientContract;

class DashboardController extends Controller
{
    use AccessToken;
    /**
     * @var ClientContract
     */
    private $client;

    /**
     * DashboardController constructor.
     * @param ClientContract $client
     */
    public function __construct(ClientContract $client)
    {
        $this->client = $client;
    }

    public function index()
    {
        // set the access token
        $this->client->setAccessToken($this->getAccessToken());
        // get drive instance
        $drive = $this->client->getDrive();

        $spreadsheets = $drive->files->listFiles([
            'q' => "mimeType = 'application/vnd.google-apps.spreadsheet'",
        ])->getFiles();
        return view('dashboard', compact('spreadsheets'));

    }
}
