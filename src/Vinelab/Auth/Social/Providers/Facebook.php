<?php

namespace Vinelab\Auth\Social\Providers;

use Vinelab\Http\Response;
use Vinelab\Auth\Social\Provider;
use Vinelab\Http\Client as HttpClient;
use Vinelab\Auth\Contracts\StoreInterface;
use Vinelab\Auth\Contracts\ProfileInterface;
use Vinelab\Auth\Exceptions\InvalidProfileException;
use Vinelab\Auth\Exceptions\AuthenticationException;
use Vinelab\Auth\Exceptions\InvalidFacebookCodeException;
use Vinelab\Auth\Social\Providers\Facebook\Contracts\AccessTokenInterface;
use Illuminate\Routing\Redirector;
use Illuminate\Config\Repository as Config;

class Facebook extends Provider
{
    protected $name = 'facebook';

    protected $mandatory = [
        'api_key',
        'secret',
        'redirect_uri',
        'permissions',
        'api_url',
        'authentication_url',
        'token_url',
        'profile_uri',
    ];

    /**
     * The redirector instance.
     *
     * @var Illuminate\Routing\Redirector
     */
    protected $redirect;

    /**
     * The prefix to use when storing
     * keys in the store.
     *
     * @var string
     */
    protected $prefix = 'social:auth:state:';

    /**
     * Create a new Facebook instance.
     *
     * @param Illuminate\Config\Repository            $config
     * @param Illuminate\Routing\Redirector           $redirect
     * @param Vinelab\Http\Client                     $http
     * @param Vinelab\Auth\Contracts\StoreInterface   $store
     * @param Vinelab\Auth\Contracts\ProfileInterface $profile
     */
    public function __construct(Config $config,
                                Redirector $redirect,
                                HttpClient $http,
                                StoreInterface $store,
                                ProfileInterface $profile,
                                AccessTokenInterface $access_token)
    {
        parent::__construct($config);

        $this->config = $config;
        $this->redirect = $redirect;
        $this->http = $http;
        $this->store = $store;
        $this->profile = $profile;
        $this->access_token = $access_token;
    }

    /**
     * Redirects to the corresponding
     * authentication URL.
     */
    public function authenticate()
    {
        $state = $this->makeState();

        $this->store->put($state, $this->settings);

        return $this->redirect->to($this->authURL($state));
    }

    /**
     * Handles the authentication callback
     * returned from the provider.
     *
     * @param mixed $input
     *
     * @return Vinelab\Auth\Contracts\ProfileInterface
     */
    public function callback($input)
    {
        if (isset($input['error'])) {
            throw new AuthenticationException($input['error'].':'.$input['error_description']);
        }

        if (!isset($input['code']) || empty($input['code'])) {
            throw new AuthenticationException('invalid code');
        }

        if (!isset($input['state']) || empty($input['state'])) {
            throw new AuthenticationException('invalid state');
        }

        if (!$this->store->has($input['state'])) {
            throw new AuthenticationException('state expired');
        }

        $access_token = $this->requestAccessToken($input['code']);

        return $this->requestProfile($access_token);
    }

    public function authenticateWithToken($token, $secret = null)
    {
        return $this->requestProfile($this->access_token->makeFromToken($token));
    }

    /**
     * Returns the authentication
     * URL to which we should be
     * redirecting to.
     *
     *
     * @return string
     */
    public function authURL($state)
    {
        $url = $this->settings['authentication_url'];

        $params = [
            'client_id' => $this->settings('api_key'),
            'redirect_uri' => $this->settings('redirect_uri'),
            'scope' => $this->settings('permissions'),
            'state' => $state,
        ];

        return $url.'?'.http_build_query($params);
    }

    /**
     * Requests an access token from Facebook
     * according to the returned code.
     *
     * @param string $code The code returned from Facebook after authentication
     *
     * @return Vinelab\Contracts\AccessTokenInterface
     */
    public function requestAccessToken($code)
    {
        if (!$code || empty($code)) {
            throw new InvalidFacebookCodeException();
        }

        $request = [
            'url' => $this->settings['token_url'],

            'params' => [
                'client_id' => $this->settings['api_key'],
                'redirect_uri' => $this->settings['redirect_uri'],
                'client_secret' => $this->settings['secret'],
                'code' => $code,
                'format' => 'json',
            ],
        ];

        return $this->access_token->make($this->http->get($request));
    }

    /**
     * Sends a request for a Facebook profile
     * using the acquired access token.
     *
     * @param Vinelab\Contracts\AccessTokenInterface $access_token
     *
     * @return Vinelab\Auth\Social\Profile
     */
    public function requestProfile(AccessTokenInterface $access_token)
    {
        $request = [
            'url' => $this->settings['api_url'].$this->settings['profile_uri'],
            'params' => ['access_token' => $access_token->token()],
        ];

        return $this->parseProfileResponse($this->http->get($request), $access_token);
    }

    /**
     * Parses a response coming from Facebook
     * containing a profile.
     *
     * @param Vinelab\Http\Response                  $response
     * @param Vinelab\Contracts\AccessTokenInterface $access_token
     *
     * @return Vinelab\Auth\Social\Profile
     */
    public function parseProfileResponse(Response $response, AccessTokenInterface $access_token)
    {
        $profile = $response->json();

        if (gettype($profile) !== 'object') {
            throw new InvalidProfileException();
        }

        if (isset($profile->error)) {
            $error = $profile->error;

            throw new InvalidProfileException($error->type.': '.$error->message, $error->code);
        }

        $profile->access_token = $access_token->token();

        return $this->profile->instantiate($profile, $this->name);
    }

    /**
     * Generates an returns a random string
     * to be used as authentication state.
     * Also sets the value of the $secret.
     *
     * @return string
     */
    protected function makeState()
    {
        return $this->secret = md5(uniqid(microtime(), true));
    }
}
