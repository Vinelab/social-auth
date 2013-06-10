<?php namespace Vinelab\Auth\Social\Networks;

use Vinelab\Auth\Contracts\SocialNetworkInterface;
use Vinelab\Auth\Exception\AuthenticationException;
use Vinelab\Auth\Social\AccessToken;

use Illuminate\Config\Repository as Config;

Class Facebook extends SocialNetwork {

	protected $name = 'facebook';

	public function authenticationURL()
	{
		$url = $this->settings['authentication_url'];

		$params = [

			'client_id'    => $this->settings['api_key'],
			'redirect_uri' => $this->settings['redirect_uri'],
			'scope'        => $this->settings['permissions']
		];

		return sprintf('%s?%s', $url, http_build_query($params));
	}

	/**
	 * Handles the callback returned from facebook.com. Usually triggered by the redirect URI
	 * @param  array $input [The request input params]
	 * @return Vinelab\Auth\AccessToken
	 */
	public function authenticationCallback($input)
	{
		if(isset($input['error']))
		{
			throw new AuthenticationException($input['error'], $input['error_description']);
		}

		if(!isset($input['code']) or empty($input))
		{
			throw new AuthenticationException('Input::code', 'Not found');
		}

		return $this->requestAccessToken($input['code']);
	}

	/**
	 * Fetches the access token of the authenticated account based on the returned code
	 * @param  string $code [Returned after authorizing with the service]
	 * @return string
	 */
	protected function requestAccessToken($code)
	{
		$request = [
			'url'    => $this->settings['token_url'],

			'params' => [
				'client_id'     => $this->settings['api_key'],
				'redirect_uri'  => $this->settings['redirect_uri'],
				'client_secret' => $this->settings['secret'],
				'code'          => $code,
				'format'		=> 'json'
			]
		];

		return new AccessToken($this->parseAccessTokenResponse($this->httpClient->get($request)));
	}

	/**
	 * Exchange short-lived with long-lived access token
	 * @param  string $accessToken
	 * @return string
	 */
	protected function exchangeAccessToken($accessToken)
	{
		$exchangeRequest = [

			'url' => $this->settings['token_url'],

			'params' => [
				'client_id'         => $this->settings['api_key'],
				'client_secret'     => $this->settings['secret'],
				'grant_type'        => 'fb_exchange_token',
				'fb_exchange_token' => $accessToken
			]
		];

		$exchangeResponse = $this->httpClient->get($exchangeRequest);
		return $this->parseAccessTokenResponse($exchangeResponse);
	}

	/**
	 * Parsed the access token response
	 * @return array
	 */
	protected function parseAccessTokenResponse(\Vinelab\Http\Response $response)
	{
		$jsonResponse = $response->json();

		// the returned response must not be in JSON format unless it is an errors
		if(!is_null($jsonResponse))
		{
			if(isset($jsonResponse->error))
			{
				$error = $jsonResponse->error;
				throw new AuthenticationException($error->type, $error->message, $error->code);
			}
		} else {

			$content = $response->content();

			if(strpos($content, 'access_token') !== false)
			{
				parse_str($content, $params);
				return $params;

			} else {
				throw new AuthenticationException('access token response', 'Access Token not found');
			}
		}
	}

}