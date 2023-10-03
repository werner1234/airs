<?php
/* 	
  AE-ICT source module
    Author                 : $Author: cvs $
    Laatste aanpassing     : $Date: 2012/03/09 09:23:28 $
    File Versie            : $Revision: 1.1 $
 		
    $Log: import_functies.php,v $
    Revision 1.1  2012/03/09 09:23:28  cvs
    *** empty log message ***

 		
 	
*/

function grootboekFromText($depotBank, $text,$defaultGrootboek="")
{
  $text = strtoupper($text);
  $grootboek = $defaultGrootboek;
  $db = new DB();
  $query = "SELECT * FROM importGrootboekToewijzing WHERE depotbank = '".$depotBank."' ";
  $db->executeQuery($query);
  while ($txtRec = $db->nextRecord())
  {
    $zoekTxt = strtoupper($txtRec["tekst"]);
    $zoekLen = strlen($zoekTxt);
    if ( substr( $text,0,$zoekLen )  == $zoekTxt ) 
      $grootboek = $txtRec["grootboek"];
  }
  return $grootboek;
}

?>