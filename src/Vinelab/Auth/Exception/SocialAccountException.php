<?php namespace Vinelab\Auth\Exception;

class SocialAccountException extends \Exception {

	function __construct($error, $message, $code = 2, Exception $previous = null)
	{
		parent::__construct(sprintf('Social Account (Profile) Error: %s - %s', $error, $message), $code, $previous);
	}
}