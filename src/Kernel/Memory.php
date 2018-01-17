<?php
declare(strict_types=1);

namespace Comely\Framework\Kernel;

use Comely\Framework\Kernel\Memory\Query;
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
    }

    /**
     * @param Cache $cache
     * @return Memory
     */
    public function setCache(Cache $cache) : self
    {
        $this->cache    =   $cache;
        return $this;
    }

    /**
     * @param string $key
     * @param string $instanceOf
     * @return Query
     */
    public static function Query(string $key, string $instanceOf) : Query
    {
        return new Query($key, $instanceOf);
    }

    /**
     * @param Query $query
     * @return mixed|null
     * @throws CacheException
     * @throws \Comely\IO\DependencyInjection\Exception\RepositoryException
     */
    public function find(Query $query)
    {
        // Check in runtime memory
        if($this->repo->has($query->key)) {
            $pull   =   $this->repo->pull($query->key);
            if(is_object($pull) &&  is_a($pull, $query->instanceOf)) {
                return $pull;
            }
        }

        // Check in cache
        if(isset($this->cache)) {
            if($query->_useCache    === true) {
                $cached =   $this->cache->get($query->key);
                if(is_object($cached)   &&  is_a($cached, $query->instanceOf)) {
                    $this->repo->push($cached, $query->key); // Save in runtime memory
                    return $cached;
                }
            }
        }

        // Not found so far, proceed to callback
        if(is_callable($query->_callback)) {
            $callBack   =   call_user_func($query->_callback);
            if(is_object($callBack)) {
                $this->set($query->key, $callBack, $query->_useCache, $query->_cacheTTL);
                return $callBack;
            }
        }

        return null;
    }

    /**
     * @param string $key
     * @param $object
     * @param bool $useCache
     * @param int $cacheTTL
     * @return bool
     * @throws \Comely\IO\DependencyInjection\Exception\RepositoryException
     */
    public function set(string $key, $object, bool $useCache = false, int $cacheTTL = 0) : bool
    {
        if(!is_object($object)) {
            return false; // Memory component doesn't store non-objects
        }

        // Save in runtime memory
        $this->repo->push($object, $key);

        // Save in cache?
        if($this->cache) {
            if($useCache) {
                try {
                    $this->cache->set($key, clone $object, $cacheTTL);
                } catch (CacheException $e) {
                    trigger_error($e->getParsed(), E_USER_WARNING);
                }
            }
        }

        return true;
    }
}