<?php namespace Vinelab\Auth\Social\Networks;

use Vinelab\Auth\Contracts\SocialNetworkInterface;
use Vinelab\Http\Client as HttpClient;

use Illuminate\Config\Repository as Config;

abstract class SocialNetwork implements SocialNetworkInterface {

	/**
	 * The social network name - used when loading the configuration
	 * @var string
	 */
	protected $name;

	/**
	 * Social network settings brought from the configuration
	 *
	 * @var array
	 */
	protected $settings;

	/**
	 * Instance
	 *
	 * @var Vinelab\Http\Client
	 */
	protected $_HttpClient;

	function __construct(Config $config, HttpClient $httpClient)
	{
		$this->settings = $config->get("auth::social.{$this->name}");
		$this->_HttpClient = $httpClient;
	}

	public function settings($setting = null)
	{
		return !is_null($setting) ? $this->settings[$setting] : $this->settings;
	}

}