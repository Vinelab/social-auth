<?php namespace Vinelab\Auth\Social\Networks;

use Codebird\Codebird;

use Vinelab\Auth\Exception\TwitterRequestTokenException;

class Twitter extends SocialNetwork {

    /**
     * Required settings. Must exist in configuration
     * @var array
     */
    protected $mandatory = [
        'consumer_key',
        'consumer_secret',
        'access_token',
        'access_token_secret',
        'callback_url'
    ];

    /**
     * Service Name
     * @var string
     */
    protected $name = 'twitter';

    /**
     * The acquired Access Token
     * @var Vinelab\Auth\AccessToken
     */
    public $accessToken;

    public function authenticationURL()
    {
        // get Codebird instance
        $twt = Codebird::getInstance();
        // get request token
        $requestTokenResponse = $twt->oauth_requestToken(['oauth_callback'=>'http://localhost:5000/callback.php']);
        // validate required parameters
        if (!isset($requestTokenResponse->oauth_token) or !isset($requestTokenResponse->oauth_token_secret))
        {
            throw new TwitterRequestTokenException($requestTokenResponse->error);
        }

        // set tokens
        $twt->setToken($requestTokenResponse->oauth_token, $requestTokenResponse->oauth_token_secret);

        return $twt->oauth_authorize();
    }

}