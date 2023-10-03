<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2019/06/07 11:56:49 $
 		File Versie					: $Revision: 1.1 $

 		$Log: reconV3_functies.php,v $
 		Revision 1.1  2019/06/07 11:56:49  cvs
 		call 7803
 		
 		Revision 1.2  2018/03/28 12:57:12  cvs
 		call 3503
 		
 		Revision 1.1  2014/08/06 12:34:09  cvs
 		*** empty log message ***
 		
*/

function stripBOM($field)
{
  $response = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $field);
  return $response;
}
function makeNumber($in)
{
  return str_replace(",", ".",trim($in));
}

function textPart($str, $start, $stop)
{
  $len = $stop - $start + 1;
  return trim(substr($str, $start-1,$len));      
}

function ontnullen($in)
{
  while (substr($in,0,1) == "0")
  {
    $in = substr($in,1);
  }
  return $in;
} 		



function listError($errorArray)
{
?>
<style>
.errorBox{
  margin-top:  15px;
  min-height: 120px;
  width: 400px;
  background: red;
  color: white;
}
.foutKop{
  background: #333;
  text-align: center;
  padding:5px;
  font-size: 1.5em;
}
</style>
<div class="errorBox">
<p class="foutKop">Fouten tijdens verwerken</p>
<ul>
<?
  for ($x=0; $x < count($errorArray); $x++)
  {
    echo "<li>".$errorArray[$x]."</li>";
  }
?>
</ul>
</div>
<?  
}
?>