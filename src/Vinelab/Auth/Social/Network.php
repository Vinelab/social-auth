<?php namespace Vinelab\Auth\Social;

use Vinelab\Auth\Exception\SocialNetworkNotSupportedException;
use Vinelab\Http\Client as HttpClient;

use Illuminate\Config\Repository as Config;

class Network {

	/**
	 * States the supported social networks
	 *
	 * @var array
	 */
	protected $supported = ['facebook', 'twitter'];

	/**
	 * Social Network Name
	 *
	 * @var string
	 */
	public $name;

	/**
	 * Instance
	 *
	 * @var Vinelab\Auth\Social\Netowks\{service name}
	 */
	protected $_Service;

	/**
	 * Instance
	 *
	 * @var Illuminate\Config\Repository
	 */
	protected $_Config;

	/**
	 * Instance
	 *
	 * @var Vinelab\Http\Client
	 */
	protected $_HttpClient;

	function __construct($name, Config $config, HttpCLient $httpClient)
	{
		if (!$this->supportedService($name)) throw new SocialNetworkNotSupportedException($name);

		$this->name = $name;
		$this->_Config = $config;
		$this->_HttpClient = $httpClient;
		$this->_Service = $this->instanceForService($name);
	}

	/**
	 * Getter method for @var $_Service
	 *
	 * @return Vinelab\Auth\Social\Networks\{service name}
	 */
	public function service()
	{
		return $this->_Service;
	}

	/**
	 * Verifies whether a service is supported or not
	 *
	 * @param  string $service
	 * @return boolean
	 */
	public function supportedService($service)
	{
		return in_array($service, $this->supported);
	}

	/**
	 * Instantiates a social network service
	 *
	 * @param  string $service
	 * @return Vinelab\Auth\Social\Netowks\{service}
	 */
	public function instanceForService($service)
	{
		$class = sprintf('Vinelab\Auth\Social\Networks\%s', ucfirst($service));
		return new $class($this->_Config, $this->_HttpClient);
	}

	/**
	 * A way to proxy methods to the service
	 *
	 * @param  string $name
	 * @param  array $arguments
	 */
	function __call($name, $arguments)
	{
		return call_user_func_array([$this->_Service, $name], $arguments);
	}

}