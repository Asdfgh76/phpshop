<?php
define('VG_ACCESS', true);
//тип контента и кодировка
header('Content-Type:text/html;charset=utf-8');
session_start();

require_once 'config.php';
require_once 'core/base/settings/internal_settings.php';//для настройки проекта
require_once 'core/libraries/functions.php';

use core\base\exception\RouteException;
use core\base\controller\RouteController;

try{
	RouteController::getInstance()->route();
}
catch(RouteException $e){
	exit($e->getMessage());
}
echo $_SERVER['DOCUMENT_ROOT'];