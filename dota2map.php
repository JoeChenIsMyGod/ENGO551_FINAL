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

for ($i=0; $i<5; $i++) {
	$url = "https://api.steampowered.com/IDOTA2Match_570/GetMatchDetails/V001/?match_id=".$match_id[$i]."&key=".$key;
	$match_details_contents = file_get_contents($url);
	$match_details_contents = json_decode($match_details_contents, true);
	array_push($match_details, $match_details_contents);
}

//var_dump($match_details);
echo "First Blood Time: ";
print_r($match_details[0]['result']['first_blood_time']);
echo "<br>";
echo "Match Duration: ";
print_r($match_details[0]['result']['duration']);
echo "<br>";
$json = json_encode($match_details);
$json = json_decode($json);
//print_r ($json);

//code to get hero images
$url3 = 'https://api.steampowered.com/IEconDOTA2_570/GetHeroes/v0001/?key='.$key.'&language=en_us&format=JSON';
$hero_list = file_get_contents($url3);
$hero_list = json_decode($hero_list,true);

//var_dump($hero_list);

$hero_list_fixed = array();

for($i=0; $i<sizeof($hero_list['result']['heroes']); $i++) {
	$temp = array(
					"name" => "test",
					"id" => "test2",
					);
	$temp['name'] = substr($hero_list['result']['heroes'][$i]['name'], 14);
	$temp['id'] = $hero_list['result']['heroes'][$i]['id'];
	array_push($hero_list_fixed, $temp);
}

if (in_array("1", $hero_list_fixed[0])) {
	echo "found";
}
else {
	echo "not found";
}
//var_dump($hero_list_fixed);
// for ($i=0; $i<sizeof($hero_list_fixed); $i++) {
	// $image = 'http://cdn.dota2.com/apps/dota2/images/heroes/'.$hero_list_fixed[$i]['name'].'_sb.png';
	// $imagedata = base64_encode(file_get_contents($image));
	// echo '<img src="data:image/png;base64,'.$imagedata.'">';
// }


?>