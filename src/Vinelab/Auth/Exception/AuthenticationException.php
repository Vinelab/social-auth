<?php namespace Vinelab\Auth\Exception;

Class AuthenticationException extends \Exception {

	function __construct($error, $message, $code = 2, Exception $previous = null)
	{
		parent::__construct(sprintf('Authentication failed: %s - %s', $error, $message), $code, $previous);
	}
}