<?php

namespace Null9beta\Memcache\Command;

use Null9beta\Memcache\Config\MemcacheConfigConstants;
use Null9beta\Memcache\MemcacheFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

class AbstractMemcacheCommand extends Command
{
    const OPTION_CONFIG = 'config';
    const OPTION_FILTER = 'filter';

    /**
     * setup command
     */
    protected function configure()
    {
        $this
            ->addOption(
                self::OPTION_CONFIG,
                'c',
                InputOption::VALUE_OPTIONAL,
                'optional: path to memcache config @see config/memcache.yml.dist',
                null
            )
            ->addOption(
                MemcacheConfigConstants::SERVERS,
                's',
                InputOption::VALUE_OPTIONAL,
                'optional: add server list -s localhost:11211,localhost:11212',
                null
            )
            ->addOption(
                MemcacheConfigConstants::OPTIONS,
                'o',
                InputOption::VALUE_OPTIONAL,
                'optional: add options list -o OPT_LIBKETAMA_COMPATIBLE=1,OPT_...=<value>',
                null
            )
            ->addOption(
                MemcacheConfigConstants::PERSISTENT_ID,
                'p',
                InputOption::VALUE_OPTIONAL,
                'optional: persist id for memcached object',
                null
            );
    }

    /**
     * @param InputInterface $input
     * @return \Memcached
     */
    protected function getMemcachedInstance(InputInterface $input)
    {
        $servers = $input->getOption(MemcacheConfigConstants::SERVERS);
        $config = $input->getOption(self::OPTION_CONFIG);

        //at least one must be set
        if (!$servers and !$config or !($servers xor $config)) {
            throw new \InvalidArgumentException('specify either a config file or add config via options');
        }

        if ($servers) {
            return MemcacheFactory::createInstanceFromConsoleInput($input);
        }

        if ($config) {
            return MemcacheFactory::createInstanceFromConfigFile($config);
        }
    }
}
