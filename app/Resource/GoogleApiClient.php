<?php
namespace App\Resource;

use Google_Service_Drive;
use Google_Service_Sheets;
use Google_Client;

class GoogleApiClient implements ClientContract
{
    /**
     * @var \Google_Client
     */
    private $client;

    /**
     * DemoTest constructor.
     * @param \Google_Client $client
     */
    public function __construct(Google_Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param string $config
     * @return $this
     * @throws \Google_Exception
     */
    public function setConfig(string $config)
    {
        $this->client->setAuthConfig($config);
        return $this;
    }

    /**
     * @param $token
     * @return $this
     */
    public function setAccessToken($token)
    {
        $this->client->setAccessToken($token);
        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setApplicationName(string $name)
    {
        $this->client->setApplicationName($name);
        return $this;
    }

    /**
     * @param string $type
     * @return $this
     */
    public function setAccessType(string $type)
    {
        $this->client->setAccessType($type);
        return $this;
    }

    /**
     * @param array $scopes
     * @return $this
     */
    public function setScopes(array $scopes)
    {
        $this->client->setScopes($scopes);
        return $this;
    }

    /**
     * @return Google_Client
     */
    public function getService() : Google_Client
    {
        return $this->client;
    }

    /**
     * @return Google_Service_Sheets
     */
    public function getSheet(): Google_Service_Sheets
    {
        return new Google_Service_Sheets($this->client);
    }

    /**
     * @return Google_Service_Drive
     */
    public function getDrive() : Google_Service_Drive
    {
        return new Google_Service_Drive($this->client);
    }

}
