<?php

namespace Core\Util\Http\Client;

abstract class ContentType
{
    private $name;

    public function __construct($content_type)
    {
        $this->name = $content_type;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public static function toJson(): ContentType {
        return new ContentTypeJson();
    }
}