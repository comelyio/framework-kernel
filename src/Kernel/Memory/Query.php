<?php
declare(strict_types=1);

namespace Comely\Framework\Kernel\Memory;
use Comely\Framework\Kernel\Memory;

/**
 * Class Query
 * @package Comely\Framework\Kernel\Memory
 */
class Query
{
    /** @var bool */
    public $_useCache;
    /** @var int */
    public $_cacheTTL;
    /** @var null|string */
    public $key;
    /** @var null|string */
    public $instanceOf;
    /** @var null|callable */
    public $_callback;

    /**
     * Query constructor.
     * @param string $key
     * @param string $instanceOf
     */
    public function __construct(string $key, string $instanceOf)
    {
        $this->_useCache    =   false;
        $this->_cacheTTL    =   0;
        $this->key  =   $key;
        $this->instanceOf   =   $instanceOf;
    }

    /**
     * @param int $ttl
     * @return Query
     */
    public function cache(int $ttl = 0) : self
    {
        $this->_useCache    =   true;
        $this->_cacheTTL    =   $ttl;
        return $this;
    }

    /**
     * @param int $ttl
     * @return Query
     */
    public function useCache(int $ttl = 0) : self
    {
        $this->cache($ttl);
        return $this;
    }

    /**
     * @param callable $callback
     * @return Query
     */
    public function callback(callable $callback) : self
    {
        if(is_callable($callback)) {
            $this->_callback    =   $callback;
        }

        return $this;
    }

    /**
     * @param callable|null $callback
     * @return mixed|null
     * @throws \Comely\IO\Cache\CacheException
     * @throws \Comely\IO\DependencyInjection\Exception\RepositoryException
     */
    public function fetch(callable $callback = null)
    {
        if($callback) {
            $this->callback($callback);
        }

        return Memory::getInstance()->find($this);
    }
}