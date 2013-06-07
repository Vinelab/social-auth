<?php namespace Vinelab\Auth;

use Vinelab\Auth\Contracts\SocialAuthenticationInterface;
use Vinelab\Auth\Social\Network as SocialNetwork;

use Illuminate\Config\Repository as Config;
use Illuminate\Cache\CacheManager as Cache;
use Illuminate\Http\Response as Response;
use Illuminate\Routing\Redirector as Redirector;

Class Social implements SocialAuthenticationInterface {

	/**
	 * @var Vinelab\Auth\Social\Network
	 */
	public $network;

	/**
	 * @var Illuminate\Config\Repository
	 */
	protected $config;

	/**
	 * @var Illuminate\Cache\CacheManager
	 */
	protected $cache;

	/**
	 * @var Illuminate\Http\Response
	 */
	protected $response;

	/**
	 * @var  Illuminate\Routing\Redirector
	 */
	protected $redirect;

	/**
	 * Keeps track of the request
	 * @var string
	 */
	public $state;

	function __construct(
		$service,
		Config $config         = null,
		Cache $cache           = null,
		Response $response     = null,
		Redirector $redirector = null
	) {

		$this->config   = $config ?: new Config;
		$this->cache    = $cache ?: new Cache;
		$this->response = $response ?: new Response;
		$this->redirect = $redirector ?: new Redirector;

		$this->network  = new SocialNetwork($service, $this->config);
	}

	/**
	 *
	 * @param  string $apiKey
	 * @param  string $redirectURI
	 * @return  Illuminate\Routing\Redirector
	 */
	public function authenticate($apiKey, $redirectURI)
	{
		$this->state = $this->state ?: $this->makeState();
		$this->cache->put($this->state, ['api_key'=>$apiKey, 'redirect_uri'=>$redirectURI]);
		// TODO: verify developer account
		$url = $this->network->authenticationURL();

		$url.'&'.http_build_query(['state' => $this->state]);

		return $this->redirect->to($url);
	}

	public function makeState()
	{
		return md5(uniqid(microtime(), true));
	}
}