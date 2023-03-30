<?php

namespace Core\Util\Http\Client;

class ContentTypeJson extends ContentType
{
    public function __construct()
    {
        parent::__construct('Content-Type:application/json');
    }
}