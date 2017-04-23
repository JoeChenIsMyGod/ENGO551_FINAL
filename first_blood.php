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
		$leagueid = $_POST["leagueid"];
		$queryEnd = '';
		
		for($i=0; $i<sizeof($clusters)-1; $i++) {
			$queryEnd = $queryEnd . 'cluster =\''.$clusters[$i].'\' OR ';
		}
		$queryEnd = $queryEnd . 'cluster =\''.end($clusters).'\'';
		
		//GET AVERAGE FIRST BLOOD TIME IN EACH CLUSTER RANGE
		$queryStatement = 'SELECT first_blood_time FROM "ProfessionalMatches" WHERE leagueid = \''.$leagueid.'\' AND '.$queryEnd;
		$query = $conn->query($queryStatement);
		$results = $query->fetchAll(PDO::FETCH_OBJ);
		
		$fb_times = json_decode(json_encode(($results)),true);
		$average_fb = 0;
		
		for ($i=0; $i<sizeof($fb_times); $i++) {
			$average_fb += $fb_times[$i]['first_blood_time'];
		}
		$average_fb = $average_fb/sizeof($fb_times);
		$average_fb_convert = gmdate("H:i:s", $average_fb);
		$fb_time_json = json_encode($average_fb_convert);
		//RETURN AVERAGE FIRST BLOOD TIME
		echo $fb_time_json;
		
		}
}catch (PDOException $e){
// report error message
echo $e->getMessage();
}
?>