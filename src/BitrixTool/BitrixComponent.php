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
    }

    public function getNamespace() {
        return $this->namespace;
    }

    public function getName() {
        return $this->name;
    }

    public function getComponentDir() 
    {
        $siteRoot = BitrixTool::getInstance()->getSiteRoot();
        return "$siteRoot/bitrix/components/" . $this->getNamespace() . "/" . $this->getName();
    }

    public function getDefaultTemplates($returnFullPath=false) {
        return FileSystemHelpers::getSubdirs($this->getComponentDir() . '/templates/', $returnFullPath);
    }

    public function getSiteTemplates($location="bitrix", $returnFullPath=false, $showLocation=false) {
      
        $templates = array();
            
        $siteRoot = BitrixTool::getInstance()->getSiteRoot();
        $prefix = $location != 'bitrix' ? $location : '';

        $siteTemplates = BitrixTool::getInstance()->getSiteTemplates($location);
        foreach($siteTemplates as $siteTemplate) 
        {        
            $templatePath = implode("/", array(
                "$siteRoot/$location/templates/$siteTemplate/components/",
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

}