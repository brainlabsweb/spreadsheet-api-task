<?php

return [
    // json file path
    'credentials_path' => base_path(env('GOOGLE_CREDENTIALS_PATH','credentials.json')),
    // application name
    'application_name' => env('GOOGLE_APPLICATION_NAME','Google Spread Sheet API'),
    // scopes
    'scopes' => [\Google_Service_Sheets::SPREADSHEETS, Google_Service_Drive::DRIVE_FILE],
    // access type
    'access_type' => env('GOOGLE_ACCESS_TYPE', 'online'),
    // none, consent, select_account
    'approval_prompt' => env('GOOGLE_APPROVAL_PROMPT', 'none'),

];
