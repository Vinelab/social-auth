<?php namespace Vinelab\Auth\Social\Networks;

use Vinelab\Auth\Contracts\SocialNetworkInterface;

use Illuminate\Config\Repository as Config;

Abstract Class SocialNetwork implements SocialNetworkInterface{

	/**
	 * The social network name - used when loading the configuration
	 * @var string
	 */
	protected $name;

	function __construct(Config $config)
	{
		$this->settings = $config->get("auth::social.{$this->name}");
	}

	public function settings($setting = null)
	{
		return !is_null($setting) ? $this->settings[$setting] : $this->settings;
	}

}