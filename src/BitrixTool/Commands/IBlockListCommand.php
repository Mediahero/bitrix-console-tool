<?php

namespace BitrixTool\Commands;

use BitrixTool\BitrixTool;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

class IBlockListCommand extends Command {
    
    protected function configure() 
    {        
        $this->setDefinition(array(
            new InputArgument('type', InputArgument::REQUIRED, 'IBlock type ID'),
        ));

        parent::configure();
    }   

    public function getDescription() {
        return "List iblocks by its type";
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $type = $input->getArgument('type');

        \CModule::IncludeModule('iblock');

        $resIBlocks = \CIBlock::GetList(array('NAME'=>'asc'), array('TYPE' => $type));
        while ($arIBlock = $resIBlocks->Fetch())
        {
            $output->writeln("<info>" . $arIBlock['NAME'] . " (" . $arIBlock['ID'] . ") </info>");
        }
    }

}