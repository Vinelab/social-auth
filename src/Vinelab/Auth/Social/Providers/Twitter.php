<?php namespace Vinelab\Auth\Social\Providers;

use Vinelab\Auth\Social\Provider;
use Vinelab\Http\Client as HttpClient;
use Illuminate\Config\Repository as Config;
use Illuminate\Routing\Redirector;

use Vinelab\Auth\Contracts\StoreInterface;
use Vinelab\Auth\Contracts\ProfileInterface;
use Vinelab\Auth\Social\Providers\Twitter\Contracts\OAuthInterface;
use Vinelab\Auth\Social\Providers\Twitter\Contracts\OAuthTokenInterface;
use Vinelab\Auth\Social\Providers\Twitter\Contracts\OAuthConsumerInterface;
use Vinelab\Auth\Social\Providers\Twitter\Contracts\OAuthSignatureInterface;

use Vinelab\Auth\Exceptions\TwitterProfileException;
use Vinelab\Auth\Exceptions\InvalidOAuthTokenException;
use Vinelab\Auth\Exceptions\AuthenticationCanceledException;

class Twitter extends Provider {

    protected $name = 'twitter';

    protected $mandatory = [
        'version',
        'consumer_key',
        'consumer_secret',
        'auth_api_url',
        'access_token_uri',
        'authentication_uri',
        'authorization_uri',
        'request_token_uri'
    ];

    /**
     * The data exchange format.
     *
     * @var string
     */
    protected $format = 'json';

    /**
     * The OAuth signature instance.
     *
     * @var Vinelab\Auth\Social\Providers\Twitter\Contracts\OAuthSignatureInterface
     */
    protected $signature;

    /**
     * The consumer instance.
     *
     * @var Vinelab\Auth\Social\Providers\Twitter\Contracts\OAuthConsumerInterface
     */
    protected $consumer;

    /**
     * The prefix of the store keys.
     *
     * @var string
     */
    protected $prefix = 'social:auth:twitter:token:';

    public function __construct(Config $config,
                                HttpClient $http,
                                Redirector $redirect,
                                StoreInterface $store,
                                ProfileInterface $profile,
                                OAuthSignatureInterface $signature,
                                OAuthConsumerInterface $consumer,
                                OAuthTokenInterface $token,
                                OAuthInterface $oauth)
    {
        parent::__construct($config);

        $this->http      = $http;
        $this->signature = $signature;
        $this->consumer  = $consumer;
        $this->oauth     = $oauth;
        $this->token     = $token;
        $this->redirect  = $redirect;
        $this->store     = $store;
        $this->profile   = $profile;

        $this->consumer->make(
            $this->settings('consumer_key'),
            $this->settings('consumer_secret')
        );
    }

    /**
     * Authenticate with Twitter.
     *
     * @return void
     */
    public function authenticate()
    {
        $request_token = $this->oauth->getRequestToken($this->settings, $this->consumer, $this->token);

        $auth_url = $this->settings('auth_api_url') . $this->settings('authentication_uri');
        $auth_url .= '?' . http_build_query(['oauth_token'=>$request_token->key]);

        return $this->redirect->to($auth_url);
    }

    /**
     * Handle the callback from a previous
     * authentication request.
     *
     * @param  array   $input
     * @return Vinelab\Auth\Contracts\ProfileInterface
     */
    public function callback($input)
    {
        if (isset($input['denied']))
        {
            throw new AuthenticationCanceledException;
        }

        if ( ! isset($input['oauth_token']) or ! isset($input['oauth_verifier']))
        {
            throw new InvalidOAuthTokenException('missing oauth_token or oauth_verifier');
        }

        $verifier_token = $this->token->verifier($input['oauth_token'], $input['oauth_verifier']);
        $access_token = $this->oauth->getAccessToken($this->settings, $this->consumer, $verifier_token);

        // cache the access token
        // $this->store->put($this->storeKey($access_token->user_id), $access_token, 30000);

        return $this->getProfile($access_token);
    }

    public function authenticateWithToken($token, $secret = null)
    {
        return $this->getProfile($this->token->make($token, $secret));
    }

    public function getProfile(OAuthTokenInterface $token)
    {
        $url = $this->api($this->settings('verify_credentials_uri'));
        $options = ['Content-Type: application/x-www-form-urlencoded'];

        $headers = $this->oauth->headers($this->settings, 'GET', $url, $this->consumer, $token);
        $headers = array_merge($headers, $options);

        $response = $this->http->get(compact('url', 'headers'));

        if ($response->statusCode() == 200)
        {
            return $this->profile->instantiate($response->json(), 'twitter');
        }

        throw new TwitterProfileException('invalid response');
    }

    public function api($uri)
    {
        return $this->settings('api_url') .'/'. $this->settings('version') . $uri .'.'. $this->format;
    }

    /**
     * Generates a key to be used in the store.
     *
     * @param  string $key
     * @return string
     */
    protected function storeKey($key)
    {
        return $this->prefix . $key;
    }
}