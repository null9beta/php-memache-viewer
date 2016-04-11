<?php

namespace Null9beta\Memcache\Config;

class MemcacheConfigSimple extends AbstractMemcacheConfig
{

    /**
     * MemcacheConfig constructor.
     * @param array $servers
     * @param array $options
     * @param bool $persistentId
     * @throws \InvalidArgumentException
     */
    public function __construct(array $servers, array $options = [], $persistentId = false)
    {
        $this->servers = $servers;
        $this->options = $options;
        $this->persistentId = $persistentId;
    }
}
