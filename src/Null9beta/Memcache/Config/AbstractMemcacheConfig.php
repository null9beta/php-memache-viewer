<?php

namespace Null9beta\Memcache\Config;

abstract class AbstractMemcacheConfig implements MemcacheConfigInterface
{

    /**
     * @var string
     */
    protected $persistentId;

    /**
     * @var ['server' => '', 'port' => '']
     */
    protected $servers;

    /**
     * @var ['value' => '', 'value' => '']
     */
    protected $options;

    /**
     * @return string
     */
    public function getPersistentId()
    {
        return $this->persistentId;
    }

    /**
     * @return array
     */
    public function getServers()
    {
        return $this->servers;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @return \Memcached
     * @throws \Exception
     */
    public function getMemcacheInstance()
    {
        $memcached = new \Memcached($this->getPersistentId());

        $this
            ->addServers($memcached)
            ->addOptions($memcached)
            ->validate($memcached)
        ;

        return $memcached;
    }

    /**
     * @param \Memcached $memcached
     * @return $this
     */
    private function addServers(\Memcached $memcached)
    {
        //only add servers if they have not been added
        //for persistent connections the same servers would get added over and over again
        if (empty($memcached->getServerList())) {

            if (!is_array($this->servers) || empty($this->servers)) {
                throw new \InvalidArgumentException('server list cannot be empty');
            }

            foreach ($this->servers as $server) {
                $weight = 0;

                if (isset($server[MemcacheConfigConstants::SERVER_WEIGHT])) {
                    $weight = $server[MemcacheConfigConstants::SERVER_WEIGHT];
                }

                $memcached->addServer(
                    $server[MemcacheConfigConstants::SERVER_HOST],
                    $server[MemcacheConfigConstants::SERVER_PORT],
                    $weight
                );
            }
        }

        return $this;
    }

    /**
     * @param \Memcached $memcached
     * @return $this
     */
    private function addOptions(\Memcached $memcached)
    {
        if (is_array($this->options) && !empty($this->options)) {

            foreach ($this->options as $option) {
                $memcached->setOption(
                    constant('\Memcached::' . $option[MemcacheConfigConstants::OPTION_NAME]),
                    $option[MemcacheConfigConstants::OPTION_VALUE]
                );
            }
            $memcached->setOptions($this->options);
        }

        return $this;
    }

    /**
     * @param \Memcached $memcached
     * @throws \Exception
     */
    private function validate(\Memcached $memcached)
    {
        $stats = $memcached->getStats();
        foreach ($this->servers as $server) {
            $host = $server[MemcacheConfigConstants::SERVER_HOST] . ':' . $server[MemcacheConfigConstants::SERVER_PORT];
            if ($stats[$host]['pid'] == -1) {
                throw new \Exception("Error establishing memcache connection to {$host}");
            }
        }
    }
}
