<?php
declare(strict_types=1);

namespace Comely\Framework\Kernel;

use Comely\IO\Cache\Cache;
use Comely\IO\Cache\CacheException;
use Comely\IO\DependencyInjection\Repository;

/**
 * Class Memory
 * @package Comely\Framework\Kernel
 */
class Memory
{
    /** @var self */
    private static $instance;

    /** @var null|Cache */
    private $cache;
    /** @var bool */
    private $cacheStatus;
    /** @var int */
    private $cacheTTL;
    /** @var int */
    private $cacheTempTTL;
    /** @var Repository */
    private $repo;

    /**
     * @return Memory
     */
    public static function getInstance() : self
    {
        if(!isset(self::$instance)) {
            self::$instance =   new self();
        }

        return self::$instance;
    }

    /**
     * Memory constructor.
     */
    private function __construct()
    {
        $this->repo =   new Repository();
        $this->cacheStatus  =   false;
        $this->cacheTTL =   0;
        $this->cacheTempTTL =   0;
    }

    /**
     * @param Cache $cache
     * @return Memory
     */
    public function setCache(Cache $cache) : self
    {
        $this->cache    =   $cache;
        $this->cacheStatus  =   true;
        return $this;
    }

    /**
     * Set default "time to live" value in seconds for cached objects
     * @param int $seconds
     * @return Memory
     */
    public function setCacheTTL(int $seconds = 0) : self
    {
        $this->cacheTTL =   $seconds;
        return $this;
    }

    /**
     * Enable/disable use of Cache Engine
     * Set temp. TTL (time to live) value for next caching object
     * @param bool $status
     * @param int $ttl
     * @return Memory
     */
    public function useCache(bool $status, int $ttl = 0) : self
    {
        $this->cacheStatus  =   $status;
        if($status) {
            $this->cacheTempTTL =   $ttl    >   0 ? $ttl : $this->cacheTTL;
        }

        return $this;
    }

    /**
     * Finds object in run-time memory, then cache, otherwise calls a supplied callback function which should
     * return object to be stored in memory
     * @param string $key
     * @param string $instanceOf
     * @param callable|null $notFound
     * @return mixed|null
     */
    public function find(string $key, string $instanceOf, callable $notFound = null)
    {
        // Check in runtime memory
        if($this->repo->has($key)) {
            $pull   =   $this->repo->pull($key);
            if(is_object($pull) &&  is_a($pull, $instanceOf)) {
                return $pull;
            }
        }

        // Check in cache
        if(isset($this->cache)) {
            if($this->cacheStatus   === true) {
                $cached = $this->cache->get($key);
                if(is_object($cached)   &&  is_a($cached, $instanceOf)) {
                    return $cached;
                }
            }
        }

        if(is_callable($notFound)) {
            $callBack   =   call_user_func($notFound);
            if(is_object($callBack)) {
                $this->set($key, $callBack);
                return $callBack;
            }
        }

        $this->cacheTempTTL =   0;
        return null;
    }

    /**
     * Sets object in run-time memory and cache engine
     * @param string $key
     * @param $object
     * @return bool
     */
    public function set(string $key, $object) : bool
    {
        if(!is_object($object)) {
            return false;
        }

        $this->repo->push($object, $key);
        if($this->cache) {
            if($this->cacheStatus   === true) {
                try {
                    $ttl    =   $this->cacheTempTTL > 0 ? $this->cacheTempTTL : $this->cacheTTL;
                    $this->cache->set($key, clone $object, $ttl);
                } catch (CacheException $e) {
                    trigger_error($e->getParsed(), E_USER_WARNING);
                }
            }
        }

        $this->cacheTempTTL =   0;
        return true;
    }
}