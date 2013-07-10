<?php namespace Vinelab\Auth\Contracts;

interface SocialNetworkInterface {

	/**
	 * Generate and return the authentication URL
	 *
	 * @return string
	 */
	public function authenticationURL();

	/**
	 * Return the settings or a single setting
	 *
	 * @param  string $setting
	 * @return mixed
	 */
	public function settings($setting = null);

	/**
	 * Handle the authentication callback when received
	 *
	 * @param  mixed $input
	 * @return mixed
	 */
	public function authenticationCallback($input);

	/**
	 * Return the authenticated user's profile
	 *
	 * @return mixed
	 */
	public function profile();

}