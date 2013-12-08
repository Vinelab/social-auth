<?php namespace Vinelab\Auth\Exceptions;

class SocialAuthException extends \RuntimeException {}

class ProviderNotSupportedException extends SocialAuthException {}

class InvalidProviderSettingsException extends SocialAuthException {}

class AuthenticationException extends SocialAuthException {}

class InvalidProfileException extends SocialAuthException {}

class AccessTokenException extends SocialAuthException {}

class InvalidFacebookCodeException extends SocialAuthException {}