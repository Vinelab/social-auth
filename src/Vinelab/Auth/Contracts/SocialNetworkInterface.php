<?php namespace Vinelab\Auth\Contracts;

Interface SocialNetworkInterface {

	public function authenticationURL();
	public function settings($setting = null);
	public function authenticationCallback($input);
	public function profile();

}