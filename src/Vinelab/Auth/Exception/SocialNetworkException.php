<?php namespace Vinelab\Auth\Exception;

Class SocialNetworkException extends \Exception {

	function __construct($error, $message, $code = 5, $previous = null)
	{
		parent::__construct(sprintf('Social Network Error: %s - %s', $error, $message), $code, $previous);
	}
}