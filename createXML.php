<?php
/**
 * Создает XML по шаблону
 */

$fileName = "bigXML2.xml";
$fileHead = '<?xml version="1.0" encoding="UTF-8"?><catalog>';
$fileElement = '<book id="bk101">
		<author>Gambardella, Matthew</author>
		<title>XML Developers Guide</title>
		<genre>Computer</genre>
		<price>44.95</price>
		<publish_date>2000-10-01</publish_date>
		<description>An in-depth look at creating applications
			with XML.</description>
	</book>';
$fileEnd = '</catalog>';


$file = fopen($fileName, "w");
fwrite($file, $fileHead);
for($i=0; $i<1000000; $i++) {
	fwrite($file, $fileElement);
}
fwrite($file, $fileEnd);
fclose($file);