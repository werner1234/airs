<?php
/*
    AE-ICT CODEX source module versie 1.1.1.1, 10 november 2005
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2020/05/09 16:54:21 $
    File Versie         : $Revision: 1.203 $
*/


function microtime_float()
{
  list($usec, $sec) = explode(" ", microtime());
  return ((float)$usec + (float)$sec);
}

$startTime=microtime_float();
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$cfg = new AE_config();//AIRS\html\CRM_include\CRM_nawEditTemplate_L14.html

$alleRapporten=$__appvar["Rapporten"];
//$alleRapporten['FACTUUR']='Factuur';

$__funcvar['listurl']  = "CRM_nawList.php";
$__funcvar['location'] = "CRM_nawEdit.php";

$data = array_merge($_GET,$_POST);
$requestData = $data;

$_SESSION["facmodUrl"] = $_SERVER["REQUEST_URI"];

$object = new Naw();

if ($_GET['useSavedUrl'] == 1)  // returnURL instellen zoals deze oorspronkelijk was bij de aanroep van dit script
{
  $_SESSION['NAV']->returnUrl = $_SESSION['savedReturnUrl'];
}

if ( (isset ($requestData['template']) && $requestData['template'] === 'intake') && isset ($requestData['id'])  ) {
  $__funcvar['listurl']  = "CRM_nawEdit.php?action=edit&useSavedUrl=1&id=" . (int) $requestData['id'];
  $_SESSION['NAV']->returnUrl = $__funcvar['listurl'];
}

if($data['id'] > 0)
{
  $db=new DB();
  $query="SELECT CRM_relatieSoorten FROM Gebruikers WHERE Gebruiker='$USR'";
  $db->SQL($query);
  $CRM_relatieSoorten=$db->lookupRecord();
  $CRM_relatieSoorten=unserialize($CRM_relatieSoorten['CRM_relatieSoorten']);
  $filter='';
  if(is_array($CRM_relatieSoorten))
  {
    $query = "DESC CRM_naw";
    $db->SQL($query);
    $db->Query();
    $crmVelden = array();
    while ($rec = $db->nextRecord('num'))
    {
      $crmVelden[] = $rec[0];
    }

    $allArray = array();
    foreach ($CRM_relatieSoorten as $key => $value)
    {
      if ($value <> 'all' && $value <> 'inaktief' && $value <> 'aktief' && in_array($value, $crmVelden))
      {
        $allArray[] = $value;
      }
    }
    if (count($allArray) > 0)
    {
      $filter = "AND (" . implode('=1 OR ', $allArray) . "=1)";
    }

    if (in_array('inaktief', $CRM_relatieSoorten))
    {
      $filter .= ' AND aktief=0 ';
    }

    if (in_array('all', $CRM_relatieSoorten))
    {
      $filter = '';
    }

    if($_SESSION['usersession']['gebruiker']['overigePortefeuilles'] == 0 && $_SESSION['usersession']['gebruiker']['Accountmanager'] == '')
    {
      $query = "SELECT CRMeigenRecords FROM Gebruikers WHERE Gebruiker='$USR'";
      $db->SQL($query);
      $gebruikersData = $db->lookupRecord();
      if ($gebruikersData['CRMeigenRecords'] > 0)
        $filter .= " AND (CRM_naw.prospectEigenaar='$USR' OR CRM_naw.accountEigenaar='$USR') ";
    }
    $query = "SELECT id FROM CRM_naw WHERE id='" . addslashes($data['id']) . "' $filter"; //echo $query;

    if ($db->QRecords($query) == 0)
    {
      echo vt("Geen rechten tot deze relatie.");
      exit;
    }
  }
  
  foreach ($_SESSION['lastTableIds'] as $index=>$id)
  {
    if($id==$data['id'])
    {
      $vorigeId=$_SESSION['lastTableIds'][$index-1];
      $volgendeId=$_SESSION['lastTableIds'][$index+1];
    }
  }
  if($vorigeId > 0)
    $vorigeId ='<a href="javascript:openNawRecord(\''.$vorigeId.'\');" style="width:55px" >' . vt('vorige') . '</a>';
  if($volgendeId > 0)
    $volgendeId ='<a href="javascript:openNawRecord(\''.$volgendeId.'\');"   style="width:55px"  >' . vt('volgende') . '</a>';

  $bladerHtml="
  <script>
  function openNawRecord(id)
  {
    tab=top.frames[\"content\"].N;
    parent.frames['content'].location = 'CRM_nawEdit.php?action=edit&id='+id+'&lastTab='+tab;
  }
  </script>

  <table><tr><td>$vorigeId</td><td>$volgendeId</td></tr></table>";
}


$db = new DB();
if ($_GET['do'] == "viaFrontOffice" )
{
  $query = "SELECT * FROM CRM_naw WHERE portefeuille= '".$_GET['port']."'";
  $db->SQL($query);
  if ($crmRec = $db->lookupRecord())
  {
    $data['action'] = "edit";
    $data['id']     = $crmRec["id"];
  }
  else
  {
    $data['action'] = "new";
    $data['id']     = 0;
    $query = "SELECT Portefeuilles.Portefeuille,Clienten.Client,Clienten.Naam,Clienten.Naam1,Clienten.pc,Clienten.Adres,Clienten.Woonplaats,Clienten.Telefoon,Clienten.Fax,Clienten.Email
              FROM Portefeuilles INNER JOIN Clienten ON Portefeuilles.Client = Clienten.Client WHERE Portefeuilles.Portefeuille='".$_GET['port']."'";
    $db->SQL($query);
    $clientRec = $db->lookupRecord();
  }
}
//listarray($_SESSION);
if ($data['action'] == "new")
  $mainHeader   = "" . vt('relatie toevoegen') . ",&nbsp;&nbsp;&nbsp;";

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editcontent['javascript']=str_replace('document.editForm.submit();','if(checkFields()){document.editForm.submit();}',$editcontent['javascript']);

$query="SELECT veldnaam,omschrijving FROM CRM_eigenVelden WHERE relatieSoort=1";
$db->SQL($query);
$db->Query();
$extraRelatieChecks='';
while($veldnaam=$db->nextRecord())
  $extraRelatieChecks.=' if(theForm[z].name == \''.$veldnaam['veldnaam'].'\'){aantal++; txt=txt + "'.$veldnaam['omschrijving'].' aangevinkt. "}'."\n";



$editcontent['jsincludes'] .= "<script language=JavaScript src=\"javascript/tabbladen.js\" type=text/javascript></script>\n";
$editcontent['jsincludes'] .= "<script language=JavaScript src=\"javascript/jsrsClient.js\" type=text/javascript></script>";
$editcontent['jsincludes'] .= "<script language=JavaScript src=\"javascript/CRM_nawEdit.js\" type=text/javascript></script>";
$editcontent['jsincludes'] .=" <script language=JavaScript src=\"javascript/iban.js\" type=text/javascript></script>";

if($_GET['lastTab'])
  $lastTab=$_GET['lastTab'];
else
  $lastTab=0;

if($_GET['taakId']!='')
{
  $url="frameSet.php?page=".base64_encode("takenEdit.php?action=edit&id=".$_GET['taakId']."&deb_id=".$_GET['id']."&toHome=".$_GET['toHome']);
  $javaOpenTab="openTaak('$url');";
}
else
  $javaOpenTab="tabOpen('$lastTab');";

$editcontent['body'] = " onLoad=\"javascript: try{ initScript();toonProspectStatus();$javaOpenTab } catch(e){} \" ";


if($data['action'] == 'delete')
{
  $data['action']='update';
  $data['aktief']='0';
  $object->set('aktief','0');
}

if($data['prospectStatusOld'] != $data['prospectStatus'])
  $data['prospectStatusChange']=date("Y-m-d H:i:s");

$action = $data['action'];

$query="SELECT VermogensbeheerdersPerGebruiker.Vermogensbeheerder,Vermogensbeheerders.kwartaalCheck, 
Vermogensbeheerders.CrmPortefeuilleInformatie,Vermogensbeheerders.Layout,Vermogensbeheerders.CRM_eigenTemplate,Vermogensbeheerders.check_portaalCrmVink, Vermogensbeheerders.portaalDailyClientSync
FROM Vermogensbeheerders Join VermogensbeheerdersPerGebruiker ON VermogensbeheerdersPerGebruiker.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker = '$USR' GROUP BY VermogensbeheerdersPerGebruiker.Vermogensbeheerder";
$db->SQL($query);
$gebruikPortefeuilleInformatie = $db->lookupRecord();


if($data['template']=='intake')
  $formChecks='';
else
  $formChecks='  try {
  var theForm = document.editForm.elements,aantal=0,txt="";
  for(z=0; z<theForm.length;z++)
  {
   if(theForm[z].type == \'checkbox\' && theForm[z].checked == true)
   {
     '.$extraRelatieChecks.'
   }
  }
  if(aantal > 1){alert("Meerdere (" + aantal + ") relatie typen aangevinkt. " + txt); return false;}
  if(aantal < 1){alert("Geen relatie type aangevinkt."); return false;}
  if (document.editForm.zoekveld.value  == "" )
  {
   if (!confirm("' . vt('Veld zoekveld is leeg. Toch opslaan') . '?"))
   {
     return false;
   }
  }
  }
  catch(e){}';


$editcontent['javascript'] .= '

function checkFields()
{

  try {
    if(window.frames.extraFrame.content.editForm.noForce == undefined)
    {
      window.frames.extraFrame.content.editForm.submit();
    }
  }catch(e){}

  try
  {
    var debiteur=false;
    var theForm = document.editForm.elements
    for(z=0; z<theForm.length;z++)
    {
     if(theForm[z].type == \'checkbox\' && theForm[z].checked == true && theForm[z].name == \'debiteur\'){var debiteur=true}
    }
    
    if(debiteur == true)
    {
      if(document.editForm.kwartaalCheck.value == true);
      {
        if(document.editForm.rapVerzend_k_papier.checked == true || document.editForm.rapVerzend_k_email.checked == true || document.editForm.rapVerzend_k_geen.checked == true '.($gebruikPortefeuilleInformatie['check_portaalCrmVink']==1?'|| document.editForm.rapVerzend_k_portaal.checked == true':'').')
        {
          //oke
        }
        else
        {
         alert(\'' . vt('Geen kwartaalrapportage verzendmethode opgegeven!') . '\');
         return false;
        }
      }
    }
  }
  catch(e){}

  if(document.editForm.verzenden && document.editForm.verzenden.checked == true)
  {
    if(document.editForm.Vermogensbeheerder && document.editForm.Accountmanager && document.editForm.Depotbank && document.editForm.Portefeuille && document.editForm.Client)
    {
      if (document.editForm.Vermogensbeheerder.value  == "" ) {alert("' . vt('Geen Vermogensbeheerder geselecteerd.') . '"); return false;}
      if (document.editForm.Accountmanager.value  == "" ) {alert("' . vt('Geen Accountmanager geselecteerd.') . '"); return false;}
      if (document.editForm.Depotbank.value  == "" ) {alert("' . vt('Geen Depotbank geselecteerd.') . '"); return false;}
      if (document.editForm.Portefeuille.value  == "" ) {alert("' . vt('Geen Portefeuille geselecteerd.') . '"); return false;}
      if (document.editForm.Client.value  == "" ) {alert("' . vt('Geen Client opgegeven.') . '"); return false;}
    }
    else {alert("' . vt('Velden Vermogensbeheerder,Accountmanager,Depotbank,Portefeuille en Client niet gevonden.') . '");return false;}

  }
  
  '.$formChecks.'

  return true;
 }
 
 ';

$perioden=array('d'=>vt('Dagelijkse'),'m'=>vt('Maand'),'k'=>vt('Kwartaal'),'h'=>vt('Halfjaar'),'j'=>vt('Jaar'));
//else
//  $perioden=array('m'=>'Maand','k'=>'Kwartaal','h'=>'Halfjaar','j'=>'Jaar');

if ($action == 'update' || $action=='updateStay')
{
  if($gebruikPortefeuilleInformatie['CrmPortefeuilleInformatie'] > 0 )
  {
    foreach ($data as $key=>$value)
    {
      if(substr($key,0,10)=='rapVerzend')
      {
        $parts=explode('_',$key);
        $rapport["rap_".$parts[1]][$parts[2]]=$value;
      }
      if(substr($key,0,9)=='rapAantal')
      {
        $parts=explode('_',$key);
        $aantal[$parts[1]]=$value;
      }
      if(substr($key,0,4)=='MUT_')
      {
        $parts=explode('_',$key);
        $opties['MUT']['MUT_'.$parts[1]]=$value;
      }
    }
    foreach ($perioden as $periodeLetter=>$periode)
    {
      $prefix=$periodeLetter."_";
      $anchorLenght=strlen($prefix.'MUT_');
      foreach ($data as $key=>$value)
      {
        if(substr($key,0,$anchorLenght)==$prefix.'MUT_')
        {
          $parts=explode('UT_',$key);
          $opties[$periodeLetter]['MUT']['MUT_'.$parts[1]]=$value;
        }
      }
      if($gebruikPortefeuilleInformatie['Layout']==12)
      {
        $prefix=$periodeLetter."_";
        $anchorLenght=strlen($prefix.'mmIndex_');
        foreach ($data as $key=>$value)
        {
          if(substr($key,0,$anchorLenght)==$prefix.'mmIndex_')
          {
            $parts=explode('mmIndex_',$key);
            $opties[$periodeLetter]['mmIndex']['mmIndex_'.$parts[1]]=$value;
          }
        }
        
        $opties[$periodeLetter]['PERFG']['perfPstart']=$data[$prefix.'perfPstart'];
      }

      if($gebruikPortefeuilleInformatie['Layout']==13)
      {
        $opties[$periodeLetter]['PERFG']=array('PERFG_perc'=>$data[$prefix.'PERFG_perc'],'PERFG_totaal'=>$data[$prefix.'PERFG_totaal']);
        $opties[$periodeLetter]['TRANS']=array('TRANS_RESULT'=>$data[$prefix.'TRANS_RESULT']);
        $opties[$periodeLetter]['PERF']=array('vvgl'=>$data[$prefix.'vvgl'],'perc'=>$data[$prefix.'perc'],'opbr'=>$data[$prefix.'opbr'],'kost'=>$data[$prefix.'kost'],'kostPerc'=>$data[$prefix.'kostPerc']);
        $opties[$periodeLetter]['SMV']=array('GB_STORT_ONTTR'=>$data[$prefix.'GB_STORT_ONTTR'],'GB_overige'=>$data[$prefix.'GB_overige']);
      }
      if($gebruikPortefeuilleInformatie['Layout']==5)
      {
        $opties[$periodeLetter]['PERF']=array('perfBm'=>$data[$prefix.'perfBm']);
      }
    }
  }


  
  $intakeTemplate=false;
  if($data['template']=='intake')
  {
    $intakeTemplate = true;
  }
  if(!isset($object->data['fields']['rapportageVinkSelectie']['beperkt']) && $intakeTemplate==false)
  {
/*
    # sortering nog even uitgezet zal ook bij andere locacties waar instellingen aangepast kunnen worden moeten gebeuren.
    foreach ($perioden as $periodeLetter=>$periode)
    {
      if(is_array($data['rap_' . $periodeLetter]))
        sort($data['rap_' . $periodeLetter]);
    }
*/
    $data['rapportageVinkSelectie'] = serialize(array('rap_d' => $data['rap_d'], 'rap_m' => $data['rap_m'], 'rap_k' => $data['rap_k'], 'rap_h' => $data['rap_h'], 'rap_j' => $data['rap_j'], 'verzending' => $rapport, 'aantal' => $aantal, 'opties' => $opties));
  }
}

$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen
$editObject->usetemplate = true;

$eigenTemplate=false;
if($data['template']=='intake' && file_exists('CRM_nawEditTemplate_intake.html'))
{
  if($_GET['templateId'] && $_GET['templateId']>0 && file_exists("CRM_include/CRM_nawEditTemplate_intake_".$_GET['templateId'].".html"))
  {
    $editObject->formTemplate = "CRM_include/CRM_nawEditTemplate_intake_".$_GET['templateId'].".html";
    $eigenTemplate=true;
  }
  else
  {
    $editObject->formTemplate = "CRM_nawEditTemplate_intake.html";
  }
}
elseif($gebruikPortefeuilleInformatie['CRM_eigenTemplate']==1 && file_exists('CRM_nawEditTemplate_custom.html'))
{
  $editObject->formTemplate = "CRM_nawEditTemplate_custom.html";
  $eigenTemplate=true;
}
else
{
  $editObject->formTemplate = "CRM_nawEditTemplate.html";
}

if($action=='update')
{
  $object->getById($_POST['id']);
  $crmPortefeuille=$object->get('portefeuille');
}

if ($action == 'update'  || $action=='updateStay')
{
  if(GetCRMAccess(1) && isset($_DB_resources[DBportaal]) && count($_DB_resources[DBportaal])==4 && $gebruikPortefeuilleInformatie['portaalDailyClientSync']==1 && $_POST['id']>0 && $_POST['portefeuille'] <> '')
  {
    updateNawPortaalById(array('id' =>$_POST['id'], 'name' => $_POST['naam'], 'name1' => $_POST['naam1'], 'email' => $_POST['email'],'portefeuille'=>$_POST['portefeuille'],  'password' => $_POST['wachtwoord']),true);
  }
  elseif(GetCRMAccess(1) && isset($_DB_resources[DBportaal]) && count($_DB_resources[DBportaal])==4 && $_POST['portefeuille'] <> '')
  {
    updateNawPortaal(array('id' => $_POST['id'], 'name' => $_POST['naam'], 'name1' => $_POST['naam1'], 'email' => $_POST['email'], 'portefeuille'=>$_POST['portefeuille'], 'password' => $_POST['wachtwoord']));
  }
}

if($data['portefeuille']<>'')
{
  $db = new DB();
  $query = "SELECT id FROM Portefeuilles WHERE consolidatie=1 AND Portefeuille = '" . mysql_real_escape_string($data['portefeuille']) . "'";
  $db->SQL($query);
  $dbData = $db->lookupRecord();
  if ($dbData['id'] > 0)
  {
    $data['PortGec'] = 1;
  }
  else
  {
    $data['PortGec'] = 0;
  }
}

$editObject->controller($action,$data);

$subHeader    = "<a id=\"relatieInfo\" name=\"relatie Info\">".$object->get('naam')."</a>";
$mainHeader   = "relatie muteren,&nbsp;&nbsp;&nbsp;";
$editcontent['pageHeader'] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div>";

$editObject->template = $editcontent;
$legitimatieArray = GetSelectieVelden("legitimatie",false);
$telefoonVelden = GetSelectieVelden("telefoon",false);
$object->setOption("huwelijkseStaat"  ,"form_options",GetSelectieVelden("burgelijke staat",false));
$object->setOption("tel1_oms"         ,"form_options",$telefoonVelden);
$object->setOption("tel2_oms"         ,"form_options",$telefoonVelden);
$object->setOption("tel3_oms"         ,"form_options",$telefoonVelden);
$object->setOption("tel4_oms"         ,"form_options",$telefoonVelden);
$object->setOption("tel5_oms"         ,"form_options",$telefoonVelden);
$object->setOption("tel6_oms"         ,"form_options",$telefoonVelden);
$object->setOption("ondernemingsvorm" ,"form_options",GetSelectieVelden("rechtsvorm",true));
$object->setOption("legitimatie"      ,"form_options",$legitimatieArray);
$object->setOption("part_legitimatie" ,"form_options",$legitimatieArray);
$object->setOption("opleidingsniveau" ,"form_options",GetSelectieVelden("opleidingsniveau",false));

$object->setOption("inkomenSoort"          ,"form_options",GetSelectieVelden("soort inkomen",false));
$object->setOption("part_inkomenSoort"     ,"form_options",GetSelectieVelden("soort inkomen",false));
$object->setOption("beleggingsHorizon"     ,"form_options",GetSelectieVelden("beleggingshorizon",false));
$object->setOption("beleggingsDoelstelling","form_options",GetSelectieVelden("beleggingsdoelstelling",false));
$object->setOption("risicoprofiel"         ,"form_options",GetSelectieVelden("risicoprofiel",false));
$object->setOption("verzendFreq"           ,"form_options",GetSelectieVelden("verzend freq rapportage",false));
$object->setOption("inContactDoor"         ,"form_options",GetSelectieVelden("in contact door",false));
$object->setOption("clientenclassificatie" ,"form_options",GetSelectieVelden("clientenclassificatie",false));
$object->setOption("prospectStatus"        ,"form_options",GetSelectieVelden("prospect status",false));

$ervaringSelectie = GetSelectieVelden("ervaring",false);
$object->setOption("ervaringMetGestructureerdeProductenDatum","form_options",$ervaringSelectie);
$object->setOption("ervaringMetGestructureerdeProducten"     ,"form_options",$ervaringSelectie);
$object->setOption("ervaringBelegtInEigenbeheer"             ,"form_options",$ervaringSelectie);
$object->setOption("ervaringBelegtInVermogensadvies"         ,"form_options",$ervaringSelectie);
//$object->setOption("ervaringBelegtInProducten"               ,"form_options",$ervaringSelectie);
$object->setOption("ervaringMetVastrentende"                 ,"form_options",$ervaringSelectie);
$object->setOption("ervaringMetBeleggingsFondsen"            ,"form_options",$ervaringSelectie);
$object->setOption("ervaringMetIndividueleAandelen"          ,"form_options",$ervaringSelectie);
$object->setOption("ervaringMetOpties"                       ,"form_options",$ervaringSelectie);
$object->setOption("ervaringMetFutures"                      ,"form_options",$ervaringSelectie);
$object->setOption("ervaringInVermogensbeheer","form_options",$ervaringSelectie);
$object->setOption("ervaringInExecutionOnly","form_options",$ervaringSelectie);

if($object->get('CRMGebrNaam') <> '' && $object->data['fields']['CRMGebrNaam']['error'] =='')
  $object->setOption("CRMGebrNaam","form_extra",'readonly');

$object->setOption("portefeuille","form_extra",'onChange=\'portefeuilleChange();\'');

if(!GetCRMAccess(1))
  $object->setOption("portefeuille","form_extra",'disabled');

$object->setOption("huidigesamenstellingAandelen","form_extra","onBlur=\"this.value=formatNumericField(this);huidigeTotaal()\" style=\"text-align:right;\" ");
$object->setOption("huidigesamenstellingObligaties","form_extra","onBlur=\"this.value=formatNumericField(this);huidigeTotaal()\" style=\"text-align:right;\" ");
$object->setOption("huidigesamenstellingOverige","form_extra","onBlur=\"this.value=formatNumericField(this);huidigeTotaal()\" style=\"text-align:right;\" ");
$object->setOption("huidigesamenstellingLiquiditeiten","form_extra","onBlur=\"this.value=formatNumericField(this);huidigeTotaal()\" style=\"text-align:right;\" ");
$object->setOption("huidigesamenstellingTotaal","form_extra","READONLY style=\"text-align:right;\" ");

if(checkAccess($type))
  $beperktToegankelijk = "";
else
{
  $join = "JOIN VermogensbeheerdersPerGebruiker ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker = '".$USR."'
          JOIN Gebruikers ON Gebruikers.Gebruiker = VermogensbeheerdersPerGebruiker.Gebruiker
          JOIN Vermogensbeheerders ON VermogensbeheerdersPerGebruiker.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder ";
  $beperktToegankelijk = " AND (Portefeuilles.beperktToegankelijk = '0' OR  Gebruikers.beperkingOpheffen = '1' ) AND Vermogensbeheerders.CrmPortefeuilleInformatie = '1' ";
}

$query = "SELECT Gebruikers.id, Gebruikers.CRMlevel, Gebruikers.portefeuilledetailsAanleveren, max(Vermogensbeheerders.CrmTerugRapportage) as CrmTerugRapportage,
 max(Vermogensbeheerders.CrmAutomatischVerzenden) as CrmAutomatischVerzenden,
 max(Vermogensbeheerders.check_module_SCENARIO) as check_module_SCENARIO,
 max(Vermogensbeheerders.check_module_VRAGEN) as check_module_VRAGEN,
  Export_data_frontOffice ,Vermogensbeheerders.Vermogensbeheerder FROM Gebruikers
JOIN VermogensbeheerdersPerGebruiker ON Gebruikers.Gebruiker = VermogensbeheerdersPerGebruiker.Gebruiker
JOIN Vermogensbeheerders ON Vermogensbeheerders.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder
WHERE
VermogensbeheerdersPerGebruiker.Gebruiker = '".$USR."' GROUP BY Gebruikers.id ";
$db->SQL($query);
$gebruikersData = $db->lookupRecord();


$frontOfficeData=unserialize($gebruikersData['Export_data_frontOffice']);
foreach($frontOfficeData as $rapport=>$rapportData)
{
  if($rapportData['volgorde']=='')
    $rapportData['volgorde']=99;
  $rapportVolgorde[$rapportData['volgorde']][$rapport]=$rapportData;
}

ksort($rapportVolgorde);
$rapportenSorted=array();
foreach($rapportVolgorde as $volgordeId=>$rapportdata)
  foreach($rapportdata as $rapport=>$rapData)
  {
    if(isset($__appvar["Rapporten"][$rapport]))
      $rapportenSorted[$rapport]=$rapData;
  }
foreach ($__appvar["Rapporten"] as $rapport=>$omschrijving)
  $rapportenSorted[$rapport]['omschrijving']=$omschrijving;
$frontOfficeData=$rapportenSorted;

foreach ($rapportenSorted as $rapport=>$rapportData)
{
  if(isset($frontOfficeData[$rapport]['longName']) && $frontOfficeData[$rapport]['longName'] <> '')
    $rapportData['omschrijving']=$frontOfficeData[$rapport]['longName'];
  if(isset($frontOfficeData[$rapport]['shortName']) && $frontOfficeData[$rapport]['shortName'] <> '')
    $rapportData['short']=$frontOfficeData[$rapport]['shortName'];
  else
    $rapportData['short']=$rapport;
  $frontOfficeData[$rapport]=$rapportData;
}


if(($gebruikersData['id'] > 0 || checkAccess($type)) && $object->get('id') > 0 && $__appvar["crmOnly"]==false)
{
  
  $query = "SELECT GeconsolideerdePortefeuilles.id FROM GeconsolideerdePortefeuilles ".str_replace('Portefeuilles.Vermogensbeheerder','GeconsolideerdePortefeuilles.Vermogensbeheerder',$join)." WHERE VirtuelePortefeuille = '".$object->get('portefeuille')."'";
  $db->SQL($query);
  $dbData = $db->lookupRecord();
  if($dbData['id'] > 0)
    $virtuelePortefeuille['virtuelePortefeuilleId']=$dbData['id'];

  if($object->get('portefeuille') <> '')
  {
    $query = "SELECT * FROM laatstePortefeuilleWaarde WHERE portefeuille='" . $object->get('portefeuille') . "'";
    $db->SQL($query);
    $dbData = $db->lookupRecord();
    foreach ($dbData as $key => $value)
    {
      $editObject->formVars["laatstePortefeuilleWaarde." . $key] = $value;
    }
  }
  $query = "SELECT Portefeuilles.id ,Portefeuilles.consolidatie FROM Portefeuilles $join WHERE Portefeuille = '".$object->get('portefeuille')."' $beperktToegankelijk ";
  $db->SQL($query);
  $dbData = $db->lookupRecord();
  if($dbData['id'] > 0)
    $data['portefeuilleId']=$dbData['id'];

  if(1)//$virtuelePortefeuille['virtuelePortefeuilleId'] < 1)
  {
    $editObject->formVars["PortefeuilleTabs"]='
  	<input type="button" class="tabbuttonInActive" onclick="javascript:tabOpen(\'10\');" id="tabbutton10" name="but10" value="Portefeuille">
	  <input type="button" class="tabbuttonInActive" onclick="javascript:tabOpen(\'11\');" id="tabbutton11" name="but11" value="Beheerfee">
	  <input type="button" class="tabbuttonInActive" onclick="javascript:tabOpen(\'12\');" id="tabbutton12" name="but12" value="Staffels">
	  <input type="button" class="tabbuttonInActive" onclick="javascript:tabOpen(\'13\');" id="tabbutton13" name="but13" value="Overige">';
    if($dbData['consolidatie']==1)
    {
      $tables = array(
        'contractueleUitsluitingen'                           => '<option value="contractueleuitsluitingenList.php" >Contractueleuitsluitingen</option>',
        'ModelPortefeuillesPerPortefeuille'                   => '<option value="modelportefeuillesperportefeuilleList.php" >Modelportefeuille per portefeuille </option>',
        'NormwegingPerBeleggingscategorie'                    => '<option value="normwegingperbeleggingscategorieList.php" >Normweging per beleggingscategorie</option>',
        'portefeuilleClusters'                                => '<option value="portefeuilleclustersList.php" >Portefeuille clusters</option>',
        'PortefeuillesGeconsolideerd'                         => '<option value="portefeuillesgeconsolideerdList.php" >Portefeuilles Geconsolideerd</option>',
        'ReferentieportefeuillePerBeleggingscategorie'        => '<option value="referentieportefeuilleperbeleggingscategorieList.php" >Referentieportefeuille per beleggingscategorie</option>',
        'StandaarddeviatiePerPortefeuille'                    => '<option value="standaarddeviatieperportefeuilleList.php" >Standaarddeviatie per portefeuille</option>',
        'uitsluitingenModelcontrole'                          => '<option value="uitsluitingenmodelcontroleList.php" >Uitsluitingen Modelcontrole</option>',
        'ZorgplichtPerPortefeuille'                           => '<option value="zorgplichtperportefeuilleList.php" >Zorgplicht parameters per portefeuille</option>',
      );
    }
    else
    {
      $tables = array(
        'Beleggingsplan'                                      => '<option value="beleggingsplanList.php" >Beleggingsplan</option>',
        'contractueleUitsluitingen'                           => '<option value="contractueleuitsluitingenList.php" >Contractueleuitsluitingen</option>',
        'ModelPortefeuillesPerPortefeuille'                   => '<option value="modelportefeuillesperportefeuilleList.php" >Modelportefeuille per portefeuille </option>',
        'NormwegingPerBeleggingscategorie'                    => '<option value="normwegingperbeleggingscategorieList.php" >Normweging per beleggingscategorie</option>',
        'orderkosten'                                         => '<option value="orderkostenList.php" >Orderkosten per portefeuille</option>',
        'portefeuilleClusters'                                => '<option value="portefeuilleclustersList.php" >Portefeuille clusters</option>',
        'ReferentieportefeuillePerBeleggingscategorie'        => '<option value="referentieportefeuilleperbeleggingscategorieList.php" >Referentieportefeuille per beleggingscategorie</option>',
        'Rekeningen'                                          => '<option value="rekeningenList.php" >Rekeningen </option>',
        'StandaarddeviatiePerPortefeuille'                    => '<option value="standaarddeviatieperportefeuilleList.php" >Standaarddeviatie per portefeuille</option>',
        'uitsluitingenModelcontrole'                          => '<option value="uitsluitingenmodelcontroleList.php" >Uitsluitingen Modelcontrole</option>',
        'ZorgplichtPerPortefeuille'                           => '<option value="zorgplichtperportefeuilleList.php" >Zorgplicht parameters per portefeuille</option>',
      );
    }
    $options='';
    foreach($tables as $table=>$option)
      if($db->qrecords("SELECT id FROM $table limit 1") > 0)
        $options.=$option;

    $editObject->formVars["vulling_tab13"]='<script>
      function koppelingChanged(value)
      {
        $(\'#extraKoppelFrame\').attr(\'src\', value+"?portefeuille='.$object->get('portefeuille').'&frame=1");
      }
    </script>
    <div class="form">
      <fieldset id="Overige" >
        <legend> Overige</legend>
        <table border="0">
          <tr><td> Selectie:
         <select  class="" type="select"  name="koppeling"  id="koppeling"  onChange="javascript:koppelingChanged(this.value);" >
          <option value=""> --- </option>
          '.$options.'
         </select>
          <tr><td>
           <iframe id="extraKoppelFrame" name="extraKoppelFrame" src="blank.html" width="1200" height="1000" marginwidth="0" marginheight="0" hspace="0" vspace="0" align="middle" frameborder="0"></iframe>
          </td></tr>
        </table>

      </fieldset>
    </div>';
  }
  if($virtuelePortefeuille['virtuelePortefeuilleId'] > 0)
  {
    $editObject->formVars["PortefeuilleTabs"] .= '<input type="button" class="tabbuttonInActive" onclick="javascript:tabOpen(\'14\');" id="tabbutton14" name="but14" value="Consolidatie">';
    $editObject->formVars["vulling_tab14"]='<div class="form">
      <fieldset id="Consolidatie" >
        <legend> Consolidatie</legend>
         <script>
         function loadConsolidatie()
         {
           document.getElementById(\'consolidatieFrame\').src=\'geconsolideerdeportefeuillesEdit.php?action=edit&id='.$virtuelePortefeuille['virtuelePortefeuilleId'].'&frame=1\';
           
           
         }
         setTimeout(\'loadConsolidatie();\', 1500);
         </script>
        
        <iframe id="consolidatieFrame" name="consolidatieFrame" src="" width="1200" height="1000" marginwidth="0" marginheight="0" hspace="0" vspace="0" align="middle" frameborder="0"></iframe>
      </fieldset>
    </div>';
  }

  if($gebruikersData['CRMlevel'] > 0 && $gebruikersData['CrmTerugRapportage'] > 0 && $gebruikersData['portefeuilledetailsAanleveren'] == 1 && $object->get('id') > 0)
  {
    if($gebruikersData['CrmAutomatischVerzenden']==1)
      $editObject->formVars["naarAirsVerzenden"] .= '<tr><td> <input type="hidden" name="verzenden" value="1"></td></tr>';
    else
      $editObject->formVars["naarAirsVerzenden"] .= '<tr><td> <input type="checkbox" value="1" name="verzenden" id="verzenden"> ' . vt('Mutaties verzenden naar Airs?') . ' </td></tr>';
  }
  else
    $editObject->formVars["naarAirsVerzenden"] .= '<tr><td> <input type="hidden" name="verzenden" value="0"></td></tr>';

  $portefeuille = new Portefeuilles();
  if($dbData['id'] >0)
    $portefeuille->getById($dbData['id']);

  $huidigeKleurcode=$portefeuille->get('kleurcode');
  $huidigeKleurcode=unserialize($huidigeKleurcode);
  $editObject->formVars["kleurcode"]='';
  $kleuren=array('R','G','B');
  foreach ( $kleuren as $kleurKey => $kleur ) {
    
    $editObject->formVars["kleurcode"] .= ' <input size="3" maxlength="3" type="text" value="'.$huidigeKleurcode[$kleurKey].'" class="colorp" id="kleurcode_'.$kleur.'" data-group="kleurcode" name="kleurcode[]" >';
  }
  $editObject->formVars["kleurcode"] .= '<div id="kleurcode-colorPicker" class="colorpicker-component colorDisplayField" style="" title="Using format option">
                <input type="hidden" class="form-control input-lg" value=""/><span class="input-group-addon" ><i></i></span></div>';
  
  
  $overslaan = array('add_date','add_user','change_date','change_user','id');

  if ($action == 'update'  || $action=='updateStay')
  {
    if(checkAccess($type))
    {
      $editPortefeuille = new editObject($portefeuille);
      $editPortefeuille->__funcvar = $__funcvar;
      $editPortefeuille->__appvar  = $__appvar;
      if($data['portefeuille'])
        $data['Portefeuille'] = $data['portefeuille'];
      elseif ($data['Portefeuille'])
        $data['portefeuille'] = trim($data['Portefeuille']);
      $portefeuilleData = $data;
      $portefeuilleData['id'] = $data['portefeuilleId'];
      if(isset($data['kleurcode']) && is_array($data['kleurcode']))
        $portefeuilleData['kleurcode']=serialize($data['kleurcode']);
  
  
      if($portefeuilleData['id'] > 0 && $portefeuilleData['Portefeuille'] <> '' && $portefeuilleData['Vermogensbeheerder'] <>'')
      {
        $portefeuille = new Portefeuilles();
        $editObject2 = new editObject($portefeuille);
        $editObject2->__funcvar = $__funcvar;
        $editObject2->__appvar = $__appvar;
        $editObject2->controller($action,$portefeuilleData);
        $editObject2->getOutput();
      }
      else
      {
        //echo "Missende Portefeuille waarde.Vermogensbeheerder=". $portefeuilleData['Vermogensbeheerder']." Portefeuille=". $portefeuilleData['Portefeuille']."<br>\n";
      }

    }
  }

  if($action == 'edit' || $action == 'new'  || $action=='updateStay')
  {
    foreach ($portefeuille->data['fields'] as $key=>$data)
    {
      if(!in_array($key,$overslaan))
      {
        $object->setOption($key,'value',$data['value']);
        $object->setOption($key,'form_type',$data['form_type']);
        $object->setOption($key,'form_extra',$data['form_extra']);
        $object->setOption($key,'form_size',$data['form_size']);
        $object->setOption($key,'form_rows',$data['form_rows']);
        $object->setOption($key,'form_options',$data['form_options']);
        $object->setOption($key,'select_query',$data['select_query']);
        $object->setOption($key,'select_query_ajax',$data['select_query_ajax']);
        $object->setOption($key,'form_visible',$data['form_visible']);
        $object->setOption($key,'description',$data['description']);

        if($data['crm_readonly'])
        {
          if($key=='Startdatum')
          {
            if($portefeuille->get('Depotbank') <> 'INT')
              $object->setOption($key, 'form_extra', 'disabled');
          }
          elseif($dbData['id'] >0)
          {
            $object->setOption($key, 'form_extra', 'disabled');
          }

        }
        if($key=='Portefeuille')
          $object->setOption($key,'form_extra',' id="Portefeuille_Portefeuille" ');

      }
      $editObject->formVars['portefeuilleId'] = $dbData['id'];
    }

    $object->setOption("BeheerfeeBasisberekening", "form_select_option_notempty" ,true);
    $object->setOption("BeheerfeeBasisberekening", "form_options" ,$__appvar["BeheerfeeBasisberekening"]);
    $object->data['fields']["Taal"]["form_options"] = $__appvar["TaalOptions"];

    if($dbData['id'] >0)
    {
  
      $db=new DB();
      $query="SELECT Portefeuilles.Vermogensbeheerder FROM Portefeuilles WHERE id='" . $dbData['id'] . "'";
      $db->SQL($query);
      $db->Query();
      $verm = $db->nextRecord();
  
      $query = "SELECT
VermogensbeheerdersPerBedrijf.bedrijf,
count(aantal.Vermogensbeheerder) as aantal
FROM
VermogensbeheerdersPerBedrijf
INNER JOIN VermogensbeheerdersPerBedrijf as aantal ON VermogensbeheerdersPerBedrijf.Bedrijf = aantal.Bedrijf
WHERE VermogensbeheerdersPerBedrijf.Vermogensbeheerder='" . $verm['Vermogensbeheerder'] . "' GROUP BY VermogensbeheerdersPerBedrijf.bedrijf";
      $db->SQL($query);
      $db->Query();
      $bedrijf = $db->nextRecord();
      if ($bedrijf['aantal'] > 1)
      {
        $accountmanagerQuery = "SELECT
Accountmanagers.Accountmanager AS `Value`,
concat(Accountmanagers.Accountmanager, ' - ',Accountmanagers.Vermogensbeheerder)
FROM
Accountmanagers
INNER JOIN VermogensbeheerdersPerBedrijf ON Accountmanagers.Vermogensbeheerder = VermogensbeheerdersPerBedrijf.Vermogensbeheerder
WHERE VermogensbeheerdersPerBedrijf.bedrijf='" . $bedrijf['bedrijf'] . "'
ORDER BY if(Accountmanagers.Vermogensbeheerder='".$verm['Vermogensbeheerder']."',0,1),Accountmanagers.Vermogensbeheerder,Accountmanagers.Accountmanager";
      }
      else
      {
        $accountmanagerQuery = "SELECT Accountmanagers.Accountmanager,Accountmanagers.Accountmanager FROM  Accountmanagers WHERE Accountmanagers.Vermogensbeheerder = '" . $verm['Vermogensbeheerder'] . "' ORDER BY Accountmanagers.Accountmanager";
      }
      $object->setOption('Risicoklasse', 'select_query', "SELECT Risicoklassen.Risicoklasse,Risicoklassen.Risicoklasse FROM  Risicoklassen WHERE Risicoklassen.Vermogensbeheerder = '" . $verm['Vermogensbeheerder'] . "'");
      $object->setOption('Accountmanager', 'select_query', $accountmanagerQuery);
      $object->setOption('tweedeAanspreekpunt', 'select_query', $accountmanagerQuery);
      $object->setOption('Remisier', 'select_query', "SELECT Remisiers.Remisier,Remisiers.Remisier FROM Remisiers,Portefeuilles  WHERE Remisiers.Vermogensbeheerder = '" . $verm['Vermogensbeheerder'] . "'");
      $object->setOption('SoortOvereenkomst', 'select_query', "SELECT KeuzePerVermogensbeheerder.waarde,KeuzePerVermogensbeheerder.waarde FROM KeuzePerVermogensbeheerder WHERE KeuzePerVermogensbeheerder.categorie='soortovereenkomsten' AND Vermogensbeheerder='".$verm['Vermogensbeheerder']."' ");
  
//      $object->setOption('Risicoklasse','select_query', "SELECT Risicoklassen.Risicoklasse,Risicoklassen.Risicoklasse FROM Portefeuilles, Risicoklassen WHERE Portefeuilles.Vermogensbeheerder = Risicoklassen.Vermogensbeheerder AND Portefeuilles.id = '".$dbData['id']."'");
//      $object->setOption('Accountmanager','select_query', "SELECT Accountmanagers.Accountmanager,Accountmanagers.Accountmanager FROM Portefeuilles, Accountmanagers WHERE Portefeuilles.Vermogensbeheerder = Accountmanagers.Vermogensbeheerder AND Portefeuilles.id = '".$dbData['id']."'");
//      $object->setOption('tweedeAanspreekpunt','select_query', "SELECT Accountmanagers.Accountmanager,Accountmanagers.Accountmanager FROM Portefeuilles, Accountmanagers WHERE Portefeuilles.Vermogensbeheerder = Accountmanagers.Vermogensbeheerder AND Portefeuilles.id = '".$dbData['id']."'");
//      $object->setOption('Remisier','select_query', "SELECT Remisiers.Remisier,Remisiers.Remisier FROM Remisiers,Portefeuilles  WHERE Portefeuilles.Vermogensbeheerder = Remisiers.Vermogensbeheerder AND Portefeuilles.id = '".$dbData['id']."'");
//      $object->setOption('SoortOvereenkomst','select_query', "SELECT KeuzePerVermogensbeheerder.waarde,KeuzePerVermogensbeheerder.waarde FROM KeuzePerVermogensbeheerder WHERE KeuzePerVermogensbeheerder.categorie='soortovereenkomsten' AND Vermogensbeheerder IN(SELECT vermogensbeheerder FROM Portefeuilles WHERE Portefeuilles.id = '".$dbData['id']."')");
      $object->setOption('Client','form_type', "text");
    }
    else
    {
      $object->setOption('Client','form_type', "text");
      $object->setOption('Client','value', strtoupper(str_replace(".","",str_replace(" ","",$object->get('achternaam').$object->get('voorletters')))));
      $object->setOption('Risicoklasse','select_query', "SELECT Risicoklassen.Risicoklasse,Risicoklassen.Risicoklasse FROM Risicoklassen ");
      $object->setOption('Accountmanager','select_query', "SELECT Accountmanagers.Accountmanager,Accountmanagers.Accountmanager FROM Accountmanagers ORDER BY Accountmanagers.Accountmanager");
      $object->setOption('tweedeAanspreekpunt','select_query', "SELECT Accountmanagers.Accountmanager,Accountmanagers.Accountmanager FROM Accountmanagers ORDER BY Accountmanagers.Accountmanager");
      $object->setOption('Remisier','select_query', "SELECT Remisiers.Remisier,Remisiers.Remisier FROM Remisiers");
    }
    $object->setOption('Vermogensbeheerder',"form_extra"," onChange=\"javascript:vermogensbeheerderChanged();\" ");


    $DB = new DB();
    $DB->SQL("SELECT BeheerfeePerformanceDrempelBedrag,BeheerfeePerformancePercentage, BeheerfeePerformanceDrempelPercentage,BeheerfeeMinJaarBedrag,BeheerfeeBTW,BeheerfeeMethode,BeheerfeeRemisiervergoedingsPercentage,BeheerfeeTeruggaveHuisfondsenPercentage,BeheerfeeAdministratieVergoeding  FROM Portefeuilles WHERE id = '".$dbData['id']."'");
    $DB->Query();
    $bf = $DB->NextRecord();
    $methoden=array('Geen, Toevoegen aan portefeuille','Standaard (op basis van staffels)','Standaard + procentuele korting','Percentage over vermogen in beheer','Bedrag');
    for($a=0; $a <=7; $a++)
    {
      if($eigenTemplate==true)
      {
        $editObject->formVars["Methode_" . $a] = "<input type=\"radio\" name=\"BeheerfeeMethode\" value=\"$a\" ";
        
        if (($bf["BeheerfeeMethode"] == $a && $dbData['id'] > 0) || ($portefeuille->data['fields']['BeheerfeeMethode']['default_value'] == $a && $dbData['id'] == 0))
        {
          $editObject->formVars["Methode_" . $a] .= "checked";
        }
        $editObject->formVars["Methode_" . $a] .= ' >' . $methoden[$a];
      }
      else
      {
        if (($bf["BeheerfeeMethode"] == $a && $dbData['id'] > 0) || ($portefeuille->data['fields']['BeheerfeeMethode']['default_value'] == $a && $dbData['id'] == 0))
        {
          $editObject->formVars["Methode_" . $a] .= "checked";
        }
      }
      //$editObject->formVars["Methode_".$a] ="<input type=\"radio\" name=\"BeheerfeeMethode\" value=\"$a\" ";
      //$editObject->formVars["Methode_".$a] .= ' >'. $methoden[$a];
    }

  }
  if ($action == 'update'  || $action=='updateStay')
  {
    foreach ($portefeuille->data['fields'] as $key=>$data)
    {
      if(isset($_POST[$key]))
      {
        $_POST[$key] = trim($_POST[$key]);
  
        if(isset($_POST['kleurcode']) && is_array($_POST['kleurcode']))
          $_POST['kleurcode']=serialize($_POST['kleurcode']);
        
        $data['value'] = trim($data['value']);
        if($data['form_type']=='checkbox' || $data['db_type']=='double' || $data['db_type']=='text' || $data['db_type']=='tinyint')
        {
          if($data['value']=="0" && $_POST[$key]=='')// ==''
            $data['value']='';
          if($data['value']=='' && $_POST[$key]=="0")// ==''
            $_POST[$key]='';
        }
        if($key=='Portefeuille')
        {
          $_POST[$key]=preg_replace("/[^A-Z0-9-_ \.]/i", "", $_POST[$key]);
        }
        elseif($key=='Client')
        {
          $_POST[$key]=preg_replace("/['`]/i", "", $_POST[$key]);
        }
  
        if($key=='Startdatum')
        {
          if($portefeuille->get('Depotbank') == 'INT')
            $data['crm_readonly']=false;
        }

        if(!in_array($key,$overslaan) && ($data['crm_readonly'] == false || $dbData['id'] < 1))
        {
          if($data['default_value']==$data['value'] && $portefeuille->get('id') < 1)
            $data['value']='';

          if($data['form_type']=='datum' || $data['form_type']=='calendar' )
          {
            if(substr($data['value'],0,10) != formdate2db($_POST[$key]) && !empty($_POST[$key]))
            {
              $mutaties['Portefeuilles'][$key]['oud']=substr($data['value'],0,10);
              $mutaties['Portefeuilles'][$key]['nieuw']=jul2sql(form2jul($_POST[$key]));
            }
          }
          elseif($_POST[$key] != $data['value'] || ($_POST[$key]=='' && $data['value'] <> ''))
          {
            $mutaties['Portefeuilles'][$key]['oud']=$data['value'];
            $mutaties['Portefeuilles'][$key]['nieuw']=$_POST[$key];
            //echo "k: $key | p: ".$_POST[$key]."| d:".$data['value']." <br>\n";
            //listarray($data);
          }
        }
      }
    }

    // listarray($mutaties);exit;
    if($_POST['verzenden'])
    {
      $tmpPortefeuilleBijwerken=false;

      if($_POST['Portefeuille']=='')
        $_POST['verzenden']=false;


      if( ($_POST['Portefeuille']=='' && (count($mutaties['Portefeuilles']) > 0 && $crmPortefeuille<>'')   ))
      {
        echo "Portefeuille veld is niet gevuld. Verzenden (".count($mutaties['Portefeuilles']).") mutaties niet mogelijk.<br>\n";
        exit;
      }

      $DB = new DB();
      if($_POST['portefeuilleId'] =='' && $DB->QRecords("SELECT id FROM Portefeuilles WHERE Portefeuille='".$_POST['Portefeuille']."'"))
      {
        echo "Portefeuille is al aanwezig maar nog niet gekoppeld. Verzenden mutaties niet mogelijk.<br>\n";
        exit;
      }
      //echo $portefeuille->data['fields']['Einddatum']['value'];exit;
      if(form2jul($portefeuille->data['fields']['Einddatum']['value']) < time())
      {
        echo "Portefeuille heeft een einddatum. Verzenden mutaties niet mogelijk.<br>\n";
        exit;
      }

      if($__appvar["bedrijf"]=='ANO')
      {
        if ($_POST['portefeuilleId'] == '' && $DB->QRecords("SELECT id FROM CRM_naw WHERE tempPortefeuille='" . $_POST['Portefeuille'] . "'"))
        {
          echo "Portefeuille heeft al een tijdelijke koppeling in met een CRM record.<br>\n";
          exit;
        }
        elseif ($_POST['portefeuilleId'] == '' && $object->get('tempPortefeuille') <> '')
        {
          echo "CRM record heeft al een tijdelijke koppeling met " . $object->get('tempPortefeuille') . ".<br>\n";
          exit;
        }
        else
        {
          $tmpPortefeuilleBijwerken = true;
        }
      }
    }


    if($_POST['verzenden'])
    {
      $tableIds['Portefeuilles']=$portefeuille->get('id');
      if($tableIds['Portefeuilles'] < 1)
      {
        $tableIds['Portefeuilles']="9".sprintf("%05d", $gebruikersData['id']).date('ymdHis');
        if($_POST['Client'] !='')
        {
          $tableIds['Clienten']     ="9".sprintf("%05d", $gebruikersData['id']).(date('ymdHis')+1);
          $mutaties['Clienten']=array('Client'=>array('oud'=>'','nieuw'=>$_POST['Client']));
        }
      }
      else
      {
        if($_POST['Client'] !='')
        {
          if($mutaties['Portefeuilles']['Client']['oud'] != $mutaties['Portefeuilles']['Client']['nieuw'])
          {
            $tableIds['Clienten'] = "9" . sprintf("%05d", $gebruikersData['id']) . (date('ymdHis') + 1);
            $mutaties['Clienten'] = array('Client' => array('oud' => '', 'nieuw' => $_POST['Client']));
          }
        }
      }

      $vermogensbeheerder=$portefeuille->get('Vermogensbeheerder');
      if($vermogensbeheerder =='')
        $vermogensbeheerder=$_POST['Vermogensbeheerder'];

      if($vermogensbeheerder=='')
      {
        echo "Vermogensbeheerder veld is niet gevuld!<br>\n";
        exit;
      }
      $counter=0;

      foreach ($mutaties as $tabel=>$wijziging)
      {
        foreach ($wijziging as $veld=>$waarden)
        {
          if($veld=='Einddatum' && adodb_db2jul($waarden['nieuw']) < (time()+31536000) )
          {
            $object->set("aktief",0);
            $object->save();
          }
          $queueDB = new DB(2);
          $query = "INSERT INTO klantMutaties SET
                  tabel = '".mysql_real_escape_string($tabel)."',
                  recordId = '".$tableIds[$tabel]."',
                  veld='".mysql_real_escape_string($veld)."',
                  oudeWaarde='".mysql_real_escape_string($waarden['oud'])."',
                  nieuweWaarde='".mysql_real_escape_string($waarden['nieuw'])."',
                  verwerkt='0',
                  Vermogensbeheerder='".mysql_real_escape_string($vermogensbeheerder)."',
                  emailAdres='".mysql_real_escape_string($_SESSION['usersession']['gebruiker']['emailAdres'])."',
                  add_date = now(),add_user = '$USR',change_date = now() , change_user = '$USR' ";
          $queueDB->SQL($query);
          if(!$queueDB->Query())
          {
            $foutMeldingen .= "Verzenden van aanpassingsverzoek '".$waarden['oud']."' naar ".$waarden['oud']." mislukt. <br>\n";
            //logIt($query." ".$foutMeldingen);
          }
          else
          {
            $lastId=$queueDB->last_id();
            $query.=", id = $lastId";
            $DB=new DB();
            $DB->SQL($query);
            $DB->Query();
            //logIt("CRM klantMutaties: ".$query);
          }
          $counter++;
        }
      }
      if($foutMeldingen)
        $_SESSION['verzendStatus']= $foutMeldingen;
      elseif ($counter == 0 && $gebruikersData['CrmAutomatischVerzenden']==0)
        $_SESSION['verzendStatus']= "<br><b>Geen aanpassingen gevonden?</b>";
      elseif($counter > 0)
        $_SESSION['verzendStatus']= "<br><b>Aanpassingsverzoek ($counter) velden verzonden.</b>";

      if($tmpPortefeuilleBijwerken==true)
      {
        $object->set('tempPortefeuille',$_POST['Portefeuille']);
        $object->save();
      }
    }
  }
}
if ($action == "new")
{
  // $object->set("debiteur",1);
  $object->set("aktief",1);
  if ($_GET['port'])
  {
    $object->set("portefeuille",$_GET['port']);
    $object->set("naam",$clientRec['Naam'].$clientRec['Naam1']);
    $object->set("adres",$clientRec['Adres']);
    $object->set("pc",$clientRec['pc']);
    $object->set("plaats",$clientRec['Woonplaats']);
    $object->set("tel1",$clientRec['Telefoon']);
    $object->set("fax",$clientRec['Fax']);
    $object->set("email",$clientRec['E-mail']);
    $object->set("memo",'toegevoegd via Frontoffice selecte');
  }

}
else
{
  if (!GetCRMAccess(1))
  {
    $object->setOption("debiteur" ,"form_extra","DISABLED");
    $object->setOption("crediteur" ,"form_extra","DISABLED");
    $object->setOption("prospect" ,"form_extra","DISABLED");
    $object->setOption("overige" ,"form_extra","DISABLED");
    $object->setOption("tag" ,"form_extra","DISABLED");
    $object->setOption("aktief" ,"form_extra","DISABLED");
    $object->setOption("contactTijd" ,"form_extra","DISABLED");
  }
}
$object->setOption("rekeningnr","form_extra","onChange=\"elfProef(this);\" ");

$isDebiteur = $object->get("debiteur");
$isCrediteur = $object->get("crediteur");
if ($object->error)
{
  echo "<h4><font color=\"maroon\">" . vt('Er zijn velden fout ingevuld in dit formulier, na correctie kunt u opnieuw opslaan') . "</font></h4>";
}

if($object->get('enOfRekening'))
  $editObject->formVars['enOfcheck'] = 'CHECKED';

$errorHtml .='
			<script type="text/javascript">
			function hideStatus()
			{
			  javascript:document.getElementById("status").style.visibility="hidden";
			}
			</script>';
$useError=0;
foreach ($editObject->object->data['fields'] as $field=>$values)
{
  if($values['error'])
  {
    if($useError == 0)
    {
      $errorHtml .='<div id=status STYLE="position:absolute;top:10px;left:20px;background:white;border:1px dashed #000000;padding:30px;margin:30px;z-index:1;" >';
      $useError=1;
    }
    $errorHtml .= $values['description']." ".$values['error']."<br/>\n";
  }
}
if($object->get("contactTijd") > 0)
{
  $db->SQL("SELECT count(id) as aantal FROM CRM_naw_dossier WHERE datum > (now() - interval '".$object->get("contactTijd")."'  day) AND rel_id = '".$object->get("id")."' AND ClientGesproken=1 ");
  $tmp=$db->lookupRecord();
  if($tmp['aantal'] < 1)
    $editObject->formVars['contactTijdStyle'] = 'style="background-color :#ee0000;"';
}
if($useError == 1)
  $errorHtml .='		  <br/>
			<a href="javascript:hideStatus();" class="letterButton"> verbergen. </a>
      </div>';

if($_SESSION['verzendStatus'] && $action=='updateStay')
{
  $errorHtml.=$_SESSION['verzendStatus'];
  unset($_SESSION['verzendStatus']);
}

$editObject->formVars['errorHtml']=$errorHtml;

$editObject->formVars['koppelingen']=
  "<table>
<tr class=\"list_kopregel\"><td>Koppeling</td><td>Aantal</td> </tr>
<tr><td>Relaties</td><td align=\"right\">".$db->QRecords("SELECT id FROM CRM_naw_kontaktpersoon WHERE rel_id = '".$object->get("id")."'"). "</td> </tr>
<tr><td>Gespreksverslagen</td><td align=\"right\">".$db->QRecords("SELECT id FROM CRM_naw_dossier WHERE rel_id = '".$object->get("id")."'"). "</td> </tr>
<tr><td>Documenten</td><td align=\"right\">".$db->QRecords("SELECT id FROM dd_reference WHERE module_id  = '".$object->get("id")."' AND module='CRM_naw' "). "</td> </tr>
<tr><td>Evenementen</td><td align=\"right\">".$db->QRecords("SELECT id FROM CRM_evenementen WHERE rel_id = '".$object->get("id")."'"). "</td> </tr>
<tr><td>Adressen</td><td align=\"right\">".$db->QRecords("SELECT id FROM CRM_naw_adressen WHERE rel_id = '".$object->get("id")."'"). "</td> </tr>
<tr><td>Rekeningen</td><td align=\"right\">".$db->QRecords("SELECT id FROM CRM_naw_rekeningen WHERE rel_id = '".$object->get("id")."'"). "</td> </tr>
<tr><td>Niet afgewerkte taken</td><td align=\"right\">".$db->QRecords("SELECT id FROM taken WHERE rel_id = '".$object->get("id")."' AND afgewerkt = 0 "). "</td> </tr>
</table>
";

if($gebruikPortefeuilleInformatie['CrmPortefeuilleInformatie'] > 0)
{
  $_SESSION['lastPost']=array();
  $data['rapportageVinkSelectie']=unserialize($object->get('rapportageVinkSelectie'));//listarray($data['rapportageVinkSelectie']);
  $opties='';
  foreach ($perioden as $periodeLetter=>$periode)
  {
    $prefix=$periodeLetter."_";
    $checks=array();
    if($gebruikPortefeuilleInformatie['Layout']==13)
    {
      if($data['rapportageVinkSelectie']['opties'][$periodeLetter]['PERF']['vvgl'] == 1)
        $checks['vvglCheck'] = 'checked';
      if($data['rapportageVinkSelectie']['opties'][$periodeLetter]['PERF']['perc'] == 1)
        $checks['percCheck'] = 'checked';
      if($data['rapportageVinkSelectie']['opties'][$periodeLetter]['PERF']['opbr'] == 1)
        $checks['opbrCheck'] = 'checked';
      if($data['rapportageVinkSelectie']['opties'][$periodeLetter]['PERF']['kost'] == 1)
        $checks['kostCheck'] = 'checked';
      if($data['rapportageVinkSelectie']['opties'][$periodeLetter]['PERF']['kostPerc'] == 1)
        $checks['kostPerc'] = 'checked';

      if($data['rapportageVinkSelectie']['opties'][$periodeLetter]['PERFG']['PERFG_totaal'] == 1)
        $checks['PERFG_totaalCheck'] = 'checked';
      if($data['rapportageVinkSelectie']['opties'][$periodeLetter]['PERFG']['PERFG_perc'] == 1)
        $checks['PERFG_percCheck'] = 'checked';

      if($data['rapportageVinkSelectie']['opties'][$periodeLetter]['SMV']['GB_STORT_ONTTR'] == 1)
        $checks['STORT_ONTTRCheck'] = 'checked';
      if($data['rapportageVinkSelectie']['opties'][$periodeLetter]['SMV']['GB_overige'] == 1)
        $checks['overigeCheck'] = 'checked';

      if($data['rapportageVinkSelectie']['opties'][$periodeLetter]['TRANS']['TRANS_RESULT'] == 1)
        $checks['TRANS_RESULT'] = 'checked';

      foreach ($data['rapportageVinkSelectie']['opties'][$periodeLetter]['MUT'] as $grootboek=>$check)
        $checks[$prefix.$grootboek] = $check;

      include('rapportFrontofficeClientSelectieLayout.php');

      $opties.= $rapportSettings[13]."<script>$('#".$prefix."settingsContainer').hide();
     
        $('.".$prefix."PERF_Settings').show();
        $('#".$prefix."MUT_Settings').show();
        $('.".$prefix."MUT_Settings').show();
        $('.".$prefix."SMV_Settings').show();
        $('.".$prefix."TRANS_Settings').show();
        $('.".$prefix."Model_Settings').show();
      
      </script>";//$('#".$prefix."MUT_Settings').show();$('#".$prefix."SMV_Settings').show();$('#".$prefix."TRANS_Settings').show();$('#".$prefix."Model_Settings').show();
    }
    elseif($gebruikPortefeuilleInformatie['Layout']==5)
    {
      foreach ($data['rapportageVinkSelectie']['opties'][$periodeLetter]['MUT'] as $grootboek=>$check)
        $checks[$prefix.$grootboek] = $check;
      if($data['rapportageVinkSelectie']['opties']['PERF']['perfBm'] == 1)
        $perfBm = 'checked';
      include('rapportFrontofficeClientSelectieLayout.php');
      $opties.=$rapportSettings[5]."<script>$('#".$prefix."settingsContainer').hide();$('#".$prefix."MUT_Settings').show();</script>";
    }
    elseif($gebruikPortefeuilleInformatie['Layout']==12)
    {
      foreach ($data['rapportageVinkSelectie']['opties'][$periodeLetter]['MUT'] as $grootboek=>$check)
        $checks[$prefix.$grootboek] = $check;
      foreach ($data['rapportageVinkSelectie']['opties'][$periodeLetter]['mmIndex'] as $fonds=>$check)
        $checks[$prefix.$fonds] = 1;
      if($data['rapportageVinkSelectie']['opties'][$periodeLetter]['PERFG']['perfPstart'] == 1)
        $perfPstart = 'checked';
      else
        $perfPstart='';
      include('rapportFrontofficeClientSelectieLayout.php');
  
      $opties.= $rapportSettings[12]."<script>$('#".$prefix."settingsContainer').hide();
              $('.".$prefix."MUT_Settings').show();
              $('.".$prefix."PERFG_Settings').show();
              $('.".$prefix."mmIndex_Settings').show();

      </script>";
    }
    else
    {
      if(is_array($data['rapportageVinkSelectie']['opties'][$periodeLetter]))
        foreach ($data['rapportageVinkSelectie']['opties'][$periodeLetter]['MUT'] as $grootboek=>$check)
          $checks[$prefix.$grootboek] = $check;
      include('rapportFrontofficeClientSelectieLayout.php');
      $opties.='<td valign="top">
<div id="'.$prefix.'settingsContainer">'.$rapportSettings['default'].'</div></td>';
      $opties.="<script>$('#".$prefix."settingsContainer').hide();$('#".$prefix."MUT_Settings').show();</script>";//$('#".$prefix."SMV_Settings').show();$('#".$prefix."TRANS_Settings').show();$('#".$prefix."Model_Settings').show();
    }
  }


  $rapportageMatrix='<table data-type="rapportageMatrix"><tr><td><table>';
  if($gebruikPortefeuilleInformatie['check_portaalCrmVink']==1)
    $verzendTypen=array('email'=>vt('eMail'),'papier'=>vt('Papier'),'portaal'=>vt('Portaal'),'geen'=>vt('Geen'));
  else
    $verzendTypen=array('email'=>vt('eMail'),'papier'=>vt('Papier'),'geen'=>vt('Geen'));

  $rapportageMatrix.='<tr><td><b>' . vt('Verzending') . '<b></td>';
  foreach ($perioden as $periodeLetter=>$periode)
    $rapportageMatrix.="<td><b>" . vt($periode) . "</b></td>";
  $rapportageMatrix.="</tr>";

  if(isset($object->data['fields']['rapportageVinkSelectie']['beperkt']))
  {
    $disabled='disabled';
    $readonly='readonly';
  }
  else
  {
    $disabled='';
    $readonly='';
  }
  foreach ($verzendTypen as $type=>$omschrijving)
  {
    $rapportageMatrix.='<tr data-type="verzending"><td><b><label title="'.vt($omschrijving).'">'.vt($omschrijving).'</label> </b></td>';
    foreach ($perioden as $periodeLetter=>$periode)
    {
      if($data['rapportageVinkSelectie']['verzending']['rap_'.$periodeLetter][$type] == 1)
        $checked='checked';
      else
        $checked='';
      if($periodeLetter =='d' && $type <> 'portaal')
        $rapportageMatrix.="<td> &nbsp; </td>";
      else
        $rapportageMatrix.="<td> <input type='checkbox' $disabled $checked name='rapVerzend_".$periodeLetter."_$type' value='1'></td>";

    }
    $rapportageMatrix.="<tr>\n";
  }
  $rapportageMatrix.='<tr><td><b>' . vt('Afdrukken') . '<b></td>';
  foreach ($perioden as $periodeLetter=>$periode)
    $rapportageMatrix.="<td><b>" . vt($periode) . "</b></td>";

  $rapportageMatrix.='<tr data-type="afdrukken"><td><b>' . vt('aantal') . '</b></td>';
  foreach ($perioden as $periodeLetter=>$periode)
    if($periodeLetter <> 'd')
      $rapportageMatrix.="<td><input type='text' $readonly name='rapAantal_".$periodeLetter."' size='2' value='".$data['rapportageVinkSelectie']['aantal'][$periodeLetter]."'></td>";
    else
      $rapportageMatrix.="<td>&nbsp;</td> ";

  $rapportageMatrix.='</tr>
  <tr><td>&nbsp;</td></tr>
  <tr><td><b>' . vt('Rapport') . '<b></td>';
  foreach ($perioden as $periodeLetter=>$periode)
    $rapportageMatrix.='<td><div class="buttonDiv" title="Klik hier om de extra rapportage instelling voor de '.strtolower($periode).' rapportages te tonen." onclick="$(\'#'.$periodeLetter.'_settingsContainer\').toggle();"><b>'.vt($periode).'</b></div></td>';


  // $rapportageMatrix.="<td><a href=\"javascript:$('#".$periodeLetter."_settingsContainer').toggle();\"><b>$periode</b></a></td>";

//  $rapportageMatrix.="<td><b>ytd<b></td>";
  $rapportageMatrix.="</tr>";
  foreach ($frontOfficeData as $rapport=>$rapportData)
  {
    if($rapportData['shortName']<>'')
      $shortname=$rapportData['shortName'];
    else
      $shortname=$rapportData['short'];

    $omschrijving=$rapportData['omschrijving'];
    if($frontOfficeData[$rapport]['toon']==1) {
      $regel='<tr data-type="rapport"><td><b><label for="'.$rapport.'" title="'.$omschrijving.'">'.$shortname.'</label> </b></td>';
    } elseif($rapportData['toonNiet'] == 0) {
      $regel='<tr data-type="overige"><td><b><label for="'.$rapport.'" title="'.$omschrijving.'">'.$shortname.'</label> </b></td>';
    }

    foreach ($perioden as $periodeLetter=>$periode)
    {
      if(in_array($rapport,$data['rapportageVinkSelectie']['rap_'.$periodeLetter]))
        $checked='checked';
      else
        $checked='';
      $regel.="<td> <input type='checkbox' $disabled $checked name='rap_".$periodeLetter."[]' value='$rapport'></td>";
    }

    $regel.="<tr>\n";

    if($frontOfficeData[$rapport]['toon']==1)
      $rapportageMatrix.=$regel;
    elseif($rapportData['toonNiet'] == 0)
      $rapportageMatrixEind.=$regel;
  }

  $rapportageMatrix.='<tr><td><b>' . vt('Overige') . '<b></td>';
  foreach ($perioden as $periodeLetter=>$periode)
    $rapportageMatrix.="<td><b>" . vt($periode) . "</b></td>";
  //$rapportageMatrix.="<td><b>ytd<b></td>";
  $rapportageMatrix.="</tr>";
  $rapportageMatrix .=$rapportageMatrixEind;


  $rapportageMatrix.='</table>
  </td>'.$opties.'</tr></table>';
  $editObject->formVars['rapportageMatrix']=$rapportageMatrix;
}
if($gebruikPortefeuilleInformatie['kwartaalCheck']==1)
  $editObject->formVars['rapportageMatrix'].="<input type='hidden' name='kwartaalCheck' value='1'>";

for($i=1;$i<7;$i++)
  $editObject->formVars["bel".$i] = "<a href='callto://".$object->get("tel".$i)."' title='kies telefoonnummer' ><img src='icon/16/telephone.png' /></a>";
//callto://0345572412

if($editObject->formVars["naarAirsVerzenden"] =='')
  $editObject->formVars["naarAirsVerzenden"] .= '<tr><td>   <input type="hidden" name="verzenden" value="0"></td></tr>';


if(strlen($object->get('memo')) > 0)
  $editObject->formVars['bevatMemo']="<span style=\"color:red\"> Let op bevat memo. </span>";

if($_GET['frameSrc'])
  $editObject->formVars['frameSrc']="frameSet.php?page=".$_GET['frameSrc'];
else
  $editObject->formVars['frameSrc']='blank.html';

$object->setOption('prospect','form_extra',"onclick='toonProspectStatus();'");

$autocomplete = new Autocomplete();
$autocomplete->resetVirtualField('Client');
$autocomplete->addVirtuelField('Client', array(
  'autocomplete' => array(
    'table' => 'Clienten',
    'label' => array(
      'Clienten.Client',
      'Clienten.Naam',
      'Portefeuilles.Vermogensbeheerder'
    ),
    'prefix' => true,
    'join' => array(
      'Portefeuilles' => array(
        'type' => 'left',
        'on' => array(
          'Clienten.Client' => 'Portefeuilles.Client'
        )
      )
    ),
    'searchable' => array(
      'Clienten.Client',
      'Clienten.Naam'
    ),
    'field_value' => array(
      'Clienten.Client',
    ),
    'group' => 'Clienten.Client',
    'value' => 'Clienten.Client',
    'order' => 'Clienten.Client ASC',
  ),
  'form_size' => '30',
));
$editObject->template['script_voet'] .= $autocomplete->getAutoCompleteVirtuelFieldScript('Client');


echo $editObject->getOutput();
//echo $editObject->getTemplate();


if($intakeTemplate == true && $_POST['createPdf']==1)
{
  if($object->get('id') > 0)
  {
    $relId=$object->get('id');
    $intakeFilter='';
    if($_POST['intakeOmschrijving']<>'')
      $intakeFilter="AND intakeOmschrijving='".mysql_real_escape_string($_POST['intakeOmschrijving'])."'";
    $query = "SELECT veldenPerTab FROM CRM_naw_templates WHERE intake=1 $intakeFilter ORDER by change_date desc limit 1";
    $db->SQL($query);
    $velden = $db->lookupRecord();
    $veldenArray = unserialize($velden['veldenPerTab']);
    define('FPDF_FONTPATH', $__appvar["basedir"] . "/html/font/");
    include_once('./rapport/PDFRapport.php');
    $pdf = new PDFRapport();
    $pdf->setFont('Arial', 'B', 10);
    $pdf->addPage();
    $pdf->setWidths(array(50, 150));
    $pdf->setAligns(array('L', 'L'));
    foreach ($veldenArray as $tab => $veldData)
    {
      $pdf->setFont('Arial', 'B', 10);
      $pdf->row(array($tab));
      $pdf->setFont('Arial', '', 10);
      foreach ($veldData['velden'] as $veld => $veldDetails)
      {
        $pdf->row(array($veldDetails['description'],$object->get($veld)));
      }
      $pdf->ln();
    }
    $pdfData=$pdf->Output('',"S");
    $file='intake_'.date('Ymd_His').'_'.$relId.'.pdf';
    $dd = new digidoc();
    $rec=array("filename"=>$file,"filesize"=>strlen($pdfData),"filetype"=>'application/pdf','description'=>'intake '.date('d-m-Y H:i:s'),
               "blobdata" => $pdfData,"keywords"=>$file,"categorie"=>'intake',"module"=>'CRM_naw',"module_id"=>$relId);
    $dd->useZlib = false;
    $dd->addDocumentToStore($rec);
  }
}

if ($editObject->result)
{
  if($gebruikPortefeuilleInformatie['portaalDailyClientSync']==1 && $_POST['id']==0)
  {
    if(GetCRMAccess(1) && isset($_DB_resources[DBportaal]) && count($_DB_resources[DBportaal])==4 && $_POST['portefeuille'] <> '')
    {
      updateNawPortaalById(array('id' =>$object->get('id'), 'name' => $_POST['naam'], 'name1' => $_POST['naam1'], 'email' => $_POST['email'],'portefeuille'=>$_POST['portefeuille'],  'password' => $_POST['wachtwoord']),true);
    }
  }
}

if ($editObject->result && $action <> 'updateStay')
{
  header("Location: ".$returnUrl);
}
else
{
  $deb_id = $object->get("id");
  if ($deb_id)
  {
    $_SESSION['submenu'] = New Submenu();

//    $_SESSION['submenu']->addItem("Extra velden","crm_naw_cfEdit.php?deb_id=$deb_id");
//checkChange('bedrijfsgegevensList.php','content')

    $javaCheck="
 <script language=\"JavaScript\" TYPE=\"text/javascript\">
function checkChange(url,target)
    {
      var confirmed = false;
      if(parent.frames['content'].fromChanged)
      {
        if(confirm ('U verlaat het scherm. Wijzigingen worden niet opgeslagen. Weet u het zeker?'))
          confirmed = true;
      }
      else
        confirmed = true;

      if(confirmed)
      {
        if(target ==  '_top')
          document.location = url;
        else if(target ==  '_blank')
        {
          mywindow= window.open(url,'','menubar=0,resizable=1,width=1000,height=500');
          mywindow.moveTo(200,550);
        }
        else
        {
          parent.frames['content'].tabOpen('9');
          parent.frames['content'].window.frames.extraFrame.location.href = url;

        }
      }
    }
 function doRapport(type)
 {
  if(type != 'geen')
  {
  	document.selectForm.rapport.value = '';
  	var tel =0;
  	for(var i=0; i < document.selectForm.rapport_type.length; i++)
  	{
  		if(document.selectForm.rapport_type[i].checked == true)
  		{
  			document.selectForm.rapport.value = document.selectForm.rapport.value + '|' + document.selectForm.rapport_type[i].value;
  			tel++;
  		}
  	}
	}

	if(document.selectForm.type.value == 'email')
	{
    parent.frames['content'].tabOpen('9');
    document.selectForm.target='extraFrame';
	}
	
	if(document.selectForm.type.value == 'emailLos')
	{
    parent.frames['content'].tabOpen('9');
    document.selectForm.target='extraFrame';
	}
	document.selectForm.submit();
 }

</script>";

    $_SESSION['submenu']->addItem($javaCheck,"");
    $_SESSION['submenu']->addItem(vt("Relaties"),"javascript:checkChange('frameSet.php?page=".base64_encode("CRM_naw_kontaktpersoonList.php?deb_id=$deb_id")."','extraFrame')",array('target'=>''));
    $_SESSION['submenu']->addItem(vt("Gespreksverslagen"),"javascript:checkChange('frameSet.php?page=".base64_encode("CRM_naw_dossierList.php?deb_id=$deb_id")."','extraFrame')",array('target'=>''));
    $_SESSION['submenu']->addItem(vt("Documenten"),"javascript:checkChange('frameSet.php?page=".base64_encode("dd_referenceList.php?module=CRM_naw&id=$deb_id")."','extraFrame')",array('target'=>''));
    $_SESSION['submenu']->addItem(vt("Sjablonen"),"javascript:checkChange('frameSet.php?page=".base64_encode("CRM_naw_rtfMergeList.php?deb_id=$deb_id")."','extraFrame')",array('target'=>''));
    $_SESSION['submenu']->addItem(vt("Evenementen"),"javascript:checkChange('frameSet.php?page=".base64_encode("crm_evenementenList.php?deb_id=$deb_id")."','extraFrame')",array('target'=>''));
    $_SESSION['submenu']->addItem(vt("Adressen"),"javascript:checkChange('frameSet.php?page=".base64_encode("CRM_naw_adressenList.php?deb_id=$deb_id")."','extraFrame')",array('target'=>''));
    $_SESSION['submenu']->addItem(vt("Rekeningen"),"javascript:checkChange('frameSet.php?page=".base64_encode("CRM_naw_rekeningenList.php?deb_id=$deb_id")."','extraFrame')",array('target'=>''));
    $_SESSION['submenu']->addItem(vt("Agendapunt"),"javascript:checkChange('frameSet.php?page=".base64_encode("agendaEdit.php?action=new&deb_id=$deb_id")."','extraFrame')",array('target'=>''));
    $_SESSION['submenu']->addItem(vt("Takenlijst"),"javascript:checkChange('frameSet.php?page=".base64_encode("takenList.php?deb_id=$deb_id")."','extraFrame')",array('target'=>''));
    $_SESSION['submenu']->addItem(vt("Kopie&euml;r relatie"),"javascript:checkChange('frameSet.php?page=".base64_encode("CRM_naw_copy.php?deb_id=$deb_id")."','extraFrame')",array('target'=>''));
    $ordermoduleAccess=GetModuleAccess("ORDER");
    if(!isset($__appvar["crmOnly"]))
    {
      if($ordermoduleAccess==1)
        $_SESSION['submenu']->addItem(vt("Order regels"),"javascript:checkChange('frameSet.php?page=".base64_encode("orderregelsList.php?rel_id=$deb_id")."','extraFrame')",array('target'=>''));
      elseif($ordermoduleAccess==2)
        $_SESSION['submenu']->addItem(vt("Order regels"),"javascript:checkChange('frameSet.php?page=".base64_encode("orderregelsListV2.php?rel_id=$deb_id")."','extraFrame')",array('target'=>''));

    }
    if(GetModuleAccess("FACTUURHISTORIE"))
      $_SESSION['submenu']->addItem(vt("Factuur historie"),"javascript:checkChange('frameSet.php?page=".base64_encode("factuurhistorieList.php?rel_id=$deb_id")."','extraFrame')",array('target'=>''));
    if($gebruikersData['CrmTerugRapportage'] > 0 && !isset($__appvar["crmOnly"]))
      $_SESSION['submenu']->addItem(vt("Klantmutaties"),"javascript:checkChange('frameSet.php?page=".base64_encode("klantmutatiesList.php?portefeuille=".$object->get('portefeuille').'&resetFilter=1')."','extraFrame')",array('target'=>''));
    $_SESSION['submenu']->addItem(vt("Standaard taken"),"javascript:checkChange('frameSet.php?page=".base64_encode("standaardtakenToevoegen.php?deb_id=$deb_id")."','extraFrame')",array('target'=>''));

    $_SESSION['submenu']->addItem(vt("Facturen"), "javascript:checkChange('frameSet.php?page=" . base64_encode("factuurTemplateSettings.php?deb_id=$deb_id") . "','extraFrame')", array('target' => ''));

    if($gebruikersData['check_module_SCENARIO'])
    {
      $_SESSION['submenu']->addItem(vt("Cashflow"),"javascript:checkChange('frameSet.php?page=".base64_encode("crm_naw_cashflowList.php?rel_id=$deb_id")."','extraFrame')",array('target'=>''));
      $_SESSION['submenu']->addItem(vt("Scenario"),"javascript:checkChange('frameSet.php?page=".base64_encode("CRM_nawScenario.php?action=edit&id=$deb_id&frame=1")."','extraFrame')",array('target'=>''));
    }
    if($gebruikersData['check_module_VRAGEN'])
    {
      $_SESSION['submenu']->addItem(vt("Vragenlijsten"),"javascript:checkChange('frameSet.php?page=".base64_encode("vrageningevuldList.php?rel_id=$deb_id")."','extraFrame')",array('target'=>''));
      $_SESSION['submenu']->addItem(vt("VragenlijstenV2"),"javascript:checkChange('frameSet.php?page=".base64_encode("vragenlijstenperrelatieList.php?rel_id=$deb_id")."','extraFrame')",array('target'=>''));
    }
    if ($__appvar["HZ"]["apiKey"] != "")
    {
      $_SESSION['submenu']->addItem(vt("Handelzeker"),"CRM_nawHandelzeker.php?rel_id=$deb_id");
    }

    $query = "SELECT id,intakeOmschrijving FROM CRM_naw_templates WHERE intake=1 GROUP BY intakeOmschrijving ORDER by change_date ";
    $query = "
      SELECT
        *
      FROM
        (
          SELECT
            intake,
            intakeOmschrijving,
            MAX(change_date) `change_date`
          FROM
            CRM_naw_templates
          WHERE
            intake = 1
          GROUP BY
            intakeOmschrijving
        ) `filtered_templates`
      JOIN CRM_naw_templates USING (
        intakeOmschrijving,
        change_date
      )
      GROUP BY
        intakeOmschrijving
      ORDER BY
        change_date DESC
    
    ";
    $db->SQL($query);
    $db->query();
    $aantalIntakeTemplates=0;
    $intakeSelectHtml='' . vt('Intake template') . ':<br><select name="intakeOmschrijving" id="intakeOmschrijving">';
    $intakeTemplates=array();
    while($intakeData=$db->nextRecord())
    {
      if( ! empty ($intakeData['intakeOmschrijving']) ) {
        $intakeSelectHtml .= "<option value='" . $intakeData['id'] . "'>" . $intakeData['intakeOmschrijving'] . "</option>";
        $aantalIntakeTemplates++;
      }
    }
    $intakeSelectHtml.="</select>";
    if($aantalIntakeTemplates>1)
      $_SESSION['submenu']->addItem($intakeSelectHtml,"");
    else
      $_SESSION['submenu']->addItem("<input type='hidden' name='intakeOmschrijving' id='intakeOmschrijving'>","");
  
    $_SESSION['submenu']->addItem(vt("Intake"),"#",array('target'=>'','onclick'=>"javascript:parent.frames['content'].location='CRM_nawEdit.php?action=edit&template=intake&id=$deb_id&templateId='+$('#intakeOmschrijving').val();"));
    if (DBsimbis > 0)  // Alleen voor intern gebruik
    {
      $_SESSION['submenu']->addItem(vt("Simbis calls"),"javascript:checkChange('CRM_Simbis_callList.php?deb_id=$deb_id','extraFrame')",array('target'=>''));
    }

    if(GetModuleAccess('ParticipatieGebruiker') == 1) {
      $_SESSION['submenu']->addItem(vt("participaties"),"javascript:checkChange('frameSet.php?page=".base64_encode("participantsEdit.php?nawId=$deb_id")."','extraFrame')",array('target'=>''));
    }
    if($__appvar['logAccess'] == 1)
      $_SESSION['submenu']->addItem(vt("Track & Trace"),"javascript:checkChange('frameSet.php?page=".base64_encode("CRM_trackandtraceList.php?rel_id=$deb_id")."','extraFrame')",array('target'=>''));



    $_SESSION['submenu']->addItem('<br>',"");


    ///// call 7675
    if ($__appvar["factuurmodule"] == 1)
    {
      $_SESSION["submenu"]->addItem(vt("factuurregels"),"facmod_factuurregelsList.php?do=notinvoiced&deb_id=$deb_id",array("style"=>"background-color: #E6FAE6;"));
      $_SESSION["submenu"]->addItem(vt("factuurhistorie"),"facmod_factuurbeheerList.php?do=all&deb_id=$deb_id",array("style"=>"background-color: #E6FAE6;"));
      $_SESSION["submenu"]->addItem(vt("factuurregel historie"),"facmod_factuurregelsList.php?do=old&deb_id=$deb_id",array("style"=>"background-color: #E6FAE6;"));
      $_SESSION["submenu"]->addItem(vt("abonnementen"),"facmod_abonnementList.php?deb_id=$deb_id&do=deb",array("style"=>"background-color: #E6FAE6;"));
      $_SESSION["submenu"]->addItem("<br>","");
    }

    ///// call 7675


    $db=new DB();
    $db->SQL("SELECT naam FROM CRM_naw_RtfTemplates WHERE standaard='1'");
    $standaardbrief=$db->lookupRecord();
    $standaardbrief=$standaardbrief['naam'];
    if($standaardbrief <> '')
      $_SESSION['submenu']->addItem("<img src=\"images/16/word.gif\" width=\"16\" height=\"16\" border=\"0\"> Standaard brief","CRM_naw_rtfMergeList.php?action=print&file=$standaardbrief&deb_id=$deb_id",array('target'=>''));

    if($__appvar["crmOnly"]==true)
      $_SESSION['submenu']->addItem("<br />
      <div class='buttonDiv' style='width:110px;' onclick='javascript:parent.frames[\"content\"].tabOpen(\"9\");document.selectForm.submit();'>&nbsp;&nbsp;".maakKnop('mail_add.png',array('size'=>16))." Naar eMail</div>    
      </form>","");

    $_SESSION['submenu']->addItem('<br>' . vt('Bladeren door selectie') . '',"");
    $_SESSION['submenu']->addItem("$bladerHtml");
    //Todo: agenda
    // Afspraken
    // Taken
    $query = "SELECT Portefeuilles.id
          FROM Portefeuilles
          $join
          WHERE Portefeuille = '".$object->get('portefeuille')."' AND Portefeuilles.Einddatum > NOW()
          $beperktToegankelijk ";

    if($db->QRecords($query) > 0 || $virtuelePortefeuille['virtuelePortefeuilleId'] >0)
    {
      $_SESSION['submenu']->addItem("

<script language=\"JavaScript\" TYPE=\"text/javascript\">
function checkAll(optie)
{
  var theForm = document.selectForm.elements, z = 0;
  for(z=0; z<theForm.length;z++)
  {
   if(theForm[z].type == 'checkbox' && theForm[z].name.substr(0,7) == 'rapport')
   {
      if(optie == -1)
      {
        if(theForm[z].checked == true)
          theForm[z].checked=false;
        else
          theForm[z].checked=true;  
      }
      else
      {
        theForm[z].checked = optie;
      }
   }
  }
}

function checkPeriode(periode)
{
  var theForm = parent.frames[\"content\"].editForm.elements, z = 0;
  document.selectForm.periodeSettings.value=periode;
  var checkedItems = [];

  for(z=0; z<theForm.length;z++)
  {
   if(theForm[z].type == 'checkbox' && theForm[z].name.substr(0,5) == 'rap_'+periode)
   {
      if(theForm[z].checked == true)
      {
        checkedItems.push(theForm[z].value);
      }
   }
  }

 
  var theForm = document.selectForm.elements, z = 0;
  var i;
  for(z=0; z<theForm.length;z++)
  {
   if(theForm[z].type == 'checkbox' && theForm[z].name.substr(0,7) == 'rapport')
   {
    theForm[z].checked=false; 
     for (i = 0; i < checkedItems.length; i++) 
     {
        if(checkedItems[i] == theForm[z].value)
          theForm[z].checked=true;
     }
   }
  }
  
}

 </script>



<form name='selectForm' target='_blank' action='rapportFrontofficeClientAfdrukken.php'>
      <input type='hidden' name='rapport'><input type='hidden' name='periodeSettings' value=''><input type='hidden' name='save' value='0'><input type='hidden' name='type' value='pdf'><input type='hidden' name='portefeuille' value='".$object->get('portefeuille')."'>","");     $_SESSION['submenu']->addItem("<input type='hidden' name='selected' value=''><br> <br><table border=\"0\" cellspacing=\"0\" cellpadding=\"0\"> <tr><td colspan=\"2\"> 
<button onclick='javascript:checkPeriode(\"d\");return false;'>" . vt('D') . "</button><button onclick='javascript:checkPeriode(\"m\");return false;'>" . vt('M') . "</button><button onclick='javascript:checkPeriode(\"k\");return false;'>" . vt('K') . "</button><button onclick='javascript:checkPeriode(\"h\");return false;'>" . vt('H') . "</button><button onclick='javascript:checkPeriode(\"j\");return false;'>" . vt('J') . "</button><br>
<input type='checkbox' onclick='javascript:checkAll(-1);'> " . vt('Rapporten') . ":","");
      if(is_array($frontOfficeData))
        foreach ($frontOfficeData as $rapport=>$rapportData)
        {
          $omschrijving=$rapportData['omschrijving'];
          if($frontOfficeData[$rapport]['toon'] == 1)
          {
            $_SESSION['submenu']->addItem("</td></tr><tr><td><input type='checkbox' name='rapport_type' value='$rapport'></td><td>\n","");
            $_SESSION['submenu']->addItem($rapportData['short'],
                                          "javascript:void(0);",array('onclick'=>"javascript:document.selectForm.type.value='pdf';document.selectForm.rapport.value='$rapport';doRapport('geen');", 'style'=>'width:100px'));
            //rapportFrontofficeClientAfdrukken.php?portefeuille=".$object->get('portefeuille')."&rapport=$rapport
          }
        }
      $totdatum=getLaatsteValutadatum();
      $totJul=db2jul($totdatum);
      $totFromDatum=date("d-m-Y",$totJul);
      $jr = substr($totdatum,0,4);
      $maand = substr($totdatum,5,2);
      $kwartaal = ceil(date("m",$totJul) / 3);
      $datumSelctie['beginMaand']=date("d-m-Y",mktime(0,0,0,$maand-1,0,$jr));
      $datumSelctie['eindMaand']=date("d-m-Y",mktime(0,0,0,$maand,0,$jr));
      $datumSelctie['beginKwartaal']=date("d-m-Y",mktime(0,0,0,$kwartaal*3-5,0,$jr));
      $datumSelctie['eindKwartaal']=date("d-m-Y",mktime(0,0,0,$kwartaal*3-2,0,$jr));
      $datumSelctie['beginJaar']=date("d-m-Y",mktime(0,0,0,1,1,$jr-1));
      $datumSelctie['eindJaar']=date("d-m-Y",mktime(0,0,0,13,0,$jr-1));
      $datumSelctie['beginMaand2']=date("d-m-Y",mktime(0,0,0,$maand,0,$jr));
      $datumSelctie['beginKwartaal2']=date("d-m-Y",mktime(0,0,0,$kwartaal*3-2,0,$jr));
      $datumSelctie['beginJaar2']=date("d-m-Y",mktime(0,0,0,1,1,$jr));
      foreach ($datumSelctie as $naam=>$datum)
      {
        if(substr($naam,0,5)=='begin' && substr($datum,0,5)=='31-12')
          $datumSelctie[$naam]="01-01-".((substr($datum,6,4))+1);
      }


      $_SESSION['submenu']->addItem("</td></tr></table>
      <table>
      <tr><td><input type='text' size='12' name='datum_van' value='".date("01-01-Y",db2jul(getLaatsteValutadatum()))."'></td></tr>
      <tr><td><input type='text' size='12' name='datum' value='".date("d-m-Y",db2jul(getLaatsteValutadatum()))."'></td></tr>
      </table>
      <table border=0>
      <tr><td ><span style=\"font: 11px 'Arial';\">" . vt('Vorige') . ":</span></td><td>
      <div class='buttonDiv' style=\"font: 11px 'Arial'; width:40px; display:inline; text-align: center; color: Navy;font-weight: bold;\"  href=\"#\" onclick=\"javascript:document.selectForm.datum_van.value='".$datumSelctie['beginMaand']."';document.selectForm.datum.value='".$datumSelctie['eindMaand']."';\">" . vt('Mnd') . "</div>
      <div class='buttonDiv' style=\"font: 11px 'Arial'; width:40px; display:inline; text-align: center; color: Navy;font-weight: bold;\" href=\"#\" onclick=\"javascript:document.selectForm.datum_van.value='".$datumSelctie['beginKwartaal']."';document.selectForm.datum.value='".$datumSelctie['eindKwartaal']."';\">" . vt('Kw.') . "</div>
      <div class='buttonDiv' style=\"font: 11px 'Arial'; width:40px; display:inline; text-align: center; color: Navy;font-weight: bold;\" href=\"#\" onclick=\"javascript:document.selectForm.datum_van.value='".$datumSelctie['beginJaar']."';document.selectForm.datum.value='".$datumSelctie['eindJaar']."';\">" . vt('Jr.') . " </div>
      </td></tr>
      <tr><td><span style=\"font: 11px 'Arial';\">" . vt('Huidige') . ":</span></td><td>
      <div class='buttonDiv' style=\"font: 11px 'Arial'; width:40px; display:inline; text-align: center; color: Navy;font-weight: bold;\"  href=\"#\" onclick=\"javascript:document.selectForm.datum_van.value='".$datumSelctie['beginMaand2']."';document.selectForm.datum.value='".$totFromDatum."';\">" . vt('Mnd') . "</div>
      <div class='buttonDiv' style=\"font: 11px 'Arial'; width:40px; display:inline; text-align: center; color: Navy;font-weight: bold;\" href=\"#\" onclick=\"javascript:document.selectForm.datum_van.value='".$datumSelctie['beginKwartaal2']."';document.selectForm.datum.value='".$totFromDatum."';\">" . vt('Kw.') . "</div>
      <div class='buttonDiv' style=\"font: 11px 'Arial'; width:40px; display:inline; text-align: center; color: Navy;font-weight: bold;\" href=\"#\" onclick=\"javascript:document.selectForm.datum_van.value='".$datumSelctie['beginJaar2']."';document.selectForm.datum.value='".$totFromDatum."';\">" . vt('Jr.') . " </div>
      </td></tr>   
      </table>
      <table>   
      <tr><td><div class='buttonDiv' style='width:50px;' onclick='javascript:document.selectForm.type.value=\"pdf\";doRapport();'>&nbsp;&nbsp;".maakKnop('pdf.png',array('size'=>16))."</div></td><td>
      ","");
      $_SESSION['submenu']->addItem("
<div class='buttonDiv' style='width:50px;' onclick='javascript:document.selectForm.type.value=\"email\";doRapport();'>&nbsp;&nbsp;".maakKnop('mail_add.png',array('size'=>16))." </div>    
       </tr>
       
       <tr>
       <td colspan='2'>
       <div class='buttonDiv' style='width:106px;' onclick='javascript:document.selectForm.type.value=\"emailLos\";doRapport();'>&nbsp;&nbsp;".maakKnop('mail_add.png',array('size'=>16))." " . vt('Losse mail') . " </div>    

</td>
</tr>
       
       </table>
       <input type=\"checkbox\" name=\"passwd\" value=\"1\"> " . vt('Met wachtwoord') . " <br>
       <input type=\"checkbox\" name=\"digiDoc\" value=\"1\"> " . vt('Opslaan als Doc.(PDF)') . "
      </form>","");
    }
    else
    {

      $_SESSION['submenu']->addItem("<form name='selectForm' target='extraFrame' action='CRM_mailer.php'>
      <input type='hidden' name='id' value='".$object->get('id')."'>","");
      if($__appvar["crmOnly"]==false)
        $_SESSION['submenu']->addItem("<br />
      <div class='buttonDiv' style='width:110px;' onclick='javascript:parent.frames[\"content\"].tabOpen(\"9\");document.selectForm.submit();'>&nbsp;&nbsp;".maakKnop('mail_add.png',array('size'=>16))." " . vt('Naar eMail') . "</div>    
      </form>","");
    }

    if ( ! isset ($requestData['template']) || ( isset ($requestData['template']) && $requestData['template'] !== 'intake' ) ) {
      $_SESSION['savedReturnUrl'] = $_SESSION['NAV']->returnUrl;
    }
  }

  echo $_error = $editObject->_error;
}
$endTime=microtime();
//echo "time: ".($endTime-$startTime);
?>