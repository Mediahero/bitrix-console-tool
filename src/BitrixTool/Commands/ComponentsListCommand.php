<?php

namespace BitrixTool\Commands;

use BitrixTool\BitrixTool;
use BitrixTool\FileSystemHelpers;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

class ComponentsListCommand extends Command
{
    protected function configure() 
    {      
        parent::configure();
    }       
    
    public function getDescription() {
        return "Lists all installed components";
    }    

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $bitrix = BitrixTool::getInstance();

        $components = array_merge(
            $bitrix->getComponents('bitrix'),
            $bitrix->getComponents('local')
        );

        foreach ($components as $component) {
            $output->writeln("<info>$component</info>");            
        }
    }

}