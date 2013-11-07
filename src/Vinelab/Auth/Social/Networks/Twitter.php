<?php namespace Vinelab\Auth\Social\Networks;

use Hybrid_Auth as HybridAuth;
use Hybrid_Endpoint as HybridEndpoint;
use Hybrid_Error as HybridError;

use Vinelab\Auth\Exception\TwitterRequestTokenException;

class Twitter extends SocialNetwork {

    /**
     * Required settings. Must exist in configuration
     * @var array
     */
    protected $mandatory = ['base_url', 'key', 'secret', 'redirect_uri'];

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

    public function authenticate()
    {
        $auth = new HybridAuth($this->hybridConfiguration());

        if (!isset($_SERVER['QUERY_STRING']) or empty($_SERVER['QUERY_STRING']) or isset($_GET['denied']))
        {
            $auth->authenticate('twitter', ['hauth_return_to'=>$this->settings['redirect_uri']]);
        }

        HybridEndpoint::process();
    }

    public function authenticationCallback($input)
    {
        $auth = new HybridAuth($this->hybridConfiguration());
        $twitter = $auth->authenticate('twitter');
        $profile = $twitter->getUserProfile();
        $auth->logoutAllProviders();

        return $profile;
    }

    public function authorize($service)
    {
        HybridEndpoint::process();
    }

    public function authenticationURL()
    {
        return $this->settings['base_url'];
    }

    public function profile()
    {
        return $this->authenticate();
    }

    protected function hybridConfiguration()
    {
        return [
            'base_url' => $this->settings['base_url'],
            'providers' => [
                'Twitter' => [
                    'enabled' => true,
                    'keys' => ['key' => $this->settings['key'], 'secret' => $this->settings['secret']]
                ]
            ]
        ];
    }

}