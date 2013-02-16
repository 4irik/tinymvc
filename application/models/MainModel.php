<?php
/**
 * @todo функиця получения элемента дерева по его id в БД
 * @todo функция генацрии xml из БД
 * @todo сделать откат изменений в БД вслучае ошибки парсинга xml-файла
 */
class MainModel extends Model
{
	public function getData(){
		return array(
			'title'=>'Заголовок',
			'text'=>'Текст для главной страницы',
		);
	}

	/**
	 * Записывает xml-файл в БД
	 *
	 * @param XMLReader $xml
	 * @param null $parentId - (int)id родительского элемента, если есть
	 *
	 * @todo заменить на обход итератора
	 */
	public function parseXML(XMLReader $xml, $parentId=null) {
		while ($xml->read()) {
			/**
			 * Обнуляем параметры перед каждой итерацией
			 */
			$name = null;
			$attributes = null;
			$value = null;
			$complex = 0;

			switch ($xml->nodeType) {
				case XMLReader::END_ELEMENT:
					return;
				case XMLReader::ELEMENT:
					$name = $xml->name;
					/**
					 * считываем атрибуты элемента
					 */
					if ($xml->hasAttributes) {
						$tmp = array();
						while($xml->moveToNextAttribute()) {
							$tmp[] = $xml->name .'='.$xml->value;
						}
						$attributes = implode(',', $tmp);
					}
					/**
					 * Проверяем узел на пустоту (пустой тэг или нет)
					 */
					if (!$xml->isEmptyElement) {
						$complex = 1;
					}
					break;
				/**
				 * Если содержимое узла - текст, то обновляем запись об этом узле
				 */
				case XMLReader::TEXT:
				case XMLReader::CDATA:
					$str = DB::db()->connection()->prepare("UPDATE xml SET value=:value, complex=:complex WHERE id=:id");
					$str->execute(array(
						':id'=>DB::db()->connection()->lastInsertId(),
						':value'=>$xml->value,
						':complex'=>0,
					));
					break;
			}

			/**
			 * Записываем новый элемент в БД
			 */
			if (!is_null($name)) {
				echo "Parent ID: ". $parentId."<br>Name: ".$name."<br>Attr: ".$attributes."<br>Value: ".$value."<br>Is compex: ".$complex."<br>------------<br>";

				$str = DB::db()->connection()->prepare("INSERT INTO xml (parent_id, name, attributes, value, complex) VALUES (:parent_id, :name, :attributes, :value, :complex)");
				$str->execute(array(
					':parent_id'=>$parentId,
					':name'=>$name,
					':attributes'=>$attributes,
					':value'=>$value,
					':complex'=>$complex,
				));

				if ($complex == 1) {
					$this->parseXML($xml, DB::db()->connection()->lastInsertId());
				}
			}
		}
	}
}