<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/08/18 12:40:14 $
File Versie					: $Revision: 1.6 $

$Log: rapportVertaal.php,v $
Revision 1.6  2018/08/18 12:40:14  rvv
php 5.6 & consolidatie

Revision 1.5  2014/01/18 17:26:40  rvv
*** empty log message ***

Revision 1.4  2012/08/22 15:46:30  rvv
*** empty log message ***

Revision 1.3  2005/10/26 11:47:39  jwellner
no message

Revision 1.2  2005/09/16 07:32:55  jwellner
aanpassingen rapportage.

Revision 1.1  2005/06/30 08:22:56  jwellner
Rapportage toegevoegd

*/
function vertaalTekst($tekst, $taal)
{

	global $teksten;
	$teksten[$tekst]+=1;
	if($taal <> 0)
	{
		$zoekTekst = str_replace("\n","",$tekst);
		$DB = new DB();
		$DB->SQL("SELECT Vertaling FROM Vertalingen WHERE Term = '".mysql_escape_string($zoekTekst)."' AND Taal = '".$taal."' LIMIT 1");
		$DB->Query();
		if($DB->records() == 1)
		{
			$data = $DB->nextRecord();
			$vertaling = str_replace('<enter>',"\n",$data['Vertaling']);
		}
		else
		{
		  $zoekTekst = str_replace("-","",$zoekTekst);
		  $DB->SQL("SELECT Vertaling FROM Vertalingen WHERE Term = '".mysql_escape_string($zoekTekst)."' AND Taal = '".$taal."' LIMIT 1");
		  $DB->Query();
		  if($DB->records() == 1)
		  {
			  $data = $DB->nextRecord();
			  $vertaling = str_replace('<enter>',"\n",$data['Vertaling']);
		  }
      else
			  $vertaling = $tekst;
		}
	}
	else
	{
		$vertaling = $tekst;
	}

	return $vertaling;
}
?>