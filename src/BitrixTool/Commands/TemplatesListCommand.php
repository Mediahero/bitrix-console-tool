<?php

namespace BitrixTool\Commands;

use BitrixTool\BitrixTool;
use BitrixTool\BitrixComponent;
use BitrixTool\FileSystemHelpers;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

class TemplatesListCommand extends Command {
    
    protected function configure() 
    {
        // bxtool template list componentName
        $this->setDefinition(array(
            new InputArgument('component', InputArgument::REQUIRED, 'name of the component which templates we want to list'),
            //new InputOption('full-path', 'p', InputOption::VALUE_OPTIONAL, 'Use custom path for config'),
        ));
      
        parent::configure();
    }   

    public function getDescription()
    {
        return "Lists templates of specified components";
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $componentName = $input->getArgument('component');
        $component = new BitrixComponent($componentName);
        if (!$component->exists()) {
            $output->writeln("<error>Component $componentName not found </error>");
            exit(1);
        }

        $templates = array_merge(
            $component->getDefaultTemplates(),
            $component->getSiteTemplates('bitrix'),
            $component->getSiteTemplates('local')
        );

        foreach($templates as $template) {
            $output->writeln("<info>$template</info>");
        }
    }

}