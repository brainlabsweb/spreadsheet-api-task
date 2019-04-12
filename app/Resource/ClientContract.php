<?php

namespace App\Resource;


use Google_Client;
use Google_Service_Drive;
use Google_Service_Sheets;

interface ClientContract
{
    public function setConfig(string $config);

    public function setAccessToken($token);

    public function setApplicationName(string $name);

    public function setAccessType(string $type);

    public function setScopes(array $scopes);

    public function getService():Google_Client;

    public function getSheet():Google_Service_Sheets;

    public function getDrive():Google_Service_Drive;
}
