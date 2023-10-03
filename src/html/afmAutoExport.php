<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2012/11/21 19:17:54 $
File Versie					: $Revision: 1.4 $

$Log: afmAutoExport.php,v $
Revision 1.4  2012/11/21 19:17:54  rvv
*** empty log message ***

Revision 1.3  2012/11/17 16:00:47  rvv
*** empty log message ***

Revision 1.2  2012/11/14 17:02:09  rvv
*** empty log message ***

Revision 1.1  2012/11/14 16:47:16  rvv
*** empty log message ***


*/

if($_GET['user'] <> '')
{
  $gebruiker=addslashes($_GET['user']);
  $passwd=addslashes($_GET['pass']);
  $rapportageDatum=addslashes($_GET['date']);
  $bedrijfscode=addslashes($_GET['code']);
}
elseif($_POST['user'] <> '')
{
  $gebruiker=addslashes($_POST['user']);
  $passwd=addslashes($_POST['pass']);
  $rapportageDatum=addslashes($_POST['date']);
  $bedrijfscode=addslashes($_POST['code']);
}
else
{
 exit(); 
}

$disable_auth=true;
include('wwwvars.php');

if(login($gebruiker,$passwd,$bedrijfscode) && checkLogin())
{ 
  include_once("rapport/rapportRekenClass.php");
  include_once("../classes/portefeuilleSelectieClass.php");

  $__appvar['TijdelijkeRapportageMaakUniek'] = " AND TijdelijkeRapportage.add_user = '".$USR."' AND TijdelijkeRapportage.sessionId = '".$sessionId."' ";

  $selectie = new portefeuilleSelectie();
  $records = $selectie->getRecords();
  $portefeuilles = $selectie->getSelectie();

  $afmCategorien=array('01'=>'LIQ','02'=>'AAA','03'=>'A','07'=>'IG EUR','04'=>'Non-EUR AAA','05'=>'Non-EUR A','13'=>'RE Eur','09'=>'IG Non-Eur',
    '14'=>'Re ex-Eur','06'=>'Gov EMM','15'=>'HF','08'=>'HY EUR','10'=>'HY Non-EUR','11'=>'EQ','17'=>'PE','12'=>'EQ EMM','16'=>'COMM');
   
  $header=array("Asset Manager","Account Code","Account Type","Account Number","Client");
    
  foreach($afmCategorien as $index=>$waarde)
    array_push($header,$waarde);
    
  $excelData[] = $header;
  $db=new DB();
  foreach($portefeuilles as $pdata)
	{  
    $query="SELECT Naam FROM Vermogensbeheerders WHERE Vermogensbeheerder='".$pdata['Vermogensbeheerder']."'";
    $db->SQL($query);
    $vermogensbeheerder=$db->lookupRecord();
    vulTijdelijkeTabel(berekenPortefeuilleWaarde($pdata['Portefeuille'],$rapportageDatum),$pdata['Portefeuille'],$rapportageDatum);
    $afm=AFMstd($pdata['Portefeuille'],$rapportageDatum);
    $afmCategorieverdeling=getAFMWaarden($pdata['Portefeuille'],$rapportageDatum);
    $tmp=array($vermogensbeheerder['Naam'],'','Managed Account',$pdata['Portefeuille'],$pdata['Client']);
    foreach ($afmCategorien as $index=>$cat)
    {
      $afmCat=$afmCategorieverdeling['codeKoppel'][$index];
      $waardeEur=$afmCategorieverdeling['verdeling'][$afmCat]['actuelePortefeuilleWaardeEuro'];
      array_push($tmp,round($waardeEur,2));
    }
    $excelData[] = $tmp;
    verwijderTijdelijkeTabel($pdata['Portefeuille'],$rapportageDatum);
	}
  $data=generateCSV($excelData);

	if(headers_sent())
		echo "FOUT: headers zijn al verzonden";
      
	$appType = "text/comma-separated-values";
	header('Content-type: '. $appType);
	header("Content-Length: ".strlen($data));
	header("Content-Disposition: inline; filename=\"afm_export.txt\"");
	header("Pragma: public");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
  echo $data; 
}
else
{
  echo "login failed";
}
?>
