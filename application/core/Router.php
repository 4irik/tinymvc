<?php

/**
 * Обработчик маршрутов: вызывает метод класса указанные в маршруте
 *
 * @property string $defaulController имя контроллера(класса) по-умолчанию
 * @property string $defaultAction имя действия(метода) по-умолчанию
 *
 * @todo добавить возможность обработки параметров передаваемых в вызываемый метод
 * @todo добавить возможность переопределять контроллер и метод по-умолчанию
 */
class Router {
	/**
	 * @static string имя контроллера по-умолчанию
	 */
	protected static $defaulController = "Main";

	/**
	 * @static string имя действия по-умолчанию
	 */
	protected static $defaultAction = "Index";

	/**
	 * @var string постфикс для контроллеров
	 */
	const CONTROLLER_POSTFIX = "Controller";

	/**
	 * @var string префикс для действия контроллера
	 */
	const ACTION_PREFIX = "action";

	/**
	 * Запускает, в соответсвии с маршрутом, метод класса
	 *
	 * @static
	 * @throws Exception
	 */
	public static function start() {
		/**
		 * Имена по-умолчанию
		 */
		$controllerName = self::$defaulController;
		$actionName = self::$defaultAction;

		/**
		 * Получаем имя вызыванных контроллера и метода
		 */
		$routes = explode("/", $_SERVER['REQUEST_URI']);
		if (!empty($routes[1])){
			$controllerName = $routes[1];
		}
		/**
		 * отделяем передаваемые параметры от имени метода
		 */
		if (!empty($routes[2])){
			$params = explode("?",$routes[2]);
			$actionName = $params[0];
		}

		/**
		 * устанавливаем имена котроллера и запускаемого в нем действия согласно правилам именования
		 *
		 * @example MainController
		 * @example actionIndex
		 */
		$controllerName .= self::CONTROLLER_POSTFIX;
		$actionName = self::ACTION_PREFIX . $actionName;

		/**
		 * Создаем экземпляр класса и вызываем метод
		 */
		$controller = new $controllerName();
		$action = $actionName;

		if (!method_exists($controller, $action)) {
			throw new Exception(__CLASS__ .": Метод \"". $actionName ."\" контроллера \"". $controllerName ."\" не найден.");
		}

		$controller->$action();
	}
}
