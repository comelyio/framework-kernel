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
    public $useCache;
    /** @var int */
    public $cacheTTL;
    /** @var null|string */
    public $key;
    /** @var null|string */
    public $instanceOf;
    /** @var null|callable */
    public $callback;

    /**
     * Query constructor.
     * @param string $key
     * @param string $instanceOf
     */
    public function __construct(string $key, string $instanceOf)
    {
        $this->useCache =   false;
        $this->cacheTTL =   0;
        $this->key  =   $key;
        $this->instanceOf   =   $instanceOf;
    }

    /**
     * @param int $ttl
     * @return Query
     */
    public function cache(int $ttl = 0) : self
    {
        $this->useCache =   true;
        $this->cacheTTL =   $ttl;
        return $this;
    }

    /**
     * @param callable $callback
     * @return Query
     */
    public function callback(callable $callback) : self
    {
        if(is_callable($callback)) {
            $this->callback =   $callback;
        }

        return $this;
    }

    /**
     * @return mixed|null
     * @throws \Comely\IO\Cache\CacheException
     * @throws \Comely\IO\DependencyInjection\Exception\RepositoryException
     */
    public function fetch()
    {
        return Memory::getInstance()->find($this);
    }
}