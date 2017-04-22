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
		$queryEnd = 'WHERE ';
		
		for($i=0; $i<sizeof($clusters)-1; $i++) {
			$queryEnd = $queryEnd . 'cluster =\''.$clusters[$i].'\' OR ';
		}
		$queryEnd = $queryEnd . 'cluster =\''.end($clusters).'\'';
		
		//GET AVERAGE MATCH DURATION IN EACH CLUSTER RANGE
		$queryStatement = 'SELECT duration FROM "ProfessionalMatches" '.$queryEnd;
		$query = $conn->query($queryStatement);
		$results = $query->fetchAll(PDO::FETCH_OBJ);

		$durations = json_decode(json_encode(($results)),true);
		$average_duration = 0;

		for ($i=0; $i<sizeof($durations); $i++) {
			$average_duration += $durations[$i]['duration'];
		}
		$average_duration = $average_duration/sizeof($durations);
		$average_duration_convert = gmdate("H:i:s", $average_duration);
		$duration_json = json_encode($average_duration_convert);
		//RETURN AVERAGE MATCH DURATION
		echo $duration_json;
		
		}
}catch (PDOException $e){
// report error message
echo $e->getMessage();
}
?>