<?php namespace Vinelab\Auth\Controllers;

use App;
use Response;
use Input;

use Vinelab\Auth\Social as SocialAuth;
use Vinelab\Auth\Repositories\UserRepository;
use Vinelab\Auth\Repositories\SocialAccountRepository;

use Vinelab\Auth\Contracts\UserEntityInterface as User;
use Vinelab\Auth\Contracts\SocialAccountEntityInterface as SocialAccount;

// use Illuminate\Config\Repository as Config;
// use Illuminate\Cache\CacheManager as Cache;
// use Illuminate\Routing\Redirector as Redirector;

Class AuthenticationController extends BaseController {

	public function __construct()
	{
		$this->auth = new SocialAuth(App::make('config'),
									 App::make('cache'),
									 App::make('redirect'),
									 App::make('vinelab.httpclient'),
									 new UserRepository(new User),
									 new SocialAccountRepository(new SocialAccount));
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

