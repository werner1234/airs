<?php
/*
 		Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2018/08/11 09:00:02 $
 		File Versie					: $Revision: 1.9 $

 		$Log: degiro_validate.php,v $
 		Revision 1.9  2018/08/11 09:00:02  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2017/11/20 14:13:29  cvs
 		rekeningAdd
 		
 		Revision 1.7  2017/10/16 12:26:39  cvs
 		call 6170
 		
 		Revision 1.6  2017/09/22 14:31:32  cvs
 		call 6205
 		
 		Revision 1.5  2016/12/13 12:19:05  cvs
 		aanpassing import bestandsindeling
 		
 		Revision 1.4  2015/12/01 09:02:21  cvs
 		*** empty log message ***
 		
 		Revision 1.3  2015/07/01 14:07:15  cvs
 		*** empty log message ***
 		
 		Revision 1.2  2015/06/22 09:05:46  cvs
 		*** empty log message ***
 		
 		Revision 1.1  2015/06/03 13:25:48  cvs
 		*** empty log message ***
 		


*/

function giroCheckRekening($rekeningNr)
{
  global $error,$row;
  $db = new DB();
  $query = "SELECT id FROM Rekeningen WHERE consolidatie=0 AND Rekening = '".$rekeningNr."' AND DepotBank = 'GIRO' ";
	
  return ($rekening = $db->lookupRecordByQuery($query));
  
}



function validateCvsFile($file,$soort)
{
	global $error, $csvRegels,$prb,$row,$memRekening,$rekeningAddArray;
  $DB = new DB();
  $row = 0;
  $handle = fopen($file, "r");
  $pro_multiplier = (100/$csvRegels);
  $_tfile = explode("/",$file);
  $_file = $_tfile[count($_tfile)-1];
  $skipped = "";
  while ($data = fgetcsv($handle, 1000, ","))
  {
    if ($row == 0)
    {
      
      $cashFile = ($data[0] == "id");   
      $secrFile = ($data[0]  == "transaction_id");
      
      if ($soort == "STRA")
      {
        if (!$secrFile)   return false;
      }
      else
      {
        if (!$cashFile)   return false;        
      }
      
      
      $row = 1;
      continue;
    }
    
    
    
    
    $row++;
    
    $data = array_reverse($data);
	  $data[] = "leeg";
	  $data = array_reverse($data);
    if ($cashFile)
    {
      
      $_rekNr = $data[3].$data[6];
      if (!getRekening($_rekNr) )
      {
        $error[] = "$row :CTRA: Rekeningnummer komt niet voor ($_rekNr icm depotbank)";
        addToRekeningAdd($data[3], $data[6]);
      }  
      
      if ($data[13] == "" AND ($data[14] == "" AND $data[15]=="" ) )
      {
         // get fonds dus check overbodig        
      }
      else
      {
        
        if ($data[5] <> "3040" AND
            $data[5] <> "3020" AND
            $data[5] <> "6104")
        {  
          if (!giroCheckFonds($data[13],$data[14],$data[15]) )
          {
            $error[] = "$row :CTRA: Fondscode komt niet voor fonds tabel (".$data[13]. " / ".$data[14]." icm ".$data[15].")";
          }
        }
      }
      
    }
    else  // transacties
    {
      $_rekNr = $data[3].$data[12];
      if (!getRekening($_rekNr) )
      {
        $error[] = "$row :STRA: Rekeningnummer komt niet voor ($_rekNr icm depotbank)";
        addToRekeningAdd($data[3], $data[12]);
      } 
      if (!giroCheckFonds($data[8],$data[10],$data[12]) )
      {
          $error[] = "$row :STRA: Fondscode komt niet voor fonds tabel (".$data[8]. " / ".$data[10]." icm ".$data[12].")";
      }
      // fondscheck?
    }
    if ($VB == "")
    {
      if ($pRec = $DB->lookupRecordByQuery("SELECT Vermogensbeheerder FROM Portefeuilles WHERE consolidatie=0 AND Portefeuille = '".$data[3]."' "))
      {
        $VB = $pRec["Vermogensbeheerder"];
      }
    }
  }
  $_SESSION["VB"] = $VB;

  if (count($rekeningAddArray) > 0)
  {
    $_SESSION["rekeningAddArray"] = $rekeningAddArray;
  }
  else
  {
    $_SESSION["rekeningAddArray"] = array();
  }
  return true;
  //return (Count($error) == 0);
  // TODO: validatie nog inbouwen
   
  
}


?>