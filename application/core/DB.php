<?php
/**
 * Представляет соединение с БД, создан по шаблону Singleton
 *
 * @link http://ru.wikipedia.org/wiki/Одиночка_(шаблон_проектирования)
 * @todo переместить init() в __construnct()
 */
class DB
{
	/**
	 * @var DB экземпляр объекта
	 */
	protected static $db;
	/**
	 * @var PDO экземпляр объекта соединения с БД
	 */
	protected $connection;

	/**
	 * Защищаем от создания ч\з new
	 *
	 * @link http://php.net/manual/ru/pdo.construct.php
	 */
	private function __construct(){
		$config = Base::getConfig("db");

		$this->connection = new PDO(
			$config['dsn'],
			$config['user'],
			$config['password'],
			$config['options']
		);
		$this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}

	/**
	 * Защищаем от создания ч\з копирование
	 */
	private function __clone(){

	}

	/**
	 * Защищаем от создания ч\з unserialize
	 */
	private function __wakeup() {

	}

	/**
	 * Ч/з неё будут проходить все операции с классом, в случае, если экземпляр еще не создан создает его
	 *
	 * @return DB|DB
	 */
	public static function db() {
		if (is_null(self::$db)){
			self::$db = new DB();
		}

		return self::$db;
	}

	/**
	 * Предоставляет доступк к экземпляру класса PDO
	 *
	 * @return PDO
	 */
	public function connection(){
		return $this->connection;
	}
}
