<?php namespace Vinelab\Auth\Social;

/**
 * @author  Abed Halawi <abed.halawi@vinelab.com>
 */

use DateTime;

use Vinelab\Auth\Contracts\ProfileInterface;

use Illuminate\Config\Repository as Config;

class Profile implements ProfileInterface {

    /**
     * The configuration instance
     *
     * @var Illuminate\Config\Repository
     */
    protected $config;

    /**
     * The profile info;
     *
     * @var array
     */
    protected $profile;

    /**
     * The social network service.
     *
     * @var string
     */
    protected $provider;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Instantiates a profile by parsing
     * it and mapping it to commonly used
     * values.
     *
     * @param  stdClass | Hybrid_User_Profile $profile
     * @return Najem\Api\Social\Profile
     */
    public function instantiate($profile, $provider)
    {
        $this->provider = $provider;

        switch (get_class($profile))
        {
            case 'Hybrid_User_Profile':

                $this->profile = $this->parseHUP($profile);

            break;

            case 'stdClass':
            default:

                $this->profile = $this->parse($profile);

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
        return $this->profile;
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
     * Parses a Hybrid_User_Profile instance
     * into a common mapping b/w differnt provider
     * profiles
     *
     * @param  Hybrid_User_Profile $profile
     * @return array
     */
    public function parseHUP($profile)
    {
        // set the birthDay to null
        // this property doesn't exist
        // by default from HUP
        $profile->birthDay = isset($profile->birthDay) ? $profile->birthDay : null;

        if ($profile->birthYear and $profile->birthMonth and $profile->birthDay)
        {
            $profile->birthDay = new DateTime(
                sprintf('%s-%s-%s', $profile->birthYear, $profile->birthMonth, $profile->birthDay)
            );
        }

        return [

            'id'          => $profile->identifier,
            'link'        => $profile->profileURL,
            'website_url' => $profile->webSiteURL,
            'avatar'      => $profile->photoURL,
            'username'    => $profile->displayName,
            'about'       => $profile->description,
            'first_name'  => $profile->firstName,
            'last_name'   => $profile->lastName,
            'name'        => $profile->firstName . $profile->lastName,
            'gender'      => $profile->gender,
            'language'    => $profile->language,
            'age'         => $profile->age,
            'birthday'    => $profile->birthDay,
            'email'       => $profile->email,
            'verified'    => $profile->emailVerified,
            'phone'       => $profile->phone,
            'address'     => $profile->address,
            'region'      => $profile->region,
            'city'        => $profile->city,
            'zip'         => $profile->zip

        ];
    }

    /**
     * Parses a generic social profile,
     * currently used for Facebook
     *
     * @param  stdClass $raw_profile
     * @return array
     */
    public function parse($raw_profile)
    {
        $profile = (array) $raw_profile;
        $profile['avatar'] = $this->pluckAvatar($raw_profile);

        return $profile;
    }

    /**
     * Picks an avatar out of a profile.
     *
     * @param  stdClass | Hybrid_User_Profile $profile
     * @return string
     */
    protected function pluckAvatar($profile)
    {
        return isset($profile->avatar) ?
                        $profile->avatar :
                        sprintf($this->config->get('social.facebook.picture_url'), $profile->username);
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
        return isset($this->profile[$attr]) ? $this->profile[$attr] : null;
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
        $this->profile[$attr] = $val;
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
        return isset($this->profile[$attr]);
    }
}