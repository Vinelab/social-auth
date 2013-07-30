<?php namespace Vinelab\Auth\Social\Networks;

use Vinelab\Auth\Contracts\SocialNetworkInterface;
use Vinelab\Http\Client as HttpClient;

use Illuminate\Config\Repository as Config;

use Vinelab\Auth\Exception\SocialNetworkSettingsInvalidException;

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
	 * Holds the required settings
	 * that must exist
	 *
	 * @var array
	 */
	protected $mandatory;

	/**
	 * Instance
	 *
	 * @var Vinelab\Http\Client
	 */
	protected $_HttpClient;

	function __construct(Config $config, HttpClient $httpClient)
	{
		$this->settings = $config->get("social.{$this->name}");

		if (!$this->settingsConfirmed($this->settings)) throw new SocialNetworkSettingsInvalidException;

		$this->_HttpClient = $httpClient;
	}

	public function settings($setting = null)
	{
		return !is_null($setting) ? $this->settings[$setting] : $this->settings;
	}

	public function settingsConfirmed($settings)
	{
		$intersection = array_intersect(array_keys($settings), $this->mandatory);
		return count($intersection) === count($this->mandatory);
	}

}