<?php namespace Vinelab\Auth\Contracts;

interface SocialAuthenticationInterface {

	/**
	 * Preforms the actual authentication according to service
	 */
	public function authenticate();
}