<?php namespace Vinelab\Auth\Cache;

use Vinelab\Auth\Contracts\StoreInterface;
use Illuminate\Cache\CacheManager as Cache;

class Store implements StoreInterface {

    /**
     * The duration to keep the
     * state storage available
     * in minutes.
     *
     * @var integer
     */
    protected $duration = 2;

    /**
     * Create a new store instance.
     *
     * @param Illuminate\Cache\CacheManager $cache
     */
    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Put the data in the cache
     * and return the generated
     * key.
     *
     * @param  string $key
     * @param  mixed $data
     * @return string
     */
    public function put($key, $data, $duration = null)
    {
        return $this->cache->put(
            $key,
            $data,
            ($duration) ?: $this->duration
        );
    }

    /**
     * Determines whether a key exists in the cache
     *
     * @param  string $key
     * @return boolean
     */
    public function has($key)
    {
        return $this->cache->has($key);
    }

    /**
     * Retrieve a value from the cache.
     *
     * @param  string $key
     * @return mixed
     */
    public function get($key)
    {
        return $this->cache->get($key);
    }
}
