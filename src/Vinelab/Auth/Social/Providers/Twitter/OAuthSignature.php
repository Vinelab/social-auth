<?php namespace Vinelab\Auth\Social\Providers\Twitter;

use Vinelab\Auth\Social\Providers\Twitter\Contracts\OAuthTokenInterface;
use Vinelab\Auth\Social\Providers\Twitter\Contracts\OAuthConsumerInterface;

class OAuthSignature implements Contracts\OAuthSignatureInterface {

    protected  $method = 'HMAC-SHA1';

    /**
     * Returns the signing method name used.
     *
     * @return string
     */
    public function method()
    {
        return $this->method;
    }

   /**
    * Get a signature for the request.
    *
    * @param  OAuthConsumerInterface $consumer
    * @param  OAuthTokenInterface    $token
    * @param  string                 $httpverb
    * @param  string                 $url
    * @param  array                 $params
    * @return string
    */
    public function get(OAuthConsumerInterface $consumer,
                        OAuthTokenInterface $token,
                        $httpverb,
                        $url,
                        $params = [])
    {
        uksort($params, 'strcmp');

        $base_url = $this->baseURL($httpverb, $url, $params);
        $key = $consumer->secret . '&' . $token->secret;

        return base64_encode(hash_hmac('sha1', $base_url, $key, true));
    }

   /**
    * Build the signature base url.
    *
    * @param  string $httpverb
    * @param  string $url
    * @param  array $params
    * @return string
    */
   public function baseURL($httpverb, $url, $params)
   {
        uksort($params, 'strcmp');

        return strtoupper($httpverb) . '&' .
                rawurlencode($url) . '&' .
                rawurlencode(http_build_query($params));
   }
};