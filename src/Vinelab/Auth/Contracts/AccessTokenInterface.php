<?php namespace Vinelab\Auth\Contracts;

use Vinelab\Http\Response;

interface AccessTokenInterface {

    public function make(Response $response);

    public function token();

    public function expiry();
}