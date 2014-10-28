<?php namespace Vinelab\Auth\Contracts;

interface ProviderInterface {

    public function authenticate();

    public function callback($input);

    public function authenticateWithToken($token, $secret = null);
}
