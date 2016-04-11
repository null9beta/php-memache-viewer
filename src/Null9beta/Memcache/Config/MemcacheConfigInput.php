<?php

namespace Null9beta\Memcache\Config;

use Symfony\Component\Console\Input\InputInterface;

class MemcacheConfigInput extends AbstractMemcacheConfig
{

    /**
     * MemcacheConfigInput constructor.
     * @param InputInterface $input
     */
    public function __construct(InputInterface $input)
    {
        $this->servers = $this->extractServer($input->getOption(MemcacheConfigConstants::SERVERS));
        $this->options = $this->extractOptions($input->getOption(MemcacheConfigConstants::OPTIONS));
        $this->persistentId = $input->getOption(MemcacheConfigConstants::PERSISTENT_ID);
    }

    /**
     * @param $server
     * @return array
     */
    private function extractServer($server)
    {
        if (!$server) {
            throw new \InvalidArgumentException('server list cannot be empty');
        }

        $serverList = explode(',', $server);

        return array_map(function($server) {
            $serverParts = explode(':', $server);
            return [
                MemcacheConfigConstants::SERVER_HOST => trim($serverParts[0]),
                MemcacheConfigConstants::SERVER_PORT => trim($serverParts[1]),
            ];
        }, $serverList);
    }

    /**
     * @param $options
     * @return array
     */
    private function extractOptions($options)
    {
        if (!$options) {
            return [];
        }

        $parsedOptions = [];
        $optionsList = explode(',', $options);
        foreach ($optionsList as $option) {
            $optionParts = explode('=', $option);
            $parsedOptions[] = [
                MemcacheConfigConstants::OPTION_NAME => trim($optionParts[0]),
                MemcacheConfigConstants::OPTION_VALUE => trim($optionParts[1]),
            ];
        }

        return $parsedOptions;
    }
}
