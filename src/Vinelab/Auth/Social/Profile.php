<?php namespace Vinelab\Auth\Social;

/**
 * @author  Abed Halawi <abed.halawi@vinelab.com>
 */

use DateTime;

use Vinelab\Auth\Contracts\ProfileInterface;

class Profile implements ProfileInterface {

    /**
     * The profile info;
     *
     * @var array
     */
    protected $info;

    /**
     * The social network service.
     *
     * @var string
     */
    protected $provider;

    /**
     * Instantiates a profile by parsing
     * it and mapping it to commonly used
     * values across the different providers.
     *
     * @param object $profile
     * @param string $provider
     * @return Vinelab\Auth\Social\Profile
     */
    public function instantiate($profile, $provider)
    {
        $this->provider = $provider;

        switch ($provider)
        {
            case 'facebook':
                $this->info = $this->parseFb($profile);
            break;

            case 'twitter':
                $this->info = $this->parseTwt($profile);
            break;
        }

        return $this;
    }

    /**
     * Returns the profile.
     *
     * @return array
     */
    public function info()
    {
        return $this->info;
    }

    /**
     * Returns the provider.
     *
     * @return string
     */
    public function provider()
    {
        return $this->provider;
    }

    /**
     * Parses a Twitter profile
     *
     * @param  object $profile
     * @return array
     */
    public function parseTwt($profile)
    {
        // remove unecessary fields
        unset($profile->status);
        // stick avatar
        $profile->avatar = $profile->profile_image_url;
        return (array) $profile;
    }

    /**
     * Parse a Facebook profile.
     *
     * @param  object $raw_profile
     * @return array
     */
    public function parseFb($raw_profile)
    {
        $profile = $raw_profile;
        $profile->avatar = sprintf("http://graph.facebook.com/%s/picture", $profile->id);

        return (array) $profile;
    }

    /**
     * Implement the __get Magic method
     * to return profile attributes directly.
     *
     * @param  string $attr
     * @return mixed
     */
    public function __get($attr)
    {
        return isset($this->info[$attr]) ? $this->info[$attr] : null;
    }

    /**
     * Implement the __set Magic method
     * to open the ability to assign
     * values to the profile.
     *
     * @param string $attr
     * @param mixed $val
     */
    public function __set($attr, $val)
    {
        $this->info[$attr] = $val;
    }

    /**
     * Implement the __isset Magic method
     * to bypass issues when checking for
     * empty attributes of this class.
     *
     * @param  string  $attr
     * @return boolean
     */
    public function __isset($attr)
    {
        return isset($this->info[$attr]);
    }
}
