<?php namespace Vinelab\Auth\Exception;

class SocialNetworkNotSupportedException extends \Exception {

	function __construct($message, $code = 1, Exception $previous = null)
	{
		parent::__construct(sprintf('[%d] [%s] service not supported', $code, $message), $code, $previous);
	}
}