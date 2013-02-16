<?php
/**
 * Конфигурационный файл
 */
return array(
	/**
	 * Параметры подключения к БД
	 *
	 * @name dsn
	 * @name user
	 * @name password
	 */
	'db' => array(
		'dsn' => 'mysql:dbname=pxl;host=localhost',
		'user' => 'root',
		'password' => '',
		'options'=>array(),
	),

	/**
	 * Контроллер и действие по умолчанию.
	 * Если $_SERVER['REQUEST_URI']="contant", то будет запущен метод 'actionContact' контроллера 'controller'."Controller"
	 * Если $_SERVER['REQUEST_URI']="", то будет запущен метод "action".'action' контроллера 'controller'."Controller"
	 *
	 * @todo сделать чтобы работало
	 */
	'default'=>array(
		'controller'=>'Main',
		'action'=>'Index',
	),

);