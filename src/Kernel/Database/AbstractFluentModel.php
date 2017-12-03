<?php
declare(strict_types=1);

namespace Comely\Framework\Kernel\Database;

use Comely\Framework\Kernel;
use Comely\IO\Database\Exception\FluentException;
use Comely\IO\Database\Fluent;
use Comely\IO\Database\Schema;

/**
 * Class AbstractFluentModel
 * @package Comely\Framework\Kernel\Database
 */
abstract class AbstractFluentModel extends Fluent implements \Serializable
{
    /** @var Kernel */
    protected $app;
    /** @var array */
    protected $onCachePurge;

    /**
     * This is the callBack method for Fluent models
     * This method is also called when models extending this class are un-serialised
     * @param Kernel $app
     */
    final public function callBack(Kernel $app)
    {
        $this->setKernelInstance($app);
        $this->onLoad();
        $this->onCachePurge =   ["app","schemaTable"];
    }

    /**
     * @param Kernel $app
     */
    final public function callBackCached(Kernel $app)
    {
        $this->setKernelInstance($app);
        call_user_func([$this,"onLoadCached"]);
    }

    /**
     * @return mixed
     */
    abstract public function onLoad();

    /**
     * @param Kernel $app
     */
    final public function setKernelInstance(Kernel $app)
    {
        $this->app  =   $app;
    }

    /**
     * @return AbstractFluentModel
     */
    final public function removeKernelInstance() : self
    {
        unset($this->app);
        return $this;
    }

    /**
     * @return string
     * @throws FluentException
     */
    final public function serialize()
    {
        if(!method_exists($this, "onLoadCached")) {
            throw new FluentException(
                get_called_class(),
                'Model cannot be serialized for cache because it does not define required method "onLoadCached"'
            );
        }

        if(!method_exists($this, "onCache")) {
            throw new FluentException(
                get_called_class(),
                'Model cannot be serialized for cache because it does not define required method "onCache"'
            );
        }

        $this->onCache();
        $reflect    =   new \ReflectionClass($this);
        $props  =   [];
        /** @var $prop \ReflectionProperty */
        foreach($reflect->getProperties() as $prop) {
            if($prop->isPrivate()) {
                trigger_error(
                    sprintf(
                        'Model "%s" contains one or more private prop. which could not be cached',
                        get_class($this)
                    ),
                    E_USER_WARNING
                );
            }

            $key    =   $prop->getName();

            // Check purge list
            if(in_array($key, $this->onCachePurge)) {
                continue;
            } else {
                $value  =   $this->$key ?? null;
                if(!is_scalar($value)) {
                    $this->__cleanse($value);
                }
            }

            $props[$key]    =   $value ?? null;
        } unset($key, $value);

        $props["schemaTable"]   =   get_class($this->schemaTable);
        return serialize($props);
    }

    /**
     * @param $var
     * @throws FluentException
     */
    final public function __cleanse($var)
    {
        if(is_object($var)) {
            if($var instanceof AbstractFluentModel) {
                throw new FluentException(
                    get_called_class(),
                    'Model cannot be serialized for cache because one of its own or a child property contains an ' .
                    '"AbstractFluentModel" instance'
                );
            }

            $reflect    =   new \ReflectionClass($var);
            /** @var $prop \ReflectionProperty */
            foreach($reflect->getProperties() as $prop) {
                $key    =   $prop->getName();
                if(!is_scalar($reflect->$key)) {
                    $this->__cleanse($reflect->$key);
                }
            };
        } elseif(is_array($var)) {
            foreach($var as $key => $value) {
                if(!is_scalar($value)) {
                    $this->__cleanse($value);
                }
            }
        }
    }

    /**
     * @param string $serialized
     */
    final public function unserialize($serialized)
    {
        $props  =   unserialize($serialized);
        $reflect    =   new \ReflectionClass(get_class($this));
        /** @var $prop \ReflectionProperty */
        foreach($reflect->getProperties() as $prop) {
            $key    =   $prop->getName();
            if(!$prop->isPrivate()) {
                $this->$key =   $props[$key] ?? null;
            }
        }

        $this->schemaTable  =   Schema::getTable($this->schemaTable);
        call_user_func_array([$this, "callBackCached"], Schema::getCallbackArgs());
    }
}