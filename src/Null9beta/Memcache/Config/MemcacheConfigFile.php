<?php

namespace Null9beta\Memcache\Config;

use Symfony\Component\Yaml\Yaml;

class MemcacheConfigFile extends AbstractMemcacheConfig
{

    /**
     * MemcacheConfigFile constructor.
     * @param $filePath
     */
    public function __construct($filePath)
    {
        $config = $this->loadConfigFromFile($filePath);

        $this->servers = isset($config[MemcacheConfigConstants::SERVERS]) ?
            $config[MemcacheConfigConstants::SERVERS] : null;
        $this->options = isset($config[MemcacheConfigConstants::OPTIONS]) ?
            $config[MemcacheConfigConstants::OPTIONS] : null;
        $this->persistentId = isset($config[MemcacheConfigConstants::PERSISTENT_ID]) ?
            $config[MemcacheConfigConstants::PERSISTENT_ID] : null;
    }

    /**
     * @param $filePath
     * @return mixed
     */
    private function loadConfigFromFile($filePath)
    {
        if (!file_exists($filePath)) {
            throw new \InvalidArgumentException("Error loading config file. {$filePath} does not exist.");
        }

        return Yaml::parse(
            file_get_contents($filePath)
        );
    }
}
