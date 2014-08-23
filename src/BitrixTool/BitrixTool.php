<?php

namespace BitrixTool;

class BitrixTool 
{  
    const VERSION = "0.0.1";

    private static $instance = null;

    private $siteRoot = '';

    private function __constructor() {}

    public function getInstance() 
    {        
        if (!self::$instance)
            self::$instance = new self();        
        
        return self::$instance;
    }    

    public function getSiteRoot($start_dir=false) 
    {        
        if ($this->siteRoot)
            return $this->siteRoot;

        $dir = $start_dir == false ?  getcwd() : $start_dir;
        
        if (empty($dir) || $dir == '/')
            return '';

        if ($this->isSiteRoot($dir)) {
            $this->siteRoot = $dir;
            return $this->siteRoot;
        }
        
        return $this->getSiteRoot(dirname($dir));
    }

    private function isSiteRoot($dir)
    {        
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

    public function getSiteTemplates($location='bitrix', $returnFullPath=false, $showLocation=false) 
    {
        $templatesRoot = $this->getSiteRoot() . "/$location/templates";
        $siteTemplates = FileSystemHelpers::getSubdirs($templatesRoot, $returnFullPath);
        if ($showLocation) 
        {
            for($i=0; $i<count($siteTemplates); $i++) {
                $siteTemplates[$i] = $siteTemplates[$i] . " ($location)";
            }
        }
        return $siteTemplates;
    }

}