<?php

namespace Core\Util\Http\Client;

// TODO: Gerar Header
class Response {
    private $statusCode;
    private $body;

    public function __construct($ch, $result) {
        $this->statusCode = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        $this->body = new Body($result);
    }

    public function getStatusCode(): int
    {
        return intval($this->statusCode);
    }

    public function getBody(): Body
    {
        return $this->body;
    }
}