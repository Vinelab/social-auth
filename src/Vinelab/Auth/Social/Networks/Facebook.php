<?php namespace Vinelab\Auth\Social\Networks;

use Vinelab\Auth\Contracts\SocialNetworkInterface;

use Illuminate\Config\Repository as Config;

Class Facebook extends SocialNetwork {

	protected $name = 'facebook';

	public function authenticationURL()
	{
		$url = $this->settings['authentication_url'];

		$params = [

			'client_id'    => $this->settings['api_key'],
			'redirect_uri' => $this->settings['redirect_uri'],
			'scope'        => $this->settings['permissions']
		];

		return sprintf('%s?%s', $url, http_build_query($params));
	}

}