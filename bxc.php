#!/usr/bin/env php
<?php

require_once dirname(__FILE__) . '/autoload.php';

use Symfony\Component\Console\Application; 

//FIXME: Хак, но пока штатно не реализован CliApplication в Битриксе, у нас выхода другого нет.
$_SERVER['DOCUMENT_ROOT'] = BitrixTool\BitrixTool::getInstance()->getSiteRoot();
$prolog = $_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php";
if (file_exists($prolog)) include($prolog); else die("Not in a Bitrix site root." . PHP_EOL);

$app = new Application('BitrixTool', BitrixTool\BitrixTool::VERSION);
$app->add(new BitrixTool\Commands\ShowWebRootCommand('show-web-root'));
$app->add(new BitrixTool\Commands\TemplatesListCommand('templates:list'));
$app->run();
