<?php
header('Content-Type: text/html; charset=utf-8');
defined('BASEPATH') or exit('No direct script access allowed');

class Sms_africastalking extends App_sms
{

    // Username
    private $username;

    // apiKey
    private $apiKey;

    public function __construct()
    {
        parent::__construct();

        $this->username = $this->get_option('africastalking', 'username');
        $this->apiKey = $this->get_option('africastalking', 'apiKey');
        $this->senderId = $this->get_option('africastalking', 'senderId');

        $this->add_gateway('africastalking', [
            'name'    => "Africa's Talking",
            'info'    => '<p>With simplified access to telco infrastructure, developers use our powerful SMS, USSD, Voice, Airtime and Payments APIs to bring their ideas to life, as they build and sustain scalable businesses. </p><hr class="hr-10" />',
            'options' => [
                [
                    'name'  => 'username',
                    'label' => 'Username',
                ],
                [
                    'name'  => 'apiKey',
                    'label' => 'Api Key',
                ], 
                [
                    'name'  => 'senderId',
                    'label' => 'Sender Id',
                ],              
            ],
        ]);
    }

    public function send() //$number, $message
    {
    
    }
}
