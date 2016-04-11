<?php

namespace Null9beta\Memcache;

interface MemcacheInterface
{
    /**
     * @param $key
     * @return mixed
     */
    public function get($key);

    /**
     * @param $key
     * @param $value
     * @param \DateTime|null $expiration
     * @return mixed
     */
    public function set($key, $value, \DateTime $expiration = null);

    /**
     * @param string $filterPattern
     * @param bool $asMap
     * @return mixed
     */
    public function find($filterPattern = '', $asMap = true);

}
