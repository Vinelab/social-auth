<?php namespace Vinelab\Auth\Exception;

class TwitterRequestTokenException extends \Exception {

    function __construct($message, $code = 7, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}