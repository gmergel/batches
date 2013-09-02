<?php

$link = mysql_connect('localhost','root');
if (!$link) {
    die("error");
}

mysql_select_db('ctool');


$condition = urldecode($_GET['condition']);

if($condition != ''){

	$result = mysql_query($condition);

	if($result){
		$gonogo = mysql_fetch_array($result);
	}else{
		echo "Condition failed.";
		die();
	}

	if(!$gonogo[0]){
		echo "Condition failed.";
		die();
	}
}

$queries = explode(';',urldecode($_GET['sql']));

foreach($queries as $query){

	if($query != '')
		mysql_query($query) OR die(mysql_error());
		
}

mysql_close($link);

?>