<?php

namespace App\Services;

use Vonage\Client;
use Vonage\Client\Credentials\Basic;
use Vonage\SMS\Message\SMS;




class SmsService
{

    protected $client;
    public function __construct()
    {
        $credentials = new Basic(config('services.vonage.key'), config('services.vonage.secret'));
        $this->client = new Client($credentials);
    }

    public function send(string $to, string $message)
    {
        $response = $this->client->sms()->send(
            new SMS(
                $to,
                'Car rent',
                $message
            )
        );
        $message = $response->current();
        return $message;
    }

}