<?php

namespace Null9beta\Memcache;

use Null9beta\Memcache\Config\MemcacheConfigFile;
use Null9beta\Memcache\Config\MemcacheConfigInput;
use Null9beta\Memcache\Config\MemcacheConfigSimple;
use Symfony\Component\Console\Input\InputInterface;

class MemcacheFactory
{

    /**
     * @param array $servers
     * @param array $options
     * @param bool $persistentId
     * @return \Memcached
     */
    public static function createInstanceFromValues(array $servers, array $options = [], $persistentId = false)
    {
        $memcacheConfig = new MemcacheConfigSimple($servers, $options, $persistentId);

        return $memcacheConfig->getMemcacheInstance();
    }

    /**
     * @param $filePath
     * @return \Memcached
     */
    public static function createInstanceFromConfigFile($filePath)
    {
        $memcacheConfig = new MemcacheConfigFile($filePath);

        return $memcacheConfig->getMemcacheInstance();
    }

    /**
     * @param InputInterface $input
     * @return \Memcached
     */
    public static function createInstanceFromConsoleInput(InputInterface $input)
    {
        $memcacheConfig = new MemcacheConfigInput($input);

        return $memcacheConfig->getMemcacheInstance();
    }
}
