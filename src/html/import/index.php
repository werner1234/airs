<?
/* 	
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2005/05/30 17:09:46 $
 		File Versie					: $Revision: 1.13 $
 		
 		$Log: index.php,v $
 		Revision 1.13  2005/05/30 17:09:46  cvs
 		einde dag 30-5-2005
 		
 		Revision 1.12  2005/05/23 17:17:50  cvs
 		einde dag
 		
 	  Concept inlezen ABN tekstfiles
 	  
 		
 		
	
*/
function cnvBedrag($txt)
{
	return str_replace(',','.',$txt);
}	
include_once("wwwvars.php");
$mt940_skip = Array("KOOP","VERKOOP","DIVIDEND","COUPON");

$content = array();
echo template("../".$__appvar["templateContentHeader"],$content);  
$csvRegels = 1;
//include("validate.php");

setlocale( LC_ALL, '');

if (!$file)  $file   = "test554.sta";
$volgnr = 1;

$row = 1;
$handle = fopen($file, "r");
$ndx=0;
$dataSet = Array();
$row = Array();
$_tempRow = Array();
$regtel = 0;

//
// lees alle bekende records in array
//

while ($data = fgets($handle, 4096)) 
{
	$regtel++;
  switch (trim($data)) 
  {
   	case "ABNANL2A":
        //cycle
   		break;
   	case "500":	
   	case "510":
   	case "571":
   	case "554":
   	case "940":  //type record
   	  $dataSet[$ndx][type] = $data;
 			break;	
   	case "-":  // einde record
      $ndx++;
 			break;	
  	default:
//
//
//
  	  if (substr($data,0,1) <> ":")
   	  {
   	    $dataSet[$ndx][txt] = substr($dataSet[$ndx][txt],0,-1)." ".$data;
   	  }  
   	  else  
   	  {
   	  	$_regel = explode(":",$data);
   	  	$_prevKey = $_regel[1];  
   	  	$dataSet[$ndx][txt] .= $_regel[1]."&&".$_regel[2];  // vul data velden
   	  }  
   		break;
   } 

}
fclose($handle);
$_mt554Count = 0;
$_mt940Count = 0;
$mt940_83_skip = 0;
$_mt940CountClean = 0;

//
//  filter de geldige records uit alle records
//
for($loopndx = 0;$loopndx < count($dataSet);$loopndx++)
{
	$_var = trim($dataSet[$loopndx][type]);
	
	switch ($_var)
	{
		case "940":
		  $_mt940Count++;
		  $_data = explode(chr(10),$dataSet[$loopndx][txt]);
		  //listarray($_data);
		  $addRecord = false;
		  for ($subLoop = 0; $subLoop < count($_data);$subLoop++)
		  {
		  	$_r = explode("&&",$_data[$subLoop]);
		  	if ($_r[0] == "61") $addRecord = true; // veld 61 bestaat
		  	if ($_r[0] == "86")                    // skip veld 83 als de tekst begin met waarden uit de $mt940_skip array
		  	{
		  		$addRecord = true;
		  		echo "<br>".$_r[1];
		  		for ($xx=0; $xx < count($mt940_skip);$xx++)
		    	{
		    		$arrValue = $mt940_skip[$xx];
		  	  	if (substr(strtoupper($_r[1]),0,strlen($arrValue)) == $arrValue) 
		  	  	{
		  	  		echo " [SKIP]";
		  	  		$mt940_83_skip++;
		  	  		$addRecord = false;	
		  	  	}	
		    	}	
		  	}
		  	
		  }	
		  if ($addRecord == true) 
		  {
	  		$_mt940CountClean++;
	  		$dataSet940[] = $dataSet[$loopndx];
	  	}	
		  break;
    case "554":
		  $_mt554Count++;
		  $dataSet554[] = $dataSet[$loopndx];
		  break;	
		default:
		  	  
	}
}

///
///  transaktie type bepalen van 554 records
///

if (count($dataSet554) > 0) // er bestaand MT554 mutaties
{
	for ($_ndx = 0; $_ndx < count($dataSet554);$_ndx++)
	{
		$rec = $dataSet554[$_ndx];
		$_data = explode(chr(10),$dataSet554[$_ndx][txt]);
		 //listarray($_data);
		
		for ($subLoop = 0; $subLoop < count($_data);$subLoop++)
		{
		  	$_r = explode("&&",$_data[$subLoop]);
				if ($_r[0] == "72")
				{
					$_data = explode(chr(10),$rec[txt]);
		 			//listarray($_data);
	        $wr = array();
		 			for ($subLoop = 0; $subLoop < count($_data);$subLoop++)
		 			{
		 				$_r = explode("&&",$_data[$subLoop]);
		 				$_tempRec[$_r[0]] = $_r[1];
		 				switch ($_r[0])
		 				{
		 					case "23":
		 						$wr[transactienr] = $_r[1];
		 						break;
		 					case "53a":
		 						$wr[rekeningnr] = intval($_r[1]);
		 						break;
		 					case "83a":
		 						$wr[depotnr] = $_r[1];
		 						break;
		 					case "72":
		 					  if (Trim($_r[1]) <> "")  // niet overschrijven als leeg
		 						  $wr[transaktietype] = $_r[1];
		 						break;
		 					case "35A":
		 					  for($xx=0;$xx < strlen($_r[1]);$xx++)
		 					  {
		 					   $_l = 	substr($_r[1],$xx,1);
		 					   if ($_l >= "0" AND $_l <= "9")
		 					     $wr[aantal] .= $_l;
		 					   elseif ($_l == ",")
		 					     $wr[aantal] .= ".";
		 					  } 
		 						break;
		 					case "35B":
		 						$_val = explode(" ",$_r[1]);
		 						$xx=1;
		 						while($xx < count($_val))
		 						{
		 							if ($_val[$xx] <> "") 
		 							{
		 							  $wr[aabcode] = $_val[$xx];
		 							  break;
		 							}
		 							$xx++;   
		 						}
		 						break;
		 					case "35U":
		 					  if (Trim($_r[1]) <> "")  // niet overschrijven als leeg
		 						{
		 							$wr[valutacode] = cnvBedrag(substr($_r[1],0,3));
		 					    $wr[fondskoers] = cnvBedrag(substr($_r[1],3));
		 						}
		 						 break; 
		 					case "36":
		 					  $wr[wisselkoers] = cnvBedrag($_r[1]);
		 						break;
		 					case "34A":
		 						if (Trim($_r[1]) <> "") // niet overschrijven als leeg
		 						{
		 							$wr[boekdatum] = substr($_r[1],0,4)."-".substr($_r[1],4,2)."-".substr($_r[1],6,2);
		 							if (substr($_r[1],8,1) == "N")
		 							{
		 							  $wr[valuta]    = substr($_r[1],9,3);
		 							  $wr[bedrag]    = cnvBedrag(substr($_r[1],11))*-1;
		 							}
		 							else 
		 							{  
		 							  $wr[valuta]    = substr($_r[1],8,3);
		 							  $wr[bedrag]    = cnvBedrag(substr($_r[1],11));
		 							}  
		 						}
		 						break;
		 					case "32G":
		 					  $wr[kosten] = cnvBedrag($_r[1]);
		 						break;
		 					case "71C":
		 					  $_l = trim($_r[1]);
		 					  $wr["valuta_".$_l] = substr($wr[kosten],1,3);
		 					  $_bedr = substr($wr[kosten],4);
		 					  if (substr($wr[kosten],0,1) == "N")
			 					  $wr["kosten_".$_l] = $_bedr * -1;
			 					else  
			 					  $wr["kosten_".$_l] = $_bedr;
		 					  
		 						break;
		 				}
					}
					listarray($wr);
				
			}
		}
	}	
}

if (count($dataSet940) > 0) // er bestaand MT940 mutaties
{
	
	for ($_ndx = 0; $_ndx < count($dataSet940);$_ndx++)
	{
		$rec = $dataSet940[$_ndx];
		$_data = explode(chr(10),$dataSet940[$_ndx][txt]);
		 //listarray($_data);
		
		for ($subLoop = 0; $subLoop < count($_data);$subLoop++)
		{
		  	$_r = explode("&&",$_data[$subLoop]);
				if ($_r[0] == "61")
				{
	
					$_debcre = substr($_r[1],10,1);
					echo "<br>".$_r[1]." --- ".$_debcre;
				}
		}
	}
}

?>