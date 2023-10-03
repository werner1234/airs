<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2020/05/06 14:56:43 $
 		File Versie					: $Revision: 1.41 $

 		$Log: rapportBackofficeClientProductie.php,v $
 		Revision 1.41  2020/05/06 14:56:43  rvv
 		*** empty log message ***
*/
include_once("wwwvars.php");
include_once("../classes/portefeuilleSelectieClass.php");
include_once("../classes/backofficeAfdrukkenClass.php");
include_once("../classes/templateEmail.php");
session_start();
$losseFactuurZonderRapportageTonen=1;

if($_GET['lookup']==1)
{
  $DB=new DB();
  $selectie=$_SESSION['backofficeSelectie'];
  $selectie['datumVan'] 							= form2jul($selectie['datumVan']);
  $selectie['datumTm'] 								= form2jul($selectie['datumTm']);
  $selectie['backoffice'] 						= true;

  $portefeuilleSelectie= new portefeuilleSelectie($selectie);
  $portefeuilles=$portefeuilleSelectie->getSelectie();
  $query="SELECT id,portefeuille FROM FactuurBeheerfeeHistorie WHERE portefeuille IN('".implode("','",array_keys($portefeuilles)) ."') AND periodeDatum='".date('Y-m-d',$selectie['datumTm'])."' ";
  $DB->SQL($query);
  $DB->Query();
  $records=$DB->records();
  $msg='leeg';
  if($records>0)
  {
    $aanwezig=array();
    while($data=$DB->nextRecord())
    {
      $aanwezig[]=$data['portefeuille'];
    }
    if(count($aanwezig)<10)
      $msg=vt("Er zijn al records aanwezig voor portefeuille(s)")." (".implode(",",$aanwezig).") ".vt("op")." ".date('d-m-Y',$selectie['datumTm']).
              ". ".vt("Deze records zullen worden overschreven. Doorgaan")."?";
    else
      $msg= vt("Er zijn al")." $records ".vt("records aanwezig op")." ".$_POST['datumTm'].". ".vt("Deze records zullen worden overschreven. Doorgaan")."?";
    $status=1;
  }
  else
  {
    $msg='All okay';
    $status=0;
  }

  echo json_encode(array('status'=>$status,'msg'=>$msg));
  exit;
}


$query = "SELECT max(check_module_CRM) as check_module_CRM,max(Vermogensbeheerders.check_portaalDocumenten) as check_portaalDocumenten, max(standaardRapportageFreq) as standaardRapportageFreq FROM Vermogensbeheerders ";
$DB = new DB();
$DB->SQL($query);
$DB->Query();
$rdata = $DB->nextRecord();

if($_POST['type']=='instellingen')
{
  $cfg=new AE_config();
  if($_POST['save']=='1')
  {
    $cfg->addItem('ProductieInstellingen',serialize($_POST));
  }
  elseif($_POST['save']=='-1')
  {
    $_POST=unserialize($cfg->getData('ProductieInstellingen'));
    $_SESSION['backofficeSelectie']=array_merge($_SESSION['backofficeSelectie'],$_POST);
  }

}
if($rdata['standaardRapportageFreq']=='r')
{
  $rapDatumCheck = '
  if( $("#crmRapDatum").is(\':checked\') == false)
  {
    var r = confirm(\'Weet u zeker dat de rapportagedatum voor de signalering niet wenst op te slaan\');
    if(r == false)
    {
      return false;
    }
  }
';
}
else
{
  $rapDatumCheck='';
}

//$_SESSION['submenu'] = New Submenu();
//$_SESSION['submenu']->addItem("CRM rapportage instellingen","CRM_rapportageInstelling.php",array('target'=>'_blank'));
$_SESSION['submenu'] = New Submenu();
$_SESSION['submenu']->addItem(vt('Instellingen opslaan'),"javascript:parent.frames['content'].saveDBSettings();");
$_SESSION['submenu']->addItem(vt('Instellingen laden'),"javascript:parent.frames['content'].loadDBSettings();");
 
if ($_GET['selectie'])
  $_SESSION['selectieMethode'] = $_GET['selectie'];
if($_SESSION['selectieMethode'] == 'portefeuille')
  $selectiePortefeuille = 'checked';
elseif($_SESSION['selectieMethode'] == 'vink')
  $selectieVink = 'checked';
else
  $selectieAlles = 'checked';

$content['style2']='<link rel="stylesheet" href="style/smoothness/jquery-ui-1.11.1.custom.css"> <link href="style/aeStyle.css" rel="stylesheet" type="text/css" media="screen">';
echo template($__appvar["templateContentHeader"],$content);

?>
<script type="text/javascript">

function saveDBSettings()
{
  document.selectForm.stap.value='productie';
	document.selectForm.type.value="instellingen";
	document.selectForm.save.value="1";
	document.selectForm.submit();
}

function loadDBSettings()
{
  document.selectForm.stap.value='productie';
	document.selectForm.type.value="instellingen";
	document.selectForm.save.value="-1";
	document.selectForm.submit();
}

function setRapportTypes()
{
	document.selectForm.rapport_types.value = "";
	var tel =0;
 	for(var i=0; i < parent.frames['submenu'].document.selectForm.rapport_type.length; i++)
 	{
 		if(parent.frames['submenu'].document.selectForm.rapport_type[i].checked == true)
 		{
 			document.selectForm.rapport_types.value = document.selectForm.rapport_types.value + '|' + parent.frames['submenu'].document.selectForm.rapport_type[i].value;
 			tel++;
 		}
 	}
}
<?
if($rapportSelectie[$rdata['layout'].'_b'])
  echo $rapportSelectie[$rdata['layout'].'_b'];
else
  echo $rapportSelectie['default_b'];
?>


function rapportageProduceren()
{
  <?php echo $rapDatumCheck;?>
  if( $("#factuurWegschrijven").is(':checked') == true)
  {
    $.ajax({
      type: "POST",
      url: "rapportBackofficeClientProductie.php?lookup=1",
      dataType: "json",
      async: false,
      data: $("#selectForm").serialize(),
      success: function (data, textStatus, jqXHR)
      {
        console.log(data);
        if (data.status == 0)
        {
          checkNaamEnSubmit();
        }
        else if (data.status == 1)
        {
          AEConfirm(data.msg, '<?=vt("Records aanwezig")?>', function ()
          {
            checkNaamEnSubmit();
          });
        }
        else if (data.status == 2)
        {
          AEMessage(data.msg, '<?=vt("Records bijwerken")?>', function ()
          {
          });
        }

      },
      error: function (jqXHR, textStatus, errorThrown)
      {
      }
    });
  }
  else
  {
     checkNaamEnSubmit();
  }


}

function checkNaamEnSubmit()
{
  var naamgekozen=false;
  for(var i=0; i < document.selectForm['bestandsnaamBegin[]'].length; i++)
  {
    if(document.selectForm['bestandsnaamBegin[]'][i].checked == true)
    {
      naamgekozen=true;
    }
  }
  if(naamgekozen==false)
  {
    AEMessage("<?=vt("Er is nog geen bestandsnaamopbouw gekozen")?>" , "<?=vt("Bestandsnaam")?>" , function() {});
  }
  else
  {
    document.selectForm.action = "rapportBackofficeClientAfdrukken2.php";
    document.selectForm.target = "generateFrame";
    document.selectForm.type.value = "pdf";
    document.selectForm.save.value = "0";
    document.selectForm.submit();
    document.selectForm.target = "";
    document.selectForm.action = "";
  }
}



function saveSettings()
{
	document.selectForm.target = "";//generateFrame
	document.selectForm.submit();
}

function checktest(box)
{
  if(box.checked)
  {
    document.selectForm.testrun.value=1;
  }
  else
  {
    document.selectForm.testrun.value=0;
  }
}

function controleerEmailInstellingen()
{
  portaalMailCheck();
  <?
  if($_SESSION['backofficeSelectie']['afzenderEmail']=='')
  {
   echo "alert('".vt("U dient eerst onder de `Opmaak` tab de email instellingen te controleren").".');";
   echo "$('#emailen').attr('checked', false);";
   echo "$('#portaalMail').attr('checked', false);";
   echo "return false;";
  }
  ?>
  if($('#emailen').prop('checked')==true){$('#eMailDiv').show();}else{$('#eMailDiv').hide()};
  
}

function controleerExportinstellingen()
{
  if($('#exporteren').prop('checked')==true){$('#exportDiv').show();}else{$('#exportDiv').hide()};
}

function controleerClusterExportinstellingen()
{
  if($('#exporterenCluster').prop('checked')==true){$('#clusterDiv').show();}else{$('#clusterDiv').hide()};
}

function factuurExportCheck()
{
  if($('#factuurExport').prop('checked')==true){$('#factuurExportDiv').show();}else{$('#factuurExportDiv').hide()};
}
function portaalCheck()
{
  if($('#portaal').prop('checked')==true)
  {
    $('#portaalDiv').show();
    $('#portaalKoppelDiv').show();
  }
  else
  {
    $('#portaalDiv').hide();
    $('#portaalKoppelDiv').hide();
  }

}
function portaalMailCheck()
{
  if($('#emailen').prop('checked')==true)
  {
    $('#emailKoppelDiv').show();
  }
  else {
    $('#emailKoppelDiv').hide();
  }

  if($('#portaalMail').prop('checked')==true && $('#emailen').prop('checked')==true)
  {
    alert("<?=vt("Portaal email en rapportagemail gaan niet samen")?>.\n<?=vt("Zorg dat de email tekst geschikt is voor de portaalmail")?>.");
    $('#emailen').attr('checked', false);
  }
  else if($('#portaalMail').prop('checked')==true)
  {
    alert("<?=vt("Zorg dat de email tekst geschikt is voor de portaalmail")?>.");
  }
}

function preRunWarning(keuze)
{
  if(keuze=='intern')
  {
    if ($('#rapportageIntern').prop('checked') == true)
    {
      $('#preRun').attr('checked', false);
      $('#preRun').attr('disabled', true)
    }
    else
    {
      $('#rapportageIntern').prop('disabled', false);
      $('#preRun').prop('disabled', false)
    }

  }
  else if(keuze=='prerun')
  {
    if ($('#preRun').prop('checked') == true)
    {
      $('#rapportageIntern').attr('checked', false);
      $('#rapportageIntern').attr('disabled', true);
    }
    else
    {
      $('#rapportageIntern').prop('disabled', false);
      $('#preRun').prop('disabled', false)
    }
  }

  if($('#preRun').prop('checked')==true)
  {
    if (confirm("<?=vt("De rapportages voor de opgegeven selectie zullen als in één PDF worden geproduceerd zodat deze gecontroleerd kunnen worden. Wilt u doorgaan")?>?")==false)
    {
      $('#preRun').attr('checked', false);
    }
  }
}

</script>

<br><br>
<div class="tabbuttonRow">
<?
$opmaakStyle='tabbuttonInActive';
$selectieStyle='tabbuttonInActive';
$samenvattingStyle='tabbuttonInActive';
$productieStyle='tabbuttonInActive'; 
if($_SESSION['backofficeSelectie']['stap'] == 'opmaak')
{
  $opmaakStyle='tabbuttonActive';
  $include='rapportBackofficeKwartaalopmaak.php';
}
elseif($_SESSION['backofficeSelectie']['stap'] == 'productie')
   $productieStyle='tabbuttonActive';
else
  $selectieStyle='tabbuttonActive';


?>
	<input type="button" class="<?=$selectieStyle?>" onclick="document.selectForm.stap.value='selectie';saveSettings();" id="tabbutton0" value="<?=vt("Selectie")?> ">
	<input type="button" class="<?=$opmaakStyle?>" onclick="document.selectForm.stap.value='opmaak';saveSettings();"  id="tabbutton1" value="<?=vt("Opmaak")?> ">
	<input type="button" class="<?=$samenvattingStyle?>" onclick="document.selectForm.stap.value='samenvatting';saveSettings();"  id="tabbutton1" value="<?=vt("Samenvatting")?> ">
	<input type="button" class="<?=$productieStyle?>" onclick="document.selectForm.stap.value='productie';saveSettings();"  id="tabbutton3" value="<?=vt("Productie")?> ">
</div>
<br>

<form method="POST" name="selectForm">
<input type="hidden" name="posted" value="true" />
<input type="hidden" name="stap" value="" />
<input type="hidden" name="type" value="" />
<input type="hidden" name="save" value="" />
<input type="hidden" name="exportRap" value="" />
<input type="hidden" name="testrun" value="" />


<table border="0">
<tr>
<td width="540" valign="top">
<fieldset id="Selectie" >
<?
$selectieData=$_SESSION['backofficeSelectie'];
$selectieData['datumVan'] =form2jul($selectieData['datumVan']);
$selectieData['datumTm']=form2jul($selectieData['datumTm']);

//$selectie=new portefeuilleSelectie($selectieData,'',true);
$selectie=new portefeuilleSelectie($selectieData,'',array('CrmClientNaam','CRM_naw.wachtwoord','CRM_naw.email','Vermogensbeheerders.CrmPortefeuilleInformatie'));//,'CRM_naw.rapportageVinkSelectie'
$afdruk=new backofficeAfdrukken($selectieData);
$aantal=$selectie->getRecords();
$portefeuilles=$selectie->getSelectie();
$afdruk->portefeuilles=$portefeuilles;

$checks=array('eMail','portaal');
$afdruk->selectie['type']='eMail';
foreach ($portefeuilles as $portefeuille=>$pdata)
{
  if($selectieData['CRM_rapport_vink']==1)
  {
    foreach($checks as $check)
    {
      $afdruk->selectie['type'] = $check;
      $afdruk->getCrmRapport($portefeuille);
      $rapporten = $afdruk->rapport_type;
      if (count($rapporten) > 0)
      {
        $emailPortefeuilles[$portefeuille] = $portefeuille;
        if ($pdata['CrmClientNaam'] && strlen($pdata['wachtwoord']) < 6)
          $geenKoppeling[$check]['wachtwoord'][] = "$portefeuille";
        if ($pdata['CrmClientNaam'] && $pdata['email'] == '')
          $geenKoppeling[$check]['Email'][] = "$portefeuille";
      }
    }
    if ($pdata['CrmClientNaam'] && $pdata['CRM_nawID'] == '')
      $geenKoppeling['algemeen']['Crm'][] = "$portefeuille";
  }
  else
  {
    $emailPortefeuilles[]=$portefeuille;
    if($pdata['CrmClientNaam'] && $pdata['CRM_nawID']=='')
      $geenKoppeling['algemeen']['Crm'][] = "$portefeuille";
    foreach($checks as $check)
    {
      if ($pdata['CrmClientNaam'] && strlen($pdata['wachtwoord']) < 6)
        $geenKoppeling[$check]['wachtwoord'][] = "$portefeuille";
      if ($pdata['CrmClientNaam'] && $pdata['email'] == '')
        $geenKoppeling[$check]['Email'][] = "$portefeuille";
    }
  }
}


foreach($geenKoppeling as $check=>$velddata)
{
  foreach($velddata as $veld=>$portefueilles)
  {
    foreach($portefueilles as $portefeuille)
    {
      $table[$check][$portefeuille][$veld]='X';
    }
  }
  foreach($table as $check=>$portefeuilles)
  {
    if($check=='algemeen')
      $checks=array('Crm'=>'CRM koppeling');
    else
      $checks=array('Email'=>'email','wachtwoord'=>'wachtwoord');

    $html[$check]="<table><tr><td><b>".vt("portefeuille")."</b></td>";
    foreach($checks as $c=>$checkOmschrijving)
      $html[$check].="<td><b>$checkOmschrijving</b></td>";
    $html[$check].="</td></tr>\n";

    foreach($portefeuilles as $portefeuille=>$veldData)
    {
      $html[$check].="<tr><td>$portefeuille</td>";//<td>email</td><td>wachtwoord</td></tr>"
      foreach($checks as $c=>$omschrijving)
      {
        if($veldData[$c])
          $html[$check].="<td style='background-color: red;text-align: center'>".$veldData[$c]."</td>";
        else
          $html[$check].="<td style='background-color: green;text-align: center'>V</td>";
      }
      $html[$check].="</tr>\n";
    }
    $html[$check].="</table>\n";
  }
}
//listarray($html);


$template=new templateEmail($_SESSION['backofficeSelectie']['email'],$_SESSION['backofficeSelectie']['onderwerp']);
if(!isset($_SESSION['backofficeSelectie']['rapportageIntern']))
{
  $_SESSION['backofficeSelectie']['rapportageIntern']='checked';
}
$items=array('afdrukken','exporteren','exporterenSftp','exporterenCluster','emailen','edossier','portaal','factuurPrinten','factuurExport','factuurExcel','factuurDb','factuurWegschrijven','factuurExact','factuurExactOnline','factuurTwinfield','factuurSnelstart','losseFactuur','losseFactuurZonderRapportage','consolidatieToevoegen','portaalMail','portaalLosseFactuur','exportWachtwoord','exportLosseFactuur','rapportageIntern');
foreach($items as $i)
{
  $disableTags[$i]=''; 
  if($_SESSION['backofficeSelectie'][$i]==1)
    $checkedTags[$i]='checked';
}  
$items=array('emailen','edossier','portaal');
foreach($items as $i)
{
  $disableTags[$i]='disabled'; 
  if($_SESSION['backofficeSelectie'][$i]==1)
    $checkedTags[$i]='checked';
}

echo "".vt("De huidige selectie bevat")." $aantal ".vt("portefeuilles").".<br><br>\n";
echo '<fieldset><legend> <b>'.vt("Missende koppelingen").'</b></legend> ';
echo '<div id="" style="overflow-y: scroll; max-height:200px; width:400px;">';
echo '<div>'.$html['algemeen'].'</div><br>';
echo '<div id="emailKoppelDiv" '.(($checkedTags['emailen']=='checked')?'':'style="display:none"').'><b>'.vt("Email koppelingen").'</b><br>'.$html['eMail'].'</div><br>';
echo '<div id="portaalKoppelDiv" '.(($checkedTags['portaal']=='checked')?'':'style="display:none"').'><b>'.vt("Portaal koppelingen").'</b><br>'.$html['portaal'].'</div>';
echo '</div>
</fieldset>';

	if($rdata['check_module_CRM'])
	{
	  if($_SESSION['usersession']['gebruiker']['verzendrechten']==1 || $_SESSION['usersession']['gebruiker']['verzendrechten']==3)
	  {
  	  $disableTags['edossier']=''; 
    }
    if($_SESSION['usersession']['gebruiker']['verzendrechten']==2 || $_SESSION['usersession']['gebruiker']['verzendrechten']==3)
    {
      $disableTags['emailen']=''; 
      if(GetModuleAccess('PORTAAL')==1)
      {
        $disableTags['portaal']='';
      }
    }
    $db = new DB();
    $crmVelden='';
    $extraJoin='';
    $extraVelden=array('Rapportagetenaamstelling','Rapportageafkorting');
    foreach($extraVelden as $veld)
    {
      $query="SHOW fields FROM CRM_naw like '$veld'";
      if($db->QRecords($query) > 0)
      {
         if(in_array($veld,$_POST['bestandsnaamBegin']))
           $checked='checked';
         else
           $checked='';  
         $Rapportagetenaamstelling .='</br> <input type="checkbox" '.$checked.' name="bestandsnaamBegin[]" value="'.$veld.'"> '.$veld.'';
      }
    }
	}
  
  $items=array('Portefeuille','Client');
  foreach($items as $i)
  {
    if(in_array($i,$_POST['bestandsnaamBegin']))
      $checkedTags[$i]='checked';
    else
      $checkedTags[$i]='';  
  }
?>

<fieldset id="bestandsnaam" ><legend> <?=vt("Instellingen")?></legend>
  <table border="0"><tr><td colspan="2">
 <b><?=vt("Bestandsnaam (PortefeuilleNr/Client + Extra tekst + .pdf)")?></b>
 </td></tr>
 <tr><td width="200">
<input type="hidden" name="bestandsnaamBegin[]" value="" />
<input type="checkbox" <?=$checkedTags['Portefeuille']?> name="bestandsnaamBegin[]" value="Portefeuille"> Portefeuille</br>
<input type="checkbox" <?=$checkedTags['Client']?> name="bestandsnaamBegin[]" value="Client"> Client
  <?php
  echo $Rapportagetenaamstelling;
  ?>
 </td><td valign="top">
 <?=vt("Extra tekst")?> </br> <input type="text" name="bestandsnaamEind" value="<?=$_POST['bestandsnaamEind']?>" size="25" maxlength="25">
   </td></tr></table>

      <?php
      if($rdata['standaardRapportageFreq']=='r')
      {
      ?>
      <table border="0"><tr><td><b><?=vt("Overige instellingen")?></b></td></tr>
      <tr><td>
      <?php
      if($rdata['standaardRapportageFreq']=='r')
      {
        echo '<input type="hidden" name="crmRapDatum" value="" />
        <input type="checkbox" '.$checkedTags['crmRapDatum'].' value="1" name="crmRapDatum" id="crmRapDatum" /> Wegschrijven datum voor signalering';
      }
      ?>
      </td></tr></table>
      <?php
      }
      ?>
      

</fieldset>
</br>
<input type="hidden" name="afdrukken" value="" />
<input type="hidden" name="exporteren" value="" />
<input type="hidden" name="emailen" value="" />
<div> <input type="checkbox" <?=$checkedTags['afdrukken']?> value="1" <?=$disableTags['afdrukken']?> name="afdrukken" id="afdrukken" /> <?=maakKnop('pdf.png',array('size'=>16))?><span id="afdrukkenStatus"> <?=vt("Afdrukken")?> </span></div><br>
<div>
  <input type="checkbox" <?=$checkedTags['exporteren']?> value="1" <?=$disableTags['exporteren']?> name="exporteren" id="exporteren" onclick="javascript:controleerExportinstellingen();" /> <span id="exporterenStatus">  <?=vt("Exporteren")?> </span>
  <?if(isset($ftpClient) && count($ftpClient)>=3){ ?>
  <input type="checkbox" <?=$checkedTags['exporterenSftp']?> value="1" <?=$disableTags['exporterenSftp']?> name="exporterenSftp" id="exporterenSftp" onclick="javascript:controleerExportinstellingen();" /> <span id="exporterenStatus">  <?=vt("naar sftp",false)?> </span>
  <? }?>
</div><br>

<?php
  if($checkedTags['exporteren']=='checked')
    echo '<fieldset id="exportDiv">';
  else
    echo '<fieldset id="exportDiv" style="display: none;">';
?>
  <input type="hidden" name="exporterenPdf" value="0">
  <input type="hidden" name="exporterenEmail" value="0">
  <input type="checkbox"  name="exporterenPdf" value="1" checked ><?=vt("PDF selectie")?><br>
  <input type="checkbox" name="exporterenEmail" value="1" checked ><?=vt("Email selectie")?><br>
  <input type="checkbox"  <?=$checkedTags['exportWachtwoord']?> name="exportWachtwoord" value="1" /><?=vt("met wachtwoord")?><br>
 <!-- <input type="checkbox"  <?=$checkedTags['exportLosseFactuur']?> name="exportLosseFactuur" value="1" />factuur als losse bijlage<br> -->

</fieldset>
  
  <?php
  if($DB->QRecords('select id FROM portefeuilleClusters limit 3')>0)
  {
    ?>
    <div>
      <input type="hidden" name="exporterenCluster" value="" />
      <input type="checkbox" <?=$checkedTags['exporterenCluster']?> value="1" <?=$disableTags['exporterenCluster']?> name="exporterenCluster" id="exporterenCluster" onclick="javascript:controleerClusterExportinstellingen();"/>
      <?=vt("Exporteren cluster pdfs")?></span><span id="exporterenClusterStatus">  </span></div>
      <?php
      if($checkedTags['exporterenCluster']=='checked')
        echo '<fieldset id="clusterDiv">';
      else
        echo '<fieldset id="clusterDiv" style="display: none;">';
      ?>
      <legend> <?=vt("Bestandsnaam")?> </legend>
      <div class="formblock">
        <div class="formlinks">
          <?=vt("Extra tekst")?>
        </div>
        <div class="formrechts"> <input type="text" name="bestandsnaamClusterEind" value="<?=$_POST['bestandsnaamClusterEind']?>" size="25" maxlength="25">
        </div>
      </div>
    </fieldset>
    <br>
    <?
  }
?>
  <div> <input type="checkbox" <?=$checkedTags['emailen']?> value="1" <?=$disableTags['emailen']?> name="emailen" id="emailen" onclick="javascript:controleerEmailInstellingen();" /> <span id="emailenStatus"> <?=vt("Emailen")?> </span></div><br>
<?php
if($checkedTags['emailen']=='checked')
  echo '<fieldset id="eMailDiv">';
else
  echo '<fieldset id="eMailDiv" style="display: none;">';
echo "<h3>".vt("eMail instellingen")."</h3>";
echo "".vt("De selectie bevat")." ".count($emailPortefeuilles)." ".vt("portefeuille(s) voor de email zending").".<br><br>";
echo '<br><table border=1><tr><td>'.vt("veld").'</td><td>'.vt("voorbeeld").'</td></tr>';
foreach ($portefeuilles as $portefeuille=>$pdata)
{
  $allPdata=$template->getPortefeuileValues($portefeuille);
  $email=$template->templateData($allPdata);
  echo '<tr><td>'.vt("Onderwerp").'</td><td>'.$email['subject'].'</td></tr>';
  echo '<tr><td>'.vt("Email").' </td><td>'.$email['body'].'</td></tr>';
  break;
}
echo '</table>';
?>
<input type="hidden" name="losseFactuur" value="0" />
<input type="hidden" name="losseFactuurZonderRapportage" value="0" />
<input type="hidden" name="portaalLosseFactuur" value="0" />
<input type="checkbox"  <?=$checkedTags['losseFactuur']?> name="losseFactuur" value="1" /><?=vt("factuur als losse bijlage")?>
  <?
  if($losseFactuurZonderRapportageTonen==1)
  {
  ?>
  <input type="hidden" name="losseFactuurZonderRapportage" value="0" /><input <?=$checkedTags['losseFactuurZonderRapportage']?> type="checkbox" name="losseFactuurZonderRapportage" value="1" /> <?=vt("zonder rapportage")?>.
  <?
  }
  ?>
</fieldset>
<input type="hidden" name="edossier" value="" />
<div> <input type="checkbox" <?=$checkedTags['edossier']?> value="1" <?=$disableTags['edossier']?> name="edossier" id="edossier" onclick="javascript:if($('#edossier').prop('checked')==true){$('#edossierDiv').show();}else{$('#edossierDiv').hide();}"  /> <span id="edossierStatus"> <?=vt("eDossier")?> </span>
<?
if($checkedTags['edossier']=='checked')
  echo '<fieldset id="edossierDiv">';
else
  echo '<fieldset id="edossierDiv" style="display: none;">';
?>
<div class="formblock"><div class="formlinks"> <?=vt("eDossier Omschrijving")?> </div><div class="formrechts"> <input type="text" name="documentOmschrijving" value="<?=$_POST['documentOmschrijving']?>" size="25"> </div></div>
 <input type="hidden" name="eDossierPdf" value="0">
  <input type="hidden" name="eDossierEmail" value="0">
  <input type="checkbox"  name="eDossierPdf" value="1" checked ><?=vt("PDF selectie")?><br>
  <input type="checkbox" name="eDossierEmail" value="1" checked ><?=vt("Email selectie")?><br>
  
  <input type="checkbox"  <?=$checkedTags['edossierLosseFactuur']?> name="edossierLosseFactuur" value="1" /><?=vt("factuur als los document")?>
  <?
  if($losseFactuurZonderRapportageTonen==1)
  {
    ?>
    <input type="hidden" name="edossierLosseFactuurZonderRapportage" value="0" /><input <?=$checkedTags['edossierLosseFactuurZonderRapportage']?> type="checkbox" name="edossierLosseFactuurZonderRapportage" value="1" /> <?=vt("zonder rapportage")?>.
    <?
  }
  ?>
  
  </fieldset>

</div>
  <br>
<div> 
<input type="hidden" value="0" name="portaal" />
<input type="checkbox" <?=$checkedTags['portaal']?> value="1" <?=$disableTags['portaal']?> name="portaal" id="portaal" onclick="javascript:portaalCheck();" />
<span id="portaalStatus"> <?=vt("Portaal")?> </span>
<?
if($checkedTags['portaal']=='checked')
  echo '<fieldset id="portaalDiv">';
else
  echo '<fieldset id="portaalDiv" style="display: none;">';
?>
  <input type="hidden" value="0" name="portaalMail"> 
  <input type="checkbox" <?=$checkedTags['portaalMail']?> value="1" name="portaalMail" id="portaalMail" onclick="javascript:controleerEmailInstellingen();"><?=vt("verstuur portaal-mail")?>
  <?
  if($rdata['check_portaalDocumenten']==1)
  {
    ?>
    <br><input type="checkbox" <?=$checkedTags['portaalLosseFactuur']?> value="1" name="portaalLosseFactuur" id="portaalLosseFactuur"><?=vt("losse factuur naar portaal")?>
  <?
    if($losseFactuurZonderRapportageTonen==1)
    {
    ?>
    <input type="hidden" name="portaallosseFactuurZonderRapportage" value="0"/>
    <input <?=$checkedTags['portaallosseFactuurZonderRapportage']?> type="checkbox" name="portaallosseFactuurZonderRapportage" value="1"/> <?=vt("zonder rapportage")?>.
    <?
    }
  }
  ?>
</fieldset>
</div><br>

<?
if($_SESSION['backofficeSelectie']['inclFactuur']==1)
{
?>
  <fieldset id="factuurInfoDiv">
  <legend> Facturen</legend>
  <input type="hidden" name="factuurPrinten" value="" />
  <input type="hidden" name="factuurExcel" value="" />
  <input type="hidden" name="factuurDb" value="" />
  <input type="hidden" name="factuurExact" value="" />
  <input type="hidden" name="factuurExactOnline" value="" />
  <input type="hidden" name="factuurTwinfield" value="" />
  <input type="hidden" name="factuurSnelstart" value="" />
  <input type="hidden" name="factuurWegschrijven" value="" />
  <div> <input type="checkbox" <?=$checkedTags['factuurPrinten']?> value="1" <?=$disableTags['factuurPrinten']?> name="factuurPrinten" id="factuurPrinten" /> <span id="factuurPrintStatus"><?=vt("Facturen printen")?></span> </div><br>
  
  <div> <input type="checkbox" <?=$checkedTags['factuurExport']?> value="1" <?=$disableTags['factuurExport']?> name="factuurExport" id="factuurExport" onclick="javascript:factuurExportCheck();"/> <span id="factuurExportStatus"><?=vt("Facturen exporteren")?></span> </div><br>
  
  <?php
  if($checkedTags['factuurExport']=='checked')
    echo '<fieldset id="factuurExportDiv">';
  else
    echo '<fieldset id="factuurExportDiv" style="display: none;">';
  ?>
  <input type="checkbox"  <?=$checkedTags['factuurexportWachtwoord']?> name="factuurexportWachtwoord" value="1" /><?=vt("met wachtwoord")?><br>
  <input type="checkbox"  <?=$checkedTags['factuurexportBestandFactuurNr']?> name="factuurexportBestandFactuurNr" value="1" /><?=vt("Factuurnummer in bestandsnaam")?>
  <input type="checkbox"  <?=$checkedTags['factuurexportBestandFactuurNr']?> name="factuurexportBestandFactuurNr" value="2" /><?=vt("Prefix behouden")?>.<br>
   </fieldset>
 
 
  <div> <input type="checkbox" <?=$checkedTags['factuurExcel']?> value="1" <?=$disableTags['factuurExcel']?> name="factuurExcel" id="factuurExcel" /> <span id="factuurExcelStatus">  <?=vt("Factuurinfo naar Excel")?>. </span></div><br>
  <div> <input type="checkbox" <?=$checkedTags['factuurDb']?> value="1" <?=$disableTags['factuurDb']?> name="factuurDb" id="factuurDb" /> <span id="factuurDbStatus">  <?=vt("Factuurinfo naar reportbuilder")?>. </span></div><br>
  <div> <input type="checkbox" <?=$checkedTags['factuurWegschrijven']?> value="1" <?=$disableTags['factuurWegschrijven']?> name="factuurWegschrijven" id="factuurWegschrijven" /> <span id="factuurWegschrijvenStatus">  <?=vt("Factuurinfo wegschrijven naar tabel")?>. </span></div><br>

  <?if ($__exact["dagboek"] <> ""){?>
  <div> <input type="checkbox" <?=$checkedTags['factuurExact']?> value="1" <?=$disableTags['factuurExact']?> name="factuurExact" id="factuurExact" /> <span id="factuurExactStatus">  <?=vt("Factuurinfo naar ExactGlobe")?>. </span></div><br>
 <?}
 if($__exactOnline["dagboek"] <> ""){?>
  <div> <input type="checkbox" <?=$checkedTags['factuurExactOnline']?> value="1" <?=$disableTags['factuurExactOnline']?> name="factuurExactOnline" id="factuurExactOnline" /> <span id="factuurExactOnlineStatus">  <?=vt("Factuurinfo naar ExactOnline")?>. </span></div><br>
<?}
  if($__twinfield["grootboek_debiteur"] <> ""){?>
    <div> <input type="checkbox" <?=$checkedTags['factuurTwinfield']?> value="1" <?=$disableTags['factuurTwinfield']?> name="factuurTwinfield" id="factuurTwinfield" /> <span id="factuurTwinfieldStatus">  <?=vt("Factuurinfo naar Twinfield")?>. </span></div><br>
  <?}
  if($__snelstart["dagboek"] <> ""){?>
    <div> <input type="checkbox" <?=$checkedTags['factuurSnelstart']?> value="1" <?=$disableTags['factuurSnelstart']?> name="factuurSnelstart" id="factuurSnelstarte" /> <span id="factuurSnelstartStatus">  <?=vt("Factuurinfo naar Snelstart")?>. </span></div><br>
  <?}
  echo '</fieldset>';
}
?>
  <br>

  <div>
    <input type="checkbox" <?=$checkedTags['rapportageIntern']?> value="1" <?=$disableTags['rapportageIntern']?> name="rapportageIntern" id="rapportageIntern" onclick="javascript:preRunWarning('intern');" > <span id="rapportageInternStatus"> <?=vt("Gehele rapportageset")?> </span>
  </div><br>
  <div> <input type="checkbox" <?=$checkedTags['preRun']?> value="1" <?=$disableTags['preRun']?> name="preRun" id="preRun" onclick="javascript:preRunWarning('prerun');" > <span id="preRunStatus"> <?=vt("Pre-run")?> </span></div><br>

  <div class="buttonDiv" style="width:200px" onclick="javascript:rapportageProduceren();"> &nbsp; &nbsp; <?=vt("Rapportage produceren")?> </div><br>

</td>


</fieldset>
</tr>

<tr>
	<td colspan="2">
		<iframe width="540" height="300" name="generateFrame" frameborder="0" ></iframe>
	</td>
</tr>
</table>
</form>
<?
if($__debug) {
	echo getdebuginfo();
}

echo template($__appvar["templateRefreshFooter"],$content);
