<?php
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$cfg = new AE_config();

$__funcvar['listurl']  = "CRM_nawOnlyList.php";
$__funcvar['location'] = "CRM_nawOnlyEdit.php";

foreach($_POST as $key=>$value)
{
  if($key=='Client')
  {
    $_POST[$key] = preg_replace("/['`]/i", "", $value);
  }
}

$data = array_merge($_POST,$_GET);

$object = new Naw();
if ($_GET['useSavedUrl'] == 1)  // returnURL instellen zoals deze oorspronkelijk was bij de aanroep van dit script
{
  $_SESSION['NAV']->returnUrl = $_SESSION['savedReturnUrl'];
}

if ($_GET['do'] == "viaFrontOffice" )
{
  $db = new DB();
  $query = "SELECT * FROM CRM_naw WHERE portefeuille='".$_GET['port']."'";
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
    $query = "
                SELECT
                  `Portefeuilles`.`Portefeuille`,
                  `Clienten`.`Client`,
                  `Clienten`.`Naam`,
                  `Clienten`.`Naam1`,
                  `Clienten`.`Adres`,
                  `Clienten`.`Woonplaats`,
                  `Clienten`.`Telefoon`,
                  `Clienten`.`Fax`,
                  `Clienten`.`Email`
                FROM
                  `Portefeuilles`
                INNER JOIN
                  `Clienten` ON `Portefeuilles`.`Client` = `Clienten`.`Client`
                WHERE
                  `Portefeuilles`.`Portefeuille`= '".$_GET['port']."'";
    $db->SQL($query);
    $clientRec = $db->lookupRecord();
  }
}
//listarray($_SESSION);




$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;


$editcontent['jsincludes'] .= "<script language=JavaScript src=\"javascript/tabbladen.js\" type=text/javascript></script>\n";
$editcontent['jsincludes'] .= "<script language=JavaScript src=\"javascript/jsrsClient.js\" type=text/javascript></script>";
$editcontent['jsincludes'] .= "<script language=JavaScript src=\"javascript/CRM_nawEdit.js\" type=text/javascript></script>";

$editcontent['javascript']=str_replace('document.editForm.submit();','if(checkFields()){document.editForm.submit();}',$editcontent['javascript']);

$formChecks='';
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
        if(document.editForm.rapVerzend_k_papier.checked == true || document.editForm.rapVerzend_k_email.checked == true)
        {
          //oke
        }
        else
        {
         alert(\'Geen kwartaalrapportage verzendmethode opgegeven!\');
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
      if (document.editForm.Vermogensbeheerder.value  == "" ) {alert("Geen Vermogensbeheerder geselecteerd."); return false;}
      if (document.editForm.Accountmanager.value  == "" ) {alert("Geen Accountmanager geselecteerd."); return false;}
      if (document.editForm.Depotbank.value  == "" ) {alert("Geen Depotbank geselecteerd."); return false;}
      if (document.editForm.Portefeuille.value  == "" ) {alert("Geen Portefeuille geselecteerd."); return false;}
      if (document.editForm.Client.value  == "" ) {alert("Geen Client opgegeven."); return false;}
    }
    else {alert("Velden Vermogensbeheerder,Accountmanager,Depotbank,Portefeuille en Client niet gevonden.");return false;}

  }
  
  '.$formChecks.'

  return true;
 }';

$object->setOption("portefeuille","form_extra",'onChange=\'portefeuilleChange();\'');

$action = $data['action'];
$perioden=array('d'=>vt('Dagelijkse'),'m'=>vt('Maand'),'k'=>vt('Kwartaal'),'h'=>vt('Halfjaar'),'j'=>vt('Jaar'));
//else
//  $perioden=array('m'=>'Maand','k'=>'Kwartaal','h'=>'Halfjaar','j'=>'Jaar');

if ($action == 'update' || $action=='updateStay')
{
  if(1||$gebruikPortefeuilleInformatie['CrmPortefeuilleInformatie'] > 0 )
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
  // listarray($data);exit;
  
  $intakeTemplate=false;
  if($data['template']=='intake')
  {
    $intakeTemplate = true;
  }
  if(!isset($object->data['fields']['rapportageVinkSelectie']['beperkt']) && $intakeTemplate==false)
    $data['rapportageVinkSelectie']=serialize(array('rap_d'=>$data['rap_d'],'rap_m'=>$data['rap_m'],'rap_k'=>$data['rap_k'],'rap_h'=>$data['rap_h'],'rap_j'=>$data['rap_j'],'verzending'=>$rapport,'aantal'=>$aantal,'opties'=>$opties));
}

$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen
$editObject->usetemplate = true;

$editObject->formTemplate = $__BTR_CONFIG["CUSTOM_NAWONLY_TEMPLATE"] ? "CRM_nawOnlyEditTemplate_BTR.html" : "CRM_nawOnlyEditTemplate.html";
//listarray($_POST);exit;

if ($action == 'update'  || $action=='updateStay')
{
  if(GetCRMAccess(1) && isset($_DB_resources[DBportaal]) && count($_DB_resources[DBportaal])==4 && $_POST['portefeuille'] <> '')
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

if ($data['action'] == "new")
  $mainHeader   = vt('relatie toevoegen') . ",&nbsp;&nbsp;&nbsp;";
else
  $mainHeader   = vt('relatie muteren') . ",&nbsp;&nbsp;&nbsp;";
$subHeader    = "<a id=\"relatieInfo\" name=\"relatie Info\">".$object->get('naam')."</a>";
$editcontent['pageHeader'] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div><br><br>";
$editObject->template = $editcontent;

if ($action == "new")
{
  $object->set("aktief",1);
  if ($_GET['port'])
  {
    $object->set("portefeuille",$_GET['port']);
    $object->set("naam",$clientRec['Naam'].$clientRec['Naam1']);
    $object->set("adres",$clientRec['Adres']);
    $object->set("plaats",$clientRec['Woonplaats']);
    $object->set("tel1",$clientRec['Telefoon']);
    $object->set("fax",$clientRec['Fax']);
    $object->set("email",$clientRec['E-mail']);
    $object->set("memo",'toegevoegd via Frontoffice selecte');
  }
}

////

$db=new DB();
$query = "SELECT Gebruikers.id, Gebruikers.CRMlevel, Gebruikers.portefeuilledetailsAanleveren, max(Vermogensbeheerders.CrmTerugRapportage) as CrmTerugRapportage,
 max(Vermogensbeheerders.CrmAutomatischVerzenden) as CrmAutomatischVerzenden,
 max(Vermogensbeheerders.check_module_SCENARIO) as check_module_SCENARIO,
 max(Vermogensbeheerders.check_module_VRAGEN) as check_module_VRAGEN,
  Export_data_frontOffice FROM Gebruikers
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

  $query = "SELECT Portefeuilles.id ,Portefeuilles.consolidatie FROM Portefeuilles $join WHERE Portefeuille = '".$object->get('portefeuille')."'  ";//$beperktToegankelijk
  $db->SQL($query);
  $dbData = $db->lookupRecord();
  if($dbData['id'] > 0)
    $data['portefeuilleId']=$dbData['id'];

  if($virtuelePortefeuille['virtuelePortefeuilleId'] < 1 || 1)
  {
    $editObject->formVars["PortefeuilleTabs"]='
  	<input type="button" class="tabbuttonInActive" onclick="javascript:tabOpen(\'10\');" id="tabbutton10" name="but10" value="' . vt('Portefeuille') . '">
	  <input type="button" class="tabbuttonInActive" onclick="javascript:tabOpen(\'11\');" id="tabbutton11" name="but11" value="' . vt('Beheerfee') . '">
	  <input type="button" class="tabbuttonInActive" onclick="javascript:tabOpen(\'12\');" id="tabbutton12" name="but12" value="' . vt('Staffels') . '">
	  <input type="button" class="tabbuttonInActive" onclick="javascript:tabOpen(\'13\');" id="tabbutton13" name="but13" value="' . vt('Overige') . '">
	  <input type="button" class="tabbuttonInActive" onclick="javascript:tabOpen(\'6\');" id="tabbutton6" name="Rapportage" value="' . vt('Rapportage') . '">';
  
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
        $(\'#extraKoppelFrame\').attr(\'src\', value+"?portefeuille=" + $(\'#Portefeuille\').val() + "&frame=1");
      }
    </script>
    <div class="form">
      <fieldset id="Overige" >
        <legend> Overige</legend>
        <table border="0">
          <tr><td> ' . vt('Selectie') . ':
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

    if( $__BTR_CONFIG["CUSTOM_NAWONLY_TEMPLATE"]  )
    {
      $editObject->formVars["vulling_tab13"]='<script>
      function koppelingChanged(value)
      {
        $(\'#extraKoppelFrame\').attr(\'src\', value+"?portefeuille=" + $(\'#Portefeuille\').val() + "&frame=1");
      }
      </script>
      <div class="form">
        <fieldset id="Overige" >
          <legend> Overige</legend>
          <row>
            <column>
            <group>
              <span>Selectie:</span>
            <select  class="" type="select"  name="koppeling"  id="koppeling"  onChange="javascript:koppelingChanged(this.value);" >
            <option value=""> --- </option>
            <option value="masterdata/maintenance-portfolio/duty-of-care-parameters-per-portfolio//wallet/{walletId}" >Zorgplicht parameters per portefeuille</option>
            <option value="masterdata/maintenance-portfolio/account//wallet/{walletId}" >Rekeningen </option>
            <option value="masterdata/maintenance-portfolio/modelportfolios-per-portfolio//wallet/{walletId}" >Modelportefeuille per portefeuille </option>
            <option value="masterdata/maintenance-portfolio/standard-deviation-per-portfolio//wallet/{walletId}" >Standaarddeviatie per portefeuille</option>
          </select>
            
            <iframe id="extraKoppelFrame" name="extraKoppelFrame" src="blank.html" width="1200" height="1000" marginwidth="0" marginheight="0" hspace="0" vspace="0" align="middle" frameborder="0"></iframe>
            </group>
            </column>
          </row>

        </fieldset>
      </div>';
    }


  }
  
  if($virtuelePortefeuille['virtuelePortefeuilleId'] > 0)
  {
    $editObject->formVars["PortefeuilleTabs"] .= '<input type="button" class="tabbuttonInActive" onclick="javascript:tabOpen(\'14\');" id="tabbutton14" name="but14" value="Consolidatie">';
    $editObject->formVars["vulling_tab14"].='<div class="form">
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
      $data['portefeuille'] = $data['Portefeuille'];
    $portefeuilleData = $data;
    $portefeuilleData['id'] = $data['portefeuilleId'];
  
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
        if($dbData['id'] >0)
          $object->setOption($key,'form_extra','disabled');
        if($key=='Startdatum')
          $object->setOption($key,'form_extra','disabled');
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
    $object->setOption('Risicoklasse','select_query', "SELECT Risicoklassen.Risicoklasse,Risicoklassen.Risicoklasse FROM Portefeuilles, Risicoklassen WHERE Portefeuilles.Vermogensbeheerder = Risicoklassen.Vermogensbeheerder AND Portefeuilles.id = '".$dbData['id']."'");
    $object->setOption('Accountmanager','select_query', "SELECT Accountmanagers.Accountmanager,Accountmanagers.Accountmanager FROM Portefeuilles, Accountmanagers WHERE Portefeuilles.Vermogensbeheerder = Accountmanagers.Vermogensbeheerder AND Portefeuilles.id = '".$dbData['id']."'");
    $object->setOption('tweedeAanspreekpunt','select_query', "SELECT Accountmanagers.Accountmanager,Accountmanagers.Accountmanager FROM Portefeuilles, Accountmanagers WHERE Portefeuilles.Vermogensbeheerder = Accountmanagers.Vermogensbeheerder AND Portefeuilles.id = '".$dbData['id']."'");
    $object->setOption('Remisier','select_query', "SELECT Remisiers.Remisier,Remisiers.Remisier FROM Remisiers,Portefeuilles  WHERE Portefeuilles.Vermogensbeheerder = Remisiers.Vermogensbeheerder AND Portefeuilles.id = '".$dbData['id']."'");
  }
  else
  {
    $object->setOption('Client','form_type', "text");
    $object->setOption('Client','value', strtoupper(str_replace(".","",str_replace(" ","",$object->get('achternaam').$object->get('voorletters')))));
    $object->setOption('Risicoklasse','select_query', "SELECT Risicoklassen.Risicoklasse,Risicoklassen.Risicoklasse FROM Risicoklassen ");
    $object->setOption('Accountmanager','select_query', "SELECT Accountmanagers.Accountmanager,Accountmanagers.Accountmanager FROM Accountmanagers ");
    $object->setOption('tweedeAanspreekpunt','select_query', "SELECT Accountmanagers.Accountmanager,Accountmanagers.Accountmanager FROM Accountmanagers ");
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
    $editObject->formVars["Methode_".$a] = '';
		if($bf["BeheerfeeMethode"] == $a) {
			$editObject->formVars["Methode_".$a] = "checked";
    }
	}

}
 if ($action == 'update'  || $action=='updateStay')
 {
  foreach ($portefeuille->data['fields'] as $key=>$data)
  {
    if(isset($_POST[$key]))
    {
      $_POST[$key] = trim($_POST[$key]);
      $data['value'] = trim($data['value']);
      if($data['form_type']=='checkbox' || $data['db_type']=='double' || $data['db_type']=='text' || $data['db_type']=='tinyint')
      {
        if($data['value']=="0" && $_POST[$key]=='')// ==''
          $data['value']='';
        if($data['value']=='' && $_POST[$key]=="0")// ==''
          $_POST[$key]='';  
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
    if($_POST['Portefeuille']=='')
      $_POST['verzenden']=false;
    
  
    if( ($_POST['Portefeuille']=='' && (count($mutaties['Portefeuilles']) > 0 && $crmPortefeuille<>'')   ))
    {
      echo vtb("Portefeuille veld is niet gevuld. Verzenden (%s) mutaties niet mogelijk.", array(count($mutaties['Portefeuilles']))) . "<br>\n";
      exit;
    }

    $DB = new DB();
    if($_POST['portefeuilleId'] =='' && $DB->QRecords("SELECT id FROM Portefeuilles WHERE Portefeuille='".$_POST['Portefeuille']."'"))
    {
      echo vt('Portefeuille is al aanwezig maar nog niet gekoppeld. Verzenden mutaties niet mogelijk.') . "<br>\n";
      exit;
    }
    //echo $portefeuille->data['fields']['Einddatum']['value'];exit;
    if(form2jul($portefeuille->data['fields']['Einddatum']['value']) < time())
    {
      echo vt('Portefeuille heeft een einddatum. Verzenden mutaties niet mogelijk.') . "<br>\n";
      exit;
    }
    //listarray($_POST);exit;
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
        $queueDB = new DB(2);
        if($veld=='Einddatum' && adodb_db2jul($waarden['nieuw']) < (time()+31536000) )
        { 
          $object->set("aktief",0);
          $object->save();
        }
        $query = "INSERT INTO klantMutaties SET
                  tabel = '$tabel',
                  recordId = '".$tableIds[$tabel]."',
                  veld='$veld',
                  oudeWaarde='".$waarden['oud']."',
                  nieuweWaarde='".$waarden['nieuw']."',
                  verwerkt='0',
                  Vermogensbeheerder='$vermogensbeheerder',
                  add_date = now(),add_user = '$USR',change_date = now() , change_user = '$USR' ";
        $queueDB->SQL($query);
        if(!$queueDB->Query())
        {
          $foutMeldingen .= vtb("Verzenden van aanpassingsverzoek '%s' naar %s mislukt.", array($waarden['oud'], $waarden['oud'])) . "<br>\n";
        }
        else
        {
          $lastId=$queueDB->last_id();
          $query.=", id = $lastId";
          $DB=new DB();
          $DB->SQL($query);
          $DB->Query();
        }
        $counter++;
      }
    }
    if($foutMeldingen)
      $_SESSION['verzendStatus']= $foutMeldingen;
    elseif ($counter == 0 && $gebruikersData['CrmAutomatischVerzenden']==0)
      $_SESSION['verzendStatus']= "<br><b>" . vt('Geen aanpassingen gevonden?') . "</b>";
    elseif($counter > 0)
      $_SESSION['verzendStatus']= "<br><b>" . vtb('Aanpassingsverzoek (%s) velden verzonden.', array($counter)) . "</b>";

  }
 }
} 
 ////

if ($object->error)
{
  echo "<h4><font color=\"maroon\">" . vt('Er zijn velden fout ingevuld in dit formulier, na correctie kunt u opnieuw opslaan') . "</font></h4>";
}


if(1||$gebruikPortefeuilleInformatie['CrmPortefeuilleInformatie'] > 0)
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
        $checks[$prefix.$grootboek] = 1;
      
      include('rapportFrontofficeClientSelectieLayout.php');
      
      $opties.=$rapportSettings[13]."<script>$('#".$prefix."settingsContainer').hide();$('#".$prefix."MUT_Settings').show();</script>";//$('#".$prefix."MUT_Settings').show();$('#".$prefix."SMV_Settings').show();$('#".$prefix."TRANS_Settings').show();$('#".$prefix."Model_Settings').show();
    }
    elseif($gebruikPortefeuilleInformatie['Layout']==5)
    {
      foreach ($data['rapportageVinkSelectie']['opties'][$periodeLetter]['MUT'] as $grootboek=>$check)
        $checks[$prefix.$grootboek] = 1;
      if($data['rapportageVinkSelectie']['opties']['PERF']['perfBm'] == 1)
        $perfBm = 'checked';
      include('rapportFrontofficeClientSelectieLayout.php');
      $opties.=$rapportSettings[5]."<script>$('#".$prefix."settingsContainer').hide();$('#".$prefix."MUT_Settings').show();</script>";
    }
    else
    {
      if(is_array($data['rapportageVinkSelectie']['opties'][$periodeLetter]))
        foreach ($data['rapportageVinkSelectie']['opties'][$periodeLetter]['MUT'] as $grootboek=>$check)
          $checks[$prefix.$grootboek] = 1;
      include('rapportFrontofficeClientSelectieLayout.php');
      $opties.='<td valign="top">
<div id="'.$prefix.'settingsContainer">'.$rapportSettings['default'].'</div></td>';
      $opties.="<script>$('#".$prefix."settingsContainer').hide();$('#".$prefix."MUT_Settings').show();</script>";//$('#".$prefix."SMV_Settings').show();$('#".$prefix."TRANS_Settings').show();$('#".$prefix."Model_Settings').show();
    }
  }
  $dataType = array();
  $dataType["verzending"] = $__BTR_CONFIG["CUSTOM_NAWONLY_TEMPLATE"] ? 'data-type="verzending"' : '';
  $dataType["afdrukken"] = $__BTR_CONFIG["CUSTOM_NAWONLY_TEMPLATE"] ? 'data-type="afdrukken"': '';
  $dataType["rapport"] = $__BTR_CONFIG["CUSTOM_NAWONLY_TEMPLATE"]  ? 'data-type="rapport"' : '';
  $dataType["overige"] = $__BTR_CONFIG["CUSTOM_NAWONLY_TEMPLATE"]  ? 'data-type="overige"' : '';
   
  
  $rapportageMatrix='<table data-type="rapportageMatrix"><tr><td><table>';
  if($gebruikPortefeuilleInformatie['check_portaalCrmVink']==1)
    $verzendTypen=array('email'=>'eMail','papier'=>'Papier','portaal'=>'Portaal','geen'=>'Geen');
  else
    $verzendTypen=array('email'=>'eMail','papier'=>'Papier','geen'=>'Geen');
  
  $rapportageMatrix.='<tr><td><b>' . vt('Verzending') . '<b></td>';
  foreach ($perioden as $periodeLetter=>$periode)
    $rapportageMatrix.="<td><b>$periode</b></td>";
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
    $rapportageMatrix.='<tr '.$dataType["verzending"].'><td><b><label title="'.$omschrijving.'">'.$omschrijving.'</label> </b></td>';
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
  $rapportageMatrix.='<tr><td><b>Afdrukken<b></td>';
  foreach ($perioden as $periodeLetter=>$periode)
    $rapportageMatrix.="<td><b>$periode</b></td>";
  
  $rapportageMatrix.='<tr '.$dataType["afdrukken"].'><td><b>aantal</b></td>';
  foreach ($perioden as $periodeLetter=>$periode)
    if($periodeLetter <> 'd')
      $rapportageMatrix.="<td><input type='text' $readonly name='rapAantal_".$periodeLetter."' size='2' value='".$data['rapportageVinkSelectie']['aantal'][$periodeLetter]."'></td>";
    else
      $rapportageMatrix.="<td>&nbsp;</td> ";
  
  $rapportageMatrix.='</tr>
  <tr><td>&nbsp;</td></tr>
  <tr><td><b>Rapport<b></td>';
  foreach ($perioden as $periodeLetter=>$periode)
    $rapportageMatrix.='<td><div class="buttonDiv" title="Klik hier om de extra rapportage instelling voor de '.strtolower($periode).' rapportages te tonen." onclick="$(\'#'.$periodeLetter.'_settingsContainer\').toggle();"><b>'.$periode.'</b></div></td>';
  
  
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
    $currentDataType = $dataType['rapport'];
    if( $frontOfficeData[$rapport]['toon'] != 1 && $rapportData['toonNiet'] == 0 ) {
      $currentDataType = $dataType['overige'];
    }

    $regel='<tr '.$currentDataType.'><td><b><label title="'.$omschrijving.'">'.$shortname.'</label> </b></td>';
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
  
  $rapportageMatrix.='<tr><td><b>Overige<b></td>';
  foreach ($perioden as $periodeLetter=>$periode)
    $rapportageMatrix.="<td><b>$periode</b></td>";
  //$rapportageMatrix.="<td><b>ytd<b></td>";
  $rapportageMatrix.="</tr>";
  $rapportageMatrix .=$rapportageMatrixEind;
  
  
  $rapportageMatrix.='</table>
  </td>'.$opties.'</tr></table>';
  $editObject->formVars['rapportageMatrix']=$rapportageMatrix;
}
if($gebruikPortefeuilleInformatie['kwartaalCheck']==1)
  $editObject->formVars['rapportageMatrix'].="<input type='hidden' name='kwartaalCheck' value='1'>";


if(GetModuleAccess('NAW_inclDocumenten'))
{
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
    </script>
    ";
  
  $editObject->formVars['PortefeuilleTabs'].=' <input type="button" class="tabbuttonInActive" onclick="javascript:tabOpen(\'9\');$(\'#RelatieSectie1\').show();" id="tabbutton9" name="Frame" value="Frame">';
  $_SESSION['submenu'] = New Submenu();
  $_SESSION['submenu']->addItem($javaCheck, "");
  $_SESSION['submenu']->addItem(vt("Documenten"), "javascript:checkChange('frameSet.php?page=" . base64_encode("dd_referenceList.php?module=CRM_naw&id=".$object->get('id')). "','extraFrame')", array('target' => ''));
}
$object->setOption('Client','form_type', "text");
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



if ($result = $editObject->result)
{
  //echo $returnUrl;exit;
	header("Location: ".$returnUrl);
}
else
{
	echo $_error = $editObject->_error;
}
?>