<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use SoapClient;
use stdClass;

class SmsService
{
    protected $username;
    protected $password;
    protected $alias;
    protected $wsdl;

    public function __construct()
    {
        $this->username = config('services.mobitel_esms.username');
        $this->password = config('services.mobitel_esms.password');
        $this->alias = config('services.mobitel_esms.alias');
        $this->wsdl = config('services.mobitel_esms.wsdl');
    }

    /**
     * Create a SOAP client instance.
     */
    protected function getClient()
    {
        ini_set("soap.wsdl_cache_enabled", "0");
        return new SoapClient($this->wsdl);
    }

    /**
     * Create a session with the ESMS gateway.
     */
    protected function createSession()
    {
        try {
            $client = $this->getClient();

            $user = new stdClass();
            $user->id = '';
            $user->username = $this->username;
            $user->password = $this->password;
            $user->customer = '';

            $params = new stdClass();
            $params->user = $user;

            $response = $client->createSession($params);
            return $response->return;
        } catch (\Exception $e) {
            Log::error('SmsService: Failed to create session', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Close an active session.
     */
    protected function closeSession($session)
    {
        try {
            $client = $this->getClient();
            $params = new stdClass();
            $params->session = $session;
            $client->closeSession($params);
        } catch (\Exception $e) {
            Log::error('SmsService: Failed to close session', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Send an SMS to one or more recipients.
     * 
     * @param string|array $recipients
     * @param string $message
     * @return string|null Response from API
     */
    public function sendSms($recipients, string $message)
    {
        if (empty($recipients)) {
            return null;
        }

        $recipientsArray = is_array($recipients) ? $recipients : explode(',', $recipients);
        
        $session = $this->createSession();
        if (!$session) {
            return null;
        }

        try {
            $client = $this->getClient();

            $smsMessage = new stdClass();
            $smsMessage->message = $message;
            $smsMessage->messageId = "";
            $smsMessage->recipients = $recipientsArray;
            $smsMessage->retries = "";
            $smsMessage->sender = $this->alias;
            $smsMessage->messageType = 0; // 0 for normal, 1 for promotional
            $smsMessage->sequenceNum = "";
            $smsMessage->status = "";
            $smsMessage->time = "";
            $smsMessage->type = "";
            $smsMessage->user = "";

            $params = new stdClass();
            $params->session = $session;
            $params->smsMessage = $smsMessage;

            $response = $client->sendMessages($params);
            
            Log::info('SmsService: SMS sent', [
                'recipients' => $recipientsArray,
                'response' => $response->return ?? $response
            ]);

            $this->closeSession($session);
            return $response->return ?? $response;
        } catch (\Exception $e) {
            Log::error('SmsService: Failed to send SMS', ['error' => $e->getMessage()]);
            $this->closeSession($session);
            return null;
        }
    }

    /**
     * Send a vaccination scheduled notification SMS.
     */
    public function sendVaccineScheduledSms($phone, $babyName, $vaccineName, $date, $midwifeName)
    {
        $message = "Vaccination Scheduled: A {$vaccineName} dose is scheduled for {$babyName} on {$date} by Midwife {$midwifeName}. Please visit the clinic on the scheduled date.";
        return $this->sendSms($phone, $message);
    }
}
