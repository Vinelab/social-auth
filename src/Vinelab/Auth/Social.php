<?php namespace Vinelab\Auth;

use Vinelab\Auth\Social\Network as SocialNetwork;
use Vinelab\Auth\Exception\AuthenticationException;
use Vinelab\Auth\Exception\SocialAccountException;
use Vinelab\Http\Client as HttpClient;

use Vinelab\Auth\Contracts\UserRepositoryInterface;
use Vinelab\Auth\Contracts\SocialAccountRepositoryInterface;

use Illuminate\Config\Repository as Config;
use Illuminate\Cache\CacheManager as Cache;
use Illuminate\Routing\Redirector;

class Social {

	/**
	 * Instance
	 *
	 * @var Vinelab\Auth\Social\Network
	 */
	public $_Network;

	/**
	 * Instance
	 *
	 * @var Illuminate\Config\Repository
	 */
	protected $_Config;

	/**
	 * Instance
	 *
	 * @var Illuminate\Cache\CacheManager
	 */
	protected $_Cache;

	/**
	 * Instance
	 *
	 * @var  Illuminate\Routing\Redirector
	 */
	protected $_Redirect;

	/**
	 * Instance
	 *
	 * @var Vinelab\Http\Client
	 */
	protected $_HttpClient;

	/**
	 * Instance
	 *
	 * @var Vinelab\Auth\Contracts\UserRepositoryInterface
	 */
	protected $_users;

	/**
	 * Instance
	 *
	 * @var Vinelab\Auth\Contracts\SocialAccountRepositoryInterface
	 */
	protected $_socialAccounts;

	/**
	 * Keeps track of the request
	 *
	 * @var string
	 */
	public $state;

	protected $stateCacheKeyPrefix = 'auth_social_state_';

	function __construct(Config $config,
						 Cache $cache,
						 Redirector $redirector,
						 HttpClient $httpClient,
						 UserRepositoryInterface $userRepository,
						 SocialAccountRepositoryInterface $socialAccountRepository)
	{
		$this->_Config              = $config;
		$this->_Cache               = $cache;
		$this->_Redirect            = $redirector;
		$this->_HttpClient          = $httpClient;
		$this->_users 		    	= $userRepository;
		$this->_socialAccounts		= $socialAccountRepository;
	}

	/**
	 *
	 * @param  string $service
	 * @return  Illuminate\Routing\Redirector
	 */
	public function authenticate($service)
	{
		$this->_Network = $this->networkInstance($service);

		$this->state = $this->state ?: $this->makeState();

		$apiKey = $this->_Network->settings('api_key');
		$redirectURI = $this->_Network->settings('redirect_uri');

		$this->_Cache->put($this->stateCacheKey($this->state), ['api_key'=>$apiKey, 'redirect_uri'=>$redirectURI], 5);

		$url = $this->_Network->authenticationURL();

		$url = $url.'&'.http_build_query(['state' => $this->state]);

		return $this->_Redirect->to($url);
	}

	public function authenticationCallback($service, $input, $save_profile = true)
	{
		$this->_Network = $this->networkInstance($service);

		// check for state
		if (!isset($input['state']) or empty($input['state']))
		{
			throw new AuthenticationException('state', 'not found');
		}

		$state = $input['state'];
		$stateCacheKey = $this->stateCacheKey($state);

		// verify state existance
		if(!$this->_Cache->has($stateCacheKey))
		{
			throw new AuthenticationException('Timeout', 'Authentication has taken too long, please try again.');
		}

		$accessToken = $this->_Network->authenticationCallback($input);

		// add access token to cached data and extend to another 5 min
		$cachedStateData = $this->_Cache->get($this->stateCacheKey($state));
		$cachedStateData['access_token'] = $accessToken;
		$this->_Cache->put($stateCacheKey, $cachedStateData, 5);

		if (!$save_profile)
			return $this->_Network->profile();

		$this->saveUser($this->_Network->profile());
	}

	protected function saveUser($profile)
	{
		if ($profile and isset($profile->email))
		{

			if (!$userFound = $this->_users->findByEmail($profile->email))
			{
				// proceeding with a new user

				$user = $this->_users->fillAndSave((array) $profile);
				$socialAccount = $this->_socialAccounts->create($this->_Network->name, $profile->id, $user->id, $profile->access_token);

			} else {

				// user is already registered,
				// now we just check for the social account, if found we do nother
				// otherwise we add it

				if (!$socialAccountFound = $this->_socialAccounts->userAccount($userFound->id, $profile->id))
				{
					$socialAccount = $this->_socialAccounts->create($this->_Network->name, $profile->id, $userFound->id, $profile->access_token);
				}
			}


		} else {
			throw new SocialAccountException('Profile', 'Invalid type or structure');
		}
	}

	public function makeState()
	{
		return md5(uniqid(microtime(), true));
	}

	protected function networkInstance($service)
	{
		return new SocialNetwork($service, $this->_Config, $this->_HttpClient);
	}

	protected function stateCacheKey($state)
	{
		return $this->stateCacheKeyPrefix.$state;
	}
}