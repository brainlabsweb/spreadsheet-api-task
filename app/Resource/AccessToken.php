<?php


namespace App\Resource;


trait AccessToken
{
    /**
     * @return array
     */
    public function getAccessToken(): array
    {
        return [
            'access_token'  => auth()->user()->access_token,
            'refresh_token' => auth()->user()->refresh_token,
            'expires_in'    => 3600,
            'created'       => auth()->user()->updated_at->getTimestamp(),
        ];
    }
}
