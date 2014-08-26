<?php

namespace BitrixTool\Commands;

use BitrixTool\BitrixTool;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

class IBlockListTypesCommand extends Command {
    
    protected function configure() 
    {        
        parent::configure();
    }   

    public function getDescription() {
        return "List all iblock types";
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        \CModule::IncludeModule('iblock');
        $resTypes = \CIBlockType::GetList(array('id'=>'asc'));
        while ($arType = $resTypes->Fetch())
        {
            $output->writeln("<info>" . $arType['ID'] . "</info>");
        }
    }

}