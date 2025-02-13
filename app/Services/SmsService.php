<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SmsService
{
    protected string $apiUrl;
    protected string $acode;
    protected string $apiKey;
    protected string $sender;

    public function __construct()
    {
        $this->apiUrl = config('services.sms.api_url');
        $this->acode = config('services.sms.acode');
        $this->apiKey = config('services.sms.api_key');
        $this->sender = config('services.sms.sender');
    }

    public function sendSms(string $contacts, string $message, string $type = 'text', string $transactionType = 'T', string $contentID = ''): bool
    {
        $response = Http::withOptions(['verify' => false])->get($this->apiUrl, [
            'acode' => $this->acode,
            'api_key' => $this->apiKey,
            'senderid' => $this->sender,
            'type' => $type,
            'msg' => $message,
            'contacts' => $contacts,
            'transactionType' => $transactionType,
            'contentID' => $contentID,
        ]);

        return $response->successful();
    }
}
