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

class GenerateIncludeCommand extends Command
{
    protected function configure() 
    {      
        $this->setDefinition(array(
            new InputArgument('template', InputArgument::OPTIONAL, 'component template name'),
            new InputOption('component', 'c', InputOption::VALUE_REQUIRED, 'name of a component'),
        ));

        parent::configure();
    }       
    
    public function getDescription() {
        return "Generates IncludeComponent snippet";
    }    

    protected function execute(InputInterface $input, OutputInterface $output)
    {   
        $componentName = $input->getOption('component');
        if (!$componentName)
        {
            $output->writeln('<error>Component name must be given</error>');
            $output->writeln('<comment>Use -c option to specify component name</comment>');
            exit(1);
        }

        $component = new BitrixComponent($componentName);
        if (!$component->exists())
        {
            $output->writeln("<error>Component $componentName not found </error>");
            exit(1);            
        }

        $templateName = $input->getArgument('template');
        if (!$templateName) $templateName = '';

        $output->writeln('<info>'.sprintf('<?$APPLICATION->IncludeComponent("%s", "%s", array(', $componentName, $templateName).'</info>');

        $parameters = $component->getParameters()['PARAMETERS'];        
        foreach ($parameters as $name => $settings) {
            // TODO: Добавить вывод комментария с именем параметра.
            $defaultValue = $settings['DEFAULT'] ? $settings['DEFAULT'] : '';
            $output->writeln('<info>'.sprintf('    "%s" => %s,', $name, var_export($defaultValue, true)).'</info>'); 
        }

        $output->writeln('<info>'.'  ),'.'</info>');
        $output->writeln('<info>'.'  false'.'</info>');
        $output->writeln('<info>'.');/*' . $componentName . "*/?>".'</info>');
    }

}