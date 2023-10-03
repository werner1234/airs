<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2018/08/18 12:40:14 $
 		File Versie					: $Revision: 1.8 $
*/

include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
include_once("../classes/selectOptieClass.php");
define('FPDF_FONTPATH',$__appvar["basedir"]."/html/font/");
include_once("../classes/AE_cls_fpdf.php");
include_once("rapport/PDFRapport.php");

if($_POST['posted'])
{
 	include_once("rapport/rapportRekenClass.php");
	if(!empty($_POST['datum_van']) && !empty($_POST['datum_tot']))
	{
		$dd = explode($__appvar["date_seperator"],$_POST['datum_van']);
		if(!checkdate(intval($dd[1]),intval($dd[0]),intval($dd[2])))
		{
			echo "<b>" . vt('Fout: ongeldige datum opgegeven!') . "</b>";
			exit;
		}

		$dd = explode($__appvar["date_seperator"],$_POST['datum_tot']);
		if(!checkdate(intval($dd[1]),intval($dd[0]),intval($dd[2])))
		{
			echo "<b>" . vt('Fout: ongeldige datum opgegeven!') . "</b>";
			exit;
		}
	}
	else
	{
		echo "<b>" . vt('Fout: geen datum opgegeven!') . "</b>";
		exit;
	}



	// selecteer rapportage volgorde
	$portefeuille = $_POST['Portefeuille'];

	if(empty($portefeuille))	{
		echo "<b>" . vt('Fout: geen portefeuille opgegeven') . " </b>";
		exit;
	}


	// controle of gebruiker bij vermogensbeheerder mag
	if(checkAccess($type))
		$join = "";
	else
	{
		$join = " INNER JOIN VermogensbeheerdersPerGebruiker ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND ".
						" VermogensbeheerdersPerGebruiker.Gebruiker = '".$USR."'
						JOIN Gebruikers ON Gebruikers.Gebruiker = VermogensbeheerdersPerGebruiker.Gebruiker ";
		$beperktToegankelijk = " AND  (Portefeuilles.beperktToegankelijk = '0' OR  Gebruikers.beperkingOpheffen = '1' ) ";

	}
	// check begin datum rapportage!
	$query = "SELECT Portefeuilles.Startdatum, ".
					"Portefeuilles.Einddatum,		".
					"Portefeuilles.RapportageValuta, ".
					"Vermogensbeheerders.layout, ".
					"Vermogensbeheerders.AfdrukvolgordeOIH,		".
					"Vermogensbeheerders.AfdrukvolgordeOIS, 	".
					"Vermogensbeheerders.AfdrukvolgordeOIR, 	".
					"Vermogensbeheerders.AfdrukvolgordeHSE, 	".
					"Vermogensbeheerders.AfdrukvolgordeOIB, 	".
					"Vermogensbeheerders.AfdrukvolgordeOIV, 	".
					"Vermogensbeheerders.AfdrukvolgordePERF, 	".
					"Vermogensbeheerders.AfdrukvolgordeVOLK, 	".
					"Vermogensbeheerders.AfdrukvolgordeVHO, 	".
					"Vermogensbeheerders.AfdrukvolgordeTRANS, ".
					"Vermogensbeheerders.AfdrukvolgordeMUT, 	".
					"Vermogensbeheerders.AfdrukvolgordeGRAFIEK,	".
					"Vermogensbeheerders.AfdrukvolgordeATT,	".
					"Vermogensbeheerders.attributieInPerformance,	".
					"Vermogensbeheerders.Vermogensbeheerder,	".
					"Vermogensbeheerders.Export_data_frontOffice	".
					" FROM (Portefeuilles, Vermogensbeheerders) ".$join." WHERE Portefeuilles.Portefeuille = '".$portefeuille."'".
					" AND Portefeuilles.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder $beperktToegankelijk";

	verwijderTijdelijkeTabel($portefeuille);
	// asort
	$DB = new DB();
	$DB->SQL($query);
	$DB->Query();
	$pdata = $DB->nextRecord();
	// todo : sorteer rapporttypes
	// todo : controlleer of datum in data bereik zit!!
	$frontOfficeData=unserialize($pdata['Export_data_frontOffice']);

	$rapportageDatum[a] = jul2sql(form2jul($_POST['datum_van']));
	$rapJul=form2jul($_POST['datum_tot']);
	$valutaDatum = getLaatsteValutadatum();
  $valutaJul = db2jul($valutaDatum);
	if($rapJul > $valutaJul + 86400)
	{
		echo "<b>" . vt('Fout: kan niet in de toekomst rapporteren.') . "</b>";
		exit;
	}
	$rapportageDatum['b'] = jul2sql($rapJul);

	if(db2jul($rapportageDatum[a]) < db2jul($pdata['Startdatum']))
	{
		$rapportageDatum[a] = $pdata['Startdatum'];
	}

	if(db2jul($rapportageDatum[b]) > db2jul($pdata[Einddatum]))
	{
		echo "<b>" . vt('Fout: Deze portefeille heeft een einddatum') . "  (".date("d-m-Y",db2jul($pdata[Einddatum])).")</b>";
		exit;
	}

	// controlleer of datum a niet groter is dan datum b!
	if(db2jul($rapportageDatum[a]) > db2jul($rapportageDatum[b]))
	{
		echo "<b>" . vt('Fout: Van datum kan niet groter zijn dan  T/m datum!') . " </b>";
		exit;
	}



	$julrapport = db2jul($rapportageDatum[a]);
	$rapportMaand = date("m",$julrapport);
	$rapportDag = date("d",$julrapport);
	$rapportJaar = date("Y",$julrapport);

//if($pdata['layout'] == 26)
//  $vulKwartalen=true;

	if($rapportMaand == 1 && $rapportDag == 1)
	{
		$startjaar = true;
		$extrastart = false;
	}
	else
	{
		$startjaar = false;
		// 1 dag eraf is de startdatum!
		$julrapport = db2jul($rapportageDatum[a]);
		$rapportageDatum[a] = jul2sql($julrapport);

		$extrastart = mktime(0,0,0,1,1,$rapportJaar);
		if($extrastart < 	db2jul($pdata['Startdatum']))
			$extrastart = $pdata['Startdatum'];
		else
			$extrastart = date("Y-m-d",$extrastart);
	}

/*
	$fondswaarden[a] =  berekenPortefeuilleWaarde($portefeuille, $rapportageDatum[a],$startjaar,$pdata['RapportageValuta'],$rapportageDatum[a]);
	$fondswaarden[b] =  berekenPortefeuilleWaarde($portefeuille, $rapportageDatum[b],0,$pdata['RapportageValuta'],$rapportageDatum[a]);

	vulTijdelijkeTabel($fondswaarden[a] ,$portefeuille,$rapportageDatum[a]);
	vulTijdelijkeTabel($fondswaarden[b] ,$portefeuille,$rapportageDatum[b]);
	if($extrastart)
	{
	 	//verwijderTijdelijkeTabel($portefeuille,$extrastart);
		$fondswaarden['c'] =  berekenPortefeuilleWaarde($portefeuille, $extrastart,true,$pdata['RapportageValuta'],$extrastart);
		vulTijdelijkeTabel($fondswaarden['c'] ,$portefeuille,$extrastart);
	}



	$RapStartJaar = date("Y", db2jul($rapportageDatum[a]));
	$RapStopJaar = date("Y", db2jul($rapportageDatum[b]));
	$RapJaar = $RapStopJaar;
	if ($RapJaar != $RapStartJaar)
	{
     $fondswaarden[c] =  berekenPortefeuilleWaarde($portefeuille, $RapStartJaar."-12-31",0,$pdata['RapportageValuta']);
	   vulTijdelijkeTabel($fondswaarden[c] ,$portefeuille,$RapStartJaar."-12-31");
	}
*/

	$rapportageDatumVanaf = $rapportageDatum[a];
	$rapportageDatum = $rapportageDatum[b];

	$pdf = new PDFRapport('L','mm');

	$pdf->SetAutoPageBreak(true,15);
	$pdf->pagebreak = 190;
	$pdf->__appvar = $__appvar;
	$pdf->extra = $_POST['extra'];

	if($pdata['RapportageValuta'] != "EUR" && $pdata['RapportageValuta'] != "")
	{
	  $pdf->rapportageValuta = $pdata['RapportageValuta'];
	  $pdf->ValutaKoersBegin = getValutaKoers($pdf->rapportageValuta,$rapportageDatumVanaf);
	  $pdf->ValutaKoersEind  = getValutaKoers($pdf->rapportageValuta,$rapportageDatum);
	  $pdf->ValutaKoersStart = getValutaKoers($pdf->rapportageValuta,$RapStartJaar."-01-01");//$rapportageDatumVanaf);
	}
	else
	{
	  $pdf->rapportageValuta = "EUR";
	  $pdf->ValutaKoersEind  = 1;
	  $pdf->ValutaKoersStart = 1;
	  $pdf->ValutaKoersBegin = 1;
	}

	$pdf->PortefeuilleStartdatum = $pdata['Startdatum'];
	$pdf->GrootboekPerVermogensbeheerder = $GrootboekPerVermogensbeheerder;

	$pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
	$pdf->rapport_datum = db2jul($rapportageDatum);

  $pdf->lastPOST = $_POST;

	loadLayoutSettings($pdf, $portefeuille);

 
  $rapport = new RapportJournaalpost($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
  $rapport->writeRapport();




	$pdf->rapport_typen=$rapport_type;
  $pdf->volgorde=$volgorde;

 


if($_POST['extra']=='xls')
{
  if($rapportnaam=='')
    $rapportnaam='export.xls';
  $pdf->OutputXLS($rapportnaam,'S');//,"F"
}
exit;

}



































$selectie=new selectOptie();

if(!is_array($_SESSION['lastGET']))
  $_SESSION['lastGET']=array();
$_SESSION['lastGET']=array_merge($_SESSION['lastGET'],$_GET);

$type='portefeuille';
$query = "SELECT layout, CrmClientNaam,Export_data_frontOffice  FROM Vermogensbeheerders
					  JOIN VermogensbeheerdersPerGebruiker ON VermogensbeheerdersPerGebruiker.Vermogensbeheerder =  Vermogensbeheerders.Vermogensbeheerder
					 WHERE VermogensbeheerdersPerGebruiker.Gebruiker = '".$USR."' LIMIT 1";
$DB = new DB();
$DB->SQL($query);
$DB->Query();
$rdata = $DB->nextRecord();

$editScript = "portefeuillesEdit.php";
$list = new MysqlList();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

$list->addField("Portefeuilles","id",array("width"=>100,"search"=>false));
$list->addField("Portefeuilles","Portefeuille",array("list_width"=>150,"search"=>true));
$list->addField("Portefeuilles","Client",array("list_width"=>200,"search"=>true));

if($rdata['CrmClientNaam'] == '1')
{
  $list->addField("","Naam",array('sql_alias'=>'CRM_naw.naam',"search"=>true));
  $list->addField("","crmId",array('sql_alias'=>'CRM_naw.id',"search"=>true,'list_invisible'=>true));
}
else
  $list->addField("Client","Naam",array("search"=>true));


$allow_add = false;
$internDepotToegang='';
if(!checkAccess($type))
{
   if($_SESSION['usersession']['gebruiker']['internePortefeuilles'] == '1')
     $internDepotToegang="OR Portefeuilles.interndepot=1";

   if($_SESSION['usersession']['gebruiker']['Accountmanager'] <> '' && $_SESSION['usersession']['gebruiker']['overigePortefeuilles'] == 0)
	 {
	   $beperktToegankelijk = " AND (Portefeuilles.Accountmanager='".$_SESSION['usersession']['gebruiker']['Accountmanager']."' OR Portefeuilles.tweedeAanspreekpunt ='".$_SESSION['usersession']['gebruiker']['Accountmanager']."' $internDepotToegang) ";
	 }
	 else
	 {
    	$list->setJoin(" INNER JOIN VermogensbeheerdersPerGebruiker ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker = '".$USR."'
	                  JOIN Gebruikers ON Gebruikers.Gebruiker = VermogensbeheerdersPerGebruiker.Gebruiker");
	    $beperktToegankelijk = " AND  (Portefeuilles.beperktToegankelijk = '0' OR  Gebruikers.beperkingOpheffen = '1' ) ";
	 }
}



if($_SESSION['lastGET']['actief'] <> "inactief" )
	$alleenActief = " AND Portefeuilles.Einddatum  >=  NOW() ";

if($_SESSION['lastGET']['letter'] && !$_GET['selectie'])
	$extraWhere = " AND Portefeuilles.Client LIKE '".mysql_escape_string($_SESSION['lastGET']['letter'])."%' ";


if(!isset($_SESSION['portefeuilleIntern']) && !isset($_GET['portefeuilleIntern']))
 $_SESSION['portefeuilleIntern']=0;

if(!isset($_SESSION['portefeuilleIntern']) || $_SESSION['portefeuilleIntern']=='0')
	$extraWhere .= " AND Portefeuilles.interndepot=0 ";
elseif($_SESSION['portefeuilleIntern'] == "1")
  $extraWhere .= " AND Portefeuilles.interndepot=1 ";


if($rdata['CrmClientNaam'] == '1')
{
  $list->setWhere(" 1 ".$extraWhere.$alleenActief.$beperktToegankelijk);
  $list->setJoin("LEFT JOIN CRM_naw ON Portefeuilles.Portefeuille = CRM_naw.Portefeuille INNER JOIN VermogensbeheerdersPerGebruiker ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker = '".$USR."'
	                JOIN Gebruikers ON Gebruikers.Gebruiker = VermogensbeheerdersPerGebruiker.Gebruiker");
}
else
  $list->setWhere("Portefeuilles.Client = Clienten.Client ".$extraWhere.$alleenActief.$beperktToegankelijk);

$_GET['sort'][] = "Portefeuilles.Client";
$_GET['direction'][] = "ASC";

// set sort
$list->setOrder($_GET['sort'],$_GET['direction']);
// set searchstring
$list->setSearch($_GET['selectie']);
// select page
$list->selectPage($_GET['page']);

session_start();
$_SESSION['NAV'] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION['NAV']->addItem(new NavList($_GET['page'], $list->records(), $__appvar['rowsPerPage'],$allow_add));
$_SESSION['NAV']->addItem(new NavSearch($_GET['selectie']));


$content['jsincludes'] .= "<script language=JavaScript src=\"javascript/ae_ajax_client.js\" type=text/javascript></script>";
$content['calendarinclude'] = "<script language=JavaScript src=\"javascript/algemeen.js\" type=text/javascript></script>";
$kal = new DHTML_Calendar();
$content['calendar'] = $kal->get_load_files_code();

echo template($__appvar["templateContentHeader"],$content);
// selecteer laatst bekende valutadatum
$totdatum = getLaatsteValutadatum();

?>
<script type="text/javascript">

var counter=0;

function responseHandler(requester,formName)
{
	var theForm = document.forms[formName];
	return true;
}

function xls()
{
  document.selectForm.target = "_blank";
  document.selectForm.extra.value = "xls";
  document.selectForm.action = "rapportJournaalpost.php?counter="+counter;
	document.selectForm.save.value="1";
	document.selectForm.submit();
	counter++;
}

	function selectSelected()
	{
	  if(document.selectForm['selectedFields[]'])
	  {
  		var selectedFields 	= document.selectForm['selectedFields[]'];
  		for(j=0; j < selectedFields.options.length; j++)
		  {
 	  		selectedFields.options[j].selected = true;
		  }
	  }
	}
</script>


<br>

<form action="rappoerJoernaalpost.php" method="POST" target="_blank" name="selectForm">
<input type="hidden" name="posted" value="true" />
<input type="hidden" name="save" value="" />
<input type="hidden" name="rapport_types" value="" />
<input type="hidden" name="extra" value="" />

<table border="0">
<tr>
<td width="570">
<div class="form">

<fieldset id="Selectie" >
<legend accesskey="S"><?= vt('Selectie'); ?></legend>
<?php
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

$kal = new DHTML_Calendar();
$inp = array ('name' =>"datum_van",'value' =>(!empty($_SESSION['rapportDateFrom']))?$_SESSION['rapportDateFrom']:date("d-m-Y",mktime(0,0,0,1,1,$jr)),'size'  => "11");
$kal2 = new DHTML_Calendar();
$inp2 = array ('name' =>"datum_tot",'value' =>(!empty($_SESSION['rapportDateTm']))?$_SESSION['rapportDateTm']:date("d-m-Y",db2jul($totdatum)),'size'  => "11");

?>
<table>
<tr>
    <td width="100">
      <?= vt('Van datum'); ?>:
    </td>
    <td>
      <?=$kal->make_input_field("",$inp,"")?>
    </td>
    <td>
      &nbsp; <?= vt('Vorige'); ?> &nbsp; &nbsp; &nbsp;<a style="color: Navy;font-weight: bold;"  href="#" onclick="javascript:document.selectForm.datum_van.value='<?=$datumSelctie['beginMaand']?>';document.selectForm.datum_tot.value='<?=$datumSelctie['eindMaand']?>';"><?= vt('Maand'); ?></a>,
      <a style="color: Navy;font-weight: bold;" href="#" onclick="javascript:document.selectForm.datum_van.value='<?=$datumSelctie['beginKwartaal']?>';document.selectForm.datum_tot.value='<?=$datumSelctie['eindKwartaal']?>';"><?= vt('Kwartaal'); ?></a>,
      <a style="color: Navy;font-weight: bold;"  href="#" onclick="javascript:document.selectForm.datum_van.value='<?=$datumSelctie['beginJaar']?>';document.selectForm.datum_tot.value='<?=$datumSelctie['eindJaar']?>';"><?= vt('Jaar'); ?></a>
    </td>
</tr>
<tr>
    <td>
      <?= vt('T/m datum'); ?>:
    </td>
    <td>
      <?=$kal2->make_input_field("",$inp2,"")?>
    </td>
    <td>
      &nbsp; <?= vt('Huidige'); ?> &nbsp; <a style="color: Navy;font-weight: bold;" href="#" onclick="javascript:document.selectForm.datum_van.value='<?=$datumSelctie['beginMaand2']?>';document.selectForm.datum_tot.value='<?=$totFromDatum?>';"><?= vt('Maand'); ?></a>,
      <a style="color: Navy;font-weight: bold;" href="#" onclick="javascript:document.selectForm.datum_van.value='<?=$datumSelctie['beginKwartaal2']?>';document.selectForm.datum_tot.value='<?=$totFromDatum?>';"><?= vt('Kwartaal'); ?></a>,
      <a style="color: Navy;font-weight: bold;" href="#" onclick="javascript:document.selectForm.datum_van.value='<?=$datumSelctie['beginJaar2']?>';document.selectForm.datum_tot.value='<?=$totFromDatum?>';"><?= vt('Jaar'); ?></a>
    </td>
</tr>

</table>

</fieldset>

</div>
</td>
<td>
<div class="buttonDiv" onclick="javascript:xls();">&nbsp;&nbsp;<?=maakKnop('xls.png',array('size'=>16))?> <?= vt('XLS uitvoer'); ?></div><br>

</td>
</tr>
<tr>
<td colspan="2">
<a href="<?=$PHP_SELF?>?letter=0-9" class="letterButton" > 0-9 </a>
<?
for($a=65; $a <= 90; $a++)
{
	echo "<a href=\"".$PHP_SELF."?letter=".chr($a)."\" class=\"letterButton\">".chr($a)."</a>\n";
}
?>
	<a href="<?=$PHP_SELF?>?letter=" class="letterButton" style="width:26px">alles</a>
</td>
</tr>
</table>

<table>
<tr>
<td valign="top">


<table cellspacing="0">
<?=$list->printHeader();?>
<?php
$template = '<tr class="list_dataregel" onmouseover="this.className=\'list_dataregel_hover\'" onmouseout="this.className=\'list_dataregel\'" onClick="javascript:document.getElementById(\'{Portefeuille_value}\').checked=true;">
<td class="list_button">
<div class="icon"><input type="radio" value="{Portefeuille_value}" name="Portefeuille" id="{Portefeuille_value}"></div>
</td>
<td class="listTableData"  width="150" align="left" >{Portefeuille_value} &nbsp;</td>
<td class="listTableData"  width="150" align="left" >{Client_value} &nbsp;</td>
<td class="listTableData"  align="left" >{Naam_value} &nbsp;</td>
</tr>';

while($data = $list->getRow())
{
	echo $list->buildRow($data,$template,"");
}


?>
</table>
</td>
<td valign="top">
  <fieldset id="Model_Settings" >
    <div class="formblock">
  	<u><?= vt('Niveau'); ?></u><br>
  	<input type="radio" name="verdeling" value="portefeuille" checked><?= vt('Portefeuille'); ?><br>
	  <input type="radio" name="verdeling" value="rekening" ><?= vt('Rekening'); ?><br>
	</fieldset>
  </div>
</td>
</tr>
</table>

</form>
<?

logAccess();

if($__debug) {
	echo getdebuginfo();
}
echo template($__appvar["templateRefreshFooter"],$content);


class RapportJournaalpost
{
	function RapportJournaalpost($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "MUT";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Journaalpost";

		if ($this->pdf->rapportageValuta != 'EUR' && $this->pdf->rapportageValuta != '')
		  $this->pdf->rapport_titel .= " in ".$this->pdf->rapportageValuta;

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
	}
  
  
  
 	function writeRapport()
	{ 
	  global $__appvar;
	 // listarray($this->pdf->lastPOST);
    $DB=new DB();
    
    $query="SELECT Rekening FROM Rekeningen WHERE Portefeuille = '".$this->portefeuille."'";
    $DB->SQL($query);
    $DB->Query(); 
    $rekeningen=array();
    while($rekening = $DB->nextRecord())
    {
      $rekeningen[]=$rekening['Rekening'];
    }
    
    if($this->pdf->lastPOST['verdeling']=='portefeuille')
    {
      $groupby=' GROUP BY Rekeningmutaties.Grootboekrekening';
      $verdelingTxt="Portefeuille";
    }
    else
    {
      $groupby=' GROUP BY Rekeningmutaties.Rekening, Rekeningmutaties.Grootboekrekening'; 
      $verdelingTxt="Rekening"; 
    }

    
    $query="SELECT SUM(Debet * Valutakoers) as Debet, SUM(Credit * Valutakoers) as Credit, SUM(Rekeningmutaties.Bedrag) as Bedrag, Rekening
    FROM Rekeningmutaties 
    WHERE YEAR(Boekdatum)='".date('Y',db2jul($this->rapportageDatumVanaf))."' AND 
          Boekdatum <='".$this->rapportageDatumVanaf."' AND 
          Rekening IN('".implode("','",$rekeningen)."') GROUP BY Rekening";
    $DB->SQL($query);
    $DB->Query();
    while($data = $DB->nextRecord())
    {
      $begin[$data['Rekening']]+=$data['Credit']-$data['Debet'];
      $begin[$this->portefeuille]+=$data['Credit']-$data['Debet'];
    }
    
    $query="SELECT SUM(Debet * Valutakoers) as Debet, SUM(Credit * Valutakoers) as Credit, SUM(Rekeningmutaties.Bedrag) as Bedrag, Rekening
    FROM Rekeningmutaties 
    WHERE YEAR(Boekdatum)='".date('Y',db2jul($this->rapportageDatumVanaf))."' AND 
          Boekdatum <='".$this->rapportageDatum."' AND 
          Rekening IN('".implode("','",$rekeningen)."') GROUP BY Rekening";
    $DB->SQL($query);
    $DB->Query();
    while($data = $DB->nextRecord())
    {
      $eind[$data['Rekening']]+=($data['Credit']-$data['Debet']);
      $eind[$this->portefeuille]+=($data['Credit']-$data['Debet']);
    }
    
  		$query = "SELECT '".$this->portefeuille."' as Portefeuille, ".
			"SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers) as Debet, ".
			"SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers) as Credit, ".
			"Rekeningmutaties.Rekening, ".
			"Rekeningmutaties.Grootboekrekening, ".
			"Grootboekrekeningen.Omschrijving AS gbOmschrijving ".
			"FROM Rekeningmutaties, Grootboekrekeningen ".
			"WHERE  ".
			" Rekeningmutaties.Rekening IN('".implode("','",$rekeningen)."') ".
			"AND Rekeningmutaties.Verwerkt = '1' ".
			"AND Rekeningmutaties.Boekdatum > '".$this->rapportageDatumVanaf."' ".
			"AND Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."' ".
			"AND Grootboekrekeningen.Afdrukvolgorde IS NOT NULL ".
			"AND Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening ".
			"AND (Grootboekrekeningen.Kosten = '1' OR Grootboekrekeningen.Opbrengst = '1' OR Grootboekrekeningen.Onttrekking = '1' OR Grootboekrekeningen.Storting = '1' OR Grootboekrekeningen.Kruispost = '1') ".
			" $groupby ORDER BY Grootboekrekeningen.Afdrukvolgorde, Rekeningmutaties.Boekdatum";

		$DB->SQL($query);
		$DB->Query();
		while($mutaties = $DB->nextRecord())
		{
		  $jourmaalData[$mutaties[$verdelingTxt]][$mutaties['Grootboekrekening']]=$mutaties;
    }
    
    $query = "SELECT Fondsen.Omschrijving, '".$this->portefeuille."' as Portefeuille,".
		"Fondsen.Fondseenheid, 
    Rekeningmutaties.Rekening,".
		"Rekeningmutaties.Boekdatum, ".
		"Rekeningmutaties.Transactietype,
		 Rekeningmutaties.Valuta,
		 Rekeningmutaties.Afschriftnummer,
     Rekeningmutaties.omschrijving as rekeningOmschrijving,
		 Rekeningmutaties.Aantal AS Aantal, Rekeningmutaties.Fonds,  ".
		"Rekeningmutaties.Fondskoers, ".
		"Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers as Debet, ".
		"Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers as Credit, ".
		"Rekeningmutaties.Valutakoers ".
		"FROM Rekeningmutaties, Fondsen, Grootboekrekeningen ".
		"WHERE ".
		"Rekeningmutaties.Fonds = Fondsen.Fonds AND ".
    "Rekeningmutaties.Rekening IN('".implode("','",$rekeningen)."') AND ".
		"Rekeningmutaties.Verwerkt = '1' AND ".
		"Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening AND ".
		"Rekeningmutaties.Transactietype <> 'B' AND ".
		"Grootboekrekeningen.FondsAanVerkoop = '1' AND ".
		"Rekeningmutaties.Boekdatum > '".$this->rapportageDatumVanaf."' AND ".
		"Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."' ".
		"ORDER BY Rekeningmutaties.Boekdatum, Rekeningmutaties.Fonds, Rekeningmutaties.id ";
    $DB->SQL($query);
		$DB->Query();
		while($mutaties = $DB->nextRecord())
		{ 
		  if(substr($mutaties['Transactietype'],0,1)=='A')
      {
		    $jourmaalData[$mutaties[$verdelingTxt]]['FONDS aankoop']['Debet']+=$mutaties['Debet'];
        $jourmaalData[$mutaties[$verdelingTxt]]['FONDS aankoop']['Credit']+=$mutaties['Credit'];
      }
      if(substr($mutaties['Transactietype'],0,1)=='V')
      {

        $historie = berekenHistorischKostprijs($this->portefeuille, $mutaties['Fonds'], $mutaties['Boekdatum'],$this->pdf->rapportageValuta,substr($this->rapportageDatum,0,4).'-01-01');
        $beginwaarde=(abs($mutaties['Aantal'])*$historie['beginwaardeLopendeJaar']*$historie['beginwaardeValutaLopendeJaar']*$historie['fondsEenheid']);
      //  $result[$mutaties[$verdelingTxt]]+=($mutaties['Credit'] - $beginwaarde);
        $jourmaalData[$mutaties[$verdelingTxt]]['FONDS verkoop']['Credit']+= $beginwaarde;
      }
    }
    
    $this->pdf->excelData[]=array('','Rapport journaalpost');
    foreach($jourmaalData as $verdeling=>$data)
    {
      $totalen=array();
      $this->pdf->excelData[]=array($verdelingTxt.' '.$verdeling);
      $this->pdf->excelData[]=array('Grootboek','Af','Bij');
      foreach($data as $grootboek=>$mutatie)
      {
        $this->pdf->excelData[]=array($grootboek,round($mutatie['Debet'],2),round($mutatie['Credit'],2));
        $totalen['Debet']+=$mutatie['Debet'];
        $totalen['Credit']+=$mutatie['Credit'];
      }
      $mutatieWaarde=$eind[$verdeling]-$begin[$verdeling];
      $result=($mutatieWaarde-($totalen['Credit']-$totalen['Debet']));
      if($result < 0)
      {
        $result=abs($result);
        $totalen['Debet']+=$result;
        $this->pdf->excelData[]=array("RESULT",round($result,2));
      }
      else
      {
        $totalen['Credit']+=$result;
        $this->pdf->excelData[]=array("RESULT",'',round($result,2));      
      }
   
      $this->pdf->excelData[]=array();
      $this->pdf->excelData[]=array('TOTAAL',round($totalen['Debet'],2),round($totalen['Credit'],2));
      $this->pdf->excelData[]=array();
      $this->pdf->excelData[]=array('MUTATIE',round($mutatieWaarde,2));
      $this->pdf->excelData[]=array();
      $this->pdf->excelData[]=array('Saldo begin',round($begin[$verdeling],2));
      $this->pdf->excelData[]=array('Saldo eind',round($eind[$verdeling],2));
      $this->pdf->excelData[]=array();
      $this->pdf->excelData[]=array();
      //listarray($this->pdf->excelData);exit;
     // $this->pdf->excelData[]=array('Saldo verschil',$eind['actuelePortefeuilleWaardeEuro']-$begin['actuelePortefeuilleWaardeEuro']);
    }
    
  }
}
?>