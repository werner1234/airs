<?php
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2010/04/29 09:38:28 $
 		File Versie					: $Revision: 1.11 $

 		$Log: rabo_BRIimport.php,v $
 		Revision 1.11  2010/04/29 09:38:28  cvs
 		Effectennota gewijzigd naar Eff.nota
 		
 		Revision 1.10  2009/07/08 15:10:57  cvs
 		*** empty log message ***

 		Revision 1.9  2009/06/24 13:00:13  cvs
 		*** empty log message ***

 		Revision 1.8  2009/06/24 12:40:12  cvs
 		*** empty log message ***

 		Revision 1.7  2009/05/28 12:37:15  cvs
 		*** empty log message ***

 		Revision 1.6  2009/05/06 10:33:34  cvs
 		*** empty log message ***

 		Revision 1.5  2009/05/06 10:24:38  cvs
 		*** empty log message ***

 		Revision 1.4  2009/05/06 10:16:00  cvs
 		*** empty log message ***

 		Revision 1.3  2009/04/29 14:26:34  cvs
 		bewaarloon meenemen

 		Revision 1.2  2009/04/08 12:39:14  cvs
 		*** empty log message ***

 		Revision 1.1  2009/04/08 11:24:13  cvs
 		*** empty log message ***



*/


  $handle = fopen($file, "r");
  $_tfile = explode("/",$file);
  $_file = $_tfile[count($_tfile)-1];
  if (!$handle = @fopen($file, "r"))
	{
		$error[] = "FOUT bestand $file is niet leesbaar";
		return false;
	}
	$csvRegels = Count(file($file));
	$prb->setLabelValue('txt1','inlezen BRI bestand ('.$csvRegels.' regels)');
  $pro_multiplier = 100/$csvRegels;
  $row = 0;
  $ndx = 0;
  while ($data = fgets($handle, 1000))
  {
    $row++;
  	$pro_step += $pro_multiplier;
    $prb->moveStep($pro_step);
    if (recordType() == 2)
    {
      $rawData[$ndx]["r2"] = $data;
      $rawData[$ndx]["row"] = $row;
      $data = fgets($handle, 1000);
      $row++;
      $omschrijving  = trim(substr($data,56,8));
      $omschrijving2 = trim(substr($data,88,10));

      if (  ( recordType() == 3 AND strtoupper($omschrijving) <> "EFF.NOTA" ) OR
            ( recordType() == 3 AND strtoupper($omschrijving) == "EFF.NOTA" AND strtoupper($omschrijving2) == "BEWAARLOON") )
      {
        $rawData[$ndx]["r3"] = $data;
        $ndx++;
      }
      else
        unset($rawData[$ndx]["r2"]);


    }

  }
  $prb->hide();
  fclose($handle);
//listarray($rawData);

  for ($x=0; $x <count($rawData);$x++)
  {
    $valuta = substr($rawData[$x]["r2"],10,3);
    $mr = array();
	  $mr[aktie]              = "BRI";
  	$mr[bestand]           = $_file;
	  $mr[regelnr]           = $rawData[$x]["row"];
	  $mr[Boekdatum]         = "20".substr($rawData[$x]["r2"],87,2)."-".substr($rawData[$x]["r2"],89,2)."-".substr($rawData[$x]["r2"],91,2);
	  $rekNr                 = intval(substr($rawData[$x]["r2"],0,10));
    $mr[Rekening]          = $rekNr.$valuta;
	  $mr[Omschrijving]      = trim(substr($rawData[$x]["r3"],56,32))." ".trim(substr($rawData[$x]["r3"],88,32));
	  $mr[Debet]             = 0;
    $mr[Credit]            = 0;
    $mr[Bedrag]            = 0;
		$mr[Transactietype]    = "";
		$mr[Verwerkt]          = 0;
		$mr[Memoriaalboeking]  = 0;
	  $mr[Valuta]            = $valuta;
	  if ($mr[Valutakoers] <> "EUR")
	  {
       $query = "SELECT Koers FROM Valutakoersen WHERE Valuta = '".$valuta."' AND Datum <= '".$mr[Boekdatum]."' ORDER BY Datum DESC LIMIT 1";
       $DBvk = new DB();
       $DBvk->SQL($query);
       $valutaKoersRec = $DBvk->lookupRecord();
       $mr[Valutakoers] = $valutaKoersRec["Koers"];
	  }
	  else
		  $mr[Valutakoers]       = 1;

    $soort = substr($rawData[$x]["r2"],86,1);
    $bewaarloon = trim(substr($rawData[$x]["r3"],88,32));
    if ( $bewaarloon == "Bewaarloon")
    {
      if ($soort == "D")
      {
        $mr[Grootboekrekening] = "BEW";
        $mr[Debet]             = substr($rawData[$x]["r2"],73,13)/100;
		    $mr[Bedrag]            = -1 * $mr[Debet];
		    }
      else
      {
        $mr[Grootboekrekening] = "BEW";
        $mr[Credit]             = substr($rawData[$x]["r2"],73,13)/100;
		    $mr[Bedrag]            = $mr[Credit];
      }
    }
    else
    {
      if ($soort == "D")
      {
        $mr[Grootboekrekening] = "ONTTR";
        $mr[Debet]             = substr($rawData[$x]["r2"],73,13)/100;
		    $mr[Bedrag]            = -1 * $mr[Debet];
      }
      else
      {
        $mr[Grootboekrekening] = "STORT";
        $mr[Credit]             = substr($rawData[$x]["r2"],73,13)/100;
		    $mr[Bedrag]            = $mr[Credit];
      }

		  //if (stristr(substr($rawData[$x]["r3"],56,32),"RENTE"))
		  //  $mr[Grootboekrekening] = "RENTE";

    }
    if ($mr[Bedrag]  <> 0)
		    $output[] = $mr;
  }

function recordType()
{
  global $data;
  return substr($data,23,1);
}




?>