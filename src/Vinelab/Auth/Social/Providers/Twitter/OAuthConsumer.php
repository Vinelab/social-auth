<?php namespace Vinelab\Auth\Social\Providers\Twitter;

class OAuthConsumer implements Contracts\OAuthConsumerInterface {

    /**
     * The consumer credentials.
     *
     * @var array
     */
    protected $credentials = array();

    public function make($key, $secret, $redirect_url = null)
    {
        $this->credentials['key'] = $key;
        $this->credentials['secret'] = $secret;
        $this->credentials['redirect_url'] = $redirect_url;

        return $this;
    }

    public function __get($attr)
    {
        return (isset($this->credentials[$attr])) ? $this->credentials[$attr] : null;
    }
}