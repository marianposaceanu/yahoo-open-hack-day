<?PHP

include("include/db.php");

$data = $_POST['data'];

if(isset($_GET['file'])){
	$fh = fopen($_GET['file'], 'r');
	$data = fread($fh, filesize($_GET['file']));
	fclose($fh);
}

$data_obj = json_decode($data);

//print_r($data_obj->results);

foreach($data_obj->results as $obj){

$obj->lat=$obj->geometry->location->lat;
$obj->long=$obj->geometry->location->lng;
$obj->types_str = ','.implode(',',$obj->types).',';

//echo 'ADDING:<pre>';
//print_r($obj);
//echo '<br/>==================================<br/>';
//die();

$sql = "insert into places (id,name,vicinity,icon,lat,lng,types_str) VALUES('".cleanQuery($obj->id)."','".cleanQuery($obj->name)."','".cleanQuery($obj->vicinity)."','".cleanQuery($obj->icon)."',".cleanQuery($obj->lat).",".cleanQuery($obj->long).",'".$obj->types_str."');";
//die($sql);
//echo $sql;
mysql_query($sql);

}
echo "DONEEEE";
?>