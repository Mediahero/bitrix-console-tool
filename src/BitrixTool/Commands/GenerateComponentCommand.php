<?php

namespace BitrixTool\Commands;

use BitrixTool\BitrixTool;
use BitrixTool\BitrixComponent;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

class GenerateComponentCommand extends Command {
    
    protected function configure() 
    {        
        $this->setDefinition(array(
            new InputOption('bitrix', 'b', InputOption::VALUE_NONE, 'Create component in core Bitrix folder'),
            new InputArgument('name', InputArgument::REQUIRED, 'Component name'),
        ));

        parent::configure();
    }   

    public function getDescription() {
        return "Creates new empty component";
    } 

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $component = new BitrixComponent($input->getArgument('name'));
        if ($component->exists())
        {
            $output->writeln("<error>Component $componentName already exists!</error>");
            $output->writeln("<info>" . $component->getComponentDir() . "</info>");
            return 1;             
        }        

        $namespace = $component->getNamespace();
        $componentName = $component->getName();

        $siteRoot = BitrixTool::getInstance()->getSiteRoot();
        if (!$siteRoot)
        {
            $output->writeln("<error>Not in a Bitrix web root!</error>");
            return 1;
        }

        $bitrixFolder = $input->getOption('bitrix') ? 'btrix' : 'local';

        $componentFolder = "$siteRoot/$bitrixFolder/components/$namespace/$componentName";
        $componentTemplateFolder = "$componentFolder/template/.default";

        $componentFiles = array(
            'description' => "$componentFolder/.description.php",
            'parameters' => "$componentFolder/.parameters.php",
            'component' => "$componentFolder/component.php",
            'template' => "$componentTemplateFolder/template.php",
        );

        // Создаем структуру каталогов.
        if (!file_exists($componentTemplateFolder) && !$this->createFolder($componentTemplateFolder))
        {
            return 1;
        }

        file_put_contents($componentFiles['component'], COMPONENT_TEMPLATE_COMPONENT);
        file_put_contents($componentFiles['parameters'], COMPONENT_TEMPLATE_PARAMETERS);
        file_put_contents($componentFiles['description'], COMPONENT_TEMPLATE_DESCRIPTION);
        file_put_contents($componentFiles['template'], COMPONENT_TEMPLATE_TEMPLATE);
    }

    private function createFolder($path) 
    {
        if (!@mkdir($path, 0777, true))
        {
            $error = error_get_last();
            $output->writeln("<error>Failed to create directory: </error>");
            $output->writeln("<error>$path</error>");
            $output->writeln("<error>" . $error['message'] . "!</error>");
            return false;
        }

        return true;
    }

}

define('COMPONENT_TEMPLATE_COMPONENT', <<<EO_COMPONENT_TEMPLATE_COMPONENT
<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(\$_REQUEST["AJAX_CALL"] == "Y" || \$_REQUEST["is_ajax_post"] == "Y")
{
    \$APPLICATION->RestartBuffer();
}

if(!CModule::IncludeModule("iblock")) 
    return;

\$arResult = array();

// Place your component code here

if(\$_REQUEST["AJAX_CALL"] == "Y" || \$_REQUEST["is_ajax_post"] == "Y")
{
    echo json_encode(\$arResult);
    exit(0);
}
else 
{
    \$this->IncludeComponentTemplate();
}
EO_COMPONENT_TEMPLATE_COMPONENT
);

define('COMPONENT_TEMPLATE_PARAMETERS', <<<EO_COMPONENT_TEMPLATE_PARAMETERS
<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock")) 
    return;

\$arTypesEx = CIBlockParameters::GetIBlockTypes(array("-"=>" "));

\$arIBlocks = array();
\$res = CIBlock::GetList(
    array("SORT"=>"ASC"), 
    array(
        "SITE_ID"=>\$_REQUEST["site"], 
        "TYPE" => (\$arCurrentValues["IBLOCK_TYPE"] != "-" ? \$arCurrentValues["IBLOCK_TYPE"] : "")
    )
);

while(\$ib = \$res->Fetch())
    \$arIBlocks[\$ib["ID"]] = \$ib["NAME"];

\$arComponentParameters = array(
    'PARAMETERS' => array(

        "IBLOCK_TYPE" => Array(
            "PARENT" => "BASE",
            "NAME" => "Тип инф. блока для списка городов",
            "TYPE" => "LIST",
            "VALUES" => \$arTypesEx,
            "REFRESH" => "Y",
        ),

        "IBLOCK_ID" => array(
            "PARENT" => "BASE",
            "NAME" => "Код инф. блока для списка городов",
            "TYPE" => "LIST",
            "VALUES" => \$arIBlocks,
            "REFRESH" => "Y",
        ),

        /*  
        "MY_PARAM" => array(
            "PARENT" => "GENERAL",
            "NAME" => "Мой параметр",
            "TYPE" => "STRING",        
        ), 
        */
    ),
    'CACHE_TIME'  =>  array('DEFAULT'=>3600),
);
EO_COMPONENT_TEMPLATE_PARAMETERS
);

define('COMPONENT_TEMPLATE_DESCRIPTION', <<<EO_COMPONENT_TEMPLATE_DESCRIPTION
<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

\$arComponentDescription = array(

    "NAME"    => 'Название компонента',
    "DESCRIPTION" => 'Описание компонента',
    
    //"ICON" => "",

    "PATH" => array(
        "ID" => "mediahero",
        "NAME" => "Медиагерой",
        "CHILD" => array(
            'ID' => "mediahero-subitems",
            'NAME' => "Компоненты" 
        ),
    ),

    "CACHE_PATH" => "Y",

   /*
   "AREA_BUTTONS" => array(
      array(
         'URL' => "javascript:alert('Hello, World!');",
         'SRC' => '/images/button.jpg',
         'TITLE' => "Greetings"
      ),
   ),*/    
);
EO_COMPONENT_TEMPLATE_DESCRIPTION
);

define('COMPONENT_TEMPLATE_TEMPLATE', <<<EO_COMPONENT_TEMPLATE_TEMPLATE
<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
EO_COMPONENT_TEMPLATE_TEMPLATE
);