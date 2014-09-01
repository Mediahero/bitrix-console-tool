<?php

namespace BitrixTool;

class BitrixComponent
{
    private $namespace = 'bitrix';
    private $name = '';

    public function __construct($name)
    {
        $this->parseComponentName($name);
    }

    private function parseComponentName($name) {
        
        $this->namespace = 'bitrix';
        $this->name = $name;
        
        $matches = array();
        if (preg_match("/^(\w+):(.+?)$/", $name, $matches)) 
            list(, $this->namespace, $this->name) = $matches;

        if (!$this->namespace) 
            $this->namespace = 'bitrix';
    }

    public function getNamespace() {
        return $this->namespace;
    }

    public function getName() {
        return $this->name;
    }

    public function getFullName() {
        return sprintf("%s:%s", $this->getNamespace(), $this->getName());
    }

    public function getComponentDir() 
    {
        $siteRoot = BitrixTool::getInstance()->getSiteRoot();
        
        $localPath = "$siteRoot/local/components/" . $this->getNamespace() . "/" . $this->getName();
        if (file_exists($localPath))
            return $localPath;

        return "$siteRoot/bitrix/components/" . $this->getNamespace() . "/" . $this->getName();
    }

    public function getDefaultTemplates($returnFullPath=false) {
        return FileSystemHelpers::getSubdirs($this->getComponentDir() . '/templates', $returnFullPath);
    }

    public function getSiteTemplates($location="bitrix", $returnFullPath=false, $showLocation=false) {
      
        $templates = array();
            
        $siteRoot = BitrixTool::getInstance()->getSiteRoot();
        $prefix = $location != 'bitrix' ? $location : '';

        $siteTemplates = BitrixTool::getInstance()->getSiteTemplates($location);
        foreach($siteTemplates as $siteTemplate) 
        {        
            $templatePath = implode("/", array(
                "$siteRoot/$location/templates/$siteTemplate/components",
                $this->getNamespace(),
                $this->getName()
            ));
        
            if (file_exists($templatePath)) 
            {
                $tpls = FileSystemHelpers::getSubdirs($templatePath);
                foreach ($tpls as $tpl) 
                {
                    if ($returnFullPath)
                        $templates[] = "$templatePath/$tpl";     
                    else 
                        $templates[] = $showLocation ? "$tpl ($prefix/$siteTemplate)" : $tpl;
                }
            }

        }
      
        return $templates;           
    }

    public function exists() {
        return file_exists($this->getComponentDir());
    }

    public function getTemplatePath($templateName) {
        return $this->getComponentDir() . "/templates/" . $templateName;
    }

    public function getLocalTemplatePath($siteTemplateName, $templateName) 
    {
        return implode('/', array(
            BitrixTool::getInstance()->getSiteRoot(),
            'local/templates',
            $siteTemplateName,
            'components',
            $this->getNamespace(),
            $this->getName(),
            $templateName
        ));
    }

    public function getParameters() 
    {      
        $parametersFilePath = $this->getComponentDir() . '/.parameters.php';
        
        if (!is_file($parametersFilePath))
            return false;
        
        @include ($parametersFilePath);

        if (!isset($arComponentParameters))
            $arComponentParameters = array('GROUPS' => array(), 'PARAMETERS' => array());

        // FIXME: Почему-то NAME не для всех параметров заполняются, 
        //скорее всего проблема связана с подключением lang-файла.    
        return $arComponentParameters;
    }
}