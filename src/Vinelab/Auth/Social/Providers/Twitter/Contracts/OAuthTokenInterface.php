<?php

namespace Vinelab\Auth\Social\Providers\Twitter\Contracts;

use Vinelab\Http\Response;

interface OAuthTokenInterface
{
    public function make($key, $secret);

    public function verifier($key, $verifier);

    public function makeRequestToken(Response $response);

    public function makeAccessToken(Response $response);
}
