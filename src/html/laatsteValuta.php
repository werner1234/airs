<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2015/10/11 17:30:27 $
 		File Versie					: $Revision: 1.4 $
 		
 		$Log: laatsteValuta.php,v $
 		Revision 1.4  2015/10/11 17:30:27  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2014/02/08 17:43:33  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2008/05/16 08:08:44  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2006/12/14 10:51:23  rvv
 		*** empty log message ***
 		
 	
*/

include_once("wwwvars.php");

$totdatum = getLaatsteValutadatum();
$totdatum = db2jul($totdatum);
$totdatum = jul2form($totdatum);


?>
<html>
  <head>
    <title>
      Koersinformatie
    </title>
    <META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">
    <meta http-equiv="refresh" content="90">
    <link href="style/submenu.css" rel="stylesheet" type="text/css" media="screen">
    <script language="JavaScript" TYPE="text/javascript">
    </script>
  </head>
<body text="#000000"  marginwidth="0" marginheight="0" leftmargin="0" topmargin="0"  >
<?= vtb('Koersinformatie %s beschikbaar %s t/m %s', array('<br>', '<br>', $totdatum));?>

<?

//checkKeys();

?>
</body>
</html>

<?
/*
$object = new Fonds();
foreach ($object->data['fields'] as $key=>$value) 
{
	echo $key."\n";
}
*/
	function checkKeys()
	{
	  
  
	  global $__appvar,$USR;
	  $tmpTabel = array();
 	  $updateTabel = array();
 	  $tabellen = $__appvar['tabelObjecten'];
 	  $tabellen2 = $tabellen;
	
 	  foreach ($tabellen as $tabel)
	  {
	    $object = new $tabel;
	   $tabel = $object->data['table']; 
	    while (list($field, $fieldData) = each($object->data['fields']))
	    {
        switch ($field)
        {
          case "id":  
          case "add_date":
          case "add_user": 
          case "change_date": 
          case "change_user": 
          break;
          
          default:
           $tmpTabel[$tabel][$field]= $fieldData;  

         }	      
	    }
    }
    reset($tabellen);


 //   foreach ($tabellen as $tabel)
    while (list($tabel,$tabeldata) = each($tmpTabel))
	  {
	    $tabelOrg=$tabel;
	    $tabel=strtolower($tabel);
	    
       while (list($field,$data) = each($tabeldata) ) // for($i=0; $i<count($tmpTabel[$tabel]); $i++) 
       {
      
         if($data['key_field'])
           $resultaat[$tabel]['key']=$field;
 
         if($data['keyIn']) 
         {  
           $keyin = strtolower($data['keyIn']);
           $parts=explode(',',$keyin);
           echo $keyin.' ';
           foreach($parts as $keyin)
           {
             $txt=$tabelOrg.' -> '.$field;
             if($data['keyCondition'])
               $txt.=" (WHERE ".$data['keyCondition']." = '$keyin')";
              

            
             $resultaat[$keyin]['keyIn'][]=$txt;
           }
           
           
             
         }
           
       
       }
    }
    ksort($resultaat);

    
 listarray($resultaat);
    
	}


?>