<?php namespace Vinelab\Auth\Exception;

class SocialNetworkException extends \Exception {

	function __construct($error, $message, $code = 5, $previous = null)
	{
		parent::__construct(sprintf('Social Network Error: %s - %s', $error, $message), $code, $previous);
	}
}