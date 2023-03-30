<?php

namespace Core\Util\Http\Client;

class Request
{
    private $ch;

    public function __construct($ch) {
        $this->ch = $ch;
    }

    public function send(): Response {
        $response = new Response($this->ch, curl_exec($this->ch));
        curl_close($this->ch);
        return $response;
    }
}