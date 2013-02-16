<?php
/**
 * Предоставляет базовые функции
 *
 * @todo пути поиска классов не пренадлежащих ядру вынести в конфигурационный файл
 * @todo добавить обработку критических ошибок
 */
class Base {
	/**
	 * Пути до файлов с классами ядра
	 * @var array
	 */
	protected static $_coreClassesPath = array(
		"Controller"=>"/application/core",
		"Model"=>"/application/core",
		"View"=>"/application/core",
		"Router"=>"/application/core",
		"DB"=>"/application/core",
	);

	/**
	 * Пути до файлов с классами приложения
	 * @var array
	 */
	protected static $_applicationClassesPath = array(
		'/application/controllers',
		'/application/models',
		'/application/vendors',
	);

	/**
	 * Включает файл вызываемого класса
	 *
	 * @static
	 * @param $className string имя вызываемого класса
	 * @throws Exception
	 *
	 * @todo избавиться от file_exists в цикле (тавтология получается)
	 */
	public static function autoload($className)	{
		/**
		 * Ищем файл с классом
		 */
		if (isset(self::$_coreClassesPath[$className])){
			$filePath = $_SERVER['DOCUMENT_ROOT'] . self::$_coreClassesPath[$className] .DIRECTORY_SEPARATOR. $className .".php";
		} else {
			foreach (self::$_applicationClassesPath as $path) {
				$filePath = $_SERVER['DOCUMENT_ROOT'] . $path .DIRECTORY_SEPARATOR. $className .".php";
				if (file_exists($filePath)){
					break;
				}
			}
		}

		if (!file_exists($filePath)){
			throw new Exception(__CLASS__ .": файл \"". $filePath ."\" не существует.");
		}

		include_once $filePath;
	}

	/**
	 * Возвращет конфигурацию приложения
	 *
	 * @param string $paramName имя параметра который необходимо получить, по-умолчанию NULL
	 * @return mixed значение параметра конфигурации либо вся конфигурация вслучае, если $paramName==NULL
	 * @throws Exception вслучае если файл конфигурации либо требуемая конфигурация отсутствует
	 */
	public static function getConfig($paramName=null) {
		$configPath = $_SERVER['DOCUMENT_ROOT'] .DIRECTORY_SEPARATOR. "application/config/config.php";

		if (!file_exists($configPath)){
			throw new Exception (__CLASS__ . ": отсутствует конфигурационный файл \"".$configPath."\".");
		}

		$config = include_once $configPath;

		if (is_null($paramName)){
			return $config;
		}

		if (!array_key_exists($paramName, $config)) {
			throw new Exception(__CLASS__ . ": в конфигурационном файле отсутствует параметр \"". $paramName ."\"");
		}

		return $config[$paramName];
	}

	/**
	 * Заменяет вывод ошибки исключением
	 *
	 * @param $errno номер ошибки
	 * @param $msg сообщение об ошибке
	 * @param $file файл, в котором произошла ошибка
	 * @param $line строка файла, в которой произошла ошибка
	 * @throws Exception
	 */
	public static function error($errno, $msg, $file, $line) {
		$message = 'file: '.$file.";\n";
		$message .= "line: ".$line.";\n";
		$message .= $msg;

		throw new Exception($message, $errno);
	}
}

/**
 * Устанавливаем класс и метод ответственные за включение запрашиваемых классов
 */
spl_autoload_register(array('Base','autoload'));
/**
 * Устанавливаем класс и метод для обработки ошибок
 */
set_error_handler(array('Base','error'), E_ALL);
