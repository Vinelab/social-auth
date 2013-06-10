<?php namespace Vinelab\Auth\Social\Networks;

use Vinelab\Auth\Contracts\SocialNetworkInterface;
use Vinelab\Http\Client as HttpClient;

use Illuminate\Config\Repository as Config;

Abstract Class SocialNetwork implements SocialNetworkInterface{

	/**
	 * The social network name - used when loading the configuration
	 * @var string
	 */
	protected $name;

	function __construct(Config $config, HttpClient $httpClient)
	{
		$this->settings = $config->get("auth::social.{$this->name}");
		$this->httpClient = $httpClient;
	}

	public function settings($setting = null)
	{
		return !is_null($setting) ? $this->settings[$setting] : $this->settings;
	}

}