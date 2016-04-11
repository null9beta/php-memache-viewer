<?php

namespace Null9beta\Memcache\Command;


use Null9beta\Memcache\Config\MemcacheConfigConstants;
use Null9beta\Memcache\Config\MemcacheConfigFile;
use Null9beta\Memcache\Config\MemcacheConfigInput;
use Null9beta\Memcache\Config\MemcacheConfigInterface;
use Null9beta\Memcache\Config\MemcacheConfigSimple;
use Null9beta\Memcache\Memcache;
use Null9beta\Memcache\MemcacheConfig;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MemcacheShowItemsCommand extends Command
{
    const OPTION_CONFIG = 'config';
    const OPTION_FILTER = 'filter';

    /**
     * setup command
     */
    protected function configure()
    {
        $this
            ->setName('null9beta:memcache:items:show')
            ->setDescription('show items from memcache, possible filter keys')
            ->addOption(
                self::OPTION_FILTER,
                'f',
                InputOption::VALUE_OPTIONAL,
                'filter for keys, this is a regex string delimited with /',
                ''
            )
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
            )
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws \Exception
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $memcache = $this->getMemcached($input);

        $filter = $input->getOption(self::OPTION_FILTER);
        $items = $memcache->find($filter, false);

        $table = new Table($output);
        $table->setHeaders(['Key', 'Value']);
        $table->setRows($items);
        $table->render();
    }

    /**
     * @param InputInterface $input
     * @return Memcache
     */
    private function getMemcached(InputInterface $input)
    {
        $memcacheConfig = null;

        $servers = $input->getOption(MemcacheConfigConstants::SERVERS);
        $config = $input->getOption(self::OPTION_CONFIG);

        //at least one must be set
        if (!$servers and !$config or !($servers xor $config)) {
            throw new \InvalidArgumentException('specify either a config file or add config via options');
        }

        if ($servers) {
            $memcacheConfig = new MemcacheConfigInput($input);
        }

        if ($config) {
            $memcacheConfig = new MemcacheConfigFile($config);
        }

        return new Memcache($memcacheConfig->getMemcacheInstance());
    }
}
