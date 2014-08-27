<?php

namespace BitrixTool\Commands;

use BitrixTool\BitrixTool;
use BitrixTool\BitrixComponent;
use BitrixTool\FileSystemHelpers;
use BitrixTool\ClipboardStream;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\Console\Output\BufferedOutput;
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
            new InputOption('sort', 's', InputOption::VALUE_NONE, 'sort component parameters by name'),
            new InputOption('--xclip', '-x', InputOption::VALUE_NONE, 'sort component parameters by name'),            
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

        $buff = new BufferedOutput();
        $buff->writeln(sprintf('<?$APPLICATION->IncludeComponent("%s", "%s", array(', 
            $component->getFullName(), $templateName));

        $parameters = $component->getParameters()['PARAMETERS'];  
        if ($input->getOption('sort')) ksort($parameters);

        foreach ($parameters as $name => $settings) {
            // TODO: Добавить вывод комментария с именем параметра.
            $defaultValue = $settings['DEFAULT'] ? $settings['DEFAULT'] : '';
            $buff->writeln(sprintf('    "%s" => %s,', $name, var_export($defaultValue, true))); 
        }

        $buff->writeln('  ),');
        $buff->writeln('  false');
        $buff->writeln(');/*' . $componentName . "*/?>");

        $code = $buff->fetch();
        if ($input->getOption('xclip')) 
        {
            $clip = new ClipboardStream();
            $clip->write($code);
            $output->writeln('<comment>Generated code was copied to clipboard</comment>');
        }
        else 
        {
            $output->write("<info>$code</info>");
        }
    }

}
