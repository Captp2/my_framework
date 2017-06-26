<?php
namespace my_framework;
class Core{
	static function run(){
		try{
			define('WEBROOT',str_replace('index.php','',$_SERVER['SCRIPT_NAME'] . '../'));
			define('ROOT',str_replace('index.php','',$_SERVER['SCRIPT_FILENAME'] . '../'));
			error_reporting(E_ALL);
			ini_set('display_error', 1);
			session_start();
			Core::registerAutoload();
			$params = explode('/',$_GET['p']);
			$controller_name = !empty($params[0]) ? $params[0] : 'Index';
			$action = isset($params[1]) ? $params[1] : 'index';
			$controller_name = 'Controller\\'. $controller_name;
			$controller = new $controller_name();
			if (method_exists($controller, $action)){
				unset($params[0]);
				unset($params[1]);
				call_user_func_array(array($controller,$action),$params);
			} else {
				require_once('404.php');
			}
		}
		catch(Exception $e){
			if($e instanceof NotFoundException){
				header("HTTP/1.1 404 Not Found");
			}
			else{
				header("HTPP/1.1. 500 Internal Server Error");
			}
		}
	}

	static function registerAutoload(){
		spl_autoload_register(function ($class_name){
			if(count(explode('\\', $class_name)) == 2){
				$namespace = explode('\\', $class_name)[0];
				$class_name = explode('\\', $class_name)[1];
				if($namespace === 'Model'){
					if(file_exists(ROOT . 'app/models/' . strtolower($class_name) . '.php')){
						include ROOT . 'app/models/' . strtolower($class_name) . '.php';
					}
				}
				elseif($namespace === 'Controller'){
					if(file_exists(ROOT . 'app/controllers/' . strtolower($class_name) . '.php')){
						include ROOT . 'app/controllers/' . strtolower($class_name) . '.php';
					}
				}
				elseif($namespace === 'my_framework'){
					if(file_exists(ROOT . 'lib/' . $class_name . '.php')){
						include ROOT . 'lib/' . $class_name . '.php';
					}
				}
			}
		});
	}

	static function registerTwig(){
		require_once(ROOT . 'lib/vendor/autoload.php');
	}
}
?>