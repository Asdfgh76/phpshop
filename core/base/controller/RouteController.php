<?php
// Одиночка
namespace core\base\controller;

use core\base\settings\Settings;
use core\base\settings\ShopSettings;

class RouteController
{
	static private $_instance;
	
	protected $routes;// свойство маршрутов
	
	protected $controller;
	protected $inputMethod;//метод который собирает данные из базы данных
	protected $outputMethod;//свойство в котором будет хранится имя метода, который отвечает за подключение вида
	protected $parameters;
		
	private function __clone()
	{
		
	}
	
	static public function getInstance(){
		//проверяем хранится ли в свойстве $_instance обьект нашего класса, если да, то возвращаем это свойство
		if(self::$_instance instanceof self){
			return self::$_instance;
		}
		
		return self::$_instance = new self;
	}
	
	private function __construct()
	{
		$adress_str = $_SERVER['REQUEST_URI'];
		
		if(strrpos($adress_str, '/') === strlen($adress_str)-1 && strrpos($adress_str, '/') !== 0){
			$this->redirect(rtrim($adress_str, '/'), 301);
		}
		
		$path = substr($_SERVER['PHP_SELF'], 0, strpos($_SERVER['PHP_SELF'], 'index.php'));
		
		if($path === PATH){
			$this->routes = Settings::get('routes');
			
			if(!$this->routes) throw new RouteException('сайт находится на техническом обслуживании');
			
			if(strpos($adress_str, $this->routes['admin']['alias']) === strlen(PATH)){
				$url = explode('/', substr($adress_str, strlen(PATH.$this->routes['admin']['alias']) + 1));
				if($url[0] && is_dir($_SERVER['DOCUMENT_ROOT'] . PATH . $this->routes['plugins']['path']. $url[0])){
					$plugin = array_shift($url);
					$pluginSettings = $this->routes['settings']['path'] . ucfirst($plugin . 'Settings');
					//проверка существует ли файл
					if(file_exists($_SERVER['DOCUMENT_ROOT'] . PATH . $pluginSettings . '.php')){
						$pluginSettings = str_replace('/', '\\', $pluginSettings);
					$this->routes = $pluginSettings::get('routes');
					}
					$dir = $this->routes['plugins']['dir'] ? '/' . $this->routes['plugins']['dir'] . '/' : '/';
					$dir = str_replace('//', '/', $dir);
					
					$this->controller = $this->routes['plugins']['path'] . $plugin . $dir;
					
					$hrUrl = $this->routes['plugins']['hrUrl'];
					$route = 'plugins';
				}else{
					$this->controller = $this->routes['admin']['path'];
					$hrUrl = $this->routes['admin']['hrUrl'];
					$route = 'admin';
				}
				
			}else{
				$url = explode('/', substr($adress_str, strlen(PATH)));
				//print_arr($url);
				$hrUrl = $this->routes['user']['hrUrl'];
				
				$this->controller = $this->routes['user']['path'];
				
				$route = 'user';
			}
			
			$this->createRoute($route,$url);
			
			if($url[1]){
				$count = count($url);
				$key = '';
				
				if(!$hrUrl){
					$i = 1;
				}else{
					$this->parameters['alias'] = $url[1];
					$i = 2;
				}
				
				for( ; $i < $count; $i++){
					if(!$key){
						$key = $url[$i];
						$this->parameters[$key] = '';
					}else{
						$this->parameters[$key] = $url[$i];
						$key = '';
					}
				}
			}
			
		exit();	
		}else{
			try{
				throw new \Exception('Не корректная директория сайта');
				
			}
			catch(\Exception $e){
				exit($e->getMessage());
			}
		}
	}
	private function createRoute($var,$arr){
		$route = [];
		
		if(!empty($arr[0])){
			if($this->routes[$var]['routes'][$arr[0]]){
				$route = explode('/', $this->routes[$var]['routes'][$arr[0]]);
				
				$this->controller .= ucfirst($route[0].'Controller');
			}else{
				$this->controller .= ucfirst($arr[0].'Controller');
			}
		}else{
			$this->controller .= $this->routes['default']['controller'];
		}
		//аналог if else
		$this->inputMethod = $route[1] ? $route[1] : $this->routes['default']['inputMethod'];
		$this->outputMethod = $route[2] ? $route[2] : $this->routes['default']['outputMethod'];
		
		return;
	}
}