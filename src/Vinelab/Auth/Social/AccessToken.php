<?php namespace Vinelab\Auth\Social;

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

	function __construct(array $data)
	{
		$this->expires = $data['expires'] ?: 0;
		$this->token   = $data['access_token'] ?: null;
	}
}