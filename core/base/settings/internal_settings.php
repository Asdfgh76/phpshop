<?php
defined('VG_ACCESS') or die('Access denied');

const TEMPLATE = 'templates/default/';//шаблоны пользовательской части сайта
const ADMIN_TEMPLATE = 'core/admin/view/';//пути к административной части сайта

const COOKIE_VERSION = '1.0.0';//версия куки(если поменять, то всем придется перелогиниться)
const CRYPT_KEY = '';//ключ шифрования
const COOKIE_TIME = 60;//время бездействия админа
const BLOCK_TIME = 3; //кол-во попыток ввести пароль
//константы для постраничной навигации
const OTY = 8;//кол-во товаров
const OTY_LINKS = 3;//кол-во ссылок
//константа путей к стилям и js файлам админки
const ADMIN_CSS_JS = [
	'styles' => [],
	'scripts' => []
]; 
//константа путей к стилям и js файлам пользовательской части
const USER_CSS_JS = [
	'styles' => [],
	'scripts' => []
];

use core\base\exception\RouteException;

function autoloadMainClasses($class_name){
	$class_name = str_replace('\\', '/', $class_name);
//"@"- отключает вывод ошибок , если не найден такой файл	
	if(!@include_once $class_name.'.php'){
		throw new RouteException('Неверное имя файла для подключения -'.$class_name);
	}
}
spl_autoload_register('autoloadMainClasses');