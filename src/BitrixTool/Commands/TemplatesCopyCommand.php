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

class TemplatesCopyCommand extends Command
{
    protected function configure() 
    {
        $this->setDefinition(array(
            new InputArgument('source', InputArgument::REQUIRED, 'source template name'),
            new InputArgument('destination', InputArgument::OPTIONAL, 'destination template name'),
            new InputOption('site-template', 's', InputOption::VALUE_REQUIRED, 'destination site template name (where to copy source component template)'),
            new InputOption('component', 'c', InputOption::VALUE_REQUIRED, 'component name which template is copied'),
            new InputOption('force', 'f', InputOption::VALUE_NONE, 'overwrite destination template if it exists'),
        ));
      
        parent::configure();
    }       
    
    public function getDescription() {
        return "Copies component template to specified site template";
    }    

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $bitrix = BitrixTool::getInstance();

        // Исходный компонент.
        $componentName = $input->getOption('component');
        $component = new BitrixComponent($componentName);
        if (!$component->exists()) 
        {
            $output->writeln("<error>Component $componentName not found </error>");
            return 1;
        }
       
        // Копируемый шаблон.
        $srcTemplateName = $input->getArgument('source');      
        $srcTemplatePath = $component->getTemplatePath($srcTemplateName);
        if (!file_exists($srcTemplatePath)) 
        {
            $output->writeln("<error>Template $srcTemplatePath not found for component $componentName</error>");
            return 1;
        }

        // Шаблон сайта, в который будет скопирован шаблон.
        $siteTemplateName = $input->getOption('site-template');
        if (!$siteTemplateName) 
        {
            $output->writeln("<error>Site template name not specified!</error>");
            $output->writeln("<comment>Use --site-template option to specify site template where " .
                "to place component template.</comment>");
            return 1;
        }
        if (!$bitrix->siteTemplateExists($siteTemplateName)) 
        {
            $output->writeln("<error>Site template $siteTemplateName does not exist</error>");
            return 1;
        } 

        // Название с которым будет скопирован шаблон.
        $dstTemplateName = $input->getArgument('destination');
        if (!$dstTemplateName)
            $dstTemplateName = $srcTemplateName;

        // Путь к целевому шаблону.
        $dstTemplatePath = $component->getLocalTemplatePath($siteTemplateName, $dstTemplateName);
        if (file_exists($dstTemplatePath) && !$input->getOption('force')) 
        {
            $output->writeln("<error>Template $dstTemplateName already exsist.</error>");
            $output->writeln("<info>Use --force option to overwrite</info>");
            return 1;
        }

        $output->writeln("<comment>Copying component template:</comment>");
        $output->writeln("<comment>--> from:</comment> <info>$srcTemplatePath</info>");
        $output->writeln("<comment>--> to:</comment> <info>$dstTemplatePath</info>");

        FileSystemHelpers::CopyDir($srcTemplatePath, $dstTemplatePath);

        $output->writeln('<comment>Done!</comment>');

    }    
}