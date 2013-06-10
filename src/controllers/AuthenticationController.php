<?php namespace Vinelab\Auth\Controllers;

use App;
use Response;
use Input;

use Illuminate\Config\Repository as Config;
use Illuminate\Cache\CacheManager as Cache;
use Illuminate\Routing\Redirector as Redirector;

Class AuthenticationController extends BaseController {

	public function __construct()
	{
		$this->auth = App::make('vinelab.social.auth');
	}

	public function index($service)
	{
		return $this->auth->authenticate($service);
	}

	public function callback($service)
	{
		try {
			return $this->auth->authenticationCallback($service, Input::get());
		} catch (\Exception $e) {
			var_dump($e->getMessage());
		}
	}
}

