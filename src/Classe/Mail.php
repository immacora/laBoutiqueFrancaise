<?php

namespace App\Classe;

use Mailjet\Resources;
use Mailjet\Client;

class Mail
{
    private $api_key = '3792aaa8463d55d59ec063eb183ecd46';
    private $api_key_secret = '12724e4bebcbab97d87f1c37a060c4b7';

    public function send($to_email, $to_name, $subject, $content) 
    {
        $mj = new Client($this->api_key, $this->api_key_secret, true, ['version' => 'v3.1']);

        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "laboutiquefrancaise@immacora.com",
                        'Name' => "La Boutique Française"
                    ],
                    'To' => [
                        [
                            'Email' => $to_email,
                            'Name' => $to_name
                        ]
                    ], 
                    'TemplateID' => 3525702,
                    'TemplateLanguage' => true,
                    'Subject' => $subject,
                    'Variables' => [
                        'content' => $content
                    ]
                ]
            ]
        ];
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        $response->success();
    }

}