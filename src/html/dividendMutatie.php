<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2019/01/16 13:18:55 $
 		File Versie					: $Revision: 1.5 $

 		$Log: dividendMutatie.php,v $
 		Revision 1.5  2019/01/16 13:18:55  cvs
 		call 7474
 		
 		Revision 1.4  2018/01/12 15:46:43  cvs
 		call 6503
 		
 		Revision 1.3  2014/12/24 09:54:51  cvs
 		call 3105
 		
 		Revision 1.2  2014/10/01 13:32:12  cvs
 		dbs 2877
 		
 		Revision 1.1  2014/03/10 09:59:00  cvs
 		*** empty log message ***
 		

*/


include_once("wwwvars.php");
include_once("../classes/AE_cls_progressbar.php");
define('FPDF_FONTPATH',$__appvar["basedir"]."/html/font/");



$tempQ = "
INSERT INTO TijdelijkeRekeningmutaties SET
  add_user = '{USR}'
, add_date = NOW()
, change_user = '{USR}'
, change_date = NOW()
, Rekening = '{rekening}'
, Omschrijving = ' {fondsOmschrijving}'
, Fonds = '{fonds}'
, Boekdatum = '{boekdatum}'
, Grootboekrekening = '{grootboek}'
, Valuta = '{dividendValuta}'
, Valutakoers = '{valutaKoers}'
, bankTransactieId ='{transId}'
, Debet = '{Debet}'
, Credit = '{Credit}'
, Bedrag = '{Bedrag}'
";

//debug($tempQ);

$content = array();
echo template($__appvar["templateContentHeader"],$content);

$db = new DB();

$dividendData = $_SESSION["dividendData"];
$dataSet = array();
foreach( $_POST as $key=>$value)
{
  $key = str_replace("~"," ",$key);
  if (substr($key,0,4) == "chk_")
    $dataSet[] = substr($key,4);
}
for ($x=0; $x < count($dividendData); $x++)
{
  $d = $dividendData[$x];
  if ($d["fondsSoort"] != "OBL")
  {
    $omsPrefix = "Dividend";
    $grootboek = "DIV";
  }
  else
  {
    $omsPrefix = "Coupon";
    $grootboek = "RENOB";
  }

  if (in_array($d["Portefeuille"], $dataSet))
  {
    $d["transId"] = "airsDiv_".rand(1111,9999)."_".date("YmdHis");
    $d["USR"] = $USR;
    $d["grootboek"] = $grootboek;
    $d["Debet"] = 0;
    $d["Credit"] = $d["brutoDividend"];
    $d["Bedrag"] = $d["brutoDividend"] * $d["valutaKoers"];
    $d["fondsOmschrijving"] = $omsPrefix." ".$d["fondsOmschrijving"];
    $q = TemplateStr($tempQ,$d);
    $db->executeQuery($q);
    $telDiv++;

    if( (float) $d["belasting"] <> 0 AND $d["VerwerkingsmethodeDiv"] <> 1  )
    {
      $d["USR"] = $USR;
      $d["grootboek"] = "DIVBE";
      $d["Credit"] = 0;
      $d["Debet"] = $d["belasting"];
      $d["Bedrag"] = $d["belasting"] * $d["valutaKoers"] * -1;
      $q = TemplateStr($tempQ,$d);
      $db->executeQuery($q);
      $telDivBe++;
    }
  }

}
?>
<br />
<h2>dividend orders naar tijdelijke rekeningmutaties</h2>
<table>
<tr>
  <td>dividend orders</td>
  <td><?=$x?></td>
</tr>
<tr>
  <td>grootboek <?=$grootboek?> boekingen</td>
  <td><?=(int)$telDiv?></td>
</tr>
<tr>
  <td>grootboek DIVBE boekingen</td>
  <td><?=(int)$telDivBe?></td>
</tr>
</table>
<br />
<button><a href="tijdelijkerekeningmutatiesList.php">Ga naar tijdelijkerekeningmutaties</a></button>

<?
echo template($__appvar["templateRefreshFooter"],$content);


function TemplateStr($template,$objectData)
{
  $data = $template;

	foreach ($objectData as $key=>$val)
	{

	    $data = str_replace( "{".$key."}", $val, $data);


 	}


  $data = eregi_replace( "\{[a-zA-Z0-9_-]+\}", "", $data);
  return $data;
}
?>