<?php namespace Vinelab\Auth\Social\Providers\Twitter;

/**
 * @author Abed Halawi <abed.halawi@vinelab.com>
 */

use Vinelab\Http\Response;
use Vinelab\Auth\Exceptions\InvalidOAuthTokenException;

class OAuthToken implements Contracts\OAuthTokenInterface {

    /**
     * The token credentials holder.
     *
     * @var array
     */
    protected $credentials = [];

    /**
     * Creates and returns an OAuthToken instance
     * setting the token key and secret.
     *
     * @param  string $key
     * @param  string $secret
     * @return Vinelab\Auth\Social\Providers\Twitter\OAuthToken
     */
    public function make($key, $secret)
    {
        $this->credentials['key']    = $key;
        $this->credentials['secret'] = $secret;

        return $this;
    }

    public function verifier($key, $verifier)
    {
        $this->credentials['key'] = $key;
        $this->credentials['verifier'] = $verifier;

        return $this;
    }

    /**
     * Creates and returns an OAuthToken instance
     * parsing values from the Twitter API Response.
     *
     * @param  Vinelab\Http\Response $response
     * @return Vinelab\Auth\Social\Providers\Twitter\OAuthToken
     */
    public function makeRequestToken(Response $response)
    {
        parse_str($response->content(), $params);

        $this->validateRequestTokenResponse($params);

        $this->credentials['key'] = $params['oauth_token'];
        $this->credentials['secret'] = $params['oauth_token_secret'];
        $this->credentials['callback_confirmed'] = (isset($params['oauth_callback_confirmed'])) ?
                                                        (boolean) $params['oauth_callback_confirmed'] : null;

        return $this;
    }

    /**
     * Parse an access token response and assign
     * credential values.
     *
     * @param  Vinelab\Http\Response $response
     * @return Vinelab\Auth\Social\Providers\Twitter\OAuthToken
     */
    public function makeAccessToken(Response $response)
    {
        parse_str($response->content(), $params);

        $this->validateAccessTokenResponse($params);

        $this->credentials['key']         = $params['oauth_token'];
        $this->credentials['secret']      = $params['oauth_token_secret'];
        $this->credentials['user_id']     = $params['user_id'];
        $this->credentials['screen_name'] = $params['screen_name'];

        return $this;
    }

    /**
     * Validates the received data for a request token.
     *
     * @param  array $params
     * @return void
     */
    public function validateRequestTokenResponse($params)
    {
        if ( ! isset($params['oauth_token']) ||
             ! isset($params['oauth_token_secret']) ||
            empty($params['oauth_token']) ||
            empty($params['oauth_token_secret']))
        {
            throw new InvalidOAuthTokenException('request token');
        }

        return true;
    }

    /**
     * Validates the received data for an access token.
     *
     * @param  array $params
     * @return void
     */
    public function validateAccessTokenResponse($params)
    {
        if ( ! isset($params['oauth_token']) ||
             ! isset($params['oauth_token_secret']) ||
            empty($params['oauth_token']) ||
            empty($params['oauth_token_secret']))
        {
            throw new InvalidOAuthTokenException('access token');
        }

        return true;
    }

    public function __get($attr)
    {
        return (isset($this->credentials[$attr])) ? $this->credentials[$attr] : null;
    }
}
