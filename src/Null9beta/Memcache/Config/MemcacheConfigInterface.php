<?php

namespace Null9beta\Memcache\Config;

interface MemcacheConfigInterface
{
    /**
     * @return string
     */
    public function getPersistentId();


    /**
     * @return array
     */
    public function getServers();


    /**
     * @return array
     */
    public function getOptions();

    /**
     * @return \Memcached
     * @throws \Exception
     */
    public function getMemcacheInstance();
}
