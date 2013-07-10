<?php namespace Vinelab\Auth\Exception;

class SocialNetworkSettingsInvalidException extends \Exception {

	function __construct($message = null, $code = 4, Exception $previous = null)
	{
		parent::__construct($message, $code, $previous);
	}
}