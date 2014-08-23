<?php

namespace BitrixTool;

class BitrixTool 
{  
    const VERSION = "0.0.1";

    private static $instance = null;

    private function __constructor() {}

    public function getInstance() {
        
        if (!self::$instance)
            self::$instance = new self();        
        
        return self::$instance;
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

}