#!/usr/bin/env php
<?php

require_once dirname(__FILE__) . '/autoload.php';

use Symfony\Component\Console\Application; 

//FIXME: Хак, но пока штатно не реализован CliApplication в Битриксе, у нас выхода другого нет.
$_SERVER['DOCUMENT_ROOT'] = BitrixTool\BitrixTool::getInstance()->getSiteRoot();
$DOCUMENT_ROOT = $_SERVER["DOCUMENT_ROOT"]; 

define("NO_KEEP_STATISTIC", true); 
define("NOT_CHECK_PERMISSIONS", true); 
set_time_limit(0); 
define("LANG", "ru"); 

$prolog = $_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php";
if (file_exists($prolog)) include($prolog); else die("Not in a Bitrix site root." . PHP_EOL);

$app = new Application('BitrixTool', BitrixTool\BitrixTool::VERSION);
$app->add(new BitrixTool\Commands\ShowWebRootCommand('show-web-root'));
$app->add(new BitrixTool\Commands\TemplatesListCommand('templates:list'));
$app->add(new BitrixTool\Commands\TemplatesCopyCommand('templates:copy'));
$app->add(new BitrixTool\Commands\ComponentsListCommand('components:list'));
$app->add(new BitrixTool\Commands\IBlockListTypesCommand('iblock:types'));
$app->add(new BitrixTool\Commands\IBlockListCommand('iblock:list'));
$app->add(new BitrixTool\Commands\GenerateIncludeCommand('generate:include'));
$app->add(new BitrixTool\Commands\GenerateComponentCommand('generate:component'));
$app->run();

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");