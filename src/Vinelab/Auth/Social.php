<?php namespace Vinelab\Auth;

use Vinelab\Auth\Contracts\SocialAuthenticationInterface;
use Vinelab\Auth\Exception\ServiceNotSupportedException;

Class Social implements SocialAuthenticationInterface {

	protected $supported = ['facebook'];

	function __construct($service)
	{
		if (!in_array($service, $this->supported))
		{
			throw new ServiceNotSupportedException($service);
		}
	}

	public function authenticate()
	{
		var_dump($this->supported);
	}
}