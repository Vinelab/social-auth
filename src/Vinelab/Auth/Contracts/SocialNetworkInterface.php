<?php namespace Vinelab\Auth\Contracts;

interface SocialNetworkInterface {

	public function authenticationURL();
	public function settings($setting = null);
	public function authenticationCallback($input);
	public function profile();

}