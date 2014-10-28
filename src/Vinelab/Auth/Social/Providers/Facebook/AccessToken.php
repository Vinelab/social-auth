<?php namespace Vinelab\Auth\Social\Providers\Facebook;

use Vinelab\Http\Response;
use Vinelab\Auth\Exceptions\AccessTokenException;
use Vinelab\Auth\Social\Providers\Facebook\Contracts\AccessTokenInterface;

class AccessToken implements AccessTokenInterface {

	/**
	 * Expiry date.
	 *
	 * @var string
	 */
	protected $expires;

	/**
	 * The actual access token.
	 *
	 * @var string
	 */
	protected $token;

	public function make(Response $response)
	{
		$data = $this->parseResponse($response);

		$this->expires    = $data['expires'] ?: 0;
		$this->token      = $data['access_token'];

		return $this;
	}

	public function makeFromToken($token)
	{
		if ( ! is_null($token) && ! empty($token))
		{
			$this->token = $token;

			return $this;
		}

		throw new AccessTokenException('invalid access token format');
	}

	public function parseToken($token)
	{
		if(strpos($token, 'access_token') !== false)
	    {
            parse_str($token, $params);

            return $params;
	    }

		throw new AccessTokenException('no access token received');
	}

	/**
	 * Parses an access token response.
	 *
	 * @param  Vinelab\Http\Response $response
	 * @return array
	 */
	public function parseResponse(Response $response)
	{
		$json = $response->json();

		/**
		 * The returned response must not be in JSON
		 * format, unless it is an error.
		 */
		if ( ! is_null($json))
		{
			if (isset($json->error))
			{
				$error = $json->error;
				throw new AccessTokenException($error->type . ': ' . $error->message, $error->code);
			}
		}

		$token = $response->content();

	    return $this->parseToken($token);
	}

	/**
	 * Returns the token value.
	 *
	 * @return string
	 */
	public function token()
	{
		return $this->token;
	}

	/**
	 * Returns the expiry date
	 * of the token.
	 *
	 * @return string
	 */
	public function expiry()
	{
		return $this->expires;
	}
}
