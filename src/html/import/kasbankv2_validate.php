<?php
/*
 		Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2018/08/11 09:00:02 $
 		File Versie					: $Revision: 1.2 $

 		$Log: kasbankv2_validate.php,v $
 		Revision 1.2  2018/08/11 09:00:02  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2017/04/03 13:01:07  cvs
 		call 5406
 		
 		Revision 1.1  2014/11/05 12:52:58  cvs
 		*** empty log message ***

*/

//
// check bestaat er een rekening
//

function checkRekening($rekening,$valuta)
{
    global $row,$DB,$error,$_rekNr;

    $_rekNr = trim(ontnullen($rekening)).trim($valuta);

		$query = "SELECT id FROM Rekeningen WHERE consolidatie=0 AND Rekening = '".$_rekNr."' ";
		$DB->SQL($query);
    if (!$rekening = $DB->lookupRecord())
    {
      $error[] = "$row :Rekeningnummer komt niet voor Rekeningen tabel ($_rekNr)";
    }

}


// check of ISIN code voorkomt in fondsen tabel
//
function checkKasbankcode()
{
  global $row,$DB,$error, $data;
  $ISIN = $data[11];
  $VALUTA = $data[17];

  $query = "SELECT * FROM Fondsen WHERE ISINCode = '".$ISIN."' AND Valuta = '".$VALUTA."'";

  $DB->executeQuery($query);

 	if ($DB->records() > 1)
 	  $error[] = "$row :ISIN ($ISIN / $VALUTA) komt meer dan eens voor.";

 	if (!$fonds = $DB->nextRecord())
  	$error[] = "$row :ISIN ($ISIN / $VALUTA) komt niet voor in fonds tabel";
}

function validateCvsFile($filename)
{
	global $data, $error, $csvRegels,$prb,$row,$memRekening, $transactieCodes;
	$error = array();
	$DB = new DB();

	if (!$handle = @fopen($filename, "r"))
	{
		$error[] = "FOUT bestand $filename is niet leesbaar";
		return false;
	}
	$csvRegels = Count(file($filename));
	$prb->setLabelValue('txt1','Validatie van Kasbank bestand ('.$csvRegels.' records)');
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

    if (substr($data[1],0,2) == "20" AND ($data[35] == "+" OR $data[35] == "-"))
    {
      checkRekening($data[5],"MEM");
      checkKasbankcode();
      
      if (!$tcRec = getTAcode($data[31]) )
      {
        $error[] = "$row : <b> transactiecode ".$data[31]." bestaat niet in codetabel </b>";
      }
      else
      {
        if ($tcRec["doActie"] == "")
        {
          $error[] = "$row : <b> transactiecode ".$data[31]." (".$tcRec["omschrijving"].") actie niet gevuld in codetabel </b>";
        }
      }
    }
    else
    {
      $error[] = "$row : <b> Geen geldige Kasbank regel </b>";
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