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
		$clusters = $_POST["clusters"];
		$queryEnd = '';
		$leagueid = $_POST["leagueid"];
		
		for($i=0; $i<sizeof($clusters)-1; $i++) {
			$queryEnd = $queryEnd . 'cluster =\''.$clusters[$i].'\' OR ';
		}
		$queryEnd = $queryEnd . 'cluster =\''.end($clusters).'\'';
		
		/////////////////////////////////////////////////////////////////////////////////////////////////////
		//GET AVERAGE NUMBER OF KILLS AND GAME DURATION IN EACH CLUSTER RANGE AND LEAGUE ID
		$queryStatement = 'SELECT kills, duration FROM "ProfessionalMatches" WHERE leagueid = \''.$leagueid.'\' AND '.$queryEnd;
		$query = $conn->query($queryStatement);
		$results = $query->fetchAll(PDO::FETCH_OBJ);
		
		$kills = json_decode(json_encode(($results)),true);
				
		for ($i=0; $i< sizeof($kills); $i++) {
			$temp = str_replace("{", "", $kills[$i]['kills']);
			$temp2 = str_replace("}", "", $temp);
			$kills[$i]['kills'] = explode(',', $temp2);
		}
		
		$average = 0;
		$kills_per_min = 0;
		
		for ($i=0; $i<sizeof($kills); $i++) {
			$sum_kills = 0;
			$temp_duration = $kills[$i]['duration'];
			$temp_duration = $temp_duration/60;
			
			for ($j=0; $j<sizeof($kills[$i]['kills']); $j++) {
				$sum_kills += $kills[$i]['kills'][$j];
			}
			$kills_per_min += $sum_kills/$temp_duration;
		}
		
		$average = $kills_per_min/sizeof($kills);
		$average = number_format((float)$average, 2, '.', '');
		$average_json = json_encode($average);
		echo $average_json;
		
		
		}
}catch (PDOException $e){
// report error message
echo $e->getMessage();
}
?>