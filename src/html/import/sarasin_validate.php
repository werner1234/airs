<?php
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/07/13 14:53:11 $
 		File Versie					: $Revision: 1.7 $

 		$Log: hhb_validate.php,v $
 		Revision 1.7  2020/07/13 14:53:11  cvs
 		call 8518
 		
 		Revision 1.6  2020/03/04 09:35:15  cvs
 		call 8025
 		
 		Revision 1.5  2020/01/24 11:25:47  cvs
 		call 8025
 		
 		Revision 1.4  2019/12/09 10:16:14  cvs
 		call 8025
 		
 		Revision 1.3  2019/11/20 15:51:21  cvs
 		call 8025
 		
 		Revision 1.2  2019/10/09 12:45:43  cvs
 		call 8025
 		
 		Revision 1.1  2018/05/07 08:32:38  cvs
 		call 6620
 		
 		Revision 1.3  2017/04/03 12:14:31  cvs
 		call 5174
 		
 		Revision 1.2  2016/07/01 14:36:48  cvs
 		call 5005
 		
 		Revision 1.1  2016/03/25 10:41:08  cvs
 		call 3691
 		
 		Revision 1.1  2015/05/06 09:39:32  cvs
 		*** empty log message ***
 		





*/

function validateCvsFile($filename)
{

	global $data,$error, $csvRegels,$prb,$rekeningAddArray, $row, $transactieCodes;


	$error = array();
  getTransactieMapping();
  
	if (!$handle = @fopen($filename, "r"))
	{
		$error[] = "FOUT bestand $filename is niet leesbaar";
		return false;
	}
	$csvRegels = Count(file($filename));
	$prb->setLabelValue('txt1','Validatie van CSV bestand ('.$csvRegels.' records)');
  $pro_multiplier = 100/$csvRegels;
  $row = 0;
  while ($data = fgetcsv($handle, 4096, "|"))
  {
    $row++;
    $pro_step += $pro_multiplier;
    $prb->moveStep($pro_step);

    array_unshift($data,"leeg");
    mapDataFields();

    if ($row == 1)
    {
      if ($data[1] <> "recordType")
      {
        $error[] = "Bestandsindeling onjuist ";
      }
      if (count($data) < 10)
      {
        $error[] = "$row :te weinig velden ";
      }
      continue;

    }

// check transactie code bestaat
//
    $_code = trim($data["transactieCode"]);
    if (!in_array($_code, $transactieCodes))
    {
      $error[] = "$row :onbekende transactiecode ($_code)";
    }

    if ($data["storno"] == "Y")
    {
      $error[] = "$row :is STORNO/correctie regel --> overgeslagen";
    }
  }
  
  if (count($rekeningAddArray) > 0)
  {
    $_SESSION["rekeningAddArray"] = $rekeningAddArray;
  }
  else
  {
    $_SESSION["rekeningAddArray"] = array();
  }
  
  fclose($handle);
  if (Count($error) == 0)
  	return true;
  else
  {
  	//return true;
  	return false;
  }

}


