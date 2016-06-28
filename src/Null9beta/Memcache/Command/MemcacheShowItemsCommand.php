<?php

namespace Null9beta\Memcache\Command;

use Null9beta\Memcache\MemcacheViewer;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MemcacheShowItemsCommand extends AbstractMemcacheCommand
{

    /**
     * setup command
     */
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('null9beta:memcache:items:show')
            ->setDescription('show items from memcache, possible filter keys')
            ->addOption(
                self::OPTION_FILTER,
                'f',
                InputOption::VALUE_OPTIONAL,
                'filter for keys, this is a regex string delimited with /',
                ''
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws \Exception
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $memcache = $this->getMemcachedInstance($input);
        $memcacheViewer = new MemcacheViewer($memcache);

        $filter = $input->getOption(self::OPTION_FILTER);
        $items = $memcacheViewer->find($filter, false);

        $table = new Table($output);
        $table->setHeaders(['Key', 'Value']);
        $table->setRows($items);
        $table->render();
    }
}
