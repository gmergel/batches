<?php

include('excel_reader2.php');
$data = new Spreadsheet_Excel_Reader($_GET['excel'].".xls");

echo '<script type="text/javascript" src="jquery.js"></script>';
echo '<script type="text/javascript" src="funcs.js"></script>';
echo '<link rel="stylesheet" type="text/css" href="styles.css" />';

$sheet = 0; //informal

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
						$extracted[$inumber]['dates'] = array();
						$extracted[$inumber]['midturns'] = array();
					}
				break;				
				
				case 2:
					$dayoff = $data->val($row,$column,$sheet);
					array_push($extracted[$inumber]['dates'],$dayoff);
				break;

				case 3:
					$midturn = (strtolower($data->val($row,$column,$sheet)) == 'yes')? 1 : 0;
					array_push($extracted[$inumber]['midturns'],$midturn);
				break;
			}

			$column++;

		}
		$column = 1;
		$row++;

	}

	//print_r($extracted);
	$counter = 1;

	echo "<hr>--- INFORMAL VACATION BATCH ---";

	foreach($extracted as $key => $value){

		$tbiinumber = $key;
		$tbidates = $value['dates'];
		$tbimidturns = $value['midturns'];

		$sqldateslist = "('".join("','",$tbidates)."')";

		/*
		condition
		SELECT count(*) from daysoff do, person ps, requests r where r.person_idperson = ps.idperson and r.idrequests = do.requests_idrequests and LOWER(ps.inumber) = LOWER('i000000') and date in ('2013-07-02')
		*/

		$sqlcondition = "SELECT IF( COUNT( * ) >0, 0, 1 ) from daysoff do, person ps, requests r where r.person_idperson = ps.idperson and r.idrequests = do.requests_idrequests and LOWER(ps.inumber) = LOWER('".$tbiinumber."') and date in ".$sqldateslist;

		echo "<br><hr>".$counter;
		echo "<div id='condition-".$counter."'>".$sqlcondition.";</div>";
		echo "<div id='sql-".$counter."'>insert into requests (person_idperson, approved, sent, date_sent, date_approved) values ((Select idperson from person where LOWER(inumber) = LOWER('".$tbiinumber."')),1,1,NOW(),NOW());<br>";

		$sqldates = "insert into daysoff (requests_idrequests, person_idperson, date, midturn) values ";

		foreach($tbidates as $idx => $date){
			if($idx != 0) $sqldates .= ',';
			$sqldates .= "((SELECT max( idrequests ) FROM requests), (Select idperson from person where LOWER(inumber) = LOWER('".$tbiinumber."')), '".$date."', ".$tbimidturns[$idx].")" ;
		}
		$sqldates .= ';</div>';
		echo $sqldates;

		echo "<br><a class='btn' id='link-".$counter."'>update DB</a>";

		$counter++;
	}



?>