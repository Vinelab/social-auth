<?php namespace Vinelab\Auth\Social\Providers\Twitter;

use Vinelab\Http\Client as HttpClient;
use Vinelab\Auth\Social\Providers\Twitter\Contracts\OAuthTokenInterface;
use Vinelab\Auth\Social\Providers\Twitter\Contracts\OAuthConsumerInterface;
use Vinelab\Auth\Social\Providers\Twitter\Contracts\OAuthSignatureInterface;

class OAuth implements Contracts\OAuthInterface {

    /**
     * The request signature interface.
     *
     * @var Vinelab\Auth\Social\Providers\Twitter\Contracts\OAuthSignatureInterface
     */
    protected $signature;

    /**
     * Create a new OAuth instance.
     *
     * @param Vinelab\Auth\Social\Providers\Twitter\Contracts\OAuthTokenInterface     $token
     * @param Vinelab\Auth\Social\Providers\Twitter\Contracts\OAuthSignatureInterface $signature
     * @param Vinelab\Http\Client $http
     */
    public function __construct(OAuthTokenInterface $token,
                                OAuthSignatureInterface $signature,
                                HttpClient $http)
    {
        $this->token     = $token;
        $this->http      = $http;
        $this->signature = $signature;
    }

    /**
     * Requests Twitter for a request token.
     *
     * @param  array $settings
     * @param  Vinelab\Auth\Social\Providers\Twitter\Contracts\OAuthConsumerInterface $consumer
     * @param  Vinelab\Auth\Social\Providers\Twitter\Contracts\OAuthTokenInterface $token
     * @return Vinelab\Auth\Social\Providers\Twitter\Contracts\OAuthTokenInterface
     */
    public function getRequestToken($settings,
                                    OAuthConsumerInterface $consumer,
                                    OAuthTokenInterface $token)
    {
        $url = $settings['auth_api_url'] . $settings['request_token_uri'];
        $options = ['oauth_callback' => $settings['callback_url']];

        $headers = $this->headers($settings, 'POST', $url, $consumer, $token, $options);
        // build request
        $request = ['url' => $url,'headers' => $headers];

        return $this->token->makeRequestToken($this->http->post($request));
    }

    /**
     * Transforms a verifier (request) token
     * into an access token.
     *
     * @param  array $settings
     * @param  Vinelab\Auth\Social\Providers\Twitter\Contracts\OAuthConsumerInterface $consumer
     * @param  Vinelab\Auth\Social\Providers\Twitter\Contracts\OAuthTokenInterface $token
     * @return Vinelab\Auth\Social\Providers\Twitter\Contracts\OAuthTokenInterface
     */
    public function getAccessToken($settings,
                                    OAuthConsumerInterface $consumer,
                                    OAuthTokenInterface $token)
    {
        $url = $settings['auth_api_url'] . $settings['access_token_uri'];
        $params = ['oauth_verifier' => $token->verifier];
        $options = [
                'Content-Type: application/x-www-form-urlencoded',
                'Content-Length: ' . strlen(http_build_query($params))
        ];

        $headers = $this->headers($settings, 'POST', $url, $consumer, $token, $options);

        // build request
        $request = compact('url', 'headers', 'params');

        return $this->token->makeAccessToken($this->http->post($request));
    }

    public function headers($settings,
                            $httpverb,
                            $url,
                            OAuthConsumerInterface $consumer,
                            OAuthTokenInterface $token,
                            $options = [])
    {
        $headers = [
            'oauth_consumer_key'     => $consumer->key,
            'oauth_nonce'            => $this->generateNonce(),
            'oauth_signature_method' => $this->signature->method(),
            'oauth_timestamp'        => $this->generateTimestamp(),
            'oauth_version'          => "1.0"
        ];

        // check for a token to be added
        if ( ! is_null($token->key))
        {
            $headers['oauth_token'] = $token->key;
        }

        // merge options with headers
        $headers = array_merge($headers, $options);

        // add signature
        $headers['oauth_signature'] = $this->signature->get($consumer, $token, $httpverb, $url, $headers);

        uksort($headers, 'strcmp');

        // normalize
        $headers = $this->normalizeHeaders($headers);

        return [
            'Authorization: OAuth ' . $headers,
            'User-Agent: ' . $settings['user_agent']
        ];
    }

    /**
     * Transforms a parameters array into
     * a series of strings compatible to be
     * embedded in a request header.
     *
     * @param  array $params
     * @return string
     */
    public function normalizeHeaders($params)
    {
        $out = '';

        foreach($params as $key => $param)
        {
            $out .= $key . '="' . rawurlencode(trim($param)) . '",';
        }

        return rtrim($out, ',');
    }

     /**
    * Generates a request nonce.
    *
    * @return string
    */
    public function generateNonce()
    {
        return md5(microtime() . mt_rand());
    }

    /**
     * Generates a request timestamp.
     *
     * @return int
     */
    public function generateTimestamp()
    {
        return time();
    }
}