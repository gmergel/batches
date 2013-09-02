<?php

include('excel_reader2.php');
$data = new Spreadsheet_Excel_Reader($_GET['excel'].".xls");

echo '<script type="text/javascript" src="jquery.js"></script>';
echo '<script type="text/javascript" src="funcs.js"></script>';
echo '<link rel="stylesheet" type="text/css" href="styles.css" />';

$sheet = 2; //employees


//Include employee:
/*

insert into person (inumber,name,email,adm_date,manager,ismanager) 
values ('i000001','John Doe','john.doe@sap.com','2009-06-13', 
(Select idperson from (Select distinct idperson from person where LOWER(inumber) = LOWER('i000000')) as T),0)

*/

	$column = 1;
	$row = 2;
	
	while($data->val($row,$column,$sheet) != ''){

		while($data->val($row,$column,$sheet) != ''){
			
			switch($column){
				case 1:
					$inumber = $data->val($row,$column,$sheet);
					if(!count($extracted[$inumber])){
						$extracted[$inumber] = array();
					}
				break;				
				
				case 2:
					$name = $data->val($row,$column,$sheet);
					$extracted[$inumber]['name'] = $name;
				break;

				case 3:
					$email = $data->val($row,$column,$sheet);
					$extracted[$inumber]['email'] = $email;
				break;

				case 4:
					$adm_date = $data->val($row,$column,$sheet);
					$extracted[$inumber]['adm_date'] = $adm_date;
				break;

				case 5:
					$manager_inumber = $data->val($row,$column,$sheet);
					$extracted[$inumber]['manager_inumber'] = $manager_inumber;
				break;

				case 6:
					$ismanager = (strtolower($data->val($row,$column,$sheet)) == 'yes')? 1 : 0;
					$extracted[$inumber]['ismanager'] = $ismanager;
				break;
			}

			$column++;

		}
		$column = 1;
		$row++;

	}

	//print_r($extracted);
	//die();
	
	$counter = 1;

	echo "<hr>--- NEW EMPLOYEES BATCH ---";

	foreach($extracted as $key => $value){

		$tbiinumber = $key;
		$tbiname = $value['name'];
		$tbiemail = $value['email'];
		$tbiadm_date = $value['adm_date'];
		$tbimanager_inumber = $value['manager_inumber'];
		$tbiismanager = $value['ismanager'];

		echo "<br><hr>".$counter;
		echo "<div id='sql-".$counter."'>insert into person (inumber,name,email,adm_date,manager,ismanager) "
		."values ('".$tbiinumber."','".$tbiname."','".$tbiemail."','".$tbiadm_date."', "
		."(Select idperson from (Select distinct idperson from person where LOWER(inumber) = LOWER('".$tbimanager_inumber."')) as T),0);<br>";
		echo "</div>";
		echo "<br><a class='btn' id='link-".$counter."'>update DB</a>";

		$counter++;
	}



?>