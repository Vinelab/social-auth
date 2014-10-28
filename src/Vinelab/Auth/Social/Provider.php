<?php namespace Vinelab\Auth\Social;

use Vinelab\Auth\Contracts\ProviderInterface;
use Vinelab\Auth\Exceptions\InvalidProviderSettingsException;

use Illuminate\Config\Repository as Config;

abstract class Provider implements ProviderInterface {

    /**
     * The name of the provider,
     * i.e. facebook, twitter, etc.
     *
     * NOTE: This will be used to load the configuration
     *         so it must match the key.
     *
     * @var string
     */
    protected $name;

    /**
     * The mandatory settings
     * that should be existing.
     *
     * @var array
     */
    protected $mandatory;

    /**
     * Social network provider settings
     * brought from the configuration.
     *
     * @var array
     */
    protected $settings;

     /**
     * The configuration instance.
     *
     * @var Illuminate\Config\Repository
     */
    protected $config;

    public function __construct(Config $config)
    {
        $this->config = $config;

        $this->settings = $this->config->get('social.' . $this->name);

        $this->validateSettings($this->settings);
    }

    /**
     * Handles the authentication process.
     *
     * @return void
     */
    abstract public function authenticate();

    /**
     * Handles the provider's authentication
     * callback.
     *
     * @return Vinelab\Auth\Contracts\ProfileInterface
     */
    abstract public function callback($input);

    /**
     * Authenticate a user using an already
     * acquired access token.
     *
     * @param  string $token
     * @return Vinelab\Auth\Contracts\ProfileInterface
     */
    abstract public function authenticateWithToken($token, $secret = null);

    /**
     * Returns settings values,
     * when no setting is specified all the settings
     * are returned, otherwise it returns the specified
     * settings or null.
     *
     * @param  setting $setting
     * @return mixed
     */
    public function settings($setting = null)
    {
        if ( ! is_null($setting) )
        {
            return isset($this->settings[$setting]) ? $this->settings[$setting] : null;
        }

        return $this->settings;
    }

    /**
     * Determines whether the assigned
     * settings for a social network
     * are valid.
     *
     * @return mixed
     */
    protected function validateSettings($settings)
    {
        if ( ! is_array($settings))
        {
            throw new InvalidProviderSettingsException;
        }

        $intersection = array_intersect(array_keys($settings), $this->mandatory);

        return count($intersection) === count($this->mandatory);
    }
}
