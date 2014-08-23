#!/usr/bin/env php
<?php

/*
if (!function_exists('_DBG')) {
    function _DBG($val) {
        print_r(is_array($val) ? $val : array($val));
    }
}
*/

class MHBitrixTool {

    private static $_instance = null;

    private $site_root = '';

    private $args = array();

    public static function Run($args) {
        self::getInstance()->RunCommand($args);
    }

    private function __constructor() {
    }

    public function getInstance() {
        if (!self::$_instance)
            self::$_instance = new self();        
        return self::$_instance;
    }

    private function RunCommand($args) {
        $this->args = $args;
        $this->site_root = $this->getSiteRoot();
        
        if (count($args) == 1) {
            $this->ShowUsage();
            return;
        }

        $command = $args[1];
        switch ($command) {
            case 'show-site-root':
            case 'show-root':
                $this->ShowSiteRoot();
                break;

            // template copy news.list [OLD_NAME] [NEW_NAME]
            case 'template':
            case 'tpl':
                call_user_func_array(array($this, 'RunTemplateCommand'), array_slice($args, 2));                                
                break;
            
            case 'include':
                // include componentName
                call_user_func_array(array($this, 'RunIncludeComponentCommand'), array_slice($args, 2));
                break;

            case 'component':
                call_user_func_array(array($this, 'RunComponentCommand'), array_slice($args, 2));
                break;

            default:
                echo "Unknown command: $command";
                $this->ShowUsage();
                break;
        }
        //_DBG($this->site_root);
    }

    public function getSiteRoot($start_dir=false) {
        
        $dir = $start_dir == false ?  getcwd() : $start_dir;
        
        if (empty($dir) || $dir == '/')
            return '';

        if ($this->isSiteRoot($dir))
            return $dir;
        
        return $this->getSiteRoot(dirname($dir));
    }

    private function isSiteRoot($dir) {        
        $files_to_check = array(
            'bitrix/.settings.php',
            'bitrix/modules/main/classes/general/access.php',
        );
        foreach ($files_to_check as $file) {
            if (!file_exists("$dir/$file") || !is_file("$dir/$file"))
                return false;
        }
        return true;
    }

    private function ShowUsage($cmd='') {
        $usage = array(
            'template' => 'bxtool template list|copy',
            'template list' => 'bxtool template list componentName',
            'template copy' => 'bxtool template copy componentName[/templateName] siteTemplate[/newTemplateName]',
        );
        if (array_key_exists($cmd, $usage)) {
            echo 'Usage: ' . $usage[$cmd] . PHP_EOL;
            return;
        }
        echo "Usage: bxtool [options]" . PHP_EOL;
    }

    private function ShowSiteRoot() {
        if ($this->site_root)
            echo $this->site_root;
        else
            echo "Not in a Bitrix site dir";
        echo PHP_EOL;
    }

    private function parseComponentName($compName) {
        $vendor = 'bitrix';
        $component = $compName;
        
        $matches = array();
        if (preg_match("/^(\w+):(.+?)$/", $compName, $matches)) 
            list(, $vendor, $component) = $matches;
        
        return array(
            'VENDOR' => $vendor, 
            'ID' => $component
        );
    }

    private function getComponentDir($component) {
        return $this->site_root . '/bitrix/components/' . $component['VENDOR'] . '/' . $component['ID'];
    }

    private function getSubdirs($rootDir) {
        $dirs = array();
        $dh  = opendir($rootDir);
        while (false !== ($filename = readdir($dh))) {
            if (!is_file("$rootDir/$filename") && $filename != '.' && $filename != '..')
                $dirs[] = $filename;
        }
        sort($dirs);        
        return $dirs;
    }

    private function CopyDir($src, $dst) { 
        $dir = opendir($src); 
        @mkdir($dst); 
        while(false !== ( $file = readdir($dir)) ) { 
            if (( $file != '.' ) && ( $file != '..' )) { 
                if ( is_dir($src . '/' . $file) ) { 
                    $this->CopyDir($src . '/' . $file,$dst . '/' . $file); 
                } 
                else { 
                    copy($src . '/' . $file,$dst . '/' . $file); 
                } 
            } 
        } 
        closedir($dir); 
    } 

    private function getLocalComponentTemplatePath($siteTplName, $component, $tplName) {
        return implode('/', array(
            $this->getSiteRoot(),
            'local/templates',
            $siteTplName,
            'components',
            $component['VENDOR'],
            $component['ID'],
            $tplName
        ));
   }

    private function RunTemplateCommand($cmd='', $param1=false, $param2=false) {

        if (empty($cmd)) {
            $this->ShowUsage('template');
            return;
        }

        switch ($cmd) {
            case 'list':

                if (!$param1) {
                    $this->ShowUsage('template list');
                    return;                    
                }
                $component = $this->parseComponentName($param1);
                $templates = $this->getSubdirs($this->getComponentDir($component) . '/templates/');
                echo implode(PHP_EOL, $templates) . PHP_EOL;
                break;
            
            case 'copy':

                if (!$param1 || !$param2) {
                    $this->ShowUsage('template copy');
                    return;
                }
                
                list($srcCompName, $srcTplName) = array_merge(explode('/', $param1), array('.default'));
                list($dstSiteTplName, $dstTplName) = array_merge(explode('/', $param2), array($srcTplName));

                $srcComponent = $this->parseComponentName($srcCompName);

                $srcTplPath = $this->getComponentDir($srcComponent) . '/templates/' . $srcTplName;
                $dstTplPath = $this->getLocalComponentTemplatePath($dstSiteTplName, $srcComponent, $dstTplName);

                if (!file_exists($srcTplPath)) {
                    echo "Failed to copy template! Source component template path does not exists: " . PHP_EOL . $srcTplPath . PHP_EOL;
                    return;
                }

                echo "Copying component template: " . PHP_EOL . "--> from: $srcTplPath" . PHP_EOL . "-->to: $dstTplName ...";

                $this->CopyDir($srcTplPath, $dstTplPath);

                echo " Done!" . PHP_EOL;

            default:
                $this->ShowUsage('template');
                break;
        }
    }

    public function RunIncludeComponentCommand($componentName) {
        $arParams = $this->getComponentParameters($componentName);
        if (!$arParams)
            return false;

        $php = sprintf('<?$APPLICATION->IncludeComponent("%s", "", array(', $componentName) . PHP_EOL;
        
        foreach ($arParams['DEFAULTS'] as $name => $value) {
            // TODO: Добавить вывод комментария с именем параметра.
            $php .= sprintf('    "%s" => %s,', $name, var_export($value, true)) . PHP_EOL; 
        }

        $php .= '  ),' . PHP_EOL . 
                '  false' . PHP_EOL .
                ');/*'.$componentName.'*/?>' . PHP_EOL;

        echo $php;
    }

    public function getComponentParameters($componentName) {
       
        $component = $this->parseComponentName($componentName);

        $parametersFilePath = $this->getComponentDir($component) . '/.parameters.php';
        if (!is_file($parametersFilePath)) {
            echo "Error: Parameters file for component $componentName not found!" . PHP_EOL;
            return;
        }
        
        include ($parametersFilePath);

        $arParams = array();
        // FIXME: Почему-то NAME не для всех параметров заполняются, 
        //скорее всего проблема связана с подключением lang-файла.    
        //$arNames = array(); 
        foreach( $arComponentParameters['PARAMETERS'] as $name => $param){
            $arParams[ $name ] = $param['DEFAULT'] ? $param['DEFAULT'] : '';
            //$arNames[ $name ] = $param['NAME'];
        }

        return array(
            'DEFAULTS' => $arParams,
            //'DESCRIPTIONS' => $arNames, 
        );        
    }

    private function RunComponentCommand($cmd='') {
        switch ($cmd) {
            case 'list':
                $arNSpaces = $this->getSubdirs($this->site_root . '/bitrix/components/');
                foreach($arNSpaces as $nspace) {
                    $arComponents = $this->getSubdirs($this->site_root . "/bitrix/components/$nspace");
                    foreach ($arComponents as $compName) {
                        echo "$nspace:$compName" . PHP_EOL;
                    }
                }
                break;
            
            default:
                # code...
                break;
        }
    }

}

//FIXME: Хак, но пока штатно не реализован CliApplication у нас выхода другого нет.
$_SERVER['DOCUMENT_ROOT'] = MHBitrixTool::getInstance()->getSiteRoot();
include($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

MHBitrixTool::Run($argv);
