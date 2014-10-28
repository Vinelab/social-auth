<?php namespace Vinelab\Auth\Facades;

use Illuminate\Support\Facades\Facade;

class Auth extends Facade {

    protected static function getFacadeAccessor() { return 'vinelab.socialauth'; }
}
