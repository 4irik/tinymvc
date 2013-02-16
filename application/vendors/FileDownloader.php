<?php

/**
 * Сохраняет файл полученный из запроса пользователя
 *
 * @todo возможность загрузки множества файлов
 * @todo показ % загрузки файлов {@link http://php.net/manual/ru/session.upload-progress.php}
 */
class FileDownloader
{
	/**
	 * @var string полный путь до каталога загрузки файлов
	 */
	protected $savePath;

	/**
	 * @var string полное имя загруженного файла
	 */
	protected $fileName;

	/**
	 * При создании экземпляра нужно указывать путь сохранения файла
	 *
	 * @param string $savePath путь сохранения файла
	 */
	public function __construct($savePath)
	{
		if (empty($savePath)) {
			throw new Exception(__CLASS__ . ": Не указан путь сохранения для закачаных файлов.");
		}

		if (!is_dir($savePath)) {
			throw new Exception(__CLASS__ . ": Каталог \"" . $savePath . "\" не существует.");
		}

		if (!is_writable($savePath)) {
			throw new Exception(__CLASS__ . ": Каталог \"" . $savePath . "\" не доступен для записи.");
		}

		$this->savePath = $savePath;
	}

	/**
	 * Сохраняет загружаемый файл
	 *
	 * @throws Exception если файл не был отправлен на сервер либо произошла ошибка с перемещением файла
	 */
	public function downloadFile()
	{
		if (!isset($_FILES['file']['name']) || empty($_FILES['file']['name'])) {
			throw new Exception (__CLASS__ . ": Для загрузки не был выбран файл.");
		}

		$tmpName = $_FILES['file']['tmp_name'];
		$newName = $this->savePath . '/' . $_FILES['file']['name'];

		if (!@is_uploaded_file($tmpName) || !@move_uploaded_file($tmpName, $newName)) {
			throw new Exception(__CLASS__ . ": Файл  " . $_FILES['file']['name'] . " не был загружен либо его невозможно переместить.");
		}

		$this->fileName = $newName;
	}

	/**
	 * Возвращает полное имя загруженного файла
	 *
	 * @return string полное имя загруженного файла
	 */
	public function getFileName() {
		return $this->fileName;
	}
}
