<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/03/22 10:48:11 $
 		File Versie					: $Revision: 1.1 $

    Dit bestand maakt een cript aan voor het converteren van de tablenamen in de AIRS db nadat deze
    is gekopieerd van Windows naar een linux omgeving.
    
 		$Log: __makeDB_win2lin_ConvertScript.php,v $
 		Revision 1.1  2020/03/22 10:48:11  cvs
 		no message
 		
*/

 		
  include_once("wwwvars.php");
  $db=new DB();

  $filename = "__cnvDB_win2lin_".date("Y-m-d").".php";
  $content = '<?php
  // $disable_auth = true;
  include_once("wwwvars.php");
  $db=new DB();
  ';


  $q = "show tables;";
  $db->executeQuery($q);
  while($rec = $db->nextRecord())
  {
    $a[] = $rec["Tables_in_airs_productie"];
  }
  $content .= '$tables = array(
  "';
  $content .= implode('",
  "',$a);
  $content .= '");';
$content .= '

  foreach($tables as $table)
  {
    $query="RENAME TABLE `".strtolower($table)."` to `$table` ;";
    $db->SQL($query);
    $db->Query(); 
    echo "<li>".$table."  :: done :: ";
  }
  echo "script uitgevoerd";
      
  exit; 
?>';

  file_put_contents($filename, $content);
  
  echo "klaar script ".$filename." is aangemaakt";
?>