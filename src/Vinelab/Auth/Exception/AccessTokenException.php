<?php namespace Vinelab\Auth\Exception;

class AccessTokenException extends \Exception {

	function __construct($error, $message, $code = 3, Exception $previous = null)
	{
		parent::__construct(sprintf('Access Token Exception: %s - %s', $error,$message), $code, $previous);
	}
}