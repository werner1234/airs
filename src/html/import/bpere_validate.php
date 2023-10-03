<?php
/*
 		Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2018/08/11 09:00:02 $
 		File Versie					: $Revision: 1.6 $

 		$Log: bpere_validate.php,v $
 		Revision 1.6  2018/08/11 09:00:02  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2012/04/25 09:58:18  cvs
 		*** empty log message ***
 		
 		Revision 1.4  2010/11/12 10:10:22  cvs
 		*** empty log message ***
 		
 		Revision 1.3  2010/11/03 11:04:28  cvs
 		*** empty log message ***
 		
 		Revision 1.2  2010/10/12 14:18:11  cvs
 		*** empty log message ***
 		
 		Revision 1.1  2010/09/21 11:36:08  cvs
 		*** empty log message ***
 		





*/


function checkPortefeuille($portefeuille)
{
    global $row,$DB,$error;
	  $query = "SELECT id FROM Portefeuilles WHERE consolidatie=0 AND Portefeuille = '".$portefeuille."' ";
		$DB->SQL($query);
    if (!$p = $DB->lookupRecord())
       $error[] = "$row :Geen Portefeuille ($portefeuille) gevonden ";
}

function checkRekening($rekening,$valuta)
{
    global $row,$DB,$error,$_rekNr;

   	$_rekNr = trim($rekening).trim($valuta);

		$query = "SELECT id FROM Rekeningen WHERE consolidatie=0 AND Rekening = '".$_rekNr."' ";
		$DB->SQL($query);
    if (!$rekening = $DB->lookupRecord())
      $error[] = "$row :Rekeningnummer komt niet voor Rekeningen tabel ($_rekNr)";
}

function validateCvsFile($filename)
{
	global $error, $csvRegels,$prb, $transactieCodes;
  foreach ($transactieCodes as $key => $value) 
  {
    $_transactieArray[] = $key;
  }
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
  $row = 0;
  while ($data = fgetcsv($handle, 1000, ","))
  {
    $row++;
  	$pro_step += $pro_multiplier;
    $prb->moveStep($pro_step);
    
    if ($data[0]=="") continue;
    if ($data[0]=="Account number") continue;
    
// BEGIN insert leeg veld in $data[0] om veldnummering gelijk te maken aan de documentatie
	 $data = array_reverse($data);
	 $data[] = "leeg";
	 $data = array_reverse($data);
// EINDE insert leeg veld in $data[0] om veldnummering gelijk te maken aan de documentatie
   
   $data = trimRecord($data);
   
   if(count($data) > 17) //Transacties
    {
      if (!in_array($data[4],$_transactieArray))
        $error[] = "$row :ongeldige transactiecode (".$data[4].")"; 
      
      
      //
      // check veld 9 numeriek en >= 0
      //
  	  $_code = trim($data[9]);
 	    if (!(is_numeric($_code) AND $_code >= 0))
  	   	$error[] = "$row :veld 9 bevat een ongeldige waarde ($_code)";

      
      if(isNumeric($data[1]))
        $portefeuille = $data[1];
       
      checkPortefeuille($portefeuille);
     
      $fondsValuta = (trim($data[11]) == "BRL")?"USD":$data[11];
      
      checkRekening($data[1],$fondsValuta);
     
      // controle veld 4 = 403
      //
      if ($data[4] == "403")
      {
        checkRekening($data[1],"DEP");
      }
     
     
       
      //
      // check of ISIN code voorkomt in fondsen tabel
      //

  	  if ($data[6])
      {
        $isinNotFound = true;
        $fonds = array();
        $isinCode = $data[6];
 
    	  if (trim($isinCode) <> "" )
        {
          $query = "SELECT * FROM Fondsen WHERE ISINCode = '".$isinCode."' ";
          $DB->SQL($query);
          if (!$fonds = $DB->lookupRecord())  
            $error[] = "$row :ISIN  code komt niet voor fonds tabel (".$isinCode.")";
        }
      }
    }
    else  //geldmutaties 131
    {
      if ($data[14] <> ""    AND 
          $data[14] <> "REM" AND
          $data[14] <> "RET"     ) continue;
      
      //
      // check veld 4 numeriek en >= 0
      //
  	  
      $_code = trim($data[4]);
 	    if (!(is_numeric($_code) AND $_code >= 0))
  	   	$error[] = "$row :veld 4 bevat een ongeldige waarde ($_code)";
      
      checkRekening($data[1],$data[2]);
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