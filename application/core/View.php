<?php
/**
 * Базовый класс для представления данных
 */
class View
{
	/**
	 * Вывод отображения
	 *
	 * @param string $layout имя общего вида в который будет включен текущий вид
	 * @param string $template имя текущего вида
	 * @param mixed $data ассоциативный массив, по-умолчанию null
	 */
	public function render($layout, $template, $data=null) {
		/**
		 * импортируем переменные из массива в текущую таблицу символов
		 * @see http://php.net/manual/ru/function.extract.php
		 */
		if (is_array($data)) {
			extract($data);
		}

		include_once "application/views/". $layout .".php";
	}
}
