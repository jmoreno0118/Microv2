<?php 
include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/conectadb.inc.php';
try {
	$sql='SELECT * FROM maximostbl';
	$s=$pdo->prepare($sql); 
	$s->execute();
	echo "<pre>";
	var_dump($s->fetchAll());
	echo "</pre>";
} catch (Exception $e) {
	echo "Error";
}
?>