<?php

namespace App\Classe;

use Mailjet\Client;
use Mailjet\Resources;

class Mail
{
    private $api_key = "96804e898078e972a1a39f661f4905e6";
    private $api_key_secret = "ed1762655db2aa06364e910b0b3c53e6";

    public function send($to_email, $to_name, $subject, $template, $vars = null)
    {
        //Récupération du template
        $content = file_get_contents(dirname(__DIR__).'/Mail/'.$template);

        //Récupère les variables facultatives
        if ($vars) {
            foreach ($vars as $key=>$var) {
                $content = str_replace('{'.$key.'}', $var, $content);
            }
        }

        $mj = new Client($this->api_key, $this->api_key_secret, true,['version' => 'v3.1']);
        $mj = new \Mailjet\Client($_ENV['MJ_APIKEY_PUBLIC'], $_ENV['MJ_APIKEY_PRIVATE'],true,['version' => 'v3.1']);
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "tzen@hotmail.fr",
                        'Name' => "Me"
                    ],
                    'To' => [
                        [
                            'Email' => "tzen@hotmail.fr",
                            'Name' => "You"
                        ]
                    ],
                    'TemplateLanguage' => true,
                    'TemplateID' => 6828956,
                    'Subject' => "My first Mailjet Email!",
                    'Variables' => [
                        'content' => $content
                    ],
                    'HTMLPart' => "<h3>Dear passenger 1, welcome to <a href=\"https://www.mailjet.com/\">Mailjet</a>!</h3><br />May the delivery force be with you!"
                ]
            ]
        ];
        $response = $mj->post(Resources::$Email, ['body' => $body]);        
        $response->success(); // && var_dump($response->getData()); //OK // && dd($response->getData());
    }
}