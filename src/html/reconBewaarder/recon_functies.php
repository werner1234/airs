<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2017/09/20 06:21:04 $
 		File Versie					: $Revision: 1.1 $

 		$Log: recon_functies.php,v $
 		Revision 1.1  2017/09/20 06:21:04  cvs
 		megaupdate 2722
 		
 		Revision 1.1  2014/08/06 12:34:09  cvs
 		*** empty log message ***
 		
*/

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
    .errorBox {
      margin-top: 15px;
      min-height: 120px;
      width: 400px;
      background: red;
      color: white;
    }

    .foutKop {
      background: #333;
      text-align: center;
      padding: 5px;
      font-size: 1.5em;
    }
  </style>
  <div class="errorBox">
    <p class="foutKop">Fouten tijdens verwerken</p>
    <ul>
      <?
      for ($x = 0; $x < count($errorArray); $x++)
      {
        echo "<li>" . $errorArray[$x] . "</li>";
      }
      ?>
    </ul>
  </div>
  <?
}
?>