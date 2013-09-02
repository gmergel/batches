<?php

include('excel_reader2.php');
$data = new Spreadsheet_Excel_Reader($_GET['excel'].".xls");

echo '<script type="text/javascript" src="jquery.js"></script>';
echo '<script type="text/javascript" src="funcs.js"></script>';
echo '<link rel="stylesheet" type="text/css" href="styles.css" />';

$sheet = 1; //formal

	$column = 1;
	$row = 2;
	
	while($data->val($row,$column,$sheet) != ''){

		while($data->val($row,$column,$sheet) != ''){
			
			//if(!count($extracted[$headers[$column-1]])) $extracted[$headers[$column-1]] = array();
			//array_push($extracted[$headers[$column-1]], $data->val($row,$column,$sheet));
			
			switch($column){
				case 1:
					$inumber = $data->val($row,$column,$sheet);
					if(!count($extracted[$inumber])){
						$extracted[$inumber] = array();
					}
				break;				
				
				case 2:
					$first_date = $data->val($row,$column,$sheet);
					$extracted[$inumber]['first_date'] = $first_date;
				break;

				case 3:
					$last_date = $data->val($row,$column,$sheet);
					$extracted[$inumber]['last_date'] = $last_date;
				break;

				case 4:
					$bonus_salary = (strtolower($data->val($row,$column,$sheet)) == 'yes')? 1 : 0;
					$extracted[$inumber]['bonus_salary'] = $bonus_salary;
				break;
			}

			$column++;

		}
		$column = 1;
		$row++;

	}

	//print_r($extracted);
	$counter = 1;

	echo "<hr>--- FORMAL VACATION BATCH ---";

	foreach($extracted as $key => $value){

		$tbiinumber = $key;
		$tbifirst_date = $value['first_date'];
		$tbilast_date = $value['last_date'];
		$tbibonus_salary = $value['bonus_salary'];

		/*

		SELECT MIN(p.idperiod) from formalvacations fv, requests r, period p where fv.requests_idrequests = r.idrequests and r.person_idperson = 825 and fv.period_idperiod <> p.idperiod and p.person_idperson = r.person_idperson

		*/

		echo "<br><hr>".$counter;
		
		echo "<div id='condition-".$counter."'>SELECT IFNULL(MIN(pe.idperiod),0) as test from period pe, person ps where pe.person_idperson = ps.idperson and LOWER(ps.inumber) = LOWER('".$tbiinumber."') and pe.idperiod not in (select fv.period_idperiod from formalvacations fv where fv.requests_idrequests in (Select r.idrequests from requests r where r.person_idperson = ps.idperson));</div>";

		echo "<div id='sql-".$counter."'>insert into requests (person_idperson, approved, sent, date_sent, date_approved) values ((Select idperson from person where LOWER(inumber) = LOWER('".$tbiinumber."')),1,1,NOW(),NOW());<br>";

		/*
		SELECT MIN(pe.idperiod) from period pe, person ps where pe.person_idperson = ps.idperson and LOWER(ps.inumber) = LOWER('I823806') and pe.idperiod not in (select fv.period_idperiod from formalvacations fv where fv.requests_idrequests in (Select r.idrequests from requests r where r.person_idperson = ps.idperson))
		*/

		$sqlformal = "INSERT into formalvacations (requests_idrequests, period_idperiod, first_date, last_date, bonus_salary) values "
		."((SELECT max( idrequests ) FROM requests), (SELECT MIN(pe.idperiod) from period pe, person ps where pe.person_idperson = ps.idperson and LOWER(ps.inumber) = LOWER('".$tbiinumber."') and pe.idperiod not in (select fv.period_idperiod from formalvacations fv where fv.requests_idrequests in (Select r.idrequests from requests r where r.person_idperson = ps.idperson))),'".$tbifirst_date."','".$tbilast_date."',".$tbibonus_salary.")";

		echo $sqlformal;
		echo "</div>";

		echo "<br><a class='btn' id='link-".$counter."'>update DB</a>";

		$counter++;
	}



?>