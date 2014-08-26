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
        $this->setDefinition(array(
            new InputOption('bitrix', 'b', InputOption::VALUE_NONE, 'Show only core components (from bitrix folder)'),
            new InputOption('local', 'l', InputOption::VALUE_NONE, 'Show only local components (from local folder)'),
        ));

        parent::configure();
    }       
    
    public function getDescription() {
        return "Lists all installed components";
    }    

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $bitrix = BitrixTool::getInstance();

        $showCoreComponents = $input->getOption('bitrix');
        $showLocalComponents = $input->getOption('local');

        if (!$showCoreComponents && !$showLocalComponents)
            $showCoreComponents = $showLocalComponents = true;

        $components = array_merge(
            $showCoreComponents ? $bitrix->getComponents('bitrix') : array(),
            $showLocalComponents ? $bitrix->getComponents('local') : array()
        );

        foreach ($components as $component) {
            $output->writeln("<info>$component</info>");            
        }
    }

}