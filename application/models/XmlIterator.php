<?php
/**
 * Позволяет обращаться с объектом как с плоским списком
 *
 * @todo проверить на предмет получения value внутри тега
 */
class XmlIterator implements Iterator {
	/**
	 * Экземпляр с которым будем работать
	 * @var XMLReader
	 */
	protected $xml;

	/**
	 * Флаг удачного считывания элемента
	 * @var boolean
	 */
	protected $validFlag;

	/**
	 * Счетчик элементов
	 * @var int
	 */
	protected $id;

	/**
	 * Уровень вложенности
	 * @var int
	 */
	protected $lastDepth;

	/**
	 * Список родительских элементов
	 *
	 * <code>
	 * <?php
	 * $parents = array(
	 *  0, 5, 7
	 * );
	 * ?>
	 * </code>
	 *
	 * @var array
	 */
	protected $parents = array();


	public function __construct(XMLReader $xml) {
		$this->xml = $xml;
	}

	public function current() {
		/**
		 * Обновляем регистр родительских ID
		 */
		$this->registerParents();

		$response = array();
		$response['id'] = $this->id;
		$response['depth'] = $this->xml->depth;
		$response['parent'] = $this->getParentID();
		$response['name'] = $this->xml->name;
		$response['attributes'] = $this->getNodeAttributes();
		$response['value'] = $this->getNodeValue();

		return $response;
	}

	public function next() {
		/**
		 * Переводим указатель на следующий элемент
		 */
		$this->nextElement();

		/**
		 * Прекращаем роботу если читать больше нечего
		 * (сделано что бы не вызывать ошибки при рекурсивном вызове в случае если достигнут конец файла)
		 */
		if (!$this->valid()) {
			return;
		}

		/**
		 * Пропускаем все кроме тегов
		 */
		if ($this->xml->nodeType != XMLReader::ELEMENT){
			$this->next();
		}
	}

	public function key() {
		// храним предидущий уровень вложенности
		$this->lastDepth = $this->xml->depth;

		return $this->id++;
	}

	public function valid() {
		return $this->validFlag;
	}

	public function rewind() {
		$this->id = 0;
		$this->lastDepth = 0;

		$this->next();
	}

	/**
	 * Переводит указатель на следующий элемент и ставит флаг валидности
	 */
	protected function nextElement() {
		$this->validFlag = $this->xml->read();
	}

	/**
	 * Регистрирует актуальные id родительских элементов
	 */
	protected function registerParents() {
		// делаем первую запись
		if ($this->xml->depth == 0) {
			$this->parents[$this->xml->depth]= $this->id;
		}
		// спустились ниже - заносим запись в реестр о новой подчиненной ветке
		if ($this->xml->depth > $this->lastDepth) {
			$this->parents[$this->xml->depth] = $this->id;
		}
		// поднялись на уровень вверх - удаляем подчиненную ветку
		if ($this->lastDepth > $this->xml->depth){
			$this->parents[$this->xml->depth] = $this->id;
			unset($this->parents[$this->lastDepth]);
		}
		// ходим по одному уровню - заменяем id
		if ($this->lastDepth == $this->xml->depth) {
			$this->parents[$this->xml->depth] = $this->id;
		}
	}

	/**
	 * Возвращает ID родительского элемента
	 *
	 * @return int
	 */
	protected function getParentID() {
		return isset($this->parents[$this->xml->depth-1]) ? $this->parents[$this->xml->depth-1] : null;
	}

	/**
	 * Считывание атрибутов узла
	 *
	 * @return array|null
	 */
	protected function getNodeAttributes() {
		if ($this->xml->hasAttributes) {
			$tmp = array();
			while($this->xml->moveToNextAttribute()) {
				$tmp = array_merge($tmp, array($this->xml->name => $this->xml->value));
			}

			return $tmp;
		}

		return null;
	}

	/**
	 * Считывает текст записанный в узел, если он (текст) есть
	 *
	 * @return null|string
	 */
	protected function getNodeValue() {
		/**
		 * Переводим указатель на следующий элемент
		 */
		$this->nextElement();

		/**
		 * Читать больше нечего
		 */
		if (!$this->valid()) {
			return null;
		}

		/**
		 * Элемент пустой
		 */
		if ($this->xml->isEmptyElement) {
			return null;
		}

		/**
		 * Элемент является текстом
		 */
		if ($this->xml->hasValue){
			return $this->xml->value;
		}

		return null;
	}
}
