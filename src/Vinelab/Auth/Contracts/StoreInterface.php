<?php namespace Vinelab\Auth\Contracts;

interface StoreInterface {

    public function put($key, $value);

    public function has($key);

    public function get($key);
}