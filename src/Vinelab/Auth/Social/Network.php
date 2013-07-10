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
	protected $supported = ['facebook'];

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
		if (!in_array($name, $this->supported))
		{
			throw new SocialNetworkNotSupportedException($name);
		}

		$this->_Config = $config;
		$this->_HttpClient = $httpClient;

		$this->name = $name;

		// instantiate the service
		$class = sprintf('Vinelab\Auth\Social\Networks\%s', ucfirst($name));
		$this->_Service = new $class($this->_Config, $this->_HttpClient);
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