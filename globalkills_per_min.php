<?php
$key='713000CA799F87C7B69F32DB26591D94';
$host='localhost';
$db = 'dotamap';
$username = 'postgres';
$password = 'admin';
$dsn =
"pgsql:host=$host;port=5432;dbname=$db;user=$username;password=$password";
try{
// create a PostgreSQL database connection
$conn = new PDO($dsn);
	if($conn){
		
		$leagueid = $_POST["leagueid"];
		
		/////////////////////////////////////////////////////////////////////////////////////////////////////
		//GET AVERAGE NUMBER OF KILLS AND GAME DURATION IN EACH LEAGUE ID
		$queryStatement = 'SELECT kills, duration FROM "ProfessionalMatches" WHERE leagueid = \''.$leagueid.'\'';
		$query = $conn->query($queryStatement);
		$results = $query->fetchAll(PDO::FETCH_OBJ);
		
		$global_kills = json_decode(json_encode(($results)),true);
				
		for ($i=0; $i< sizeof($global_kills); $i++) {
			$temp = str_replace("{", "", $global_kills[$i]['kills']);
			$temp2 = str_replace("}", "", $temp);
			$global_kills[$i]['kills'] = explode(',', $temp2);
		}
		
		$global_average = 0;
		$kills_per_min = 0;
		
		for ($i=0; $i<sizeof($global_kills); $i++) {
			$sum_kills = 0;
			$temp_duration = $global_kills[$i]['duration'];
			$temp_duration = $temp_duration/60;
			
			for ($j=0; $j<sizeof($global_kills[$i]['kills']); $j++) {
				$sum_kills += $global_kills[$i]['kills'][$j];
			}
			$kills_per_min += $sum_kills/$temp_duration;
		}
		
		$global_average = $kills_per_min/sizeof($global_kills);
		$global_average = number_format((float)$global_average, 2, '.', '');
		$global_average_json = json_encode($global_average);
		echo $global_average_json;
		
		}
}catch (PDOException $e){
// report error message
echo $e->getMessage();
}
?>