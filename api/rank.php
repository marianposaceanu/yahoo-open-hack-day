<?PHP

include("include/db.php");
include ("include/GowallaPHP.php");

function rank_flickr($lat, $long, $radius=0.5) {
		$yql_base_url = "http://query.yahooapis.com/v1/public/yql";
		$yql_query = 'select * from flickr.photos.search(10000) where has_geo="true" and (lat, lon) in ('.$lat.', '.$long.') and radius = '.$radius.'';
		$yql_query_url = $yql_base_url . "?q=" . urlencode($yql_query) . "&format=json";
		//echo($yql_query_url);
		$session = curl_init($yql_query_url);  
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);  
		$json = curl_exec($session);  
		// Convert JSON to PHP object   
		$phpObj =  json_decode($json);
		
		if(!is_null($phpObj->query->results)){  
			// Parse results and extract data to display  
			return count($phpObj->query->results->photo);
		}
		else return 0;
}


function rank_foursquare($lat, $long, $dist=100){
	$jsonurl = "https://api.foursquare.com/v2/venues/search?llAcc=1&ll=".$lat.",".$long."&intent=checkin&limit=3&oauth_token=WUTIMNXQIO4TP3PCJT5NG5JQB5CO4OKCZUWTYP33KE1EO4UK";
	//echo $jsonurl;
	$json = file_get_contents($jsonurl,0,null,null);
	$json_output = json_decode($json);
	
	//print_r($json_output->response);die();
	$count = 0;
	foreach($json_output->response->groups as $group){
		if($group->type=='nearby'){
			foreach($group->items as $obj){
				if($obj->location->distance<=$dist) $count += $obj->stats->usersCount;
			}
		}
	}
	return $count;
}



function rank_gowalla($lat, $long, $dist=160){
	/*$jsonurl = "http://api.gowalla.com/spots?lat=".$lat."&lng=".$long."&radius=".$dist;
	echo $jsonurl;
	$json = file_get_contents($jsonurl,0,null,null);
	$json_output = json_decode($json);
	print_r($json_output);die();
	*/
	
	$myGowalla = new GowallaPHP('fa574894bddc43aa96c556eb457b4009');
	$json_output = $myGowalla->get_spots($lat,$long,$dist);
	
	$count = 0;
	foreach($json_output->spots as $spot){
		$count += $spot->users_count + $spot->photos_count * 2;
	}
	//echo 'DADA:'.$count;
	//die();
	return $count;
}


function rank_panoramio($lat, $long, $radius=0.0005) {
		$query_url = 'http://www.panoramio.com/map/get_panoramas.php?set=public&from=0&to=20&minx='. ($long-$radius). '&miny='. ($lat-$radius) .'&maxx='.($long+$radius).'&maxy='.($lat+$radius).'';
		
		$session = curl_init($query_url);  
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);  
		$json = curl_exec($session); 		
		// Convert JSON to PHP object   
		$phpObj =  json_decode($json);
		
		 if(!is_null($phpObj)){  
			// Parse results and extract data to display  
			//echo $phpObj->count;die();
			return $phpObj->count;
		}
		else
			return 0;
	}




$sleep = empty($_GET['sleep'])?0.5:$_GET['sleep'];
$count = empty($_GET['count'])?100:$_GET['count'];
$type = empty($_GET['type'])?'foursquare':$_GET['type'];

echo $sleep.' '.$type.' '.$count;
//die();

$sql = "select id,lat,lng,name from places where ".$type."_cnt=-1 LIMIT ".$count.";";
echo $sql;
$result=mysql_query($sql);

while($row = mysql_fetch_array($result)){
	//echo '<pre>';
	//print_r($row);
	$count = 0;
	switch($type){
		case 'flickr': $count = rank_flickr($row['lat'],$row['lng'],1); break;
		case 'foursquare': $count = rank_foursquare($row['lat'],$row['lng']); break;
		case 'panoramio': $count = rank_panoramio($row['lat'],$row['lng']); break;
		case 'gowalla': $count = rank_gowalla($row['lat'],$row['lng']); break;
		default: break;
	}
	
	$sql = "update places set ".$type."_cnt=".$count." where id='".$row['id']."' LIMIT 1;";
	//echo '::'.$sql.'::<br/>';
	mysql_query($sql);
	sleep($sleep);
}

?>