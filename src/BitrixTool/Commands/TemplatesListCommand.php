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
            new InputOption('full-path', 'f', InputOption::VALUE_NONE, 'output full paths to the templates folders'),
            new InputOption('show-location', 's', InputOption::VALUE_NONE, 'output templates names and its locations'),
            new InputOption('default', 'd', InputOption::VALUE_NONE, 'show default component templates only'),
            new InputOption('bitrix', 'b', InputOption::VALUE_NONE, 'show local templates only'),
            new InputOption('local', 'l', InputOption::VALUE_NONE, 'show local templates only'),
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
            return 1;
        }

        $showFullPath = $input->getOption('full-path');
        $showLocation = $input->getOption('show-location');

        if ($input->getOption('default'))
        {
            $templates = $component->getDefaultTemplates($showFullPath);
        }
        else if ($input->getOption('bitrix')) 
        {
            $templates = $component->getSiteTemplates('bitrix', $showFullPath, $showLocation);           
        }
        else if ($input->getOption('local')) 
        {
            $templates = $component->getSiteTemplates('local', $showFullPath, $showLocation);
        }
        else 
        {
            $templates = array_merge(
                $component->getDefaultTemplates($showFullPath),
                $component->getSiteTemplates('bitrix', $showFullPath, $showLocation),
                $component->getSiteTemplates('local', $showFullPath, $showLocation)
            );
        }

        foreach($templates as $template) {
            $output->writeln("<info>$template</info>");
        }
    }

}