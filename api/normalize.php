<?PHP

include("include/db.php");


$sql = "select id,panoramio_cnt,flickr_cnt,twitpic_cnt,foursquare_cnt,gowalla_cnt from places;";
echo $sql;
$result=mysql_query($sql);

while($row = mysql_fetch_array($result)){
	$normalized = (int)(min($row['panoramio_cnt'],100)/1+min($row['flickr_cnt'],300)/3+min($row['foursquare_cnt'],500)/5+min($row['gowalla_cnt'],500)/5)/4; //4*5
	$sql = "update places set total_cnt=".$normalized." where id='".$row['id']."' LIMIT 1;";
	echo '::'.$sql.'::<br/>';
	mysql_query($sql);
}

?>