<?php namespace Vinelab\Auth;

use Illuminate\Config\Repository as Config;
use Illuminate\Routing\Redirector;

use Vinelab\Http\Client as HttpClient;
use Vinelab\Auth\Social\ProvidersManager;
use Vinelab\Auth\Contracts\ProvidersManagerInterface;

class Auth {

    /**
     * The configuration instance.
     *
     * @var Illuminate\Config\Repository
     */
    protected $config;

    /**
     * The cache instance.
     *
     * @var Illuminate\Cache\CacheManager
     */
    protected $cache;

    /**
     * The redirector instance.
     *
     * @var Illuminate\Routing\Redirector
     */
    protected $redirect;

    /**
     * The HTTP client instance.
     *
     * @var Vinelab\Http\Client
     */
    protected $http;

    /**
     * The network provider instance.
     *
     * @var Vinelab\Auth\Social\Provider
     */
    protected $provider;

    public function __construct(Config $config,
                                Redirector $redirector,
                                HttpClient $http,
                                ProvidersManagerInterface $manager)
    {
        $this->config   = $config;
        $this->redirect = $redirector;
        $this->http     = $http;
        $this->manager  = $manager;
    }

    public function authenticate($provider)
    {
        $this->provider = $this->providerInstance($provider);

        return $this->provider->authenticate();
    }

    public function profile($provider, $input)
    {
        $this->provider = $this->providerInstance($provider);

        return $this->provider->callback($input);
    }

    public function authenticateWithToken($provider, $token)
    {
        $this->provider = $this->providerInstance($provider);

        return $this->provider->authenticateWithToken($token);
    }

    protected function providerInstance($provider)
    {
        return $this->manager->instantiate($provider, $this->http, $this->redirect);
    }
}