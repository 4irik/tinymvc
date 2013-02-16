<?php

class MainController extends Controller
{
	public function actionIndex() {
		$model = new MainModel();
		$data = $model->getData();
		$this->view->render("layout_main", "main_index", $data);
	}

	public function actionInsert() {
		$name = $_GET['name'];
		$model = new MainModel();
		$model->insert($name);
	}

	public function actionSelect(){
		$model = new MainModel();
		$data = $model->select();

		print_r($data);
	}

	public function actionContact() {
		$this->view->render("layout_main", "main_contact");
	}

	/**
	 * @todo удалять файл после парсинга
	 */
	public function actionParse() {
		$fileName = realpath(__DIR__ . "/../../uploads/catalog.xml");

		$xml = new XMLReader();
		$xml->open($fileName, null, LIBXML_NOWARNING);
			$model = new MainModel();
			$model->parseXML($xml);
		$xml->close();
	}

	public function actionIterator() {
		$fileName = realpath(__DIR__ . "/../../uploads/cookbook.xml");
		$xml = new XMLReader();
		$xml->open($fileName, null, LIBXML_NOWARNING);

		$model = new XmlIterator($xml);

		foreach($model as $key=>$value) {
			for($i=0; $i<$value['depth'];$i++){
				echo "__";
			}
			print_r($value);
			echo "<br />";
		}
	}
}
