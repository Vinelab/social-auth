<?php namespace Vinelab\Auth\Contracts;

use Illuminate\Routing\Redirector;
use Vinelab\Http\Client as HttpClient;

interface ProvidersManagerInterface {

    public function instantiate($provider);
}