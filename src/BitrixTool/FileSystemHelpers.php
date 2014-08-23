<?php

namespace BitrixTool;

class FileSystemHelpers 
{
    public static function getSubdirs($rootDir, $returnFullPath=false) 
    {
        if (!file_exists($rootDir))
            return false;

        $dirs = array();
        
        $dh  = opendir($rootDir);
        while (false !== ($filename = readdir($dh))) 
        {
            if (!is_file("$rootDir/$filename") && $filename != '.' && $filename != '..')
                $dirs[] = $returnFullPath ? "$rootDir/$filename" : $filename;
        }
        
        sort($dirs);        
        
        return $dirs;
    }

    public static function CopyDir($src, $dst) 
    {         
        @mkdir($dst); 

        $dir = opendir($src); 

        while(false !== ( $file = readdir($dir)) ) 
        { 
            if (( $file != '.' ) && ( $file != '..' )) 
            { 
                if ( is_dir($src . '/' . $file) ) 
                { 
                    self::CopyDir($src . '/' . $file,$dst . '/' . $file); 
                } 
                else 
                { 
                    copy($src . '/' . $file,$dst . '/' . $file); 
                } 
            } 
        } 

        closedir($dir); 
    } 
}