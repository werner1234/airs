<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2019/01/19 18:04:35 $
 		File Versie					: $Revision: 1.31 $

 		$Log: CRM_rapportageInstelling.php,v $
 		Revision 1.31  2019/01/19 18:04:35  rvv
 		*** empty log message ***
 		
 		Revision 1.30  2018/11/04 11:15:02  rvv
 		*** empty log message ***
 		
 		Revision 1.29  2018/11/01 07:13:46  rvv
 		*** empty log message ***
 		
 		Revision 1.27  2018/09/05 15:48:53  rvv
 		*** empty log message ***
 		
 		Revision 1.26  2018/08/18 12:40:14  rvv
 		php 5.6 & consolidatie
 		
 		Revision 1.25  2018/07/22 12:48:43  rvv
 		*** empty log message ***
 		
 		Revision 1.24  2018/04/15 12:31:23  rvv
 		*** empty log message ***
 		
 		Revision 1.23  2017/09/02 07:40:10  rvv
 		*** empty log message ***
 		
 		Revision 1.22  2017/07/22 18:20:50  rvv
 		*** empty log message ***
 		
 		Revision 1.21  2016/11/30 16:47:12  rvv
 		*** empty log message ***
 		
 		Revision 1.20  2016/09/26 08:16:57  rvv
 		*** empty log message ***
 		
 		Revision 1.19  2016/01/10 14:42:26  rvv
 		*** empty log message ***
 		
 		Revision 1.18  2015/10/25 13:08:50  rvv
 		*** empty log message ***
 		
 		Revision 1.17  2015/10/21 16:13:47  rvv
 		*** empty log message ***
 		
 		Revision 1.16  2015/02/07 21:11:47  rvv
 		*** empty log message ***
 		
 		Revision 1.15  2014/10/11 16:21:09  rvv
 		*** empty log message ***
 		
 		Revision 1.14  2013/05/22 15:54:41  rvv
 		*** empty log message ***
 		
 		Revision 1.13  2013/04/27 16:27:35  rvv
 		*** empty log message ***
 		
 		Revision 1.12  2012/08/11 13:05:20  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2011/12/21 19:18:08  rvv
 		*** empty log message ***

 		Revision 1.10  2011/11/19 15:41:14  rvv
 		*** empty log message ***

 		Revision 1.9  2011/10/23 13:32:25  rvv
 		*** empty log message ***

 		Revision 1.8  2011/07/17 14:50:05  rvv
 		*** empty log message ***

 		Revision 1.7  2011/04/30 16:23:58  rvv
 		*** empty log message ***

 		Revision 1.6  2011/02/02 18:47:53  rvv
 		*** empty log message ***

 		Revision 1.5  2011/02/02 14:19:49  rvv
 		Zoekveld toegevoegd

 		Revision 1.4  2011/02/02 13:56:57  rvv
 		*** empty log message ***

 		Revision 1.3  2011/01/23 08:50:44  rvv
 		*** empty log message ***

 		Revision 1.2  2011/01/16 12:10:21  rvv
 		*** empty log message ***

 		Revision 1.1  2010/12/08 18:26:52  rvv
 		*** empty log message ***

 		Revision 1.2  2010/11/24 20:11:25  rvv
 		*** empty log message ***

*/

include_once("wwwvars.php");
include_once("../classes/AE_cls_fpdf.php");
include_once("rapport/PDFOverzicht.php");
include_once("../classes/portefeuilleSelectieClass.php");
define('FPDF_FONTPATH',$__appvar["basedir"]."/html/font/");

class CRM_rapportageInstelling
{
	var $selectData;
	var $excelData;
	function CRM_rapportageInstelling( $selectData )
	{
		$this->selectData = $selectData;
		$this->pdf->excelData 	= array();
		$this->pdf = new PDFOverzicht('L','mm');
		$this->pdf->SetFont("Times","",10);
		$this->pdf->tmdatum = $this->selectData[datumTm];
		$this->pdf->selectData = $this->selectData;
	}
}

if(checkAccess('portefeuille')&&1==2)
{
  $join = "";
  $beperktToegankelijk = "";
}
else
{
   $join = "LEFT JOIN VermogensbeheerdersPerGebruiker ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker='".$USR."'
            LEFT JOIN Gebruikers as GebruikersRechten ON GebruikersRechten.Gebruiker = VermogensbeheerdersPerGebruiker.Gebruiker
            LEFT JOIN laatstePortefeuilleWaarde as laatstePortefeuilleWaarde ON CRM_naw.portefeuille = laatstePortefeuilleWaarde.portefeuille ";
    $beperktToegankelijk = " AND CRM_naw.Portefeuille <> '' AND (Portefeuilles.beperktToegankelijk = '0' OR  GebruikersRechten.beperkingOpheffen = '1' OR Portefeuilles.Portefeuille is NULL) ";
 
}


if(strpos($_SESSION['lastListQuery'],'ORDER BY') > 0)
  $query=substr($_SESSION['lastListQuery'],0,strpos($_SESSION['lastListQuery'],"ORDER BY"));
else
  $query=substr($_SESSION['lastListQuery'],0,strpos($_SESSION['lastListQuery'],"LIMIT"));


$db = new DB();
$q="SELECT VermogensbeheerdersPerGebruiker.Vermogensbeheerder,Vermogensbeheerders.kwartaalCheck, Vermogensbeheerders.Export_data_frontOffice,
Vermogensbeheerders.CrmPortefeuilleInformatie,Vermogensbeheerders.Layout,Vermogensbeheerders.CRM_eigenTemplate,Vermogensbeheerders.check_portaalCrmVink
FROM Vermogensbeheerders Join VermogensbeheerdersPerGebruiker ON VermogensbeheerdersPerGebruiker.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker = '$USR' GROUP BY VermogensbeheerdersPerGebruiker.Vermogensbeheerder";
$db->SQL($q);
$gebruikPortefeuilleInformatie = $db->lookupRecord();
$frontOfficeData=unserialize($gebruikPortefeuilleInformatie['Export_data_frontOffice']);

$vertaling=array();
foreach($__appvar['Rapporten'] as $rapport=>$omschrijving)
{
  $rapData=$frontOfficeData[$rapport];
  if($rapData['shortName']<>'')
  {
    $vertaling[$rapport]['shortName'] = $rapData['shortName'];
    $vertaling[$rapport]['longName'] = $rapData['longName'];
  }
  else
  {
    $vertaling[$rapport]['shortName'] = $rapport;
    $vertaling[$rapport]['longName'] = $__appvar['Rapporten'][$rapport];
  }
}



$query="SELECT CRM_naw.Portefeuille,
Portefeuilles.Client as Client,
rapportageVinkSelectie,
CRM_naw.naam,CRM_naw.naam1,CRM_naw.VerzendPaAanhef,CRM_naw.zoekveld,CRM_naw.memo,verzendAdres,verzendPc,verzendPlaats,verzendLand,CRM_naw.email as emailAdres,
CRM_naw.maandrapportage, CRM_naw.kwartaalrapportage, CRM_naw.halfjaarrapportage, CRM_naw.jaarrapportage,
if(length(CRM_naw.email)>2,'X','') as email,if(length(CRM_naw.wachtwoord)>5,'X','') as wachtwoord,
(SELECT count(id) as aantal FROM CRM_naw_adressen WHERE CRM_naw_adressen.rel_id=CRM_naw.id AND (CRM_naw_adressen.rapportage=1 OR CRM_naw_adressen.evenement='rapportage')) as naw_adressen
FROM CRM_naw 
LEFT JOIN Portefeuilles ON CRM_naw.Portefeuille=Portefeuilles.Portefeuille AND Portefeuilles.consolidatie<2
   $join
   JOIN Vermogensbeheerders ON VermogensbeheerdersPerGebruiker.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder
".substr($query,strpos($query,"WHERE"))." $beperktToegankelijk 
ORDER BY CRM_naw.zoekveld";
$db->SQL($query);
$db->Query();
while($data=$db->nextRecord())
{
  if($data['naw_adressen']==0)
  {
    $data['naw_adressen']='';
  }
  $portefeuilles[$data['Portefeuille']]=$data;
}

foreach ($portefeuilles as $portefeuille=>$pdata)
{
  $rapportageData[$portefeuille]=unserialize($pdata['rapportageVinkSelectie']);
  $rapportTonen['rap_k'][$portefeuille]=1;
/*
  $rapportTonen['rap_m'][$portefeuille]=1;
  $rapportTonen['rap_d'][$portefeuille]=1;
  $rapportTonen['rap_h'][$portefeuille]=1;
  $rapportTonen['rap_j'][$portefeuille]=1;
  */
  if($gebruikPortefeuilleInformatie['check_portaalCrmVink']==1)
    $rapportTonen['rap_d'][$portefeuille]=1;
}

$rapport=new CRM_rapportageInstelling();
foreach ($rapportageData as $portefeuille=>$rapportData)
{
  if(is_array($rapportData))
  {
    foreach ($rapportData['verzending'] as $periode=>$methodeData)
    {
      foreach ($rapportData[$periode] as $rapportage)
        $rapportArray[$rapportage]=$rapportage;

      if($methodeData['papier'])
        $rapportVerzending[$periode][$portefeuille]['papier']="X";
      if($methodeData['email'])
        $rapportVerzending[$periode][$portefeuille]['email']="X";
      if($methodeData['portaal'])
        $rapportVerzending[$periode][$portefeuille]['portaal']="X";
      if($methodeData['geen'])
        $rapportVerzending[$periode][$portefeuille]['geen']="X";        
      $rapportTonen[$periode][$portefeuille]=$rapportData[$periode];
      $rapportAantal[$periode][$portefeuille]=$rapportData['aantal'][substr($periode,4,1)];
    }
  }
}
  $db = new DB();
  $query="SELECT VermogensbeheerdersPerGebruiker.Vermogensbeheerder,Vermogensbeheerders.kwartaalCheck, 
Vermogensbeheerders.CrmPortefeuilleInformatie,Vermogensbeheerders.Layout,Vermogensbeheerders.CRM_eigenTemplate,Vermogensbeheerders.check_portaalCrmVink
FROM Vermogensbeheerders Join VermogensbeheerdersPerGebruiker ON VermogensbeheerdersPerGebruiker.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker = '$USR' GROUP BY VermogensbeheerdersPerGebruiker.Vermogensbeheerder";
  $db->SQL($query);
  $gebruikPortefeuilleInformatie = $db->lookupRecord();
  
$periodeVertaling=array('rap_d'=>'Dag','rap_m'=>'Maand','rap_k'=>'Kwartaal','rap_h'=>'Half jaar','rap_j'=>'Jaar');
$periodeTekstVertaling=array('rap_d'=>'dagrapportage','rap_m'=>'maandrapportage','rap_k'=>'kwartaalrapportage','rap_h'=>'halfjaarrapportage','rap_j'=>'jaarrapportage');

//foreach ($rapportTonen as $type=>$periodeData)
//{
//  $rapport->pdf->excelData[] = array($type);
  foreach ($rapportTonen as $periode=>$portefeuilleData)
  {
    $rapport->pdf->excelData[] = array($periodeVertaling[$periode]);
    if($__appvar['bedrijf']=='RCN')
    { 
      if($gebruikPortefeuilleInformatie['check_portaalCrmVink']==1)
        $tmp=array("Periode","Portefeuille","Naam",'Naam1','VerzendPaAanhef','Verzendadres','Verzendpostcode','Verzendplaats','Verzendland',"Zoekveld","Rapportage info","Aantal","Papier","Email","Portaal","Geen");
      else
        $tmp=array("Periode","Portefeuille","Naam",'Naam1','VerzendPaAanhef','Verzendadres','Verzendpostcode','Verzendplaats','Verzendland',"Zoekveld","Rapportage info","Aantal","Papier","Email","Geen");
    }
    else
    {
      if($gebruikPortefeuilleInformatie['check_portaalCrmVink']==1)
        $tmp=array("Periode","Portefeuille","Naam","Client","Zoekveld","emailadres","wachtwoord",'Mail Ontv','Extra adres',"Rapportage info","Aantal","Papier","Email","Portaal","Geen");
      else
        $tmp=array("Periode","Portefeuille","Naam","Client","Zoekveld","emailadres","wachtwoord",'Mail Ontv','Extra adres',"Rapportage info","Aantal","Papier","Email","Geen");
    }
    foreach ($rapportArray as $rapportWaarde)
    {
      array_push($tmp,$vertaling[$rapportWaarde]['shortName']);
    }
    array_push($tmp,"Memo");
    $rapport->pdf->excelData[] = $tmp;
    foreach ($portefeuilleData as  $portefeuille=>$rapporten)
    {
      if($__appvar["bedrijf"] == 'RCN' && $rapportVerzending[$periode][$portefeuille]['papier'] == '' && $rapportVerzending[$periode][$portefeuille]['email'] == '')
        $leegTonen=false;
      else
        $leegTonen=true;

      if($leegTonen)
      {
        if($__appvar['bedrijf']=='RCN')
        {
          $tmp=array(substr($periode,-1),'P#_'.$portefeuille,
            $portefeuilles[$portefeuille]['naam'],
          $portefeuilles[$portefeuille]['naam1'],$portefeuilles[$portefeuille]['VerzendPaAanhef'],$portefeuilles[$portefeuille]['verzendAdres'],$portefeuilles[$portefeuille]['verzendPc'],$portefeuilles[$portefeuille]['verzendPlaats'],$portefeuilles[$portefeuille]['verzendLand'],
          $portefeuilles[$portefeuille]['zoekveld'],$portefeuilles[$portefeuille][$periodeTekstVertaling[$periode]],
          $rapportAantal[$periode][$portefeuille], 
          $rapportVerzending[$periode][$portefeuille]['papier'], 
          $rapportVerzending[$periode][$portefeuille]['email']);
          if($gebruikPortefeuilleInformatie['check_portaalCrmVink']==1)
            array_push($tmp,$rapportVerzending[$periode][$portefeuille]['portaal']);
          array_push($tmp,$rapportVerzending[$periode][$portefeuille]['geen']);
        }
        else
        {
          $tmp=array(substr($periode,-1),'P#_'.$portefeuille,
                     $portefeuilles[$portefeuille]['naam'],
                     $portefeuilles[$portefeuille]['Client'],
                     $portefeuilles[$portefeuille]['zoekveld'],
                     $portefeuilles[$portefeuille]['email'],
                     $portefeuilles[$portefeuille]['wachtwoord'],
                     $portefeuilles[$portefeuille]['emailAdres'],
                     $portefeuilles[$portefeuille]['naw_adressen'],
                     $portefeuilles[$portefeuille][$periodeTekstVertaling[$periode]],
                   $rapportAantal[$periode][$portefeuille], 
                   $rapportVerzending[$periode][$portefeuille]['papier'], 
                   $rapportVerzending[$periode][$portefeuille]['email']); 
          if($gebruikPortefeuilleInformatie['check_portaalCrmVink']==1)
            array_push($tmp,$rapportVerzending[$periode][$portefeuille]['portaal']);
          array_push($tmp,$rapportVerzending[$periode][$portefeuille]['geen']);
        }
        foreach ($rapportArray as $rapportWaarde)
        {
          if(in_array($rapportWaarde,$rapporten))
            $waarde="X";
          else
            $waarde='';
           array_push($tmp,$waarde);
        }
        if(substr($portefeuilles[$portefeuille]['memo'],0,1)=="=")
          $portefeuilles[$portefeuille]['memo']=substr($portefeuilles[$portefeuille]['memo'],1);

        array_push($tmp,$portefeuilles[$portefeuille]['memo']);
        $rapport->pdf->excelData[] = $tmp;
      }
    }
    $rapport->pdf->excelData[] = array("");
  }
//  $rapport->pdf->excelData[] = array("");
//}
$filename="CRM_rapportageInstelling.xls";
$rapport->pdf->OutputXls($filename,"S");