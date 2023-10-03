<?php
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/07/10 07:49:38 $
 		File Versie					: $Revision: 1.22 $


*/
//
// check bestaat $portefeuille
//
function checkPortefeuille($portefeuille)
{
    global $row,$error;
    $DB = new DB();
	  $query = "SELECT id FROM Portefeuilles WHERE consolidatie=0 AND Portefeuille = '".$portefeuille."' ";
		$DB->SQL($query);
    if (!$p = $DB->lookupRecord())
       $error[] = "$row :Geen Portefeuille ($portefeuille) gevonden ";
}
//
// check bestaat er een rekening
//
function checkRekening($rekening,$valuta="")
{
  global $row,$error,$_rekNr;
  $db = new DB();
  
  $_rekNr = trim($rekening).trim($valuta);

	$query = "SELECT id FROM Rekeningen WHERE consolidatie=0 AND Rekening = '".$_rekNr."' ";
//    debug($query);
	$rekening = $db->lookupRecordByQuery($query);
  if (!isset($rekening["id"])) 
  {
   $error[] = "$row :Rekeningnummer komt niet voor Rekeningen tabel ($_rekNr)";
   return false;
  }
  return true;
  //debug($rekening);
  //debug($error);
}

function checkRekeningNrMEM($rekening)
{
  
  $DB = new DB();
  $port = trim($rekening);
  $query = "SELECT Portefeuille FROM Rekeningen WHERE consolidatie=0 AND Rekening = '".$port."MEM' ";
  $DB->SQL($query);
  if ($record = $DB->lookupRecord())
  {
    $query = "SELECT Rekening FROM Rekeningen WHERE consolidatie=0 AND Portefeuille = '".$record["Portefeuille"]."' AND Memoriaal = 1  AND Inactief = 0 ";
    $DB->SQL($query);
    if ($record = $DB->lookupRecord())
      return true;
  }
  
  return false;
}

//
// check of ISIN code voorkomt in fondsen tabel
//
function checkAABCode($code)
{
  global $row,$error;

  $DB = new DB();

  if (strlen(trim($code)) == 12)
  {
    return true;
  }
 	if (trim($code) == "")
  {
    $error[] = "$row :AAB BE code is niet gevuld.";
  }
  else
  {
    $query = "SELECT * FROM Fondsen WHERE aabbeCode = '".$code."' ";
 	  $DB->SQL($query);
 	  $DB->query();

 	  if ($DB->records() > 1)
 	    $error[] = "$row :AAB BE code ($code) komt meer dan eens voor.";

 	  if (!$fonds = $DB->nextRecord())
  	 $error[] = "$row:AAB BE code ($code) komt niet voor in fonds tabel";
   }  
}




function validateCvsFile($filename)
{
	global $error, $csvRegels,$prb,$row, $transactieCodes, $_transactieArray, $checkArray;

  $DB = new DB();

  $DB->executeQuery("SELECT * FROM AABBETransactieCodes ORDER BY bankCode");

  $transactieCodes  = array();
  $_transactieArray = array();

  while ($codeRec = $DB->nextRecord())
  {
    $transactieCodes[$codeRec["bankCode"]] = $codeRec["doActie"];
    $_transactieArray[] = $codeRec["bankCode"];
  }
	//$error = array();
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
//  debug($_transactieArray);
  while (!feof($handle))
  {
    $dataRaw = fgets($handle, 4096);
    if (trim($dataRaw) == "") continue;

    $data = convertFixedLine($dataRaw);
    //listarray($data);

    $row++;
//    debug($data,$row);
  	$pro_step += $pro_multiplier;
    $prb->moveStep($pro_step);

    if($data[1] == "SECURITYTRANS") //Transacties
    {


      //checkRekening(trim($data[6]),$data[22]);
//      if (trim($checkArray[$data[3]]) == "")
//      {
//        if (!checkRekeningNrMEM($data[6]))
//        {
//          $error[] = "$row :".$data[6]."MEM bij transactie :".$data[3]." niet gevonden";
//          $checkArray[$data[3]] = "GEEN REKENING";
//        }
//      }

      checkAABCode($data[19]);

      if (!in_array($data[8], $_transactieArray))
      {
        $error[] = "$row :Onbekende transactiecode: ".$data[8]." ";
      }

    }
    elseif  ($data[1] == "CASHTRANS") //Mutaties
    {
      if ($data[55] == "0002") // call 10426 prefix L- bij leningen
      {
        $data[40] = "L-".$data[40];
      }
      if (in_array($data[41],$_transactieArray)) continue;  // alleen onbekende transactie codes verwerken
      $valCode = (is_numeric($data[40]))?$valCode = $data[45]:"";
      checkRekening($data[40],$valCode);
    }
    else
    {
      $error[] = "$row : <b> Eerste kollom bevat geen SECURITYTRANS of CASHTRANS. (Verkeerde bestandsindeling?) </b>";
    }


  }
//  debug($error);
  fclose($handle);
  if (Count($error) == 0)
  	return true;
  else
  {
  	return false;
  }

}


