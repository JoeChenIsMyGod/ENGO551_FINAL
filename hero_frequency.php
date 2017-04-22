<?php

function searcharray($value, $key, $array) {
   foreach ($array as $k => $val) {
       if ($val[$key] == $value) {
           return $k;
       }
   }
   return null;
}

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
		
		$clustermin = '221';
		$clustermax = '241';
		$queryEnd = 'WHERE ';
		
		for($i=0; $i<sizeof($clusters)-1; $i++) {
			$queryEnd = $queryEnd . 'cluster =\''.$clusters[$i].'\' OR ';
		}
		$queryEnd = $queryEnd . 'cluster =\''.end($clusters).'\'';
		
		////////////////////////////////////////////////////////////////////////////////////////////
		//GET MOST PICKED HEROES IN EACH CLUSTER RANGE
		$queryStatement = 'SELECT hero_id FROM "ProfessionalMatches" '.$queryEnd;
		$query = $conn->query($queryStatement);
		$results = $query->fetchAll(PDO::FETCH_OBJ);
		
		$hero_picks = json_decode(json_encode(($results)),true);
				
		for ($i=0; $i< sizeof($hero_picks); $i++) {
			$temp = str_replace("{", "", $hero_picks[$i]['hero_id']);
			$temp2 = str_replace("}", "", $temp);
			$hero_picks[$i]['hero_id'] = explode(',', $temp2);
		}
		
		$num_heroes = array_fill(0, 113, 0); 
		
		for ($i=0; $i< sizeof($hero_picks); $i++) {
			for ($j=0; $j< sizeof($hero_picks[$i]['hero_id']); $j++) {
				$num_heroes[$hero_picks[$i]['hero_id'][$j]-1] += 1;
			}
		}
		//sort array by most picked heroes
		$num_heroes2 = $num_heroes;
		arsort($num_heroes2, SORT_NUMERIC);
		$indexes = array_keys($num_heroes2);
		
		
		//code to get hero list
		$hero_url = 'https://api.steampowered.com/IEconDOTA2_570/GetHeroes/v0001/?key='.$key.'&language=en_us&format=JSON';
		$get_heroes = file_get_contents($hero_url);
		$get_heroes = json_decode($get_heroes,true);

		$hero_list = array();
		//create array to store list of heroes
		for($i=0; $i<sizeof($get_heroes['result']['heroes']); $i++) {
			$temp = array(
							"name" => "test",
							"id" => "test2",
							"localized_name" => "test3",
							);
			$temp['name'] = substr($get_heroes['result']['heroes'][$i]['name'], 14);
			$temp['id'] = $get_heroes['result']['heroes'][$i]['id'];
			$temp['localized_name'] = $get_heroes['result']['heroes'][$i]['localized_name'];
			array_push($hero_list, $temp);
		}
		
		$hero_pictures = array();
		
		for ($i=0; $i<9; $i++) {
			$id = searcharray($indexes[$i]+1,'id',$hero_list);
			$image = 'http://cdn.dota2.com/apps/dota2/images/heroes/'.$hero_list[$id]['name'].'_sb.png';
			$imagedata = base64_encode(file_get_contents($image));
			// echo $hero_list[$id]['localized_name'];
			// echo "<br>";
			// echo '<img src="data:image/png;base64,'.$imagedata.'">';
			// echo "<br>";
			array_push($hero_pictures, $hero_list[$id]['localized_name']);
			array_push($hero_pictures, '<img src="data:image/png;base64,'.$imagedata.'">');
		}
		
		$hero_pic_json = json_encode($hero_pictures);
		//RETURN HERO PICTURES
		echo $hero_pic_json;
		
		
		/////////////////////////////////////////////////////////////////////////////////////////////////////
		//GET AVERAGE NUMBER OF KILLS IN EACH CLUSTER RANGE
		$queryStatement4 = 'SELECT kills FROM "ProfessionalMatches" WHERE cluster BETWEEN \''.$clustermin.'\' AND \''.$clustermax.'\'';
		$query4 = $conn->query($queryStatement4);
		$results4 = $query4->fetchAll(PDO::FETCH_OBJ);
		
		// $kills = json_decode(json_encode(($results4)),true);
				
		// for ($i=0; $i< sizeof($hero_picks); $i++) {
			// $temp = str_replace("{", "", $hero_picks[$i]['hero_id']);
			// $temp2 = str_replace("}", "", $temp);
			// $hero_picks[$i]['hero_id'] = explode(',', $temp2);
		// }
		
	}
}catch (PDOException $e){
// report error message
echo $e->getMessage();
}
?>