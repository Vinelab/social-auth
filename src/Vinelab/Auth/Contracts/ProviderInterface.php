<?php namespace Vinelab\Auth\Contracts;

interface ProviderInterface {

    public function authenticate();

    public function callback($input);
}