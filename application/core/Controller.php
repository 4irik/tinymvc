<?php
/**
 * Базовый класс для контроллеров
 */
class Controller
{
	public $view;

	function __construct(){
		$this->view = new View();
	}


	/**
	 * Этот метод будет переопределяться в наследующих классах
	 */
	public function actionIndex(){

	}
}
