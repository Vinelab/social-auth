<?php namespace Vinelab\Auth\Social;

use Vinelab\Auth\Exception\AccessTokenException;

Class AccessToken {

	/**
	 * Expiry date
	 * @var Timestamp
	 */
	public $expires;

	/**
	 * The actual access token
	 * @var string
	 */
	public $token;

	function __construct($data)
	{
		if(!is_array($data))
		{
			throw new AccessTokenException('Instantiation Error', 'passed argument is not an array');
		}

		$this->expires = $data['expires'] ?: 0;
		$this->token   = $data['access_token'] ?: null;
	}
}