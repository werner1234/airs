<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2018/10/13 17:16:37 $
 		File Versie					: $Revision: 1.3 $

 		$Log: rapportKoersControleSelectie.php,v $
 		Revision 1.3  2018/10/13 17:16:37  rvv
 		*** empty log message ***

*/

//$AEPDF2=true;
include_once("wwwvars.php");
include_once("../classes/AE_cls_fpdf.php");
include_once("../classes/AE_cls_progressbar.php");
include_once("../classes/selectOptieClass.php");
include_once("../classes/portefeuilleSelectieClass.php");
define('FPDF_FONTPATH',$__appvar["basedir"]."/html/font/");
include_once("rapport/rapportVertaal.php");
include_once("rapport/rapportRekenClass.php");
include_once("rapport/PDFOverzicht.php");

$editcontent['javascript'] = $content['javascript'];
$type='portefeuille';
$maxVink=25;

$editcontent['calendarinclude'] = "<script language=JavaScript src=\"javascript/algemeen.js\" type=text/javascript></script> <script language=JavaScript src=\"javascript/selectbox.js\" type=text/javascript></script>";
$kal = new DHTML_Calendar();
$editcontent['calendar'] = $kal->get_load_files_code();

if($_SESSION['metConsolidatie']=='')
  $_SESSION['metConsolidatie']=0;

echo template($__appvar["templateContentHeader"],$editcontent);
flush();

if($_GET['actief'] == "inactief" )
{
	$inactiefChecked = "checked";
	$actief = "inactief";
	$alleenActief = " ";
}
else
{
	$actiefChecked = "checked";
	$actief = "actief";
	$alleenActief = " AND (Fondsen.EindDatum  >=  NOW() OR Fondsen.EindDatum = '0000-00-00') ";
}

if($_POST['posted'])
{
	$start = getmicrotime();

	include_once("rapport/koersControle.php");
	include_once("rapport/actieveFondsenBepalen.php");
	include_once("rapport/ouderdomsAnalyse.php");
	include_once("rapport/koersvergelijking.php");

	if(!empty($_POST['datumTm']))
	{
		$dd = explode($__appvar["date_seperator"],$_POST['datumTm']);
		if(!checkdate(intval($dd[1]),intval($dd[0]),intval($dd[2])))
		{
			echo "<b>".vt("Fout").": ".vt("ongeldige datum opgegeven")."!</b>";
			exit;
		}
		else
		{
		  $rapJul=form2jul($_POST['datumTm']);
    	$valutaDatum = getLaatsteValutadatum();
      $valutaJul = db2jul($valutaDatum);
    	if($rapJul > $valutaJul + 86400)
	    {
		    echo "<b>".vt("Fout").": ".vt("kan niet in de toekomst rapporteren").".</b>";
		//    exit;
	    }
		}
	}
	else
	{
		echo "<b>".vt("Fout").": ".vt("geen datum opgegeven")."!</b>";
		exit;
	}
  $selectData=$_POST;
	$selectData['datumVan'] 							= form2jul($_POST['datumVan']);
	$selectData['datumTm'] 								= form2jul($_POST['datumTm']);

	if($selectData['newFonds'] != '')
	  $selectData['fonds'] = $selectData['newFonds'] ;

	$selectData['selectedPortefeuilles'] = $_POST['selectedFields'];


	// maak progressbar
	$prb 						= new ProgressBar(536,8);
	$prb->color 		= 'maroon';
	$prb->bgr_color = '#ffffff';
	$prb->brd_color = 'Silver';
	$prb->left 			= 0;
	$prb->top 			=	0;
	$prb->show();

	switch($selectData['soort'])
	{
	  case "koersControle" :
			$rapport = new koersControle( $selectData );
			$rapport->USR = $USR;
			$rapport->progressbar = & $prb;
			$rapport->__appvar = $__appvar;
			$rapport->writeRapport();
			$rapportnaam = $__appvar["bedrijf"]."_KOE";
		break;
		case "actieveFondsenBepalen" :
			$rapport = new actieveFondsenBepalen( $selectData );
			$rapport->USR = $USR;
			$rapport->progressbar = & $prb;
			$rapport->__appvar = $__appvar;
			$rapport->writeRapport();
			$rapportnaam = $__appvar["bedrijf"]."_ACF";
			break;
		case "ouderdomsAnalyse" :
			$rapport = new ouderdomsAnalyse( $selectData );
			$rapport->USR = $USR;
			$rapport->progressbar = & $prb;
			$rapport->__appvar = $__appvar;
			$rapport->writeRapport();
			$rapportnaam = $__appvar["bedrijf"]."_ODA";
			break;
		case "koersvergelijking" :
			$rapport = new koersvergelijking( $selectData );
			$rapport->USR = $USR;
			$rapport->progressbar = & $prb;
			$rapport->__appvar = $__appvar;
			$rapport->writeRapport();
			$rapportnaam = $__appvar["bedrijf"]."_KVE";
			break;
	}

	switch($_POST['filetype'])
	{
		case "PDF" :
			$filename = $rapportnaam.".pdf";
			$filetype = "pdf";
			$rapport->pdf->Output($__appvar['tempdir'].$filename,"F");
		break;
		case "cvs" :
			$filename = $rapportnaam.".csv";
			$filetype = "csv";
			$rapport->pdf->OutputCSV($__appvar['tempdir'].$filename,"F");
		break;
		case "xls" :
			$filename = $rapportnaam.".xls";
			$filetype = "xls";
			$rapport->pdf->OutputXLS($__appvar['tempdir'].$filename,"F");
		break;
	}
	// push javascript de PDF te openen in een nieuw window en daarna het bestand verwijderen.
?>


<script type="text/javascript">
function pushpdf(file,save)
{

	var width='800';
	var height='600';
	var target = '_blank';
	var location = 'pushFile.php?filetype=<?=$filetype?>&file=' + file;
	if(save == 1)
	{
		// opslaan als bestand
		document.location = location + '&action=attachment';
	}
	else
	{
		// pushen naar PDF reader
		var doc = window.open("",target,'toolbar=no,status=yes,scrollbars=yes,location=no,menubar=yes,resizable=yes,directories=no,width=' + width + ',height= ' + height);
		doc.document.location = location;
	}
}
pushpdf('<?=$filename?>',<?=$save?>);
</script>
<?
	exit();
}
else
{
	// selecteer laatst bekende valutadatum
	$totdatum = getLaatsteValutadatum();
  $jr = substr($totdatum,0,4);
  session_start();

$selectie=new selectOptie();
$html='<form name="selectForm">';
$selectie->getInternExternActive();
$html.=$selectie->getSelectieMethodeHTML($PHP_SELF);
$html.=$selectie->getInternExternHTML($PHP_SELF);
if(method_exists($selectie,'getConsolidatieHTML'))
    $html.="<br>".$selectie->getConsolidatieHTML($PHP_SELF);
$html.='</form>';



	$_SESSION['NAV'] = "";
	$_SESSION['submenu'] = New Submenu();
  $_SESSION['submenu']->addItem($html,"");
	session_write_close();


?>


	<script language=JavaScript src="javascript/popup.js" type=text/javascript></script>


	<script type="text/javascript">


<?=$selectie->getSelectJava();?>


	function print()
	{
		document.selectForm.target = "generateFrame";
		document.selectForm.filetype.value="PDF";
		document.selectForm.save.value="0";
		selectSelected();
		if (checkfield())
		document.selectForm.submit();
	}


	function saveasfile()
	{
		document.selectForm.target = "generateFrame";
		document.selectForm.filetype.value="PDF";
		document.selectForm.save.value="1";
		selectSelected();
		if (checkfield())
		document.selectForm.submit();
	}

	function csv()
	{
		document.selectForm.target = "generateFrame";
		document.selectForm.filetype.value="cvs";
		document.selectForm.save.value="1";
		selectSelected();
		if (checkfield())
		document.selectForm.submit();
	}

	function xls()
	{
		document.selectForm.target = "generateFrame";
		document.selectForm.filetype.value="xls";
		document.selectForm.save.value="1";
		selectSelected();
		if (checkfield())
		document.selectForm.submit();
	}

  
	function checkfield()
	{

	  return true;
	}

	function selectSelected()
	{
	  if(document.selectForm['inFields[]'])
	  {
		var inFields  			= document.selectForm['inFields[]'];
		var selectedFields 	= document.selectForm['selectedFields[]'];

		for(j=0; j < selectedFields.options.length; j++)
		  {
 			selectedFields.options[j].selected = true;
		  }
	  }
	  if(document.selectForm['modelportefeuille[]'])
  	{
	  	var inFields  			= document.selectForm['inModelportefeuille[]'];
	  	var selectedFields 	= document.selectForm['modelportefeuille[]'];

  		for(j=0; j < selectedFields.options.length; j++)
  		{
 	  		selectedFields.options[j].selected = true;
	  	}
	  }
	  <?=$selectie->getPortefeuilleInternJava()?>
	}

	function resetSelect()
	{
	  var theForm = document.selectForm.elements, z = 0;
    for(z=0; z<theForm.length;z++)
    {
      if(theForm[z].selectedIndex)
      {
        field=theForm[z].name;
        if(field != 'soort' && field.search('PortefeuilleClusters') <0)
        {
          if(field.search('Tm') > 0)
          {
            theForm[z].selectedIndex = theForm[z].length-1;
          }
          else
          {
            theForm[z].selectedIndex = 0;
          }
        }
      }
    }
	}


	function selectTab()
	{
	  document.getElementById('afdrukkenButton').style.visibility="visible";
	  document.getElementById('opslaanButton').style.visibility="visible";
		$('#koersControleCheckDiv').hide();
		$('#gebruikPortefeuilleSelectiekDiv').hide();
		$('#aanvullenDiv').hide();
		$('#ouderdomsAnalyseDiv').hide();
		$('#gebruikInactieveFondsenDiv').hide();
		$('#vanDiv').hide();
		


		if( document.selectForm.soort.selectedIndex== 0 )
		{
			document.getElementById('csvButton').style.visibility="visible";
			document.getElementById('xlsButton').style.visibility="visible";
			document.getElementById('afdrukkenButton').style.visibility="hidden";
			document.getElementById('opslaanButton').style.visibility="hidden";
			document.getElementById('PortefueilleSelectie').style.visibility="visible";
			$('#gebruikPortefeuilleSelectiekDiv').show();
		}
		else if( document.selectForm.soort.selectedIndex== 1 )
		{
			document.getElementById('csvButton').style.visibility="visible";
			document.getElementById('xlsButton').style.visibility="visible";
			document.getElementById('afdrukkenButton').style.visibility="hidden";
			document.getElementById('opslaanButton').style.visibility="hidden";
			document.getElementById('PortefueilleSelectie').style.visibility="visible";
			$('#aanvullenDiv').show();
			$('#gebruikPortefeuilleSelectiekDiv').show();
      $('#gebruikInactieveFondsenDiv').show();
		}
		else if( document.selectForm.soort.selectedIndex== 2 )
		{
			document.getElementById('csvButton').style.visibility="visible";
			document.getElementById('xlsButton').style.visibility="visible";
			document.getElementById('afdrukkenButton').style.visibility="hidden";
			document.getElementById('opslaanButton').style.visibility="hidden";
			document.getElementById('PortefueilleSelectie').style.visibility="visible";
			$('#ouderdomsAnalyseDiv').show();
			$('#gebruikPortefeuilleSelectiekDiv').show();
      $('#gebruikInactieveFondsenDiv').show();
		}
		else if( document.selectForm.soort.selectedIndex== 3 )
		{
			document.getElementById('csvButton').style.visibility="visible";
			document.getElementById('xlsButton').style.visibility="visible";
			document.getElementById('afdrukkenButton').style.visibility="hidden";
			document.getElementById('opslaanButton').style.visibility="hidden";
			document.getElementById('PortefueilleSelectie').style.visibility="visible";
			$('#ouderdomsAnalyseDiv').show();
			$('#gebruikPortefeuilleSelectiekDiv').show();
      $('#gebruikInactieveFondsenDiv').show();
			$('#vanDiv').show();
		}		
	

		resetSelect();
	}


	function moveItem(from,to){
	var tmp_text = new Array();
	var tmp_value = new Array();
 	for(var i=0; i < from.options.length; i++) {
 		if(from.options[i].selected)
 		{
			var blnInList = false;
			for(j=0; j < to.options.length; j++)
			{
 				if(to.options[j].value == from.options[i].value)
				{
 					//alert("already in list");
 					blnInList = true;
 					break;
 				}
			}
			if(!blnInList)
 			{
				to.options.length++;
				to.options[to.options.length-1].text = from.options[i].text;
				to.options[to.options.length-1].value = from.options[i].value;
			}
 		}
		else
		{
			tmp_text.length++;
			tmp_value.length++;
			tmp_text[tmp_text.length-1] = from.options[i].text;
			tmp_value[tmp_text.length-1] = from.options[i].value;

		}
 	}
 	from.options.length = 0;
 	for(var i=0; i < tmp_text.length; i++) {
 		from.options.length++;
		from.options[from.options.length-1].text = tmp_text[i];
		from.options[from.options.length-1].value = tmp_value[i];
 	}
 	from.selectedIndex = -1;
}

    var ajax = new Array();
function getAjaxWaarden (sel,formExtra,Veld)
{
  if(document.getElementById(Veld).options.length < 10)
  {
    var oldValue = document.getElementById(Veld).value;
    if(sel.length>0){
	  	var index = ajax.length;
	  	ajax[index] = new sack();
	 	  ajax[index].element = Veld;
		  ajax[index].requestFile = 'lookups/ajaxLookup.php?module=queryLookups&query='+sel;	// Specifying which file to get
	  	ajax[index].onCompletion = function(){ setAjaxWaarden(index,Veld,oldValue,formExtra) };	// Specify function that will be executed after file has been found
	  	ajax[index].onError = function(){ alert('<?=vt("Ophalen velden mislukt")?>.') };
	  	ajax[index].runAJAX();		// Execute AJAX function
    }
	}
}
function setAjaxWaarden(index,veld,oldValue,formExtra)
{
 	var	Waarden = ajax[index].response;
	var elements = Waarden.split('\n');
	var useDiv=0;
	if(document.getElementById("div_"+veld)){useDiv=1};
 	if(elements.length > 1)
 	{
 	  var item='';
	  if(useDiv)
	  {
	    var div_a ='<select name="'+veld+'\" style="width:200px" '+formExtra+' >';
	    div_a += '<option value="" >---</option>';
	    var selectedA='';
	  }
	  else
	  {
	    document.getElementById(veld).options.length=0;
 	    AddName(veld,'---','');
	  }
    for(var i=0;i<elements.length;i++)
 	  {
 	    var fields = elements[i].split('\t');
 	    if(elements[i] != '')
 	    {
 	      if(useDiv)
	      {
	   	    if(fields[0]==oldValue){selectedA="selected";}else{selectedA=""};
          div_a += '<option value="' + fields[0] + '" ' + selectedA + '>' + fields[1] + '</option>';
	      }
	      else
	      {
          AddName(veld,fields[0],fields[1]);
	      }
      }
    }
 	}
 	if(useDiv)
 	{
 	   div_a += "</select>";
     document.getElementById("div_"+veld).innerHTML=div_a;
 	}
 	else
 	{
    document.getElementById(veld).value = oldValue;
 	}
}
function AddName(p_SelectName,p_OptionText,p_OptionValue)
{
  document.getElementById(p_SelectName).options[document.getElementById(p_SelectName).length] = new Option(p_OptionText,p_OptionValue);
}


</script>

<br><br>


<form action="<?=$PHP_SELF?>" method="POST" target="_blank" name="selectForm">
<input type="hidden" name="posted" value="true" />
<input type="hidden" name="save" value="" />
<input type="hidden" name="rapport_types" value="" />
<input type="hidden" name="filetype" value="PDF" />
<input type="hidden" name="portefeuilleIntern" value="" />
<input type="hidden" name="extra" value="" />
<input type="hidden" name="metConsolidatie" value="<?=$_SESSION['metConsolidatie']?>" />
<table border="0">
<tr>
	<td width="540">

<fieldset id="Rapport" >
<legend accesskey="C"><?=vt("Controle")?></legend>

<div class="formblock">
<div class="formlinks"> <?=vt("Controle")?> </div>
<div class="formrechts">

<select name="soort" style="width:200px" onChange="selectTab();">
	<option value="actieveFondsenBepalen"><?=vt("ActieveFondsen")?></option>
	<option value="koersControle"><?=vt("Koerscontrole")?></option>
	<option value="ouderdomsAnalyse"><?=vt("OuderdomsAnalyse")?></option>
	<option value="koersvergelijking"><?=vt("Koersvergelijking")?></option>
</select>
</div>
</div>
<?
echo $selectie->createDatumSelectie(array('divExtraVan'=>'id="vanDiv"'));//;
?>
</fieldset>

<div id="PortefueilleSelectie" style="">
<fieldset id="Selectie" >
<legend accesskey="S"><?=vt("Selectie")?></legend>
<?
// portefeuille selectie
if($_SESSION['selectieMethode'] == 'portefeuille')
{
?>
<table cellspacing="0" border = 1>
<?
$DB = new DB();
$DB->SQL($selectie->queries['ClientPortefeuille']);
$DB->Query();
?>
<br><br>
<?
  while($gb = $DB->NextRecord())
  {
    $data[$gb['Portefeuille']] = $gb;
  }
  echo "<br><br>";
  echo $selectie->createEnkelvoudigeSelctie($data,$_SESSION['backofficeSelectie']);
  echo "<br><br>";

?>
<br><br>
<?
}
// end portefeuille selectie
else
{

 $DB = new DB();
  $maxVink=25;
  $opties=array('Vermogensbeheerder'=>'Vermogensbeheerder','Accountmanager'=>'accountmanager','TweedeAanspreekpunt'=>'tweedeAanspreekpunt','Client'=>'client','Portefeuille'=>'portefeuilles','Depotbank'=>'depotbank');
  foreach ($opties as $optie=>$omschrijving)
  {
    $data=$selectie->getData($optie);
    if($_SESSION['selectieMethode'] =='vink' && count($data) < $maxVink)
      echo $selectie->createCheckBlok($optie,$data,$_SESSION['backofficeSelectie'],$omschrijving);
    else
      echo $selectie->createSelectBlok($optie,$data,$_SESSION['backofficeSelectie'],$omschrijving);
  }
  $opties=array('Risicoklasse'=>'Risicoklasse','AFMprofiel'=>'AFMprofiel','SoortOvereenkomst'=>'SoortOvereenkomst','Remisier'=>'Remisier','PortefeuilleClusters'=>'PortefeuilleClusters','selectieveld1'=>'Selectieveld1','selectieveld2'=>'Selectieveld2');
  foreach ($opties as $optie=>$omschrijving)
  {
    $data=$selectie->getData($optie);
    if(count($data) > 1)
    {
      if($_SESSION['selectieMethode'] =='vink' && count($data) < $maxVink)
        echo $selectie->createCheckBlok($optie,$data,$_SESSION['backofficeSelectie'],$omschrijving);
      else
        echo $selectie->createSelectBlok($optie,$data,$_SESSION['backofficeSelectie'],$omschrijving);
    }
  }
}

?>

</fieldset>

</div>

</td>
<td valign="top" width="400">

<div class="buttonDiv" id="afdrukkenButton" style="width:130px" onclick="javascript:print();">&nbsp;&nbsp;<?=maakKnop('pdf.png',array('size'=>16))?> <?=vt("Afdrukken")?></div><br>
<div class="buttonDiv" id="opslaanButton" style="width:130px" onclick="javascript:saveasfile();">&nbsp;&nbsp;<?=maakKnop('disk_blue.png',array('size'=>16))?> <?=vt("Opslaan")?> </div><br>
<div class="buttonDiv" id="csvButton" style="width:130px" onclick="javascript:csv();">&nbsp;&nbsp;<?=maakKnop('csv.png',array('size'=>16))?> <?=vt("CSV-export")?> </div><br>
<div class="buttonDiv" id="xlsButton" style="width:130px" onclick="javascript:xls();">&nbsp;&nbsp;<?=maakKnop('xls.png',array('size'=>16))?> <?=vt("XLS-export")?> </div><br>



	<div id="ouderdomsAnalyseDiv" style="display:none">
		<fieldset id="Selectie1">
			<legend accesskey="e"><?=vt("Ouderdomsanalye")?></legend>
			<div>
				<div class="formblock">
					<div class="formlinks"> <?=vt("Datum ingevoerd max X dagen terug")?> </div>
					<div class="formrechts" >	<input type="text" name="ouderdomDagen" value="" size="2"> </div>
				</div>
				<div class="formblock">
					<div class="formlinks"> <?=vt("Minimaal afwijkingspercentage")?> </div>
					<div class="formrechts" >	<input type="text" name="ouderdomPercentage" value="" size="2">  </div>
				</div>
			</div>
		</fieldset>
	</div>

	<div id="koersControleCheckDiv" style="display:none">
		<fieldset>
			<legend accesskey="e"><?=vt("Te controleren fondsen")?></legend>
			<div class="formblock">
				<div class="formlinks"> <?=vt("Filter op te controleren fondsen")?> </div>
				<div class="formrechts" > <input type="checkbox" value="1" name="koersControleCheck">	</div>
			</div>
	</fieldset>
	</div>

	<div id="gebruikPortefeuilleSelectiekDiv" style="display:none">
		<fieldset>
			<legend accesskey="e"><?=vt("Portefeuille selectie")?></legend>
			<div class="formblock">
				<div class="formlinks"><?=vt("Gebruik portefeuille selectie")?> </div>
				<div class="formrechts" > <input type="checkbox" value="1" name="gebruikPortefeuilleSelectie" checked>	</div>
			</div>
		</fieldset>
	</div>
  
  <div id="gebruikInactieveFondsenDiv" style="display:none">
    <fieldset>
      <legend accesskey="e">Fondsen selectie</legend>
      <div class="formblock">
        <div class="formlinks"> Incl Vervallen fondsen </div>
        <div class="formrechts" > <input type="checkbox" value="1" name="gebruikInactieveFondsen">	</div>
      </div>
    </fieldset>
  </div>

	<div id="aanvullenDiv" style="display:none">
		<? if(checkAccess("superapp") &! $__appvar['master']){ ?>
		<fieldset>
			<legend accesskey="e"><?=vt("Aanvullen")?></legend>

				<div class="formblock">
					<div class="formlinks"> <?=vt("Koersen aanvullen")?> </div>
					<div class="formrechts" > <input type="checkbox" value="1" name="aanvullen">	</div>
				</div>
		</fieldset>
		<? } ?>
	</div>



</td>
</tr>
</table>
</form>

<? echo progressFrame();?>
<iframe width="538" height="300" name="generateFrame" frameborder="0" scrolling="No" marginwidth="0" marginheight="0"></iframe>

<script type="text/javascript">
selectTab();
</script>
	<?
	if($__debug) {
		echo getdebuginfo();
	}
	echo template($__appvar["templateRefreshFooter"],$content);
}
