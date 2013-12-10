<?php namespace Vinelab\Auth\Social;

use Vinelab\Http\Client as HttpClient;

use Illuminate\Routing\Redirector;
use Illuminate\Config\Repository as Config;

use Vinelab\Auth\Contracts\StoreInterface;
use Vinelab\Auth\Contracts\ProfileInterface;
use Vinelab\Auth\Contracts\ProvidersManagerInterface;
use Vinelab\Auth\Exceptions\ProviderNotSupportedException;
use Vinelab\Auth\Social\Providers\Twitter\Contracts\OAuthInterface;
use Vinelab\Auth\Social\Providers\Twitter\Contracts\OAuthTokenInterface;
use Vinelab\Auth\Social\Providers\Facebook\Contracts\AccessTokenInterface;
use Vinelab\Auth\Social\Providers\Twitter\Contracts\OAuthConsumerInterface;
use Vinelab\Auth\Social\Providers\Twitter\Contracts\OAuthSignatureInterface;

class ProvidersManager implements ProvidersManagerInterface {

    /**
     * Lists the supported provider
     * networks.
     *
     * @var array
     */
    protected $supported = ['facebook', 'twitter'];

    /**
     * The configuration instance.
     *
     * @var Illuminate\Config\Repository
     */
    protected $config;

    /**
     * Create a new ProvidersManager instance.
     *
     * @param Illuminate\Config\Repository               $config
     * @param Illuminate\Routing\Redirector           $redirector
     * @param Vinelab\Http\Client                       $http
     * @param Vinelab\Auth\Contracts\StoreInterface       $store
     * @param Vinelab\Auth\Contracts\ProfileInterface     $profile
     * @param Vinelab\Auth\Social\Providers\Facebook\Contracts\AccessTokenInterface $access_token
     */
    public function __construct(Config $config,
                                Redirector $redirector,
                                HttpClient $http,
                                StoreInterface $store,
                                ProfileInterface $profile,
                                AccessTokenInterface $access_token,
                                OAuthInterface $oauth,
                                OAuthTokenInterface $token,
                                OAuthSignatureInterface $signature,
                                OAuthConsumerInterface $consumer)
    {
        $this->config       = $config;
        $this->redirector   = $redirector;
        $this->http         = $http;
        $this->store        = $store;
        $this->profile      = $profile;
        $this->access_token = $access_token;
        $this->signature    = $signature;
        $this->consumer     = $consumer;
        $this->oauth        = $oauth;
        $this->token        = $token;
    }

    /**
     * Instantiates and returns a
     * social provider instance.
     *
     * @param  string $provider
     * @return Vinelab\Auth\Contracts\ProviderInterface
     */
    public function instantiate($provider)
    {
        if ( ! $this->supported($provider))
        {
            throw new ProviderNotSupportedException($provider);
        }

        $class = $this->providerClass($provider);

        switch($provider)
        {
            case 'facebook':
                return new $class($this->config,
                        $this->redirector,
                        $this->http,
                        $this->store,
                        $this->profile,
                        $this->access_token);
            break;

            case 'twitter':
                return new $class($this->config,
                                $this->http,
                                $this->redirector,
                                $this->store,
                                $this->profile,
                                $this->signature,
                                $this->consumer,
                                $this->token,
                                $this->oauth);
            break;
        }


    }

    /**
     * Determines whether an authentication
     * provider is supported.
     *
     * @param  string $provider
     * @return boolean
     */
    public function supported($provider)
    {
        return in_array($provider, $this->supported);
    }

    /**
     * Returns the class name of
     * a provider network.
     *
     * @param  string $provider
     * @return string
     */
    private function providerClass($provider)
    {
        return 'Vinelab\Auth\Social\Providers\\' . ucwords(strtolower($provider));
    }
}