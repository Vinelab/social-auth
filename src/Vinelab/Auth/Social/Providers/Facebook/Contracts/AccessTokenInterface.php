<?php

namespace Vinelab\Auth\Social\Providers\Facebook\Contracts;

use Vinelab\Http\Response;

interface AccessTokenInterface
{
    public function make(Response $response);

    public function makeFromToken($token);

    public function token();

    public function expiry();
}
