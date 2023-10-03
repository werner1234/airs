<?php
/*
    AE-ICT source module
    Author  						: $Author: rm $
 		Laatste aanpassing	: $Date: 2020/06/24 14:40:53 $
 		File Versie					: $Revision: 1.145 $

 		$Log: rapportFrontofficeManagementSelectie.php,v $
 		Revision 1.145  2020/06/24 14:40:53  rm
 		8550


*/

//$AEPDF2=true;
include_once("wwwvars.php");
define('FPDF_FONTPATH',$__appvar["basedir"]."/html/font/");
include_once("../classes/AE_cls_fpdf.php");
include_once("../classes/AE_cls_progressbar.php");
include_once("../classes/selectOptieClass.php");

$AETemplate = new AE_template ();
$type='portefeuille';
$maxVink=25;



$editcontent['calendarinclude'] = "<script language=JavaScript src=\"javascript/algemeen.js\" type=text/javascript></script> <script language=JavaScript src=\"javascript/selectbox.js\" type=text/javascript></script>";
$kal = new DHTML_Calendar();
$editcontent['calendar'] = $kal->get_load_files_code();

//$content['body']='onload="javascript:$(\'#zorgplichtSelectie\').hide();selectTab();"';

echo template($__appvar["templateContentHeader"],$editcontent);
flush();



$totdatum = getLaatsteValutadatum();

$jr = substr($totdatum,0,4);
if($selectie['datumVan'])
  $datumVan=$selectie['datumVan'];
else
  $datumVan= date("d-m-Y",mktime(0,0,0,1,1,$jr));
$kal = new DHTML_Calendar();
$inp = array ('name' =>"datumVan",'value' =>$datumVan,'size'  => "11");

if($selectie['datumVan'])
  $datumTm=$selectie['datumTm'];
else
  $datumTm= date("d-m-Y",db2jul($totdatum));
$inp2 = array ('name' =>"datumTm",'value' =>$datumTm,'size'  => "11");


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

$jr = substr($totdatum,0,4);
//$inp = array ('name' =>"datumVan",'value' =>(!empty($_SESSION['rapportDateFrom']))?$_SESSION['rapportDateFrom']:date("d-m-Y",mktime(0,0,0,1,1,$jr)),'size'  => "11");
//$inp2 = array ('name' =>"datumTm",'value' =>(!empty($_SESSION['rapportDateTm']))?$_SESSION['rapportDateTm']:date("d-m-Y",db2jul($totdatum)),'size'  => "11");




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
	$alleenActief = " AND (Fondsen.EindDatum >= NOW() OR Fondsen.EindDatum = '0000-00-00') ";
}




$autoCompleteConfig = array(
  'autocomplete' => array(
    'table'        => 'Fondsen',
    'label'        => array(
      'Fondsen.Fonds',
      'Fondsen.ISINCode',
      'combine' => '({Valuta})'
    ),
    'searchable'   => array('Fondsen.Fonds', 'Fondsen.ISINCode', 'Fondsen.Omschrijving', 'Fondsen.FondsImportCode'),
    'field_value'  => array('Fondsen.Omschrijving'),
    'extra_fields' => array('*'),
    'value'        => 'Fondsen.Fonds',
    'actions'      => array(
      'select' => '',
      'change' => '',
    ),
    'source_data' => array(
      'name' => array(
        'fondsInactief',
        'kostprijsfondsInactief',
        'fondsverloopfondsInactief'
      )
    )
  
  ),
  'form_size'    => '20',
);




$autocomplete = new Autocomplete();


$autocomplete->resetVirtualField('fondsOmschrijving');
$autoCompleteConfig['autocomplete']['actions']['select'] = '
    event.preventDefault();
    $("#fonds").val(ui.item.value);
    $("#fondsOmschrijving").val(ui.item.field_value);
    
    $(".verkoopIsin").html(ui.item.data.ISINCode);
    $(".verkoopValuta").html(ui.item.data.Valuta);
  ';
$autoCompleteConfig['autocomplete']['actions']['change'] = '
    if ( ui.item === null ) {
      $("#fonds").val("");
      $("#fondsOmschrijving").val("");
      
      $(".verkoopIsin").html("");
      $(".verkoopValuta").html("");
    }
  ';
$autoCompleteConfig['autocomplete']['conditions'] = array(
  'AND' => array(
    '(Fondsen.EindDatum >= now() OR Fondsen.EindDatum = "0000-00-00" OR 1 = "{$get:fondsInactief}")', // or 1=0 of 1=1 voor het tonen van inactieve fondsen
  )
);


$fondsField = $autocomplete->addVirtuelField('fondsOmschrijving', $autoCompleteConfig);
$content['script_voet'] .= $autocomplete->getAutoCompleteVirtuelFieldScript('fondsOmschrijving');


$autocomplete->resetVirtualField('aankoopFondsOmschrijving');
$autoCompleteConfig['autocomplete']['actions']['select'] = '
    event.preventDefault();
    $("#aankoopFonds").val(ui.item.value);
    $("#aankoopFondsOmschrijving").val(ui.item.field_value);
    
    $(".aankoopFondsIsin").html(ui.item.data.ISINCode);
    $(".aankoopFondsValuta").html(ui.item.data.Valuta);
  ';
$autoCompleteConfig['autocomplete']['actions']['change'] = '
    if ( ui.item === null ) {
      $("#aankoopFonds").val("");
      $("#aankoopFondsOmschrijving").val("");
      
      $(".aankoopFondsIsin").html("");
      $(".aankoopFondsValuta").html("");
    }
  ';
$autoCompleteConfig['autocomplete']['conditions'] = array(
  'AND' => array(
    '(Fondsen.EindDatum >= now() OR Fondsen.EindDatum = "0000-00-00" OR 1 = "{$get:aankoopfondsverloopfondsInactief}")', // or 1=0 of 1=1 voor het tonen van inactieve fondsen
  )
);
$aankoopFondsField = $autocomplete->addVirtuelField('aankoopFondsOmschrijving', $autoCompleteConfig);
$content['script_voet'] .= $autocomplete->getAutoCompleteVirtuelFieldScript('aankoopFondsOmschrijving');



if($_POST['posted'])
{
  include_once("../classes/portefeuilleSelectieClass.php");
	include_once("rapport/rapportVertaal.php");
	include_once("rapport/rapportRekenClass.php");
	include_once("rapport/PDFOverzicht.php");
	include_once("rapport/PDFRapport.php");
	include_once("rapport/CashPositie.php");
	include_once("rapport/Managementoverzicht.php");
  include_once("rapport/Vermogensverloop.php");
  include_once("rapport/RendementPerCategorie.php");
//	include_once("rapport/ManagementoverzichtHAR.php");
  include_once("rapport/Portefeuilleverdeling.php");
	include_once("rapport/Valutarisicooverzicht.php");
  include_once("rapport/Omloopsnelheidsoverzicht.php");
	include_once("rapport/Risicometing.php");
	include_once("rapport/Risicoanalyse.php");
	include_once("rapport/Zorgplichtcontrole.php");
	include_once("rapport/ZorgplichtcontroleDetail.php");
	include_once("rapport/Mandaatcontrole.php");
	include_once("rapport/Restrictiecontrole.php");
	include_once("rapport/PortefeuilleIndex.php");
	include_once("rapport/PortefeuilleParameters.php");
	include_once("rapport/CashLijst.php");
	include_once("rapport/Remisiervergoeding.php");
	include_once("rapport/ClientAnalyse.php");
	include_once("rapport/RapportEigendomsverhouding.php");
	include_once("rapport/RapportAfmExport.php");
  include_once("rapport/Transactieoverzicht.php");
  include_once("rapport/Modelcontrole.php");
  include_once("rapport/RendementDetails.php");
 	include_once("rapport/MutatievoorstelFondsen.php");
  include_once("rapport/VkmOpbouw.php");
  include_once("rapport/Waardeverloop.php");
  
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
	$selectData['selectedPortefeuilles'] = $_POST['selectedFields'];

	if ($selectData['VermogensbeheerderVan'] == $selectData['VermogensbeheerderTm'])
	{
		$db = new DB();
		$query = "SELECT Layout FROM Vermogensbeheerders WHERE Vermogensbeheerder='" . $selectData['VermogensbeheerderVan'] . "'";
		$db->SQL($query);
		$layoutData = $db->lookupRecord();
	}
	// maak progressbar
	$prb = new ProgressBar(536,8);	// create new ProgressBar
	$prb->color = 'maroon';	// bar color
	$prb->bgr_color = '#ffffff';	// bar background color
	$prb->brd_color = 'Silver';
	$prb->left = 0;	                  // Frame position from left
	$prb->top = 	0;
	$prb->show();	                  // show the ProgressBar

 	function loadLayoutUser($user)
	{
	$query="SELECT Vermogensbeheerders.Layout FROM Vermogensbeheerders
	JOIN  VermogensbeheerdersPerGebruiker ON (VermogensbeheerdersPerGebruiker.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder)
	WHERE  VermogensbeheerdersPerGebruiker.Gebruiker = '$user' ";
	$DB = new DB();
	$DB->SQL($query);
	$DB->Query();
	$data = $DB->nextRecord();
	return $data['Layout'];
	}
  
  $userLayout = loadLayoutUser($USR);
  $xlsuitvoer = "xls";
	switch($selectData['soort'])
	{
	  case "RendementPerCategorie" :
			$rapport = new RendementPerCategorie( $selectData );
			$rapport->USR = $USR;
			$rapport->progressbar = & $prb;
			$rapport->__appvar = $__appvar;
			$rapport->writeRapport();
			$rapportnaam = $__appvar["bedrijf"]."_REND";   
    break;
		case "CashPosities" :
			$rapport = new CashPositie( $selectData );
			$rapport->USR = $USR;
			$rapport->progressbar = & $prb;
			$rapport->__appvar = $__appvar;
			$rapport->writeRapport();
			$rapportnaam = $__appvar["bedrijf"]."_CASH";
		break;
    case "Vermogensverloop" :
			$rapport = new Vermogensverloop( $selectData );
			$rapport->USR = $USR;
			$rapport->progressbar = & $prb;
			$rapport->__appvar = $__appvar;
			$rapport->writeRapport();
			$rapportnaam = $__appvar["bedrijf"]."_VERM";
		break;
			case "Managementoverzicht" :
			$rapport = new Managementoverzicht( $selectData );
			$rapport->USR = $USR;
			$rapport->progressbar = & $prb;
			$rapport->__appvar = $__appvar;
			$rapport->writeRapport();
			$rapportnaam = $__appvar["bedrijf"]."_MAN";    
   	break;
    	case "Omloopsnelheid" :
			$rapport = new Omloopsnelheidsoverzicht( $selectData );
			$rapport->USR = $USR;
			$rapport->progressbar = & $prb;
			$rapport->__appvar = $__appvar;
			$rapport->writeRapport();
			$rapportnaam = $__appvar["bedrijf"]."_OML";    
   	break;  
		case "Portefeuilleverdeling" :
      $selectData['title'] = "portefeuille-verdeling";
      $selectData['userLayout'] = $userLayout ;
			$rapport = new Portefeuilleverdeling( $selectData );
			$rapport->USR = $USR;
			$rapport->progressbar = & $prb;
			$rapport->__appvar = $__appvar;
			$rapport->writeRapport();

			$rapportnaam = $__appvar["bedrijf"]."_VERD";
		break;
		case "Valuta Risico" :
			$rapport = new Valutarisicooverzicht( $selectData );
			$rapport->USR = $USR;
			$rapport->progressbar = & $prb;
			$rapport->__appvar = $__appvar;
			$rapport->writeRapport();
			$rapportnaam = $__appvar["bedrijf"]."_VR";
		break;
		case "Risicometing" :
			$rapport = new Risicometing( $selectData );
			$rapport->USR = $USR;
			$rapport->progressbar = & $prb;
			$rapport->__appvar = $__appvar;
			$rapport->writeRapport();
			$rapportnaam = $__appvar["bedrijf"]."_RIM";
		break;
		case "Risicoanalyse" :
			$rapport = new Risicoanalyse( $selectData );
			$rapport->USR = $USR;
			$rapport->progressbar = & $prb;
			$rapport->__appvar = $__appvar;
			$rapport->writeRapport();
			$rapportnaam = $__appvar["bedrijf"]."_RIA";
		break;
		case "Zorgplichtcontrole" :
			$rapport = new Zorgplichtcontrole( $selectData );
			$rapport->USR = $USR;
			$rapport->progressbar = & $prb;
			$rapport->__appvar = $__appvar;
			$rapport->writeRapport();
			$rapportnaam = $__appvar["bedrijf"]."_ZORG";
		break;
		case "ZorgplichtcontroleDetail" :
			$rapport = new ZorgplichtcontroleDetail( $selectData );
			$rapport->USR = $USR;
			$rapport->progressbar = & $prb;
			$rapport->__appvar = $__appvar;
			$rapport->writeRapport();
			$rapportnaam = $__appvar["bedrijf"]."_ZORGD";
		break;
		case "Mandaatcontrole" :
      if ($layoutData['Layout'] > 0 &&  file_exists("rapport/include/layout_".$layoutData['Layout']."/Mandaatcontrole_L" . $layoutData['Layout'] . ".php") )
      {
        include("rapport/include/layout_".$layoutData['Layout']."/Mandaatcontrole_L" . $layoutData['Layout'] . ".php");
        $rapClass="Mandaatcontrole_L".$layoutData['Layout'];
        $rapport = new $rapClass( $selectData );
      }
			elseif ($layoutData['Layout'] > 0 && file_exists("rapport/include/Mandaatcontrole_L" . $layoutData['Layout'] . ".php"))
			{
				include("rapport/include/Mandaatcontrole_L" . $layoutData['Layout'] . ".php");
				$rapClass="Mandaatcontrole_L".$layoutData['Layout'];
				$rapport = new $rapClass( $selectData );
			}
			else
			{
				$rapport = new Mandaatcontrole($selectData);
			}
			$rapport->USR = $USR;
			$rapport->progressbar = & $prb;
			$rapport->__appvar = $__appvar;
			$rapport->writeRapport();
			$rapportnaam = $__appvar["bedrijf"]."_MAND";
			break;
		case "Restrictiecontrole" :
			$rapport = new Restrictiecontrole( $selectData );
			$rapport->USR = $USR;
			$rapport->progressbar = & $prb;
			$rapport->__appvar = $__appvar;
			$rapport->writeRapport();
			$rapportnaam = $__appvar["bedrijf"]."_REST";
			break;
		case "PortefeuilleIndex" :
			$rapport = new PortefeuilleIndex( $selectData );
			$rapport->USR = $USR;
			$rapport->progressbar = & $prb;
			$rapport->__appvar = $__appvar;
			$rapport->writeRapport();
			$rapportnaam = $__appvar["bedrijf"]."_PortIndex";
		break;
		case "PortefeuilleParameters" :
			$rapport = new PortefeuilleParameters( $selectData );
			$rapport->USR = $USR;
			$rapport->progressbar = & $prb;
			$rapport->__appvar = $__appvar;
			$rapport->writeRapport();
			$rapportnaam = $__appvar["bedrijf"]."_PortPar";
		break;
		case "CashLijst" :
			$rapport = new CashLijst( $selectData );
			$rapport->USR = $USR;
			$rapport->progressbar = & $prb;
			$rapport->__appvar = $__appvar;
			$rapport->writeRapport();
			$rapportnaam = date('Y-m-d').'_'.$__appvar["bedrijf"]."_CASH";
		break;
		case "Remisiervergoeding" :
			$rapport = new Remisiervergoeding( $selectData );
			$rapport->USR = $USR;
			$rapport->progressbar = & $prb;
			$rapport->__appvar = $__appvar;
			$rapport->writeRapport();
			$rapportnaam = date('Y-m-d').'_'.$__appvar["bedrijf"]."_REMV";
		break;
		case "ClientAnalyse" :
			$rapport = new ClientAnalyse( $selectData );
			$rapport->USR = $USR;
			$rapport->progressbar = & $prb;
			$rapport->__appvar = $__appvar;
			$rapport->writeRapport();
			$rapportnaam = date('Y-m-d').'_'.$__appvar["bedrijf"]."_CLIENT";
		break;
		case "RapportEigendomsverhouding" :
			$rapport = new RapportEigendomsverhouding( $selectData );
			$rapport->USR = $USR;
			$rapport->progressbar = & $prb;
			$rapport->__appvar = $__appvar;
			$rapport->writeRapport();
			$rapportnaam = date('Y-m-d').'_'.$__appvar["bedrijf"]."_Owner";
		break;
 		case "afmExport" :
			$rapport = new RapportAfmExport( $selectData );
			$rapport->USR = $USR;
			$rapport->progressbar = & $prb;
			$rapport->__appvar = $__appvar;
			$rapport->writeRapport();
			$rapportnaam = date('Y-m-d').'_'.$__appvar["bedrijf"]."_AFM";
		break;
    case "Transactieoverzicht" :
 			$rapport = new Transactieoverzicht( $selectData );
			$rapport->USR = $USR;
			$rapport->progressbar = & $prb;
			$rapport->__appvar = $__appvar;
			$rapport->writeRapport();
			$rapportnaam = $__appvar["bedrijf"]."_TRA";   
    break;
    case "RendementDetails" :
 			$rapport = new RendementDetails( $selectData );
			$rapport->USR = $USR;
			$rapport->progressbar = & $prb;
			$rapport->__appvar = $__appvar;
			$rapport->writeRapport();
			$rapportnaam = $__appvar["bedrijf"]."_REND";   
    break;
//		case "Modelcontrole" :
//		  if($selectData['modelcontrole_rapport']=='gecomprimeerd')
//		    $rapport = new ModelWaardecontrole( $selectData );
//		  else
//			  $rapport = new Modelcontrole( $selectData );
//			$rapport->USR = $USR;
//			$rapport->progressbar = & $prb;
//			$rapport->__appvar = $__appvar;
//			$rapport->writeRapport();
//			$rapportnaam = $__appvar["bedrijf"]."_MOD";
//		break;
//		case "Mutatievoorstel Fondsen" :
//			$rapport = new MutatievoorstelFondsen( $selectData );
//			$rapport->USR = $USR;
//			$rapport->progressbar = &$prb;
//			$rapport->__appvar = $__appvar;
//			$rapport->writeRapport();
//			$rapportnaam = $__appvar["bedrijf"]."_MUT";
//		break;
    case "VkmOpbouw" :
      $rapport = new VkmOpbouw( $selectData );
      $rapport->USR = $USR;
      $rapport->progressbar = &$prb;
      $rapport->__appvar = $__appvar;
      $rapport->writeRapport();
      $rapportnaam = $__appvar["bedrijf"]."_VKM";
      break;
    case "Waardeverloop" :
      $rapport = new Waardeverloop( $selectData );
      $rapport->USR = $USR;
      $rapport->progressbar = &$prb;
      $rapport->__appvar = $__appvar;
      $rapport->writeRapport();
      $rapportnaam = $__appvar["bedrijf"]."_WVL";
      break;
  }
  
  $rapportnaam=$rapportnaam.'_'.date('Ymdhis');
	switch($_POST['filetype'])
	{
		case "PDF" :
			$filename = $rapportnaam.".pdf";
			$filetype = "pdf";
			$rapport->pdf->Output($__appvar['tempdir'].$filename,"F");
		break;
		case "cvs" :
			$filename =  $rapportnaam.".csv";
			$filetype = "csv";
			$rapport->pdf->OutputCSV($__appvar['tempdir'].$filename,"F");
		break;
		case "xls" :
			if(class_exists('XMLWriter')) //if($__appvar["bedrijf"]=='TEST')
			$xlsuitvoer = "xlsx";
      if($xlsuitvoer == "xlsx")
		  	$filename =  $rapportnaam.".xlsx";
      else 
        $filename =  $rapportnaam.".xls";
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
}
else
{

$DB = new DB();
$query = "SELECT ModelPortefeuilles.Portefeuille,
				 ModelPortefeuilles.Omschrijving
		  FROM ModelPortefeuilles
		  LEFT JOIN Portefeuilles on Portefeuilles.Portefeuille = ModelPortefeuilles.Portefeuille WHERE Portefeuilles.Einddatum>now() ORDER BY ModelPortefeuilles.Omschrijving";

$DB->SQL($query);
$DB->Query();
$aantal = $DB->records();
$t=0;
$Modelportefeuilles='';
while($gb = $DB->NextRecord())
{
	$t++;
	$Modelportefeuilles .= "<option value=\"".$gb['Portefeuille']."\" >".$gb['Omschrijving']."</option>\n";
}

// selecteer laatst bekende valutadatum
$totdatum = getLaatsteValutadatum();

$jr = substr($totdatum,0,4);

$DB= new DB();

$invoerData = array();
$invoerData['alles']=array('alles'=>'Alles');
$invoerData['H-cat']=array('alles'=>'Alles');
$invoerData['cat']=array('alles'=>'Alles');
$invoerData['H-sec']=array('alles'=>'Alles');
$invoerData['sec']=array('alles'=>'Alles');
$invoerData['regio']=array('alles'=>'Alles');
$invoerData['valuta']=array('alles'=>'Alles');


$DB->SQL("SELECT KeuzePerVermogensbeheerder.categorie,
KeuzePerVermogensbeheerder.waarde,
if(KeuzePerVermogensbeheerder.categorie='Beleggingscategorien',Beleggingscategorien.Omschrijving,
if(KeuzePerVermogensbeheerder.categorie='Beleggingssectoren',Beleggingssectoren.Omschrijving,
if(KeuzePerVermogensbeheerder.categorie='Regios',Regios.Omschrijving,
if(KeuzePerVermogensbeheerder.categorie='DuurzaamCategorien',DuurzaamCategorien.Omschrijving,''
)))) as Omschrijving
FROM
KeuzePerVermogensbeheerder
Inner Join VermogensbeheerdersPerGebruiker ON KeuzePerVermogensbeheerder.vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder
LEFT Join Regios ON KeuzePerVermogensbeheerder.waarde = Regios.Regio
LEFT Join Beleggingscategorien ON KeuzePerVermogensbeheerder.waarde = Beleggingscategorien.Beleggingscategorie
LEFT Join Beleggingssectoren ON KeuzePerVermogensbeheerder.waarde = Beleggingssectoren.Beleggingssector
LEFT JOIN DuurzaamCategorien ON KeuzePerVermogensbeheerder.waarde = DuurzaamCategorien.DuurzaamCategorie
WHERE
KeuzePerVermogensbeheerder.categorie IN('Beleggingssectoren','Beleggingscategorien','Regios','DuurzaamCategorien') AND
VermogensbeheerdersPerGebruiker.Gebruiker='$USR' GROUP BY waarde ORDER BY Omschrijving");
$DB->Query();
while($cat = $DB->NextRecord())
{
  if($cat['Omschrijving']=='')
    $cat['Omschrijving']=$cat['waarde'];
  if($cat['categorie'] == 'Regios')
    $invoerData['regio'][$cat['waarde']]=addslashes($cat['Omschrijving']);
  elseif($cat['categorie'] == 'Beleggingscategorien')
    $invoerData['cat'][$cat['waarde']]=addslashes($cat['Omschrijving']);
  elseif($cat['categorie'] == 'Beleggingssectoren')
    $invoerData['sec'][$cat['waarde']]=addslashes($cat['Omschrijving']);
	elseif($cat['categorie'] == 'DuurzaamCategorien')
		$invoerData['duu'][$cat['waarde']]=addslashes($cat['Omschrijving']);
}

$DB->SQL("SELECT CategorienPerHoofdcategorie.Hoofdcategorie,Beleggingscategorien.Omschrijving
FROM
CategorienPerHoofdcategorie
Inner Join Beleggingscategorien ON CategorienPerHoofdcategorie.Hoofdcategorie = Beleggingscategorien.Beleggingscategorie
Inner Join VermogensbeheerdersPerGebruiker ON CategorienPerHoofdcategorie.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder
WHERE VermogensbeheerdersPerGebruiker.Gebruiker='$USR'
GROUP BY CategorienPerHoofdcategorie.Hoofdcategorie");
$DB->Query();
while($cat = $DB->NextRecord())
  $invoerData['H-cat'][$cat['Hoofdcategorie']]=addslashes($cat['Omschrijving']);

$DB->SQL("SELECT SectorenPerHoofdsector.Hoofdsector,
Beleggingssectoren.Omschrijving,
VermogensbeheerdersPerGebruiker.Gebruiker
FROM SectorenPerHoofdsector
JOIN Beleggingssectoren ON SectorenPerHoofdsector.Hoofdsector = Beleggingssectoren.Beleggingssector
JOIN VermogensbeheerdersPerGebruiker  ON SectorenPerHoofdsector.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder
WHERE VermogensbeheerdersPerGebruiker.Gebruiker='$USR'
GROUP BY SectorenPerHoofdsector.Hoofdsector");
$DB->Query();
while($cat = $DB->NextRecord())
  $invoerData['H-sec'][$cat['Hoofdsector']]=addslashes($cat['Omschrijving']);


if(count($invoerData['cat']) == 1)
{
  $DB->SQL("SELECT Beleggingscategorien.Beleggingscategorie, Beleggingscategorien.Omschrijving
  FROM Beleggingscategorien, BeleggingscategoriePerFonds
  WHERE Beleggingscategorien.Beleggingscategorie =  BeleggingscategoriePerFonds.Beleggingscategorie
  GROUP BY Beleggingscategorien.Beleggingscategorie");
  $DB->Query();
  while($cat = $DB->NextRecord())
      $invoerData['cat'][$cat['Beleggingscategorie']]=addslashes($cat['Omschrijving']);
}

if(count($invoerData['sec']) == 1)
{
  $DB->SQL("SELECT Beleggingssectoren.Beleggingssector, Beleggingssectoren.Omschrijving
  FROM Beleggingssectoren, BeleggingssectorPerFonds
  WHERE Beleggingssectoren.Beleggingssector =  BeleggingssectorPerFonds.Beleggingssector
  GROUP BY Beleggingssectoren.Beleggingssector");
  $DB->Query();
  while($cat = $DB->NextRecord())
    $invoerData['sec'][$cat['Beleggingssector']]=addslashes($cat['Omschrijving']);
}
if(count($invoerData['sec']) == 1)
{
		$DB->SQL("SELECT DuurzaamCategorien.DuurzaamCategorie, DuurzaamCategorien.Omschrijving
  FROM DuurzaamCategorien JOIN BeleggingssectorPerFonds ON DuurzaamCategorien.DuurzaamCategorie = BeleggingssectorPerFonds.DuurzaamCategorie
  GROUP BY DuurzaamCategorien.DuurzaamCategorie");
		$DB->Query();
		while($cat = $DB->NextRecord())
			$invoerData['duu'][$cat['DuurzaamCategorie']]=addslashes($cat['Omschrijving']);
}

if(count($invoerData['regio']) == 1)
{
  $DB->SQL("SELECT Regios.Regio, Regios.Omschrijving
  FROM Regios, BeleggingssectorPerFonds
  WHERE BeleggingssectorPerFonds.Regio =  Regios.Regio
  GROUP BY Regios.Regio");
  $DB->Query();
  while($cat = $DB->NextRecord())
    $invoerData['regio'][$cat['Regio']]=addslashes($cat['Omschrijving']);
}

if(count($invoerData['valuta']) == 1)
{
  $DB->SQL("SELECT
Fondsen.Valuta,
Valutas.Omschrijving
FROM
Fondsen
INNER JOIN Valutas ON Fondsen.Valuta = Valutas.Valuta
WHERE Fondsen.Valuta <> ''
GROUP BY Fondsen.Valuta
ORDER BY Omschrijving");
  $DB->Query();
  while($cat = $DB->NextRecord())
    $invoerData['valuta'][$cat['Valuta']]=addslashes($cat['Omschrijving']);
}

$DB->SQL("SELECT afmCategorien.afmCategorie as type ,
						                afmCategorien.Omschrijving
		 		                    FROM 	afmCategorien 
                            JOIN BeleggingscategoriePerFonds ON afmCategorien.afmCategorie=BeleggingscategoriePerFonds.afmCategorie
						                GROUP BY afmCategorien.afmCategorie");
$DB->Query();
while($cat = $DB->NextRecord())
  $invoerData['afm'][$cat['type']]=addslashes($cat['Omschrijving']);

$layouts=array();
$vermogensbeheerders=array();
	$query="SELECT layout,Vermogensbeheerders.vermogensbeheerder,Vermogensbeheerders.SdFrequentie,Vermogensbeheerders.SdMethodiek,Vermogensbeheerders.SdWaarnemingen FROM Vermogensbeheerders
JOIN VermogensbeheerdersPerGebruiker ON Vermogensbeheerders.Vermogensbeheerder=VermogensbeheerdersPerGebruiker.Vermogensbeheerder
WHERE VermogensbeheerdersPerGebruiker.Gebruiker='$USR' ";
$DB->SQL($query);
$DB->Query();
while($layout = $DB->NextRecord())
{
	$layouts[] = $layout['layout'];
	$vermogensbeheerders[] = $layout['vermogensbeheerder'];
  if($layout['SdFrequentie']<>''&&$layout['SdMethodiek']<>''&&$layout['SdWaarnemingen']<>0)
    $stdevTonen=true;
  else
    $stdevTonen=false;
  
}
  
$query="SELECT Zorgplicht,Omschrijving FROM Zorgplichtcategorien WHERE Vermogensbeheerder IN('".implode("','",$vermogensbeheerders)."')";
$DB->SQL($query);
$DB->Query();

	$zorgplichtSelect = "<option value=\"\" >".vt("Allemaal")."</option>\n";
while($cat = $DB->NextRecord())
{
	$zorgplichtSelect .= "<option value=\"".$cat['Zorgplicht']."\" >".$cat['Omschrijving']."</option>\n";
}

session_start();

$einddatumFilterVerwijderen = false;
$openVermogensverloop = false;
if ( isset ($_GET['managementVermogensverloopShowInactive']) && (int) $_GET['managementVermogensverloopShowInactive'] === 1 ) {
  $einddatumFilterVerwijderen = true;
  $openVermogensverloop = true;
}

$selectie=new selectOptie($PHP_SELF, $einddatumFilterVerwijderen);
//$html='<form name="selectForm">';
$selectie->getInternExternActive();

$selectieHtml = '';

$selectieHtml .= $selectie->getHtmlInterneExternePortefeuille();
$selectieHtml .= $selectie->getHtmlConsolidatie();

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

	$koppelObject[1] = new Koppel("Fondsen","selectForm",$positieJoin);
	$koppelObject[1]->addFields($fondsPrefix."Fonds","kostprijsFonds",false,true);
	$koppelObject[1]->addFields($fondsPrefix."ISINCode","",true,true);
	$koppelObject[1]->addFields($fondsPrefix."Omschrijving","",true,true);
	$koppelObject[1]->name = "kostprijsFonds";
	$koppelObject[1]->extraQuery = $alleenActief;

	$koppelObject[2] = new Koppel("Fondsen","selectForm",$positieJoin);
	$koppelObject[2]->addFields($fondsPrefix."Fonds","fondsverloopFonds",false,true);
	$koppelObject[2]->addFields($fondsPrefix."ISINCode","",true,true);
	$koppelObject[2]->addFields($fondsPrefix."Omschrijving","",true,true);
	$koppelObject[2]->name = "fondsverloopFonds";
	$koppelObject[2]->extraQuery = $alleenActief;


	?>
	<script language=JavaScript src="javascript/popup.js" type=text/javascript></script>

	<script type="text/javascript">

	<?=$koppelObject[0]->getJavascript()?>
	<?=$koppelObject[1]->getJavascript()?>
	<?=$koppelObject[2]->getJavascript()?>
<?=$selectie->getSelectJava();?>
  
  $(function (){
    $(document).on('change', '#managementVermogensverloopShowInactive', function (){
      if ($(this).prop('checked')) {
        window.location.href = "rapportFrontofficeManagementSelectie.php?managementVermogensverloopShowInactive=1";
      } else {
        window.location.href = "rapportFrontofficeManagementSelectie.php";
      }
    });

    <?php
      if ( $openVermogensverloop === true ) {
        echo '
        $(".option-14").click();
        $("#managementVermogensverloopShowInactive").prop("checked", true);
        ';
      }

      if ( isset ($_GET['reloadTab']) && ! empty ($_GET['reloadTab']) ) {
        echo '$(".option-'.(int)$_GET['reloadTab'].'").click();';
      }
    ?>

  })
  
  function fondsChange()
  {
    var statusDisabled = false;
    var statusBackground = '#FBFBFB';
    
    for (var i=0; i < document.selectForm.transactieType.length; i++)
    {
      if (document.selectForm.transactieType[i].checked)
      {
        var rad_val = document.selectForm.transactieType[i].value;
      }
    }
    
    if((document.selectForm.fonds.value != '' && rad_val=='enkelvoudig') || (document.selectForm.aankoopFonds.value != '' && rad_val=='switch'))
    {
      statusDisabled = true;
      statusBackground = '#CCCCCC';
      document.selectForm.newFonds.value = '';
      document.selectForm.newFondsISIN.value = '';
      document.selectForm.newFondsValutaCode.value = '';
      document.selectForm.newFondsEenheid.value = '';
    }
    
    if(rad_val=='switch')
    {
      document.selectForm.berekeningswijze.disabled = true;
    }
    else
    {
      document.selectForm.berekeningswijze.disabled = false;
    }
    
    
    if(document.selectForm.fonds.value != '')
    {
      statusDisabled = true;
      statusBackground = '#CCCCCC';
      document.selectForm.newFonds.value = '';
      document.selectForm.newFondsISIN.value = '';
      document.selectForm.newFondsValutaCode.value = '';
      document.selectForm.newFondsEenheid.value = '';
    }
    
    document.selectForm.newFonds.disabled = statusDisabled;
    document.selectForm.newFonds.style.backgroundColor = statusBackground ;
    
    document.selectForm.newFondsISIN.disabled = statusDisabled;
    document.selectForm.newFondsISIN.style.backgroundColor = statusBackground ;
    
    document.selectForm.newFondsValutaCode.disabled = statusDisabled;
    document.selectForm.newFondsValutaCode.style.backgroundColor = statusBackground ;
    
    document.selectForm.newFondsEenheid.disabled = statusDisabled;
    document.selectForm.newFondsEenheid.style.backgroundColor = statusBackground ;
  }

function print()
{
	document.selectForm.target = "generateFrame";
	document.selectForm.filetype.value = "PDF";
	document.selectForm.save.value = "0";
	selectSelected();
	if (checkfield())
	{
		document.selectForm.submit();
	}
}


function saveasfile()
{
	document.selectForm.target = "generateFrame";
	document.selectForm.filetype.value = "PDF";
	document.selectForm.save.value = "1";
	selectSelected();
	if (checkfield())
	{
		document.selectForm.submit();
	}
}

function csv()
{
	document.selectForm.target = "generateFrame";
	document.selectForm.filetype.value = "cvs";
	document.selectForm.save.value = "1";
	selectSelected();
	if (checkfield())
	{
		document.selectForm.submit();
	}
}

function xls()
{
  document.selectForm.target = "generateFrame";
	document.selectForm.filetype.value="xls";
	document.selectForm.save.value="1";
	selectSelected();
	if (checkfield())
	{
		document.selectForm.submit();
	}
}

function database()
{
	document.selectForm.target = "generateFrame";
	document.selectForm.filetype.value="database";
	document.selectForm.save.value="1";
	selectSelected();
	if (checkfield())
	{
		document.selectForm.submit();
	}
}

function order()
{
	document.selectForm.target = "generateFrame";
	document.selectForm.filetype.value="order";
	selectSelected();
	if (checkfield())
	{
		document.selectForm.submit();
	}
}

	function checkfield()
	{
		if($("#soort").val()=='Modelcontrole')
		{
			if($('input[name=modelcontrole_rapport]:checked').val()=='vastbedrag')
			{
				var bedrag=$('[name=modelcontrole_vastbedrag]').val();
				if(bedrag=='' || parseFloat(bedrag)==0.0)
				{
					alert('<?=vt("Het bedrag is nog niet opgegeven")?>.');
					return false;
				}
			}
		}
		return true;
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
	  <?=$selectie->getJsPortefeuilleInternJava()?>
	  <?
	if(method_exists($selectie,'getConsolidatieJava'))
	  echo $selectie->getJsConsolidatieJava()
	?>
	return true;
}

function selectTab (selectedIndex = 0)
{

  if ($('#managementVermogensverloopShowInactive').prop('checked') && selectedIndex !== 14) {
    window.location.href = "rapportFrontofficeManagementSelectie.php?reloadTab=" + selectedIndex;
  }

  $('#rapportHolder').find('.active').removeClass('active');
  $('#rapportHolder .option-' + selectedIndex).addClass('active');
  
  $('#soortSelectie').val($('#rapportHolder .option-' + selectedIndex).attr('value'));
  
  $('#zorgplichtSelectie').hide();
  $('#Risicometing').hide();
  $('#div_filterFonds').hide();
  $('#cashlijstSelectie').hide();
  $('#Modelcontrole').hide();
  $('#portPar').hide();
  $('#Managementinfo').hide();
  $('#Mutatievoorstel').hide();
	$('#Vermogensverloop').hide();
  $('#sm').hide();
	$('#RestrictiecontroleDiv').hide();
	$('#MandaatcontroleDiv').hide();
	$('#RendementDetailsSelectie').hide();
  $('#TransactieTypeDiv').hide();
  $('#WaardeverloopSelectie').hide();
  
  
  $( "#orderButton" ).hide();
  $( "#databaseButton" ).hide();
  
  $( "#knopPDF" ).show();
  $( "#knopSAVE" ).show();
  
  
  var soort = $('#rapportHolder .option-' + selectedIndex).attr('value');

	if(soort == "Risicometing" || soort== "Risicoanalyse")
	{
		$('#Risicometing').show();
	}
	else if(soort == "Managementoverzicht" || soort == "Portefeuilleverdeling" )
	{
	  $('#Managementinfo').show();
    $( "#databaseButton" ).show();
	}
  else if (soort == "Omloopsnelheid") // portPar
	{
    //document.getElementById('databaseButton').style.visibility="visible";
	}
	else if (soort == "PortefeuilleParameters") // portPar
	{
    $('#portPar').show();
    $( "#knopPDF" ).hide();
    $( "#knopSAVE" ).hide();
	}
	else if (soort == "CashLijst" )
  {
    $('#cashlijstSelectie').show();
    $( "#knopPDF" ).hide();
    $( "#knopSAVE" ).hide();
  }
  else if (soort == "ClientAnalyse" || soort == "RapportEigendomsverhouding" ) // cashLijst
	{
    $( "#knopPDF" ).hide();
    $( "#knopSAVE" ).hide();
	}
	else if (soort == "CashPosities" || soort == "PortefeuilleIndex" )  // cash || index
	{
    $( "#knopPDF" ).hide();
    $( "#knopSAVE" ).hide();
	}
	else if (soort == "Zorgplichtcontrole") // Zorgplicht
	{
    $('#databaseButton').show();
    $('#zorgplichtSelectie').show();
	}
	// else if (soort == "Modelcontrole") // Modelcontrole
	// {
  //   $( "#Modelcontrole" ).show();
  //   $('#orderButton').show();
	// }
  else if (soort == "RendementDetails") // Modelcontrole
	{
    $('#RendementDetailsSelectie').show();
    $( "#knopPDF" ).hide();
    $( "#knopSAVE" ).hide();
	}
  else if (soort == "Waardeverloop") // Waardeverloop
  {
    $('#WaardeverloopSelectie').show();
    $( "#knopPDF" ).hide();
    $( "#knopSAVE" ).hide();
  }

// else if (soort == "Mutatievoorstel Fondsen") // Modelcontrole
// 	{
//     $('#TransactieTypeDiv').show();
//     $('#Mutatievoorstel').show();
//     $('#sm').show();
//
//     for (var i=0; i < document.selectForm.transactieType.length; i++)
//   {
//     if (document.selectForm.transactieType[i].checked)
//     {
//       var rad_val = document.selectForm.transactieType[i].value;
//     }
//   }
//
//     if(rad_val=='enkelvoudig')
//     {
//       mutatieEnkel(rad_val);
//     }
//     else
//     {
//       mutatieSwitch(rad_val);
//     }
//     $('#orderButton').show();
//   }
	else if (soort == "Vermogensverloop") // Vermogensverloop
	{
		$('#Vermogensverloop').show();
	}
	else if (soort == "Restrictiecontrole") // Mandaatcontrole
	{
		$('#RestrictiecontroleDiv').show();
	}
	else if (soort == "Mandaatcontrole") // Mandaatcontrole
	{
		$('#MandaatcontroleDiv').show();
	}
	else
	{
    $( "#Risicometing" ).hide();
	}


}




function loadField(field)
{
  inputBox = document.selectForm['invoer'];
  var Waarden = new Array();

<?
  while(list($categorie,$data)= each($invoerData))
  {
  echo "Waarden['$categorie']	= new Array(); \n";
    while(list($waarde,$omschrijving)= each($data))
    {
    echo "Waarden['$categorie']['$waarde']	= '".$omschrijving."'; \n";
    }
  }
  reset($invoerData);
?>

  for(var count = inputBox.options.length - 1; count >= 0; count--)
  {
    inputBox.options[count] = null;
  }

  if (field == 'alles1')
  {
    for (keyVar in Waarden )
    {
      LoadWaarde(Waarden[keyVar]);
    }
  }
  LoadWaarde(Waarden[field]);
}

function LoadWaarde(waarde)
{
  inputBox = document.selectForm['invoer'];
  for (keyVar in waarde )
  {
 		inputBox.options.length++;
		inputBox.options[inputBox.options.length-1].text = waarde[keyVar];
		inputBox.options[inputBox.options.length-1].value = keyVar;
  }
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
	    var div_a ='<select name="'+veld+'\" style="width:200px" '+formExtra+' >';
	    div_a += '<option value="alles"><?=vt("Alles")?></option>';
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
  
  
  
  function mutatieEnkel(rad_val)
  {
    $('#fondsVerkoopSpan').hide();

    $('#Smash').show();
    $("#Smash :input").attr("disabled", false);

    $('#fondsAankoopSpan').hide();
    $('#VoorstelSelectie').show();
    $('#fondsenSelectieKader').hide();
    editSmash(rad_val);

    fondsChange();
  }
  
  function mutatieSwitch(rad_val)
  {
    $('#fondsVerkoopSpan').show();

    // $('#Smash').hide();
    // $("#Smash :input").attr("disabled", true);

    $('#fondsAankoopSpan').show();
    $('#VoorstelSelectie').show();
    $('#fondsenSelectieKader').hide();
    editSmash(rad_val);
    fondsChange();
  }
  
  function mutatieMeer(rad_val)
  {
    $('#VoorstelSelectie').show();
    $('#fondsVerkoopSpan').hide();
    // $('#Smash').hide();
    $('#fondsAankoopSpan').hide();
    $('#fondsenSelectieKader').show();
    editSmash(rad_val);
    fondsChange();
  }
  
  function editSmash(rad_val)
  {
    for (var i=0; i < document.selectForm.transactieType.length; i++)
    {
      if (document.selectForm.transactieType[i].checked)
      {
        var rad_val = document.selectForm.transactieType[i].value;
      }
    }
    if(rad_val=='enkelvoudig__')
    {
      var statusDisabled = true;
      var statusBackground = '#CCCCCC';
    }
    else
    {
      var statusDisabled = false;
      var statusBackground = '#FBFBFB';
      document.selectForm.newFondsKoers.value='';
      document.selectForm.newFondsValutaKoers.value='';
    }
    document.selectForm.newFondsKoers.disabled=statusDisabled;
    document.selectForm.newFondsValutaKoers.disabled=statusDisabled;
    document.selectForm.newFondsKoers.style.backgroundColor=statusBackground;
    document.selectForm.newFondsValutaKoers.style.backgroundColor=statusBackground;
  }
  
  function unsetVastBedrag()
  {
    $('input[name=modelcontrole_vastbedrag]').val('');
    $('input[name=modelcontrole_rebalance]').attr('checked',false);
  }

	</script>


<br />

<div class="container-fluid">

  <form action="<?=$PHP_SELF?>" method="POST" target="_blank" name="selectForm">
    <input type="hidden" name="posted" value="true" />
    <input type="hidden" id="soortSelectie" name="soort" value="Managementoverzicht" />
  
    <input type="hidden" name="save" value="" />
    <input type="hidden" name="rapport_types" value="" />
    <input type="hidden" name="filetype" value="PDF" />
    <input type="hidden" name="portefeuilleIntern" value="" />
    <input type="hidden" name="metConsolidatie" value="<?=$_SESSION['metConsolidatie']?>" />
    
  
    <div class="formHolder" >
      
      <div class="formTabGroup ">
        <?=$AETemplate->parseBlockFromFile('rapportFrontoffice/tabbuttons.html', array(
          'management'      => 'active'
        ))?>
      </div>
      
      <div class="formTitle textB">Selectie</div>
      <div class="formContent padded-10">
        
        <div class="row no-gutters">
          <div class="col-6 col-md-4 col col-xl-3" style="border-right: #d9d9d9 1px solid; height: 161px; padding-left: 20px;">
            <?=$selectieHtml;?>
          </div>
  
          <div class="col-6 col-md-4 col col-xl-3" style="border-right: #d9d9d9 1px solid; text-align: center; height: 161px;">
            <?=$AETemplate->parseBlockFromFile('rapportFrontoffice/datum_selectie.html', array(
              'inpvalue'          => $inp['value'],
              'inpname'           => $inp['name'],
              'inp2value'         => $inp2['value'],
              'inp2name'          => $inp2['name'],
      
      
              'beginMaand'        => $datumSelctie['beginMaand'],
              'beginKwartaal'     => $datumSelctie['beginKwartaal'],
              'beginJaar'         => $datumSelctie['beginJaar'],
              'eindMaand'         => $datumSelctie['eindMaand'],
              'eindKwartaal'      => $datumSelctie['eindKwartaal'],
              'eindJaar'          => $datumSelctie['eindJaar'],
      
              'beginMaand2'       => $datumSelctie['beginMaand2'],
              'beginKwartaal2'    => $datumSelctie['beginKwartaal2'],
              'beginJaar2'        => $datumSelctie['beginJaar2'],
              'totFromDatum'      => $totFromDatum,
      
              'startThisYear'     => '01-01-' . date('Y'),
              'totDatumYear'      => date('Y', strtotime($totdatum)),
              'totDatum'          => date('d-m-Y', strtotime($totdatum)),
              'fullTotDatumJs'    => date('Y/m/d H:i:s', strtotime($totdatum))
            ))?>
          </div>
          <div class="col-1  col-xl-1  col-md-2  " style="">
            <div class=" btn-group-top btn-group-text-left btn-group-spacing-botton" style="margin-left:10px">
  
  
              <div class="btn btn-default" id="knopPDF" style="width:140px" onclick="javascript:print();"><i style="color:red" class="fa fa-file-pdf-o fa-fw  " aria-hidden="true"></i> <?=vt("Afdrukken")?></div>
              <div class="btn btn-default" id="knopSAVE" style="width:140px" onclick="javascript:saveasfile();"><i style="color:blue"  class="fa fa-floppy-o fa-fw " aria-hidden="true"></i> <?=vt("Opslaan")?> </div>
              <div class="btn btn-default" id="knopCSV" style="width:140px" onclick="javascript:csv();"><i style="color:green" class="fa fa-file-excel-o fa-fw" aria-hidden="true"></i> <?=vt("CSV-export")?> </div>
              <div class="btn btn-default" id="knopXLS" style="width:140px" onclick="javascript:xls();"><i style="color:green" class="fa fa-file-excel-o fa-fw" aria-hidden="true"></i> <?=vt("XLS-export")?> </div>
              <div class="btn btn-default" id="databaseButton" style="width:140px" onclick="javascript:database();"><i style="color:blue"  class="fa fa-table fa-fw " aria-hidden="true"></i> <?=vt("Reportbuilder")?> </div>
              <?
              if (checkOrderAcces('rapportages_aanmaken') === true || GetModuleAccess('ORDER') < 2)
                echo '<div class="btn btn-default" id="orderButton" style="width:130px; display: none;" onclick="javascript:order();">&nbsp; '.vt("Genereer orders").'</div>';
              else
                echo '<div class="btn btn-default" id="orderButton" style="width:150px; display: none;" >&nbsp; '.vt("Geen order rechten").'</div>';
              ?>
            </div>
          </div>
        </div>

      </div>
      </div>
  
  
  
      <div class="formHolder" id="rapportHolder" >
        <div class="formTitle textB"><?=vt("Rapport")?></div>
        <div class="formContent padded-10">

  
          <div class="btn-group-vertical btn-group-top btn-group-text-left  col-2">
<!--            <span class="btn btn-hover btn-default option-13" onclick="selectTab(13);" value="afmExport">AFM export</span>-->
<!--            <span class="btn btn-hover btn-default option-5" onclick="selectTab(5);" value="CashPosities">Cash Posities</span>-->
            <span class="btn btn-hover btn-default option-8" onclick="selectTab(8);" data-toggle="tooltip" data-placement="top" title="<?=vt("Cash Lijst")?>"  value="CashLijst"><?=vt("Cash Lijst")?></span>
            <span class="btn btn-hover btn-default option-5" onclick="selectTab(5);" data-toggle="tooltip" data-placement="top" title="<?=vt("Cash Posities")?>"  value="CashPosities"><?=vt("Cash Posities")?></span>
            
<!--            <span class="btn btn-hover btn-default option-11" onclick="selectTab(11);" value="ClientAnalyse">Client Analyse</span>-->
<!--            <span class="btn btn-hover btn-default option-12" onclick="selectTab(12);" value="RapportEigendomsverhouding">Eigendomsverhouding</spa>-->

            <span class="btn btn-hover btn-default option-0" onclick="selectTab(0);" data-toggle="tooltip" data-placement="top" title="<?=vt("Managementoverziht")?>"  value="Managementoverzicht"><?=vt("Managementoverzicht")?></span>
            <span class="btn btn-hover btn-default option-20" onclick="selectTab(20);" data-toggle="tooltip" data-placement="top" title="<?=vt("Mandaatcontroe")?>"  value="Mandaatcontrole"><?=vt("Mandaatcontrole")?></span>



          </div>
          <div class="btn-group-vertical btn-group-top btn-group-text-left  col-2">
            <span class="btn btn-hover btn-default option-1" onclick="selectTab(1);" data-toggle="tooltip" data-placement="top" title="<?=vt("Omloopsnelheid")?>"  value="Omloopsnelheid"><?=vt("Omloopsnelheid")?></span>


            <span class="btn btn-hover btn-default option-6" onclick="selectTab(6);" data-toggle="tooltip" data-placement="top" title="<?=vt("Portefeuille Index")?>"  value="PortefeuilleIndex"><?=vt("Portefeuille Index")?></span>
            <span class="btn btn-hover btn-default option-7" onclick="selectTab(7);" data-toggle="tooltip" data-placement="top" title="<?=vt("Portefeuille Parameters")?>"  value="PortefeuilleParameters"><?=vt("Portefeuille Parameters")?></span>
  
            <?
            if(in_array(12,$layouts))
              echo '<span class="btn btn-hover btn-default option-21" onclick="selectTab(21);" data-toggle="tooltip" data-placement="top" title="'.vt("Rendement per categorie").'"  value="RendementPerCategorie">'.vt("Rendement per categorie").'</span>';
            ?>
            <span class="btn btn-hover btn-default option-22" onclick="selectTab(22);" data-toggle="tooltip" data-placement="top" title="<?=vt("Rendement Details")?>"  value="RendementDetails"><?=vt("Rendement Details")?></span>

          </div>

          <div class="btn-group-vertical btn-group-top btn-group-text-left  col-2">

            <span class="btn btn-hover btn-default option-19" onclick="selectTab(19);" data-toggle="tooltip" data-placement="top" title="<?=vt("Restrictiecontrole")?>"  value="Restrictiecontrole"><?=vt("Restrictiecontrole")?></span>
            <!--            <span class="btn btn-hover btn-default option-16" onclick="selectTab(16);" value="Portefeuilleverdeling">portefeuille-verdeling</span>-->
            <!--            <span class="btn btn-hover btn-default option-9" onclick="selectTab(9);" value="Remisiervergoeding">Remisiervergoeding</span>-->
            <!--	<span class="btn btn-hover btn-default option-5" value="Valuta Risico">Valuta Risico</span> -->
<!--            <span class="btn btn-hover btn-default option-3" onclick="selectTab(3);" value="Risicoanalyse">Risicoanalyse</span>-->
            <span class="btn btn-hover btn-default option-2" onclick="selectTab(2);" data-toggle="tooltip" data-placement="top" title="<?=vt("Risicometing")?>"  value="Risicometing"><?=vt("Risicometing")?></span>
            <span class="btn btn-hover btn-default option-15" onclick="selectTab(15);" value="Transactieoverzicht"><?=vt("Transactieoverzicht")?></span>
            <span class="btn btn-hover btn-default option-14" onclick="selectTab(14);" value="Vermogensverloop"><?=vt("Vermogensverloop")?></span>
          </div>

          <div class="btn-group-vertical btn-group-top btn-group-text-left  col-2">
            <span class="btn btn-hover btn-default option-23" onclick="selectTab(23);" value="VkmOpbouw"><?=vt("VKM-opbouw")?></span>
            <span class="btn btn-hover btn-default option-24" onclick="selectTab(24);" value="Waardeverloop"><?=vt("Waardeverloop per categorie")?></span>
            <span class="btn btn-hover btn-default option-4" onclick="selectTab(4);" value="Zorgplichtcontrole"><?=vt("Zorgplichtcontrole")?></span>
            <span class="btn btn-hover btn-default option-10" onclick="selectTab(10);" value="ZorgplichtcontroleDetail"><?=vt("Zorgplichtcontrole Detail")?></span>

          </div>
          
          
          

        </div>
  
      </div>

    <div class="baseRow">
  
      <?php
        $blockSize = 'col-6 col-md-6 col col-xl-5';
        if( $_SESSION['selectieMethode']  === 'portefeuille' ) {
          $blockSize = 'col';
        }
      ?>
  
      <div class="<?=$blockSize;?>" id="PortefueilleSelectie">
    
    
        <div class="formHolder"  id="Selectie">
          <div class="formTabGroup ">
            <?=$selectie->getHtmlSelectieMethode()?>
          </div>
          <div class="formTitle textB">Selectie</div>
          <div class="formContent formContentForm pl-0 pt-2 PB-2" id="">
            <?
            // portefeuille selectie
            if($_SESSION['selectieMethode'] == 'portefeuille')
            {
            ?>
            <script language="Javascript">
        
            </script>
  
  
           
            
            <table cellspacing="0" border = 0>
              <?
              $DB = new DB();
              $DB->SQL($selectie->queries['ClientPortefeuille']);
              $DB->Query();
              ?>
              <?
              while($gb = $DB->NextRecord())
              {
                $data[$gb['Portefeuille']] = $gb;
              }
              echo "<br><br>";
              echo $selectie->createEnkelvoudigeSelctie($data);
              echo "<br><br>";
              ?>
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
                $opties=array('Risicoklasse'=>'Risicoklasse','ModelPortefeuille'=>'ModelPortefeuille','AFMprofiel'=>'AFMprofiel','SoortOvereenkomst'=>'SoortOvereenkomst','Remisier'=>'Remisier','PortefeuilleClusters'=>'PortefeuilleClusters','selectieveld1'=>'Selectieveld1','selectieveld2'=>'Selectieveld2');
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
              if(!method_exists($selectie,'getConsolidatieJava'))
              {
                ?>
                <div class="formblock">
                  <div class="formlinks"> <?=vt("Geconsolideerde portefeuilles opnemen")?></div>
                  <div class="formrechts">
                    <input type="checkbox" value="1" name="geconsolideerd">
                  </div>
                </div>
            
                <?
              }
              ?>
          </div>
        </div>
      </div>
  
      <div class="col-6 col-md-5 col col-xl-5" >
  
        <!-- Order type -->
        <div class="formHolder"  id="TransactieTypeDiv" style="display: none; ">
          <div class="formTitle textB"><?=vt("Order type")?></div>
          <div class="formContent formContentForm pl-4 pt-2 PB-2" id="TransactieTypeFieldset">
            <div class="formblock">
              <div class="formlinks">
                <input type="radio" name="transactieType" value="enkelvoudig" checked onClick="javascript:mutatieEnkel();"> <?=vt("Enkelvoudige order")?> <br>
                <input type="radio" name="transactieType" value="switch" onClick="javascript:mutatieSwitch();">  <?=vt("Switch order")?>  <br>
              </div>
            </div>
          </div>
        </div>
  
  
  
  
        <!-- Risico -->
        <div class="formHolder"  id="Risicometing" style="display: none; ">
          <div class="formTitle textB"><?=vt("Risico")?></div>
          <div class="formContent formContentForm pl-4 pt-2 PB-2" id="">
            <div class="formblock">
              <div class="formlinks"> <?=vt("Risico methode")?></div>
              <div class="formrechts">
                <select name="risicoMethode">
                  <option value="perBeleggingscategorie"><?=vt("obv % per beleggingscategorie")?></option>
                  <option value="perFonds"><?=vt("obv % per fonds")?></option>
                </select>
              </div>
            </div>
          </div>
        </div>
  
  
  
        <!-- zorgplichtSelectie -->
        <div class="formHolder"  id="zorgplichtSelectie" style="display: none; ">
          <div class="formTitle textB"><?=vt("zorgplicht opties")?></div>
          <div class="formContent formContentForm pl-4 pt-2 PB-2" id="">
      
            <div class="formblock">
              <div class="formlinks" style="width:300px"> <?=vt("Alleen portefeuilles die niet voldoen")?> </div>
              <div class="formrechts"> <input type="checkbox" name="zorgplichtVoldoetNiet" value="1" > </div>
            </div>
            <div class="formblock">
              <div class="formlinks" style="width:300px"> <?=vt("Alleen categorieën per portefeuille die niet voldoen")?></div>
              <div class="formrechts"> <input type="checkbox" name="zorgplichtVoldoetNietCategorie" value="1" > </div>
            </div>
            <div class="formblock">
              <div class="formlinks" style="width:300px"> <?=vt("Gehanteerde zorgplichtmethodiek")?></div>
              <div class="formrechts">
                <select name="ZpMethodeKeuze">
                  <option value="aandelen"><?=vt("Volgens categorien")?> </option>
                  <option value="afm"><?=vt("AFM standaarddeviatie")?></option>
                  <option value="stdev"><?=vt("Werkelijke standaarddeviatie")?></option>
                  <option value="contractueel"><?=vt("Contractuele methode")?></option>
                </select>
              </div>
            </div>
      
            <div class="formblock">
              <div class="formlinks" style="width:300px"> <?=vt("Extra Portefeuilleselectie")?></div>
              <div class="formrechts">
                <select name="ZorgMethodeFilter">
                  <option value="alles"><?=vt("Alle portefeuilles")?></option>
                  <option value="contractueel"><?=vt("Contractuele portefeuilles")?></option>
                  <option value="aandelen"><?=vt("Portefeuilles met categorie methode")?></option>
                  <option value="afm"><?=vt("Portefeuilles met AFM standaarddeviatie methode")?></option>
                  <option value="leeg"><?=vt("Portefeuilles zonder paramters")?></option>
                </select>
              </div>
            </div>
            <div class="formblock">
              <div class="formlinks" style="width:300px"> <?=vt("Doorkijk (huisfonds) gebruiken")?></div>
              <div class="formrechts"> <input type="checkbox" name="zorgDoorkijk" value="1"> </div>
            </div>
          </div>
        </div>
  
        
        
        
        
  
        <!-- Cash Lijst opties -->
        <div class="formHolder"  id="cashlijstSelectie" style="display: none; ">
          <div class="formTitle textB"><?=vt("Cash Lijst opties")?></div>
          <div class="formContent formContentForm pl-4 pt-2 PB-2" id="">
            <div class="formblock">
              <div class="formlinks"> <?=vt("Rekeningen met 0-saldo tonen")?> </div>
              <div class="formrechts"> <input type="checkbox" name="nulTonen" value="1" > </div>
            </div>
            <div class="formblock">
              <div class="formlinks"> <?=vt("Inactieve rekeningen tonen")?> </div>
              <div class="formrechts"> <input type="checkbox" name="inactiefTonen" value="1" > </div>
            </div>
            <div class="formblock">
              <div class="formlinks"> <?=vt("Deposito's tonen")?> </div>
              <div class="formrechts"> <input type="checkbox" name="depositoTonen" value="1" > </div>
            </div>
          </div>
        </div>
  
  
  
        <!-- RendementDetailsSelectie -->
        <div class="formHolder"  id="RendementDetailsSelectie" style="display: none; ">
          <div class="formTitle textB"><?=vt("Rendement details opties")?></div>
          <div class="formContent formContentForm pl-4 pt-2 PB-2" id="">
      
            <div class="formblock">
              <div class="formlinks"> <?=vt("Periode")?> </div>
              <div class="formrechts"> <select name="periode">
                  <option value="maanden"><?=vt("Maanden")?></option>
                  <option value="dagen"><?=vt("Dagen")?></option>
                  <option value="weken"><?=vt("Weken")?></option>
                  <option value="halveMaanden"><?=vt("Twee weken")?></option>
                </select></div>
            </div>
          </div>
        </div>
  
  
        <!-- WaardeverloopSelectie -->
        <div class="formHolder"  id="WaardeverloopSelectie" style="display: none; ">
          <div class="formTitle textB"><?=vt("Waardeverloop per categorie")?></div>
          <div class="formContent formContentForm pl-4 pt-2 PB-2" id="">
      
            <div class="formblock">
              <div class="formlinks"> <?=vt("Periode")?> </div>
              <div class="formrechts"> <select name="WaardeverloopPeriode">
                  <option value="maanden"><?=vt("Maanden")?></option>
                  <option value="dagen"><?=vt("Dagen")?></option>
                  <option value="weken"><?=vt("Weken")?></option>
                  <option value="halveMaanden"><?=vt("Twee weken")?></option>
                </select></div>
            </div>
              <div class="formblock">
            <div class="formlinks"> <?=vt("Verdeling")?> </div>
            <div class="formrechts"> <select name="WaardeverloopVerdeling">
                <option value="hoofdcategorie"><?=vt("Hoofdcategorie")?></option>
                <option value="beleggingscategorie"><?=vt("Beleggingscategorie")?></option>
                <option value="beleggingssector"><?=vt("Sector")?></option>
                <option value="Regio"><?=vt("Regio")?></option>
                <option value="valuta"><?=vt("Valuta")?></option>
                <option value="afmCategorie"><?=vt("AFM-categorie")?></option>
                <option value="AttributieCategorie"><?=vt("Attributiecategorie")?></option>
                </select></div>
          </div>
        </div>
        </div>
  
        <!-- Managementinfo -->
        <div class="formHolder"  id="Managementinfo" style="display: none; ">
          <div class="formTitle textB"><?=vt("Managementinfo")?></div>
          <div class="formContent formContentForm pl-4 pt-2 PB-2" id="">
            <div class="formblock">
              <div class="formlinks"> </div>
              <div class="formrechts">
                <input type="checkbox" value="1" name="orderbyVermogensbeheerder"> <?=vt("Subtotaal per Vermogensbeheerder")?>
              </div>
            </div>
      
            <div class="formblock">
              <div class="formlinks"> </div>
              <div class="formrechts">
                <input type="checkbox" value="1" name="orderbyAccountmanager">	<?=vt("Subtotaal per Accountmanager")?>
              </div>
            </div>
      
            <div class="formblock">
              <div class="formlinks"> </div>
              <div class="formrechts">
                <input type="checkbox" value="1" name="orderbyRisicoklasse">	<?=vt("Subtotaal per Risicoklasse")?>
              </div>
            </div>
      
            <div class="formblock">
              <div class="formlinks"> </div>
              <div class="formrechts">
                <input type="checkbox" value="1" name="orderbySoortOvereenkomst">	<?=vt("Subtotaal per SoortOvereenkomst")?>
              </div>
            </div>
      
            <div class="formblock">
              <div class="formlinks"> </div>
              <div class="formrechts">
                <input type="checkbox" value="1" name="manExtraVelden">	<?=vt("Extra velden")?>
              </div>
            </div>
  <?php
  if($stdevTonen==true)
  {
  ?>
            <div class="formblock">
              <div class="formlinks"> </div>
              <div class="formrechts">
                <input type="checkbox" value="1" name="manStdev">	<?=vt("Inclusief werkelijke standaarddeviatie")?>
              </div>
            </div>
 <?php
  }
 ?>
          </div>
        </div>
  
  
  
  
        <!-- Vermogensverloop -->
        <div class="formHolder"  id="Vermogensverloop" style="display: none; ">
          <div class="formTitle textB"><?=vt("Vermogensverloop")?></div>
          <div class="formContent formContentForm pl-4 pt-2 PB-2" id="">

            <div class="formblock">
              <div class="formlinks"> <?=vt("Toon vervallen selectievelden")?></div>
              <div class="formrechts">
                <input type="checkbox" value="1" name="managementVermogensverloopShowInactive" id="managementVermogensverloopShowInactive">
              </div>
            </div>

            <div class="formblock">
              <div class="formlinks"> <?=vt("Subtotaal per")?></div>
              <div class="formrechts">
                <select name="verloopGroupBy">
                  <option value=""> <?=vt("Geen")?></option>
                  <option value="Vermogensbeheerder"> <?=vt("Vermogensbeheerder")?></option>
                  <option value="Accountmanager"> <?=vt("Accountmanager")?></option>
                  <option value="Risicoklasse"> <?=vt("Risicoklasse")?></option>
                  <option value="SoortOvereenkomst"> <?=vt("SoortOvereenkomst")?></option>
                  <option value="Depotbank"> <?=vt("Depotbank")?></option>
                </select>
              </div>
            </div>
      
            <div class="formblock">
              <div class="formlinks"><?= vt('Indeling'); ?> </div>
              <div class="formrechts">
                <select name="verloopDetails">
                  <option value=""> <?= vt('Standaard'); ?> </option>
                  <option value="1" SELECTED> <?= vt('Inclusief details'); ?></option>
                  <option value="2"> <?= vt('AUM rapportage'); ?></option>
                </select>
              </div>
            </div>
          </div>
        </div>


        
        
        

<?
$DB = new DB();
$DB->SQL("SELECT DISTINCT Fondseenheid FROM Fondsen ORDER by Fondseenheid");
$DB->Query();
$fondseenheid .= "<option value='' >---</option>\n";
while($gb = $DB->NextRecord())
{
  $fondseenheid .= "<option value=\"".$gb['Fondseenheid']."\" >".$gb['Fondseenheid']."</option>\n";
}

$DB->SQL("SELECT DISTINCT Valuta  FROM Fondsen");
$DB->Query();
$valutaCode .= "<option value='' >---</option>\n";
while($gb = $DB->NextRecord())
{
   $valutaCode.= "<option value=\"".$gb['Valuta']."\" >".$gb['Valuta']."</option>\n";
}
$db = new DB();
$query="SELECT max(Vermogensbeheerders.OrderuitvoerBewaarder) as OrderuitvoerBewaarder FROM
Vermogensbeheerders JOIN VermogensbeheerdersPerGebruiker ON VermogensbeheerdersPerGebruiker.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder
WHERE VermogensbeheerdersPerGebruiker.Gebruiker =  '$USR' ";
$db->SQL($query);
$bewaarder=$db->lookupRecord();
if($bewaarder['OrderuitvoerBewaarder']==1)
{
  $DB->SQL("SELECT Depotbank,Omschrijving FROM Depotbanken ORDER BY Depotbank");
  $DB->Query();
  $depotbankOptions .= "<option value=\"\" >---</option>\n";
  while($gb = $DB->NextRecord())
    $depotbankOptions.= "<option value=\"".$gb['Depotbank']."\" >".$gb['Depotbank']." - ".$gb['Omschrijving']."</option>\n";
}

if($DB->QRecords("SELECT CategorienPerHoofdcategorie.id FROM CategorienPerHoofdcategorie JOIN VermogensbeheerdersPerGebruiker ON CategorienPerHoofdcategorie.vermogensbeheerder=VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker='$USR' limit 1"))
	$hoofdcategorie='<input type="radio" name="modelcontrole_level" value="hoofdcategorie" >'.vt("Hoofdcategorie").'<br>';


if($actief == "positie")
{
	$positieJoin = "JOIN ActieveFondsen a ON f.Fonds = a.Fonds";
	$alleenActief= "a.InPositie = '1'";
	$query="SELECT f.Fonds, f.Omschrijving FROM Fondsen f $positieJoin WHERE ".$alleenActief." ORDER BY Omschrijving";
	$DB->SQL($query);
	$DB->query();
	$fondsen = "";
	while($gb = $DB->NextRecord())
		$fondsen .= "<option value=\"".$gb['Fonds']."\" >".$gb['Omschrijving']."</option>\n";

	$getFonds=urlencode(base64_encode(gzcompress($query)));
}
else
{
	$getFonds = urlencode(base64_encode(gzcompress("SELECT Fonds, Omschrijving FROM Fondsen WHERE 1=1 " . $alleenActief . " ORDER BY Omschrijving")));
}
?>
  
  
        <!-- Portefeuille parameters -->
        <div class="formHolder"  id="portPar" style="display: none; ">
          <div class="formTitle textB"><?=vt("Portefeuille parameters")?></div>
          <div class="formContent formContentForm pl-4 pt-2 PB-2" id="">
      
      
      
            <table>
              <tr><td><b><?=vt("Invoer")?> </b></td><td><b><?=vt("Uitvoer")?></b> </td><td><b><?=vt("Filter")?></b> </td> </tr>
              <tr>
                <td width="120">
                  <input type="radio" name="typeInvoer" value="alles" checked onclick="javascript:loadField('alles')"> <?=vt("Alles")?> <br>
                  <input type="radio" name="typeInvoer" value="H-cat" onclick="javascript:loadField('H-cat')"> <?=vt("Hoofd categorieën")?> <br>
                  <input type="radio" name="typeInvoer" value="cat" onclick="javascript:loadField('cat')"> <?=vt("Categorien")?> <br>
                  <input type="radio" name="typeInvoer" value="H-sec"  onclick="javascript:loadField('H-sec')"> <?=vt("Hoofd sectoren")?> <br>
                  <input type="radio" name="typeInvoer" value="sec"  onclick="javascript:loadField('sec')"> <?=vt("Sectoren")?> <br>
                  <input type="radio" name="typeInvoer" value="regio"  onclick="javascript:loadField('regio')"> <?=vt("Regios")?> <br>
                  <input type="radio" name="typeInvoer" value="valuta"  onclick="javascript:loadField('valuta')"> <?=vt("Valutas")?> <br>
                  <input type="radio" name="typeInvoer" value="afm"  onclick="javascript:loadField('afm')"> <?=vt("AFM categorieën")?> <br>
                  <input type="radio" name="typeInvoer" value="duurzaam"  onclick="javascript:loadField('duu')"> <?=vt("Duurzaam categorieën")?> <br><br>
                </td>
                <td width="200">
                  <input type="radio" name="uitvoer" onclick="$('#instrumentAantal').attr('disabled','disabled');" value="alles" checked><?=vt("Alles")?> <br>
                  <input type="radio" name="uitvoer" onclick="$('#instrumentAantal').attr('disabled','disabled');" value="categorien"><?=vt("Categorieën")?> <br>
                  <input type="radio" name="uitvoer" onclick="$('#instrumentAantal').attr('disabled','disabled');" value="hoofdCategorien"><?=vt("Hoofd Categorieën")?> <br>
                  <input type="radio" name="uitvoer" onclick="$('#instrumentAantal').attr('disabled','disabled');" value="hoofdSectoren"><?=vt("Hoofd Sectoren")?> <br>
                  <input type="radio" name="uitvoer" onclick="$('#instrumentAantal').attr('disabled','disabled');" value="sectoren"><?=vt("Sectoren")?> <br>
                  <input type="radio" name="uitvoer" onclick="$('#instrumentAantal').attr('disabled','disabled');" value="regios"><?=vt("Regio's")?> <br>
                  <input type="radio" name="uitvoer" onclick="$('#instrumentAantal').attr('disabled','disabled');" value="valuta"><?=vt("Valuta's")?> <br>
                  <input type="radio" name="uitvoer" onclick="$('#instrumentAantal').attr('disabled','disabled');" value="afm"><?=vt("AFM categorieën")?> <br>
                  <input type="radio" name="uitvoer" onclick="$('#instrumentAantal').attr('disabled','disabled');" value="duurzaam"><?=vt("Duurzaam categorieën")?> <br>
                  <input type="radio" name="uitvoer"  value="instrumenten"
                         onclick="$('#div_filterFonds').show(); $('#instrumentAantal').removeAttr('disabled');"><?=vt("Instrumenten")?>
                  <div id="div_filterFonds"><select name="filterFonds" id="filterFonds" style="width:200px;" onfocus="javascript:getAjaxWaarden('<?=$getFonds?>','',this.name);"><?=$fondsen?></select></div><br>
                </td>
                <td width="200" valign="top" >
                  <br><?=vt("Type")?><br>
                  <select name="filterType" style="width:200px">
                    <option value="geen"><?=vt("geen filter")?></option>
                    <option value="groter"><?=vt("groter dan")?> </option>
                    <option value="kleiner"><?=vt("kleiner dan")?> </option>
                    <option value="groterGelijk"><?=vt("groter of gelijk dan")?> </option>
                    <option value="kleinerGelijk"><?=vt("kleiner of gelijk dan")?> </option>
                    <option value="gelijk"><?=vt("gelijk aan")?> </option>
                    <option selected value="nietGelijk"><?=vt("niet gelijk aan")?> </option>
                  </select>
                  <br><br>
                  <?=vt("Waarde")?><br>
                  <input type="text" value="0" name="filterWaarde" />
        
              </tr>
      
            </table>
            <table>
              <tr>
                <td width="210">
                  <?=vt("Invoer waarde")?> <br>
                  <select name="invoer" style="width:200px">
                    <option value="alles"><?=vt("Alles")?></option>
                  </select>
                </td>
        
              </tr>
              <tr><td>&nbsp;</td><td>&nbsp;</td> </tr>
              <tr><td> <input type="radio" name="percentages"  value="true" checked> <?=vt("Relatieve waarden")?>.</td><td></td> </tr>
              <tr><td> <input type="radio" name="percentages"  value="" ><?=vt("Absolute waarden")?></td><td></td> </tr>
              <tr><td> <input type="radio" name="percentages" id="instrumentAantal" disabled value="aantal" ><?=vt("Aantallen")?></td><td></td> </tr>
            </table>
          </div>
        </div>
  
        <!-- Mandaatcontrole -->
        <div class="formHolder"  id="MandaatcontroleDiv" style="display: none; ">
          <div class="formTitle textB"><?=vt("Mandaatcontrole")?></div>
          <div class="formContent formContentForm pl-4 pt-2 PB-2" id="MandaatUitvoer">
      
            <div class="formblock">
              <div class="formlinks"> <?=vt("Zorgplichtcategorie")?> </div>
              <div class="formrechts">
                <select name="mandaat_zorgplichtCategorie">
                  <?=$zorgplichtSelect?>
                </select>
              </div>
            </div>
      
            <div class="formblock">
              <div class="formlinks"> <?=vt("Alleen geconsolideerde portefeuilles weergeven")?> </div>
              <div class="formrechts">
                <input type="checkbox" value="1" name="restrictie_alleenConsolidaties">
              </div>
            </div>
    
          </div>
        </div>
  
  
        <!-- Restrictie -->
        <div class="formHolder"  id="RestrictiecontroleDiv" style="display: none; ">
          <div class="formTitle textB"><?=vt("Restrictie")?></div>
          <div class="formContent formContentForm pl-4 pt-2 PB-2" id="RestrictieUitvoer">
            <div class="formblock">
              <u><?=vt("Uitvoer soort")?></u><br>
              <input type="radio" name="restrictie_uitvoer" value="alles" checked> <?=vt("Alles")?><br>
              <input type="radio" name="restrictie_uitvoer" value="afwijkingen"> <?=vt("Alleen afwijkingen")?><br>
              <input type="radio" name="restrictie_uitvoer" value="afwijkingenEnBeperkingen"> <?=vt("Afwijkingn en overige beperkingen")?>
            </div>
          </div>
        </div>

 

</form>

</div>
</div>


<div class="row">
  
  <div class="col" >

<?echo progressFrame();?>

  </div>
</div>
<?php
	if($__debug) {
		echo getdebuginfo();
	}
	echo template($__appvar["templateRefreshFooter"],$content);
}

?>