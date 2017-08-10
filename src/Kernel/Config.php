<?php
declare(strict_types=1);

namespace Comely\Framework\Kernel;

use Comely\Framework\Kernel\Config\Prototype\App;
use Comely\Framework\Kernel\Config\Prototype\Site;
use Comely\Framework\KernelException;
use Comely\IO\Yaml\Yaml;

/**
 * Class Config
 * @package Comely\Framework\Kernel
 */
class Config
{
    /** @var App|null */
    public $app;
    /** @var PlainObject|null */
    public $databases;
    /** @var Site|null */
    public $site;

    /**
     * Config constructor.
     * @param string $configFile
     */
    public function __construct(string $configFile)
    {
        $parsed =   Yaml::getParser()->parse($configFile);
        $this->setProperties($parsed, $this);
    }

    /**
     * @param array $data
     * @param $object
     */
    private function setProperties(array $data, $object)
    {
        foreach($data as $key => $value) {
            $key    =   \Comely::camelCase($key);
            if(is_array($value)) {
                $object->$key =   new PlainObject();
                $this->setProperties($value, $object->$key);
                continue;
            } else {
                $object->$key =   $value;
            }
        }
    }

    /**
     * @param string $node
     * @return array
     * @throws KernelException
     */
    public function getNode(string $node) : array
    {
        if(!property_exists($this, $node)   ||  !$this->$node instanceof PlainObject) {
            throw KernelException::badConfigNode(__METHOD__, $node);
        }

        return json_decode(json_encode($this->$node), true);
    }
}