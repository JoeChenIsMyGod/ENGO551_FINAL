<?php
$key='713000CA799F87C7B69F32DB26591D94';
$url = "https://api.steampowered.com/IDOTA2Match_570/GetMatchHistory/V001/?key=".$key;
$matches = file_get_contents($url);
$matches = json_decode($matches);
//var_dump($matches);
//echo $matches->result->matches[0]->match_id;
$match_id = array();
for ($i=0; $i< sizeof($matches->result->matches); $i++) {
	array_push($match_id, $matches->result->matches[$i]->match_id);
	// echo $matches->result->matches[$i]->match_id; 
	// echo "<br>";
}

//$url = "https://api.steampowered.com/IDOTA2Match_570/GetMatchDetails/V001/?match_id=".$match_id[0]."&key=".$key;
//$match_details = file_get_contents($url);
$match_details = array();
//var_dump($match_details);

for ($i=0; $i<sizeof($match_id); $i++) {
	$url = "https://api.steampowered.com/IDOTA2Match_570/GetMatchDetails/V001/?match_id=".$match_id[$i]."&key=".$key;
	$match_details_contents = file_get_contents($url);
	array_push($match_details, $match_details_contents);
}
//var_dump($match_details);
$json = json_encode($match_details);
echo $json;
?>