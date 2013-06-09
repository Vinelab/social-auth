<?php namespace Vinelab\Auth\Social;

use Vinelab\Auth\Exception\SocialNetworkNotSupportedException;

use Illuminate\Config\Repository as Config;

Class Network {

	protected $supported = ['facebook'];
	protected $name;
	public $service;

	function __construct($name, Config $config)
	{
		if (!in_array($name, $this->supported))
		{
			throw new SocialNetworkNotSupportedException($name);
		}

		$this->config = $config;

		$this->name = $name;

		// instantiate the service
		$class = sprintf('Vinelab\Auth\Social\Networks\%s', ucfirst($name));
		$this->service = new $class($this->config);
	}

	/**
	 * A way to proxy methods to the service
	 * @param  string $name
	 * @param  array $arguments
	 */
	function __call($name, $arguments)
	{
		if (!method_exists($this, $name))
		{
			return call_user_func_array([$this->service, $name], $arguments);
		}
	}

}