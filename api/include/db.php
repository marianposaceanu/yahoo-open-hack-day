<?PHP

function cleanQuery($string)
{
  if(get_magic_quotes_gpc())  // prevents duplicate backslashes
  {
    $string = stripslashes($string);
  }
  if (phpversion() >= '4.3.0')
  {
    $string = mysql_real_escape_string($string);
  } 
  else 
  { 
    $string = mysql_escape_string($string);
  }
  return $string;
}
 
$link=mysql_connect('localhost','tg','tg2011');
   if (!$link)
   {  
      return false;
   } 
   $select=mysql_select_db('tg',$link);
   if (!$select)
   {
      echo mysql_error();
   }


?>