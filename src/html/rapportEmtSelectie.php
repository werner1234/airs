<?php
/*
    AE-ICT source module
    Author  						: $Author: rm $
 		Laatste aanpassing	: $Date: 2019/10/23 14:37:17 $
 		File Versie					: $Revision: 1.1 $

 		$Log: rapportEmtSelectie.php,v $
 		Revision 1.1  2019/10/23 14:37:17  rm
 		8110

*/

//$AEPDF2=true;
include_once("wwwvars.php");
define('FPDF_FONTPATH',$__appvar["basedir"]."/html/font/");
include_once("../classes/AE_cls_fpdf.php");
include_once("../classes/AE_cls_progressbar.php");
include_once("../classes/selectOptieClass.php");
include_once("rapport/emtRapport.php");

$emtRapport = new emtRapport();

$editcontent['javascript'] = $content['javascript'];
$type='portefeuille';
$maxVink=25;

$editcontent['calendarinclude'] = "<script language=JavaScript src=\"javascript/algemeen.js\" type=text/javascript></script> <script language=JavaScript src=\"javascript/selectbox.js\" type=text/javascript></script>";
$kal = new DHTML_Calendar();
$editcontent['calendar'] = $kal->get_load_files_code();


$query = "SELECT layout, max(orderPreValidatie) as orderPreValidatie  FROM Vermogensbeheerders
					  JOIN VermogensbeheerdersPerGebruiker ON VermogensbeheerdersPerGebruiker.Vermogensbeheerder =  Vermogensbeheerders.Vermogensbeheerder
					 WHERE VermogensbeheerdersPerGebruiker.Gebruiker = '".$USR."' GROUP BY VermogensbeheerdersPerGebruiker.Gebruiker limit 1";
$DB = new DB();
$DB->SQL($query);
$layout = $DB->lookupRecord();
$orderPreValidatie=$layout['orderPreValidatie'];
$layout = $layout['layout'];

echo template($__appvar["templateContentHeader"],$editcontent);
flush();

if($_GET['actief'] == "inactief" )
{
	$inactiefChecked = "checked";
	$actief = "inactief";
	$alleenActief = " ";
}
elseif($_GET['actief'] == "positie" )
{
	$positieChecked='checked';
	$actief = "positie";

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
	include_once("../classes/portefeuilleSelectieClass.php");
	include_once("rapport/rapportVertaal.php");
	include_once("rapport/rapportRekenClass.php");
	include_once("rapport/PDFOverzicht.php");
	include_once("rapport/emtRapport.php");
	include_once("rapport/orderChecksTotaal.php");

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
		    exit;
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

	$selectData['datumDbVan']=jul2db($selectData['datumVan'] );
	$selectData['datumDbTm']=jul2db($selectData['datumTm'] );

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
    case "emtTotaal" :
      $rapport = new emtRapport();
      $rapport->emtRapportSet( $selectData );
      $rapport->USR = $USR;
      $rapport->progressbar = &$prb;
      $rapport->__appvar = $__appvar;
      $rapport->writeRapport();
      $rapportnaam = $__appvar["bedrijf"]."_VAL";
      break;
    case "emtDetail" :
      $rapport = new emtRapport();
      $rapport->emtRapportSet( $selectData );
      $rapport->USR = $USR;
      $rapport->progressbar = &$prb;
      $rapport->__appvar = $__appvar;
      $rapport->writeRapport();
      $rapportnaam = $__appvar["bedrijf"]."_VAL";
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
			$filename = 'EMT_' . date('YmdHi') . '.csv';
			$filetype = "csv";
			$rapport->pdf->OutputCSV($__appvar['tempdir'].$filename,"F");
		break;
		case "xls" :
			if(class_exists('XMLWriter')) //$__appvar["bedrijf"]=='TEST')
			  $xlsuitvoer = "xlsx";

			if($xlsuitvoer == "xlsx")
				$filename =  'EMT_' . date('YmdHi') . '.xlsx';
			else
				$filename =  'EMT_' . date('YmdHi') . '.xls';
			$rapport->pdf->OutputXLS($__appvar['tempdir'].$filename,"F",$xlsuitvoer);
		break;
		case "database" :
			$filetype = "database";
			$rapport->OutputDatabase();
			?>
	   <script type="text/javascript">
	   	parent.document.location = 'reportBuilder2.php';
	   </script>
     <?
     exit;
		break;
		case "order" :
      $tmpOrdernr =  $rapport->OutputOrder();
      if(is_array($tmpOrdernr) && $tmpOrdernr['versie']=='V2')
			{
?>
	<script type="text/javascript">
    parent.AEConfirm('<?=$tmpOrdernr['message']?> <?=vt("Wilt u naar de orderregels gaan")?>?', '<?=vt("Orderregel verwerking")?>',
    function () 
    {
      parent.document.location = 'tijdelijkebulkordersv2List.php?rapportageInvoer=1&resetFilter=1';
    }, function () {  });
	</script>
<?
			}      
      elseif($tmpOrdernr)
			{
?>
	<script type="text/javascript">
		parent.document.location = 'orderGenereer.php?tmpOrdernr=<?=$tmpOrdernr?>';
	</script>
<?
			}
			else
			{
?>
	<script type="text/javascript">
		alert('<?=vt("Er zijn geen orderregels aangemaakt binnen deze selectie")?>.');
	</script>
<?
			}
			exit;
			// location is volgende script met ordernr ?
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
$html.='</form>';



	$_SESSION['NAV'] = "";
	$_SESSION['submenu'] = New Submenu();
  $_SESSION['submenu']->addItem($html,"");
	session_write_close();

  if($actief == "positie")
  {
	  $positieJoin = "JOIN ActieveFondsen ON Fondsen.Fonds = ActieveFondsen.Fonds";
	  $alleenActief=" AND ActieveFondsen.InPositie = '1' ";
	  $fondsPrefix="Fondsen.";
  }
	else
	{
		$fondsPrefix='';
	}
$koppelObject = array();
$koppelObject[0] = new Koppel("Fondsen","selectForm",$positieJoin);
$koppelObject[0]->addFields($fondsPrefix."Fonds","fonds",false,true);
$koppelObject[0]->addFields($fondsPrefix."ISINCode","",true,true);
$koppelObject[0]->addFields($fondsPrefix."Omschrijving","",true,true);
$koppelObject[0]->name = "fonds";
$koppelObject[0]->extraQuery = $alleenActief;


?>


	<script language=JavaScript src="javascript/popup.js" type=text/javascript></script>


	<script type="text/javascript">

	<?=$koppelObject[0]->getJavascript()?>


<?=$selectie->getSelectJava();?>



	function print()
	{
	 
		document.selectForm.target = "generateFrame";
		document.selectForm.filetype.value="PDF";
		document.selectForm.save.value="0";
		console.log('test');
		// selectSelected();
		// if (checkfield())
		document.selectForm.submit();
    console.log('test1');
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

	function database()
	{
		document.selectForm.target = "generateFrame";
		document.selectForm.filetype.value="database";
		document.selectForm.save.value="1";
		selectSelected();
		if (checkfield())
		document.selectForm.submit();
	}


	function checkfield()
	{

	  //check of velden gevuld

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

		if( document.selectForm.soort.selectedIndex == 0 ) {
      $('#emtvalidatieDiv').hide();
		} else if( document.selectForm.soort.selectedIndex == 1 ) {
      $('#emtvalidatieDiv').show();
  	}
		resetSelect();
	}


	function moveItem(from,to,moveAll){
	var tmp_text = new Array();
	var tmp_value = new Array();
 	for(var i=0; i < from.options.length; i++) {
 		if(from.options[i].selected || moveAll==true)
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
	  	ajax[index].onError = function(){ alert('Ophalen velden mislukt.') };
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
	    var div_a ='<select name="'+veld+'\" id="'+veld+'\" style="width:200px" '+formExtra+' >';
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
<h1><?=vt("EMT Rapport")?></h1>
<br>

<form action="<?=$PHP_SELF?>" method="POST" target="_blank" name="selectForm">
<input type="hidden" name="posted" value="true" />
<input type="hidden" name="save" value="" />
<input type="hidden" name="rapport_types" value="" />
<input type="hidden" name="filetype" value="PDF" />
<input type="hidden" name="portefeuilleIntern" value="" />
<input type="hidden" name="extra" value="" />

<table border="0">
<tr>
	<td width="540">

<fieldset id="Rapport" >
<legend accesskey="R"><?=vt("Rapport")?></legend>

<div class="formblock">
  <div class="formlinks"> <?=vt("Rapport")?> </div>
  <div class="formrechts">
    
    <select name="soort" style="width:200px" onChange="selectTab();">
      <option value="emtTotaal"><?=vt("EMT-checks totaal")?></option>
      <option value="emtDetail"><?=vt("EMT-checks detail")?></option>
    </select>
  </div>
</div>



<?
//$tmp['geenVan']=true;
echo $selectie->createDatumSelectie($tmp);
?>
</fieldset>

<div id="PortefueilleSelectie" style="">

	<fieldset id="SelectieOrder" >
		<legend accesskey="S"><?=vt("Fondsselectie")?></legend>
		<?php
		$opties=array('FondsenKeyActiefVKM'=>'Fonds');
		foreach ($opties as $optie=>$omschrijving)
		{
			$keyValue=false;
			$tmp=array();
			$data=$selectie->getData($optie);
			if(count($data) > 1)
			{
				if($_SESSION['selectieMethode'] =='vink' && count($data) < $maxVink)
					echo $selectie->createCheckBlok($optie,$data,$_SESSION['backofficeSelectie'],$omschrijving);
				else
					echo $selectie->createSelectBlok($optie,$data,$_SESSION['backofficeSelectie'],$omschrijving,$keyValue);
			}
		}
		?>
	</fieldset>

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
  $dataPort=array();
  while($gb = $DB->NextRecord())
    $dataPort[$gb['Portefeuille']]=$gb;
  echo "<br><br>";
  echo $selectie->createEnkelvoudigeSelctie($dataPort,$_SESSION['backofficeSelectie']);
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
  $opties=array('Risicoklasse'=>'Risicoklasse','AFMprofiel'=>'AFMprofiel','SoortOvereenkomst'=>'SoortOvereenkomst','Remisier'=>'Remisier','PortefeuilleClusters'=>'PortefeuilleClusters');
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
<div class="buttonDiv" id="databaseButton" style="width:130px" onclick="javascript:database();">&nbsp;&nbsp;<?=maakKnop('table.png',array('size'=>16))?> <?=vt("Reportbuilder")?> </div><br>

	<script>
	function checkOmkeren()
	{
	  var theForm = document.selectForm.elements, z = 0;
	  for(z=0; z<theForm.length;z++)
	  {
	    if(theForm[z].type == 'checkbox' && (theForm[z].name == 'aanw' || theForm[z].name == 'short' ||  theForm[z].name == 'liqu' || theForm[z].name == 'zorg' || theForm[z].name == 'groot' || theForm[z].name == 'vbep' || theForm[z].name == 'akkam' ))
	    {
				if(theForm[z].checked == true)
				{
					theForm[z].checked = false;
				}
				else
				{
					theForm[z].checked = true;
				}
	    }
	  }
	}
	</script>

	<?php
$allChecks=getActieveControles();

$checks="";
foreach($allChecks as $check=>$checkOmschrijving)
{
	$checks.= "<input type='checkbox' name='$check' value='1' > $checkOmschrijving <br>\n";
}
?>
  
  <fieldset><legend><?=vt("Filter")?></legend>
    <div class="formblock">
      <div class="formlinks"> <?=vt("Filter")?> </div>
      <div class="formrechts"> <select name="emtValidatieFilter">
          <option value="afwijkingen"><?=vt("Alleen afwijkingen")?></option>
          <option value="nietcontroleren"><?=vt("Alleen niet te controleren")?></option>
          <option value="nietakkoord"><?=vt("Alleen niet akkoord")?></option>
          <option value="all"><?=vt("Alles")?></option>
        </select></div>
    </div>
  </fieldset>

<div id="emtvalidatieDiv">
  <fieldset><legend><?=vt("Checks")?></legend>
    <div class="formblock">
      <div class="formlinks">  </div>
      <div class="formrechts">
        <div class="form-check">
          <?php
          foreach ( $emtRapport->toCheckGroups as $key => $name ) {
            echo '<input class="form-check-input" type="checkbox" name="toCheckGroup[]" id="' . $name.$key . '" value="'.$key.'">
            <label class="form-check-label" for="' . $name.$key . '">' . vt($name) . '</label><br />';
          }
          ?>
        </div>
      </div>
    </div>
  </fieldset>
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
