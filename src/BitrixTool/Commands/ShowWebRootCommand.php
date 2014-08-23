<?php

namespace BitrixTool\Commands;

use BitrixTool\BitrixTool;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

class ShowWebRootCommand extends Command {
    
    protected function configure() 
    {
        $this
            ->setName("web-root")
            ->setDescription("Displays full path to web root of Bitrix site");
        
        parent::configure();
    }   

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $siteRoot = BitrixTool::getInstance()->getSiteRoot();
        $output->writeln("<info>$siteRoot</info>");
    }

}