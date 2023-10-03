<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2016/10/09 14:59:33 $
 		File Versie					: $Revision: 1.20 $

 		$Log: CRM_vars.php,v $
 		Revision 1.20  2016/10/09 14:59:33  rvv
 		*** empty log message ***
 		
 		Revision 1.19  2013/09/01 13:28:37  rvv
 		*** empty log message ***
 		
 		Revision 1.18  2012/08/05 10:43:06  rvv
 		*** empty log message ***
 		
 		Revision 1.17  2011/10/23 13:20:05  rvv
 		*** empty log message ***

 		Revision 1.16  2011/09/14 18:42:37  rvv
 		*** empty log message ***

 		Revision 1.15  2011/03/13 18:34:42  rvv
 		*** empty log message ***

 		Revision 1.14  2010/09/04 08:17:12  rvv
 		*** empty log message ***

 		Revision 1.13  2010/06/30 16:07:25  rvv
 		*** empty log message ***

 		Revision 1.12  2010/02/03 17:03:50  rvv
 		*** empty log message ***

 		Revision 1.11  2010/01/24 17:01:33  rvv
 		*** empty log message ***

 		Revision 1.10  2009/11/08 14:07:47  rvv
 		*** empty log message ***

 		Revision 1.9  2009/09/20 07:26:44  rvv
 		*** empty log message ***

 		Revision 1.8  2009/09/16 15:41:34  rvv
 		*** empty log message ***

 		Revision 1.7  2009/06/10 12:51:01  rvv
 		*** empty log message ***

 		Revision 1.6  2008/06/30 07:40:56  rvv
 		*** empty log message ***

 		Revision 1.5  2008/03/25 14:49:58  rvv
 		*** empty log message ***

 		Revision 1.4  2007/10/09 06:20:30  cvs
 		CRM update

 		Revision 1.3  2006/01/25 15:15:19  cvs
 		*** empty log message ***

 		Revision 1.2  2006/01/25 11:50:17  cvs
 		*** empty log message ***

 		Revision 1.1  2006/01/05 16:03:15  cvs
 		eerste CRM test


*/
//$__modules[] = "CRM";
$__CRMvars = Array(
"selectieTypen"           => array("burgelijke staat"=>"burgelijke staat",
                                   "rechtsvorm"=>"rechtsvorm",
                                   "telefoon"=>"telefoon",
                                   "risicoprofiel"=>"risicoprofiel",
                                   "legitimatie"=>"legitimatie",
                                   "soort inkomen"=>"soort inkomen",
                                   "beleggingshorizon"=>"beleggingshorizon",
                                   "beleggingsdoelstelling"=>"beleggingsdoelstelling",
                                   "verzend freq rapportage"=>"verzend freq rapportage",
                                   "in contact door"=>"in contact door",
                                   'ervaring'=>'ervaring/kennisniveau',
                                   'opleidingsniveau'=>'opleidingsniveau',
                                   'clientenclassificatie'=>'clientenclassificatie',
                                   'relatiegeschenken'=>'relatiegeschenken',
                                   'prospect status'=>'prospect status',
                                   'evenementen'=>'evenementen',
                                   'agenda afspraak'=>'agenda afspraak',
                                   'banken'=>'banken',
                                   'docCategrien'=>'document categorien',
                                   'gesprekstypen'=>'gespreksverslag typen',
                                   'rtfCategorien'=>'RTF categorien',
                                   'standaardTaken'=>'standaard taak groepen'),
 "koppelingen"            => array('telefoon'=>array('CRM_naw'=>array('tel1_oms','tel2_oms','tel3_oms','tel4_oms','tel5_oms','tel6_oms')),
                                   'legitimatie'=>array('CRM_naw'=>array('legitimatie','part_legitimatie')),
                                   'burgelijke staat'=>array('CRM_naw'=>array('huwelijkseStaat')),
                                   'rechtsvorm'=>array('CRM_naw'=>array('ondernemingsvorm')),
                                   'soort inkomen'=>array('CRM_naw'=>array('inkomenSoort','part_inkomenSoort')),
                                   'beleggingshorizon'=>array('CRM_naw'=>array('beleggingsHorizon')),
                                   'beleggingsdoelstelling'=>array('CRM_naw'=>array('beleggingsDoelstelling')),
                                   'risicoprofiel'=>array('CRM_naw'=>array('risicoprofiel')),
                                   'verzend freq rapportage'=>array('CRM_naw'=>array('verzendFreq')),
                                   'in contact door'=>array('CRM_naw'=>array('inContactDoor')),
                                   'clientenclassificatie'=>array('CRM_naw'=>array('clientenclassificatie')),
                                   'prospect status'=>array('CRM_naw'=>array('prospectStatus')),
                                   'ervaring'=>array('CRM_naw'=>array('ervaringMetGestructureerdeProductenDatum','ervaringMetGestructureerdeProducten','ervaringBelegtInEigenbeheer','ervaringBelegtInVermogensadvies','ervaringBelegtInProducten','ervaringMetVastrentende','ervaringMetBeleggingsFondsen','ervaringMetIndividueleAandelen','ervaringMetOpties','ervaringMetFutures','ervaringInVermogensbeheer','ervaringInExecutionOnly')),
                                   'opleidingsniveau'=>array('CRM_naw'=>array('opleidingsniveau')),
                                   'evenementen'=>array('CRM_evenementen'=>array('evenement'),'CRM_naw_adressen'=>array('evenement')),
                                   'agenda afspraak'=>array('agenda'=>array('soort')),
                                   'banken'=>array(),
                                   'standaardTaken'=>array('standaardTaken'=>array('hoofdtaak')),
                                   'docCategrien'=>array('dd_reference'=>array('categorie')),
                                   'gesprekstypen'=>array('CRM_naw_dossier'=>array('type'))
                                  )
);
//'relatiegeschenken'=>array('CRM_naw'=>array('Relatie1','Relatie2','Relatie3','Relatie4','Relatie5','Relatie6','Relatie7','Relatie8','Relatie9','Relatie10')),


define("CRM_lever",0);
define("CRM_gebruiker",1);
define("CRM_beheerder",2);

$__appvar["CRM_rechten"] = array ("lezer","gebruiker","beheerder");


function GetSelectieVelden($module,$keyed=true)
{
  $output = array();
  $db = new DB();
  $q = "SELECT waarde,omschrijving FROM CRM_selectievelden WHERE module = '".$module."' ORDER BY waarde,omschrijving";
  $db->SQL($q);
  $db->Query();
  while ($row = $db->nextRecord())
  {
    if ($keyed)
      $key = $row["waarde"];
    else
      $key = $row["omschrijving"];

    $output[$key] = $row["omschrijving"];
  }
  return $output;
}



function GetCRMAccess($level=0)
{
  if (!$_SESSION["USR"]) return false;
  if($_SESSION['usersession']['superuser'])
		return true;
	else
	{
	  $query  = "SELECT CRMlevel FROM Gebruikers WHERE Gebruiker =  '".$_SESSION["USR"]."'";
    $DB = new DB();
    $DB->SQL($query);
    if ($row = $DB->lookupRecord())
      return ($row["CRMlevel"] >= $level)?true:false;
    else
      return false;
	}
}
?>