<?php
$host='localhost';
$db = 'dotamap';
$username = 'postgres';
$password = 'admin';
$dsn =
"pgsql:host=$host;port=5432;dbname=$db;user=$username;password=$password";
try{
// create a PostgreSQL database connection
$conn = new PDO($dsn);
// display a message if connected to the PostgreSQL successfully
if($conn){

//Get the data from the API
$key='713000CA799F87C7B69F32DB26591D94';
//$url = "https://api.steampowered.com/IDOTA2Match_570/GetMatchHistory/V001/?key=".$key;

$HistoryFlag = true;
$match_id = array();
$last_match_id = "";
while($HistoryFlag)
{
	$url = "https://api.steampowered.com/IDOTA2Match_570/GetMatchHistory/V001/?league_id=4479&matches_requested=300&start_at_match_id=".$last_match_id."&key=".$key;
	$matches = file_get_contents($url);
	$matches = json_decode($matches);
	
	for ($i=0; $i< sizeof($matches->result->matches); $i++) 
	{
		array_push($match_id, $matches->result->matches[$i]->match_id);
	}
	$last_match_id = end($match_id);
	if($matches->result->results_remaining == 0)
	{
		$HistoryFlag = false;
	}
}

$match_details = array();
for ($i=0; $i<sizeof($match_id); $i++) {
	$url = "https://api.steampowered.com/IDOTA2Match_570/GetMatchDetails/V001/?match_id=".$match_id[$i]."&key=".$key;
	//$url = "https://api.steampowered.com/IDOTA2Match_570/GetMatchDetails/V001/?match_id=".$match_id."&key=".$key;
	$match_details_contents = file_get_contents($url);
	array_push($match_details, $match_details_contents);
}

for ($i=0; $i<sizeof($match_details); $i++) 
{	
	$json = json_decode($match_details[$i]);
	if($json->result->game_mode == 22 || $json->result->game_mode == 2)
	{
		if($json->result->lobby_type == 2 || $json->result->lobby_type == 7 || $json->result->lobby_type == 5 || $json->result->lobby_type == 6 || $json->result->lobby_type == 1)
		{
			$flag = true;
			for ($j = 0; $j < sizeof($json->result->players); $j++)
			{
				if($json->result->players[$j]->leaver_status != 0 && $json->result->players[$j]->leaver_status != 1)
				{
					$flag = false;
				}
			}
			if($flag)
			{				
				$player_slot = array();
				$hero_id = array();
				$kills = array();
				$deaths = array();
				$assists = array();
				$kd_ratio = array();
				$leaver_status = array();
				$last_hits = array();
				$denies = array();
				$gold_per_min = array();
				$xp_per_min = array();
				
				$querystatement = 'INSERT INTO "ProfessionalMatches" (match_id, player_slot, hero_id, start_time, kills, deaths, assists, kd_ratio, leaver_status, last_hits, denies, gold_per_min, xp_per_min, radiant_win, duration, cluster, first_blood_time, lobby_type, leagueid, game_mode)'
								. ' VALUES ('
								. $json->result->match_id . ',';
								
				//Create arrays containing player specific data
				for ($k = 0; $k < sizeof($json->result->players); $k++)
				{
					array_push($player_slot, $json->result->players[$k]->player_slot);
					array_push($hero_id, $json->result->players[$k]->hero_id);
					array_push($kills, $json->result->players[$k]->kills);
					array_push($deaths, $json->result->players[$k]->deaths);
					$temp_death = $json->result->players[$k]->deaths;
					//Prevent a zero division for kd_ratio
					if($temp_death == 0)
					{
						$temp_death = 1;
					}
					$temp_kd = $json->result->players[$k]->kills / $temp_death;
					array_push($assists, $json->result->players[$k]->assists);
					array_push($kd_ratio, $temp_kd);
					array_push($leaver_status, $json->result->players[$k]->leaver_status);
					array_push($last_hits, $json->result->players[$k]->last_hits);
					array_push($denies, $json->result->players[$k]->denies);
					array_push($gold_per_min, $json->result->players[$k]->gold_per_min);
					array_push($xp_per_min, $json->result->players[$k]->xp_per_min);
				}

				$player_slot_implode = implode(",", $player_slot);
				$hero_id_implode = implode(",", $hero_id);
				$kills_implode = implode(",", $kills);
				$deaths_implode = implode(",", $deaths);
				$assists_implode = implode(",", $assists);
				$kd_ratio_implode = implode(",", $kd_ratio);
				$leaver_status_implode = implode(",", $leaver_status);
				$last_hits_implode = implode(",", $last_hits);
				$denies_implode = implode(",", $denies);
				$gold_per_min_implode = implode(",", $gold_per_min);
				$xp_per_min_implode = implode(",", $xp_per_min);
				$converted_rad_win = ($json->result->radiant_win) ? 'true' : 'false';
				
				$querystatement = $querystatement
								.  '\'{' . $player_slot_implode   . '}\'' . ','
								.  '\'{' . $hero_id_implode       . '}\'' . ','
								.  $json->result->start_time      . ',' 
								.  '\'{' . $kills_implode         . '}\'' . ','
								.  '\'{' . $deaths_implode        . '}\'' . ','
								.  '\'{' . $assists_implode       . '}\'' . ','
								.  '\'{' . $kd_ratio_implode      . '}\'' . ','
								.  '\'{' . $leaver_status_implode . '}\'' . ','
								.  '\'{' . $last_hits_implode     . '}\'' . ','
								.  '\'{' . $denies_implode        . '}\'' . ','
								.  '\'{' . $gold_per_min_implode  . '}\'' . ','
								.  '\'{' . $xp_per_min_implode    . '}\'' . ','
								.  $converted_rad_win . ','
								.  $json->result->duration . ','
								.  $json->result->cluster . ','
								.  $json->result->first_blood_time . ','
								.  $json->result->lobby_type . ','
								.  $json->result->leagueid . ','
								.  $json->result->game_mode . ')';
				$query = $conn->query($querystatement);
				
			}
		}
	}
	
}
}
}catch (PDOException $e){
// report error message
echo($e->getMessage());
} ?>