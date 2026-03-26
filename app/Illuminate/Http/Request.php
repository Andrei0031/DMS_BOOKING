<?php

namespace Illuminate\Http;

class Request
{
    public static function capture()
    {
        return new static();
    }

    public function getMethod()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    public function getPath()
    {
        return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    }
}
