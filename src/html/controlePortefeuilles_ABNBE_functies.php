<?php
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2011/04/26 07:55:54 $
 		File Versie					: $Revision: 1.4 $

 		$Log: controlePortefeuilles_ABNBE_functies.php,v $
 		Revision 1.4  2011/04/26 07:55:54  cvs
 		Aangepaste versie van Rouw en Ceulen opgehaald 26-4-2011
 		
 		Revision 1.2  2010/11/03 16:25:11  rvv
 		*** empty log message ***

 		Revision 1.1  2010/11/03 10:43:23  cvs
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

function getRekeningNr($port,$valuta)
{
  if ($valuta == "")
    return false;
  else
    return  $port.$valuta;
  //$DB = new DB();
  //$query = "SELECT Rekening FROM Rekeningen WHERE Portefeuille = '$port' AND Memoriaal = 0 AND Valuta='$valuta'";
  //$DB->SQL($query);
  //$record = $DB->lookupRecord();
  //return $record["Rekening"];
}

function convertFixedLine($rawData,$debug=false)
{
  $data[1] = textPart($rawData,1,15);
  if ($data[1] == "SECURITYPOS")
  {
    $data[3] = textPart($rawData,21,55);                // portefeuille
    $data[5] = ontnullen(textPart($rawData,60,79));    // Fondscode
    $data[7] = textPart($rawData,84,101);               // aantal stukken
    $data[7] = str_replace(",",".",$data[7]);
  }
  else
  {
    $data[5]  = textPart($rawData,60,77);               // saldo
    $data[6]  = textPart($rawData,33,35);               // valuta
    $data[11] = textPart($rawData,21,32);               // RekeneningNr
  }
  if ($debug)
    listarray($data);
  return $data;
}

function validateFile($filename)
{
  global $error, $csvRegels,$prb,$row;
  $error = array();
  $DB = new DB();

  if (!$handle = @fopen($filename, "r"))
  {
	$error[] = "FOUT bestand $filename is niet leesbaar";
	return false;
  }
  $csvRegels = Count(file($filename));
  $prb->setLabelValue('txt1','Validatie van CSV bestand ('.$csvRegels.' records)');
  $pro_multiplier = 100/$csvRegels;
  $prb->hide();
  $row = 0;
  while (!feof($handle))
  {
    $dataRaw = fgets($handle, 4096);
    if (trim($dataRaw) == "") continue;

    $data = convertFixedLine($dataRaw);

    $row++;
    $pro_step += $pro_multiplier;
    $prb->moveStep($pro_step);

    if($data[1] == "SECURITYPOS") //Transacties
    {
      if(isNumeric($data[3]))
      $portefeuille = intval($data[3]);

      checkPortefeuille($portefeuille);
      checkSNScode($data[5]);

    }
    elseif  ($data[1] == "000000000000000") //Mutaties
    {
      checkRekening($data[11],$data[6]);
    }
    else
    {
      $error[] = "$row : <b> Eerste kollom bevat geen SECURITYPOS of CASHPOS. (Verkeerde bestandsindeling?) </b>";
    }
  }

  fclose($handle);
  if (Count($error) == 0)
    return true;
  else
  {
    return false;
  }
}



?>