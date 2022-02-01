<?php

namespace App\Classe;

use Mailjet\Resources;
use Mailjet\Client;

class Mail
{
    private $api_key = '';
    private $api_key_secret = '';

    public function send($to_email, $to_name, $subject, $content, $username) 
    {
        $mj = new Client($this->api_key, $this->api_key_secret, true, ['version' => 'v3.1']);

        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "",
                        'Name' => "La Boutique Française"
                    ],
                    'To' => [
                        [
                            'Email' => $to_email,
                            'Name' => $to_name
                        ]                   
                    ],
                    'Bcc' => [
                        [
                            'Email' => "contact.laboutiquefrancaise@immacora.com",
                            'Name' => "Duplicata"
                        ]
                    ],
                    'TemplateID' => 0,
                    'TemplateLanguage' => true,
                    'Subject' => $subject,
                    'Variables' => [
                        'content' => $content,
                        'username' => $username
                    ]
                ]
            ]
        ];
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        $response->success();
    }

}
