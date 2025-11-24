<?php

namespace App\Jobs;

use App\Core\Config;

class EmailJob
{
    public function handle(array $data): void
    {
        $to = $data['to'] ?? '';
        $subject = $data['subject'] ?? '';
        $body = $data['body'] ?? '';
        $isHtml = $data['html'] ?? false;

        if (empty($to) || empty($subject) || empty($body)) {
            throw new \Exception('Missing required email parameters');
        }

        $mailConfig = Config::get('mail');
        
        // Use PHP mail function (works on all servers)
        $headers = "From: {$mailConfig['from_name']} <{$mailConfig['from_address']}>\r\n";
        $headers .= "Reply-To: {$mailConfig['from_address']}\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
        if ($isHtml) {
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        } else {
            $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        }
        
        mail($to, $subject, $body, $headers);
    }
}

