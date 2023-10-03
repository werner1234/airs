<?php
/*
    AE-ICT source module
    Author  						: $Author: rm $
 		Laatste aanpassing	: $Date: 2019/12/04 15:31:42 $
 		File Versie					: $Revision: 1.1 $

 		$Log: rapportFrontofficeFondsSelectieold.php,v $
 		Revision 1.1  2019/12/04 15:31:42  rm
 		7929
 		
 		Revision 1.122  2019/02/20 16:48:10  rvv
 		*** empty log message ***
 		
 		Revision 1.121  2018/11/05 06:49:00  rvv
 		*** empty log message ***
 		
 		Revision 1.120  2018/11/03 18:47:36  rvv
 		*** empty log message ***
 		
 		Revision 1.119  2018/10/13 17:16:37  rvv
 		*** empty log message ***
 		
 		Revision 1.118  2018/09/22 17:09:55  rvv
 		*** empty log message ***
 		
 		Revision 1.117  2018/09/19 17:31:22  rvv
 		*** empty log message ***
 		
 		Revision 1.116  2018/08/27 17:17:09  rvv
 		*** empty log message ***
 		
 		Revision 1.115  2018/06/10 14:41:03  rvv
 		*** empty log message ***
 		
 		Revision 1.114  2018/05/30 16:07:23  rvv
 		*** empty log message ***
 		
 		Revision 1.113  2018/05/23 13:45:27  rvv
 		*** empty log message ***
 		
 		Revision 1.112  2018/05/21 10:21:11  rvv
 		*** empty log message ***
 		
 		Revision 1.111  2018/05/19 16:22:32  rvv
 		*** empty log message ***
 		
 		Revision 1.110  2018/04/07 15:23:45  rvv
 		*** empty log message ***
 		
 		Revision 1.109  2018/04/04 15:45:06  rvv
 		*** empty log message ***
 		
 		Revision 1.108  2018/03/17 18:45:01  rvv
 		*** empty log message ***
 		
 		Revision 1.106  2018/02/28 16:44:09  rvv
 		*** empty log message ***
 		
 		Revision 1.105  2018/02/21 17:11:49  rvv
 		*** empty log message ***
 		
 		Revision 1.104  2018/02/10 18:07:30  rvv
 		*** empty log message ***
 		
 		Revision 1.103  2018/02/07 17:15:01  rvv
 		*** empty log message ***
 		
 		Revision 1.102  2018/01/14 12:37:31  rvv
 		*** empty log message ***
 		
 		Revision 1.101  2018/01/07 14:04:01  rvv
 		*** empty log message ***
 		
 		Revision 1.100  2018/01/06 19:03:24  rvv
 		*** empty log message ***
 		
 		Revision 1.99  2017/11/20 07:45:21  rvv
 		*** empty log message ***
 		
 		Revision 1.98  2017/11/19 14:26:52  rvv
 		*** empty log message ***
 		
 		Revision 1.97  2017/10/21 17:28:59  rvv
 		*** empty log message ***
 		
 		Revision 1.96  2017/08/27 07:36:32  rvv
 		*** empty log message ***
 		
 		Revision 1.95  2017/07/31 08:19:30  rvv
 		*** empty log message ***
 		
 		Revision 1.94  2017/07/13 05:27:24  rvv
 		*** empty log message ***
 		
 		Revision 1.93  2017/07/12 15:57:42  rvv
 		*** empty log message ***
 		
 		Revision 1.92  2017/07/09 11:56:18  rvv
 		*** empty log message ***
 		
 		Revision 1.91  2017/04/03 06:15:42  rvv
 		*** empty log message ***
 		
 		Revision 1.90  2017/04/02 10:01:41  rvv
 		*** empty log message ***
 		
 		Revision 1.89  2017/02/15 16:35:55  rvv
 		*** empty log message ***
 		
 		Revision 1.88  2016/11/27 11:07:26  rvv
 		*** empty log message ***
 		
 		Revision 1.87  2016/11/19 19:00:39  rvv
 		*** empty log message ***
 		
 		Revision 1.86  2016/11/02 16:30:11  rvv
 		*** empty log message ***
 		
 		Revision 1.85  2016/08/13 16:52:24  rvv
 		*** empty log message ***
 		
 		Revision 1.84  2016/07/03 06:45:27  rvv
 		*** empty log message ***
 		
 		Revision 1.83  2016/07/02 09:31:22  rvv
 		*** empty log message ***

 		Revision 1.82  2016/05/02 09:04:18  rm
 		orders
 		
 		Revision 1.81  2016/04/17 17:56:35  rvv
 		*** empty log message ***
 		
 		Revision 1.80  2016/03/13 16:10:39  rvv
 		*** empty log message ***
 		
 		Revision 1.79  2016/02/28 17:20:32  rvv
 		*** empty log message ***
 		
 		Revision 1.78  2016/02/27 15:11:59  rvv
 		*** empty log message ***
 		
 		Revision 1.77  2016/02/21 17:20:10  rvv
 		*** empty log message ***
 		
 		Revision 1.76  2016/01/03 09:12:52  rvv
 		*** empty log message ***
 		
 		Revision 1.75  2015/11/07 16:29:39  rvv
 		*** empty log message ***
 		
 		Revision 1.74  2015/09/30 07:54:15  rvv
 		*** empty log message ***
 		
 		Revision 1.73  2015/03/25 14:47:52  rvv
 		*** empty log message ***
 		
 		Revision 1.72  2014/12/17 16:04:26  rvv
 		*** empty log message ***
 		
 		Revision 1.70  2014/11/01 22:07:07  rvv
 		*** empty log message ***

 		Revision 1.69  2014/09/13 14:37:42  rvv
 		*** empty log message ***
 		
 		Revision 1.68  2014/09/03 15:55:22  rvv
 		*** empty log message ***
 		
 		Revision 1.67  2014/03/16 11:16:20  rvv
 		*** empty log message ***
 		
 		Revision 1.66  2014/02/22 18:42:25  rvv
 		*** empty log message ***
 		
 	
*/

//$AEPDF2=true;
include_once("wwwvars.php");
include_once("../classes/AE_cls_fpdf.php");
include_once("../classes/AE_cls_progressbar.php");
include_once("../classes/selectOptieClass.php");


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
	include_once("rapport/rapportVertaal.php");
	include_once("rapport/rapportRekenClass.php");
	include_once("rapport/PDFOverzicht.php");
	include_once("rapport/MutatievoorstelFondsen.php");
	include_once("rapport/Fondsen.php");
	include_once("rapport/Geaggregeerdoverzicht.php");
	include_once("rapport/Modelcontrole.php");
	include_once("rapport/ModelWaardecontrole.php");
	include_once("rapport/Modelrapport.php");
	include_once("rapport/PDFRapport.php");
	include_once("rapport/RapportMOD.php");
	include_once("rapport/Fondslijst.php");
	include_once("rapport/KostprijsMutatieverloop.php");
	include_once("rapport/Fondsverloop.php");
  include_once("rapport/Obligatieoverzicht.php");
	include_once("rapport/RapportWaardeprognose.php");
	include_once("rapport/RapportDoorkijkFondsselectie.php");

	if(!empty($_POST['datumTm']))
	{
		$dd = explode($__appvar["date_seperator"],$_POST['datumTm']);
		if(!checkdate(intval($dd[1]),intval($dd[0]),intval($dd[2])))
		{
			echo "<b>Fout: ongeldige datum opgegeven!</b>";
			exit;
		}
		else
		{
		  $rapJul=form2jul($_POST['datumTm']);
    	$valutaDatum = getLaatsteValutadatum();
      $valutaJul = db2jul($valutaDatum);
    	if($rapJul > $valutaJul + 86400)
	    {
		    echo "<b>Fout: kan niet in de toekomst rapporteren.</b>";
		    exit;
	    }
		}
	}
	else
	{
		echo "<b>Fout: geen datum opgegeven!</b>";
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
		case "Mutatievoorstel Fondsen" :
			$rapport = new MutatievoorstelFondsen( $selectData );
			$rapport->USR = $USR;
			$rapport->progressbar = &$prb;
			$rapport->__appvar = $__appvar;
			$rapport->writeRapport();
			$rapportnaam = $__appvar["bedrijf"]."_MUT";
		break;
		case "Fondsoverzicht" :
			$rapport = new Fondsen( $selectData );
			$rapport->USR = $USR;
			$rapport->progressbar = &$prb;
			$rapport->__appvar = $__appvar;
			$rapport->writeRapport();
			$rapportnaam = $__appvar["bedrijf"]."_FON";
		break;
		case "Geaggregeerd Portefeuille Overzicht" :
			$rapport = new Geaggregeerdoverzicht( $selectData );
			$rapport->USR = $USR;
			$rapport->progressbar = &$prb;
			$rapport->__appvar = $__appvar;
			$rapport->writeRapport();
			$rapportnaam = $__appvar["bedrijf"]."_GPO";
		break;
		case "Obligatie overzicht" :
			$rapport = new Obligatieoverzicht( $selectData );
			$rapport->USR = $USR;
			$rapport->progressbar = &$prb;
			$rapport->__appvar = $__appvar;
			$rapport->writeRapport();
			$rapportnaam = $__appvar["bedrijf"]."_OO";
		break;
		case "Modelcontrole" :
		  if($selectData['modelcontrole_rapport']=='gecomprimeerd')
		    $rapport = new ModelWaardecontrole( $selectData );
		  else
			  $rapport = new Modelcontrole( $selectData );
			$rapport->USR = $USR;
			$rapport->progressbar = & $prb;
			$rapport->__appvar = $__appvar;
			$rapport->writeRapport();
			$rapportnaam = $__appvar["bedrijf"]."_MOD";
		break;
		case "MutatievoorstelPortefeuille" :
			$rapport = new Modelrapport( $selectData );
			$rapport->USR = $USR;
			$rapport->progressbar = & $prb;
			$rapport->__appvar = $__appvar;
			$rapport->writeRapport();
			$rapportnaam = $__appvar["bedrijf"]."_MOD";
		break;
		case "WaardeprognosePortefeuille" :
			if ($layout > 0 && file_exists("rapport/include/RapportWaardeprognose_L" . $layout . ".php"))
			{
				include("rapport/include/RapportWaardeprognose_L" . $layout . ".php");
				$rapClass="RapportWaardeprognose_L".$layout;
				$rapport = new $rapClass( $selectData );
			}
			else
			{
				$rapport = new RapportWaardeprognose( $selectData );
			}

			$rapport->USR = $USR;
			$rapport->progressbar = & $prb;
			$rapport->__appvar = $__appvar;
			$rapport->writeRapport();
			$rapportnaam = $__appvar["bedrijf"]."_WPP";
		break;
		case "doorkijkFondsselectie" :
			$rapport = new RapportDoorkijkFondsselectie( $selectData );
			$rapport->USR = $USR;
			$rapport->progressbar = & $prb;
			$rapport->__appvar = $__appvar;
			$rapport->writeRapport();
			$rapportnaam = $__appvar["bedrijf"]."_DFS";
			break;
	  case "Fondsenlijst" :
			$rapport = new Fondslijst( $selectData );
			$rapport->USR = $USR;
			$rapport->progressbar = & $prb;
			$rapport->__appvar = $__appvar;
			$rapport->writeRapport();
			$rapportnaam = date('Y-m-d').'_'.$__appvar["bedrijf"]."_FLI_";
		break;
		case "KostprijsMutatieverloop" :
			$rapport = new KostprijsMutatieverloop( $selectData );
			$rapport->USR = $USR;
			$rapport->progressbar = & $prb;
			$rapport->__appvar = $__appvar;
			$rapport->writeRapport();
			$rapportnaam = $__appvar["bedrijf"]."_MOD";
		break;
		case "Fondsverloop" :
			$rapport = new Fondsverloop( $selectData );
			$rapport->USR = $USR;
			$rapport->progressbar = & $prb;
			$rapport->__appvar = $__appvar;
			$rapport->writeRapport();
			$rapportnaam = $__appvar["bedrijf"]."_FON";
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
			$filename = $rapportnaam.".csv";
			$filetype = "csv";
			$rapport->pdf->OutputCSV($__appvar['tempdir'].$filename,"F");
		break;
		case "xls" :
			if(class_exists('XMLWriter')) //$__appvar["bedrijf"]=='TEST')
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
    parent.AEConfirm('<?=$tmpOrdernr['message']?> Wilt u naar de orderregels gaan?', 'Orderregel verwerking', 
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
		alert('Er zijn geen orderregels aangemaakt binnen deze selectie.');
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
$html .="<br>";
if(method_exists($selectie,'getConsolidatieHTML'))
  $html.=$selectie->getConsolidatieHTML($PHP_SELF);
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


	$koppelObject[3] = new Koppel("Fondsen","selectForm",$positieJoin);
	$koppelObject[3]->addFields($fondsPrefix."Fonds","aankoopFonds",false,true);
	$koppelObject[3]->addFields($fondsPrefix."ISINCode","",true,true);
	$koppelObject[3]->addFields($fondsPrefix."Omschrijving","",true,true);
	$koppelObject[3]->name = "aankoopFonds";
	$koppelObject[3]->extraQuery = $alleenActief;

?>


	<script language=JavaScript src="javascript/popup.js" type=text/javascript></script>


	<script type="text/javascript">

	<?=$koppelObject[0]->getJavascript()?>
  <?=$koppelObject[1]->getJavascript()?>
  <?=$koppelObject[2]->getJavascript()?>
	<?=$koppelObject[3]->getJavascript()?>

<?=$selectie->getSelectJava();?>


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

	function database()
	{
		document.selectForm.target = "generateFrame";
		document.selectForm.filetype.value="database";
		document.selectForm.save.value="1";
		selectSelected();
		if (checkfield())
		document.selectForm.submit();
	}

	function order()
	{
    <?php
    if($orderPreValidatie==2)
    {
    ?>  
	  AEConfirm('Order validatie gelijk uitvoeren?', 'Order validatie', 
    function () 
    {
      document.selectForm.extra.value = "validatieUitvoeren";
      submitOrder();
    }, function () 
    {
      document.selectForm.extra.value = "";
      submitOrder();
    })
    <?php
    }
    elseif($orderPreValidatie==1)
    {
      ?>
      document.selectForm.extra.value = "validatieUitvoeren";
      submitOrder();   
      <?
    }
    else
    {
      ?>
      document.selectForm.extra.value = "";
      submitOrder();
      <?
    }
    ?>
	}
  
  function submitOrder()
  {
    
    document.selectForm.target = "generateFrame";
  	document.selectForm.filetype.value="order";
  	selectSelected();
  	if (checkfield())
    	document.selectForm.submit();
  }
  
	function checkfield()
	{

	  //check of velden gevuld
	  if(document.selectForm.soort.selectedIndex == 2 && document.selectForm.fonds.value == '---')
	  {
      if (document.selectForm.newFonds.value == '' ||
          document.selectForm.newFondsValutaCode.value == '' ||
          document.selectForm.newFondsEenheid.value == '' ||
          document.selectForm.newFondsKoers.value == '' ||
          document.selectForm.newFondsValutaKoers.value == '' )
          {
	          alert('Niet alle vereiste velden zijn gevuld.');
	          return false;
          }
	  }

		if(document.selectForm.soort.selectedIndex == 3)
		{
			if($('input[name=modelcontrole_rapport]:checked').val()=='vastbedrag')
			{
				var bedrag=$('[name=modelcontrole_vastbedrag]').val();
				if(bedrag=='' || parseFloat(bedrag)==0.0)
				{
					alert('Het bedrag is nog niet opgegeven.');
					return false;
				}
			}
		}

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
    if(document.selectForm['selectedModelportefeuilles[]'])
    {
      var selectedFields 	= document.selectForm['selectedModelportefeuilles[]'];
      for(j=0; j < selectedFields.options.length; j++)
      {
        selectedFields.options[j].selected = true;
      }
    }
	  <?=$selectie->getPortefeuilleInternJava()?>
		<?
		if(method_exists($selectie,'getConsolidatieJava'))
			echo $selectie->getConsolidatieJava();
		?>
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
    document.getElementById('databaseButton').style.visibility="hidden";

    document.getElementById('fondsverloop').style.visibility="hidden";
		document.getElementById('WaardeprognosePortefeuille').style.visibility="hidden";
		//document.getElementById('fondsenBewaarderDiv').style.visibility="hidden";
		document.getElementById('DivRapportDoorkijkFondsselectie').style.visibility="hidden";
		document.getElementById('fondsButtonNieuw').style.visibility="hidden";
		document.getElementById('fondsButtonExtra').style.visibility="hidden";
		$('#Smash').hide();
		$('#TransactieTypeDiv').hide();
		$('#fondsVerkoopSpan').hide();

		if(document.selectForm.soort.selectedIndex == 1 || document.selectForm.soort.selectedIndex== 2)
		{
			document.getElementById('Mutatievoorstel').style.visibility="visible";
			document.getElementById('fondsButtonNieuw').style.visibility="visible";
			document.getElementById('fondsButtonExtra').style.visibility="visible";

			document.getElementById('sm').style.visibility="hidden";
			document.getElementById('Modelcontrole').style.visibility="hidden";
			document.getElementById('MutatievoorstelPortefeuille').style.visibility="hidden";
			document.getElementById('csvButton').style.visibility="visible";
			document.getElementById('xlsButton').style.visibility="visible";
			document.getElementById('PortefueilleSelectie').style.visibility="visible";
			document.getElementById('KostprijsMutatieverloop').style.visibility="hidden";


			for (var i=0; i < document.selectForm.transactieType.length; i++)
			{
				if (document.selectForm.transactieType[i].checked)
				{
					var rad_val = document.selectForm.transactieType[i].value;
				}
			}
			if(rad_val=='enkelvoudig')
			{
				mutatieEnkel(rad_val);
			}
			else
			{
				mutatieSwitch(rad_val);
			}


			if(document.selectForm.soort.selectedIndex == 2)
			{
				$('#TransactieTypeDiv').show();
				document.getElementById('sm').style.visibility="visible";
        <?if (GetModuleAccess("ORDER") && ($_SESSION['usersession']['gebruiker']['ordersNietAanmaken']==0 || checkOrderAcces ('rapportages_aanmaken') == true)){?>
				document.getElementById('orderButton').style.visibility="visible";
        <?}?>
			}
			else
			{
				//document.getElementById('fondsenBewaarderDiv').style.visibility="visible";
        document.getElementById('databaseButton').style.visibility="visible";
				document.getElementById('orderButton').style.visibility="hidden";
			}

		}
		else if( document.selectForm.soort.selectedIndex== 3 )
		{
			document.getElementById('Modelcontrole').style.visibility="visible";
			document.getElementById('sm').style.visibility="hidden";
			document.getElementById('Mutatievoorstel').style.visibility="hidden";
			document.getElementById('MutatievoorstelPortefeuille').style.visibility="hidden";
			document.getElementById('csvButton').style.visibility="visible";
			document.getElementById('xlsButton').style.visibility="visible";
			document.getElementById('orderButton').style.visibility="hidden";
			document.getElementById('PortefueilleSelectie').style.visibility="visible";
			document.getElementById('KostprijsMutatieverloop').style.visibility="hidden";
      <?if (GetModuleAccess("ORDER") && ($_SESSION['usersession']['gebruiker']['ordersNietAanmaken']==0 || checkOrderAcces ('rapportages_aanmaken') == true)){?>
			document.getElementById('orderButton').style.visibility="visible";
      <?}?>
		}
		else if( document.selectForm.soort.selectedIndex== 4 )
		{
			document.getElementById('MutatievoorstelPortefeuille').style.visibility="visible";
			document.getElementById('sm').style.visibility="hidden";
			document.getElementById('Modelcontrole').style.visibility="hidden";
			document.getElementById('Mutatievoorstel').style.visibility="hidden";
			document.getElementById('csvButton').style.visibility="visible";
			document.getElementById('xlsButton').style.visibility="visible";
			document.getElementById('orderButton').style.visibility="hidden";
			document.getElementById('PortefueilleSelectie').style.visibility="hidden";
			document.getElementById('KostprijsMutatieverloop').style.visibility="hidden";
		}
		else if( document.selectForm.soort.selectedIndex== 5 )
		{
			document.getElementById('MutatievoorstelPortefeuille').style.visibility="hidden";
			document.getElementById('KostprijsMutatieverloop').style.visibility="hidden";
			document.getElementById('sm').style.visibility="hidden";
			document.getElementById('Modelcontrole').style.visibility="hidden";
			document.getElementById('Mutatievoorstel').style.visibility="hidden";
			document.getElementById('csvButton').style.visibility="visible";
			document.getElementById('xlsButton').style.visibility="visible";
			document.getElementById('afdrukkenButton').style.visibility="hidden";
			document.getElementById('opslaanButton').style.visibility="hidden";
			document.getElementById('orderButton').style.visibility="hidden";
			document.getElementById('PortefueilleSelectie').style.visibility="visible";
		}
		else if( document.selectForm.soort.selectedIndex== 6 )
		{
			document.getElementById('MutatievoorstelPortefeuille').style.visibility="hidden";
			document.getElementById('KostprijsMutatieverloop').style.visibility="visible";
			document.getElementById('sm').style.visibility="hidden";
			document.getElementById('Modelcontrole').style.visibility="hidden";
			document.getElementById('Mutatievoorstel').style.visibility="hidden";
			document.getElementById('csvButton').style.visibility="visible";
			document.getElementById('xlsButton').style.visibility="visible";
			document.getElementById('orderButton').style.visibility="hidden";
			document.getElementById('PortefueilleSelectie').style.visibility="visible";
		}
		else if( document.selectForm.soort.selectedIndex== 7 )
		{
			document.getElementById('MutatievoorstelPortefeuille').style.visibility="hidden";
			document.getElementById('KostprijsMutatieverloop').style.visibility="hidden";
      document.getElementById('fondsverloop').style.visibility="visible";
			document.getElementById('sm').style.visibility="hidden";
			document.getElementById('Modelcontrole').style.visibility="hidden";
			document.getElementById('Mutatievoorstel').style.visibility="hidden";
			document.getElementById('csvButton').style.visibility="visible";
			document.getElementById('xlsButton').style.visibility="visible";
			document.getElementById('orderButton').style.visibility="hidden";
			document.getElementById('PortefueilleSelectie').style.visibility="visible";
		}
    else if( document.selectForm.soort.selectedIndex== 8 )
		{
			document.getElementById('MutatievoorstelPortefeuille').style.visibility="hidden";
			document.getElementById('KostprijsMutatieverloop').style.visibility="hidden";
      document.getElementById('fondsverloop').style.visibility="hidden";
			document.getElementById('sm').style.visibility="hidden";
			document.getElementById('Modelcontrole').style.visibility="hidden";
			document.getElementById('Mutatievoorstel').style.visibility="hidden";
			document.getElementById('csvButton').style.visibility="visible";
			document.getElementById('xlsButton').style.visibility="visible";
			document.getElementById('afdrukkenButton').style.visibility="hidden";
			document.getElementById('opslaanButton').style.visibility="hidden";
			document.getElementById('orderButton').style.visibility="hidden";
			document.getElementById('PortefueilleSelectie').style.visibility="visible";
		}
		else if( document.selectForm.soort.selectedIndex== 9 )
		{
			document.getElementById('MutatievoorstelPortefeuille').style.visibility="hidden";
			document.getElementById('KostprijsMutatieverloop').style.visibility="hidden";
			document.getElementById('fondsverloop').style.visibility="hidden";
			document.getElementById('sm').style.visibility="hidden";
			document.getElementById('Modelcontrole').style.visibility="hidden";
			document.getElementById('Mutatievoorstel').style.visibility="hidden";
			document.getElementById('csvButton').style.visibility="visible";
			document.getElementById('xlsButton').style.visibility="visible";
			//document.getElementById('afdrukkenButton').style.visibility="hidden";
			//document.getElementById('opslaanButton').style.visibility="hidden";
			document.getElementById('orderButton').style.visibility="hidden";
			document.getElementById('PortefueilleSelectie').style.visibility="visible";
			document.getElementById('WaardeprognosePortefeuille').style.visibility="visible";

		}
		else if(document.selectForm.soort.selectedIndex== 10)
		{
			document.getElementById('DivRapportDoorkijkFondsselectie').style.visibility="visible";
			document.getElementById('Mutatievoorstel').style.visibility="visible";
			$('#fondsenSelectieKader').hide();
			$('#fondsAankoopSpan').hide();
			document.getElementById('xlsButton').style.visibility="visible";
			document.getElementById('afdrukkenButton').style.visibility="visible";

		}
		else
		{
		  if(document.selectForm.soort.selectedIndex == 0 )
	    {
        document.getElementById('databaseButton').style.visibility="visible";
	    }
			document.getElementById('Modelcontrole').style.visibility="hidden";
			document.getElementById('sm').style.visibility="hidden";
			document.getElementById('Mutatievoorstel').style.visibility="hidden";
			document.getElementById('MutatievoorstelPortefeuille').style.visibility="hidden";
			document.getElementById('csvButton').style.visibility="visible";
			document.getElementById('xlsButton').style.visibility="visible";
			document.getElementById('orderButton').style.visibility="hidden";
			document.getElementById('PortefueilleSelectie').style.visibility="visible";
			document.getElementById('KostprijsMutatieverloop').style.visibility="hidden";
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

	function appendFonds(selectieVeld)
	{
		var elOptNew = document.createElement('option');
		var fonds=document.selectForm.fonds.value;
		var fondsPercentage=parseFloat(document.getElementById('fondsPercentage').value);
		var oudeSom=parseFloat(document.getElementById('fondsPercentageSom').value);
	  if((oudeSom+fondsPercentage)>100)
		{
			fondsPercentage=100-oudeSom;
		}
		if(oudeSom>=100)
		{
			fondsPercentage = 0;
		}
//console.log('fonds: '+fonds+' p'+fondsPercentage+' o'+oudeSom);
		if(fonds != '' && fondsPercentage > 0)
		{
			elOptNew.text = fondsPercentage + ' - ' + fonds;
			elOptNew.value = fondsPercentage + '|' + fonds;
			var elSel = document.getElementById(selectieVeld);
			try
			{
				//console.log('add'+selectieVeld);
				elSel.add(elOptNew, null); // standards compliant;
			}
			catch (ex)
			{
				//console.log('add ie '+selectieVeld);
				elSel.add(elOptNew); // IE only
			}
			if(selectieVeld=='selectedFondsen')
			{
				fondsPercentageSomBepalen();
			}
		}
	}
	function removeFonds(selectieVeld)
	{
		var elSel = document.getElementById(selectieVeld);
		if (elSel.length > 0)
		{
			elSel.remove(elSel.length - 1);
		}
		if(selectieVeld=='selectedFondsen')
		{
			fondsPercentageSomBepalen();
		}
	}
	function fondsPercentageSomBepalen()
	{
		var elSel = document.getElementById('selectedFondsen');
		var i=0;
		var som=0;
		if (elSel.length > 0)
		{
			for(i=0;i<elSel.length;i++)
			{
				var parts=elSel.options[i].value.split('|');
				som+=parseFloat(parts[0]);

			}
			document.getElementById('fondsPercentageSom').value=som;
		}
		else
		{
			document.getElementById('fondsPercentageSom').value=0;
		}

		var inFields  			= document.selectForm['selectedFondsen[]'];
		for(i=0; i < inFields.options.length; i++)
		{
			inFields.options[i].selected = true;
		}
	}
  
  function appendPortefeuille(selectieVeld)
  {
    var elOptNew = document.createElement('option');
    var portefeuille=document.selectForm.mutatieportefeuille_portefeuille.value;

    var portefeuillePercentage=parseFloat(document.getElementById('portefeuillePercentage').value);
    var oudeSom=parseFloat(document.getElementById('portefeuillePercentageSom').value);
    if((oudeSom+portefeuillePercentage)>100)
    {
      portefeuillePercentage=100-oudeSom;
    }
    if(oudeSom>=100)
    {
      portefeuillePercentage = 0;
    }
//console.log('fonds: '+fonds+' p'+fondsPercentage+' o'+oudeSom);
    if(portefeuille != '' && portefeuillePercentage > 0)
    {
      elOptNew.text = portefeuillePercentage + ' - ' + portefeuille;
      elOptNew.value = portefeuillePercentage + '|' + portefeuille;
      var elSel = document.getElementById(selectieVeld);
      try
      {
        //console.log('add'+selectieVeld);
        elSel.add(elOptNew, null); // standards compliant;
      }
      catch (ex)
      {
        //console.log('add ie '+selectieVeld);
        elSel.add(elOptNew); // IE only
      }
      if(selectieVeld=='selectedModelportefeuilles')
      {
        portefeuillePercentageSomBepalen();
      }
    }
  }
  function removePortefeuille(selectieVeld)
  {
    var elSel = document.getElementById(selectieVeld);
    if (elSel.length > 0)
    {
      elSel.remove(elSel.length - 1);
    }
    if(selectieVeld=='selectedModelportefeuilles')
    {
      portefeuillePercentageSomBepalen();
    }
  }
  
  function portefeuillePercentageSomBepalen()
  {
    var elSel = document.getElementById('selectedModelportefeuilles');
    var i=0;
    var som=0;
    if (elSel.length > 0)
    {
      for(i=0;i<elSel.length;i++)
      {
        var parts=elSel.options[i].value.split('|');
        som+=parseFloat(parts[0]);
        
      }
      document.getElementById('portefeuillePercentageSom').value=som;
    }
    else
    {
      document.getElementById('portefeuillePercentageSom').value=0;
    }
    
    var inFields  			= document.selectForm['selectedModelportefeuilles[]'];
    for(i=0; i < inFields.options.length; i++)
    {
      inFields.options[i].selected = true;
    }
  }

	function mutatieEnkel(rad_val)
	{
		$('#fondsVerkoopSpan').hide();
		$('#Smash').show();
		$('#fondsAankoopSpan').hide();
		$('#VoorstelSelectie').show();
		$('#fondsenSelectieKader').hide();
		editSmash(rad_val);
		fondsChange();
	}

	function mutatieSwitch(rad_val)
	{
		$('#fondsVerkoopSpan').show();
		$('#Smash').hide();
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
		$('#Smash').hide();
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

<br><br>
<div class="tabbuttonRow">
	<input type="button" class="tabbuttonInActive" onclick="javascript:document.location = 'rapportFrontofficeClientSelectieold.php';" id="tabbutton0" value="Clienten">
	<input type="button" class="tabbuttonActive" onclick="" id="tabbutton1" value="Fondsen">
	<input type="button" class="tabbuttonInActive" onclick="javascript:document.location = 'rapportFrontofficeManagementSelectieold.php';" id="tabbutton2" value="Management info">
	<input type="button" class="tabbuttonInActive" onclick="javascript:document.location = 'rapportFrontofficeOptieToolsold.php';" id="tabbutton3" value="Optie tools">
	<input type="button" class="tabbuttonInActive" onclick="javascript:document.location = 'rapportFrontofficeConsolidatieSelectieold.php';" id="tabbutton4" value="Consolidatie tool">
</div>
<br>

<form action="<?=$PHP_SELF?>" method="POST" target="_blank" name="selectForm">
<input type="hidden" name="posted" value="true" />
<input type="hidden" name="save" value="" />
<input type="hidden" name="rapport_types" value="" />
<input type="hidden" name="filetype" value="PDF" />
<input type="hidden" name="portefeuilleIntern" value="" />
<input type="hidden" name="metConsolidatie" value="<?=$_SESSION['metConsolidatie']?>" />
<input type="hidden" name="extra" value="" />

<table border="0">
<tr>
	<td width="540">

<fieldset id="Rapport" >
<legend accesskey="R"><u>R</u>apport</legend>

<div class="formblock">
<div class="formlinks"> Rapport </div>
<div class="formrechts">

<select name="soort" style="width:200px" onChange="selectTab();">
	<option value="Geaggregeerd Portefeuille Overzicht">Geaggregeerd Portefeuille Overzicht</option>
	<option value="Fondsoverzicht" <?=($_GET[selectRapport]=="Fondsoverzicht"?"selected":"")?>>Fondsoverzicht</option>
	<option value="Mutatievoorstel Fondsen">Mutatievoorstel Fondsen</option>
	<option value="Modelcontrole">Modelcontrole</option>
	<option value="MutatievoorstelPortefeuille">Mutatievoorstel Portefeuille</option>
	<option value="Fondsenlijst">Fondsenlijst</option>
	<option value="KostprijsMutatieverloop">Kostprijs Mutatieverloop</option>
  <option value="Fondsverloop">Fondsverloop</option>
  <option value="Obligatie overzicht">Obligatie overzicht</option>
	<option value="WaardeprognosePortefeuille">Waardeprognose portefeuille</option>
	<option value="doorkijkFondsselectie">Doorkijk fondsselectie</option>
</select>
</div>
</div>
<?
echo $selectie->createDatumSelectie();
?>
</fieldset>

<div id="PortefueilleSelectie" style="">
<fieldset id="Selectie" >
<legend accesskey="S"><u>S</u>electie</legend>
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
    $data[$gb['Portefeuille']]=$gb;
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

<div class="buttonDiv" id="afdrukkenButton" style="width:130px" onclick="javascript:print();">&nbsp;&nbsp;<?=maakKnop('pdf.png',array('size'=>16))?> Afdrukken</div><br>
<div class="buttonDiv" id="opslaanButton" style="width:130px" onclick="javascript:saveasfile();">&nbsp;&nbsp;<?=maakKnop('disk_blue.png',array('size'=>16))?> Opslaan </div><br>
<div class="buttonDiv" id="csvButton" style="width:130px" onclick="javascript:csv();">&nbsp;&nbsp;<?=maakKnop('csv.png',array('size'=>16))?> CSV-export </div><br>
<div class="buttonDiv" id="xlsButton" style="width:130px" onclick="javascript:xls();">&nbsp;&nbsp;<?=maakKnop('xls.png',array('size'=>16))?> XLS-export </div><br>
<div class="buttonDiv" id="databaseButton" style="width:130px" onclick="javascript:database();">&nbsp;&nbsp;<?=maakKnop('table.png',array('size'=>16))?> Reportbuilder </div><br>
<?
if (checkOrderAcces('rapportages_aanmaken') == true || GetModuleAccess('ORDER') < 2)
 echo '<div class="buttonDiv" id="orderButton" style="width:130px; visibility: hidden;" onclick="javascript:order();">&nbsp; Genereer orders</div><br>';
else
 echo '<div class="buttonDiv" id="orderButton" style="width:150px; visibility: hidden;" >&nbsp; Geen order rechten</div><br>';
?>


	<?php if ( checkOrderAcces ('rapportages_aanmaken') == true ) { ?>
		<div class="buttonDiv" id = "orderButton" style = "width:130px; visibility: hidden;" onclick = "javascript:order();" >&nbsp; Genereer orders </div ><br >
	<?php } ?>




	<div id="TransactieTypeDiv">
		<fieldset id="TransactieTypeFieldset">
			<div class="formblock">
				<div class="formlinks">
					<u>Order type</u><br>
					<input type="radio" name="transactieType" value="enkelvoudig" checked onClick="javascript:mutatieEnkel();"> Enkelvoudige order <br>
					<input type="radio" name="transactieType" value="switch" onClick="javascript:mutatieSwitch();">  Switch order  <br>
		<!--			<input type="radio" name="transactieType" value="meervoudig" disabled onClick="javascript:mutatieMeer();"> Meervoudige order -->
				</div>
			</div>
		</fieldset>
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
  $getFonds=urlencode(base64_encode(gzcompress("SELECT Fonds, Omschrijving FROM Fondsen WHERE 1=1 ".$alleenActief." ORDER BY Omschrijving")));
?>


	<div id="Mutatievoorstel" style="visibility : hidden; position:absolute;">
		<fieldset id="VoorstelSelectie">
			<legend accesskey="e">S<u>p</u>ecificatie</legend>


			<div id="Fondsoverzicht">
					<div class="formblock">
						<div class="formlinks"> <span id="fondsVerkoopSpan"> Verkoop </span> Fonds
							<a href="javascript:getAjaxWaarden('<?=$getFonds?>','',document.getElementById('fonds').name);select_fonds(document.selectForm.newFondsISIN.value);">
								<img src="images/16/lookup.gif" border="0" height="18" align="middle"></a>
						</div>
						<div class="formrechts" id="div_fonds">
							<select name="fonds" id="fonds" style="width:200px" onfocus="javascript:getAjaxWaarden('<?=$getFonds?>','',this.name);" onchange="javascript:fondsChange();">
								<option value="">---</option>
								<?=$fondsen?>
							</select>
						</div>
					</div>
			</div>


			<span id="fondsAankoopSpan">
      <div class="formblock">
      <div class="formlinks"> Aankoop Fonds
        <a href="javascript:getAjaxWaarden('<?=$getFonds?>','',document.getElementById('aankoopFonds').name);select_aankoopFonds('');">
        <img src="images/16/lookup.gif" border="0" height="18" align="middle"></a>
      </div>
      <div class="formrechts" id="div_aankoopFonds">
        <select name="aankoopFonds" id="aankoopFonds" style="width:200px" onfocus="javascript:getAjaxWaarden('<?=$getFonds?>','',this.name);" onchange="javascript:fondsChange();">
					<option value="">---</option>
        <?=$fondsen?>
        </select>
      </div>
      </div>
    </span>


			<div class="formblock">




<br><br>
<div id="wrapper" style="overflow:hidden;width:=400px;"> 
<div class="buttonDiv" id="fondsButtonNieuw" style="width:120px;float:left;text-align: center;" onclick="$('#newFondsDiv').toggle();"> Nieuw fonds </div>
<div class="buttonDiv" id="fondsButtonExtra" style="width:120px;float:left;text-align: center;" onclick="$('#mutatieVoorstelOptieDiv').toggle();"> Extra </div>
</div>
</div>
<div id="newFondsDiv" style="display: none;">
<div class="formblock">
<div class="formlinks"> Nieuwe fonds naam </div>
<div class="formrechts">
<input type="text" name="newFonds" id="newFonds">
</div>
</div>

<div class="formblock">
<div class="formlinks"> Fonds ISIN code </div>
<div class="formrechts">
<input type="text" name="newFondsISIN" id="newFondsISIN">
</div>
</div>

<div class="formblock">
<div class="formlinks"> Fonds koers </div>
<div class="formrechts">
<input type="text" name="newFondsKoers" id="newFondsKoers" size="5">
</div>
</div>

<div class="formblock">
<div class="formlinks"> Fonds valuta koers </div>
<div class="formrechts">
<input type="text" name="newFondsValutaKoers" id="newFondsValutaKoers" size="5" >
</div>
</div>

<div class="formblock">
<div class="formlinks"> Fonds valuta code </div>
<div class="formrechts">

<select name="newFondsValutaCode" id="newFondsValutaCode" style="width:200px" >
<?=$valutaCode?>
</select>
</div>
</div>

<div class="formblock">
<div class="formlinks"> Fonds eenheid </div>
<div class="formrechts">
<select name="newFondsEenheid" id="newFondsEenheid" style="width:200px;" >
<?=$fondseenheid?>
</select>
</div>
</div>
</div>

<div id="mutatieVoorstelOptieDiv" style="display: none;">
<div class="formblock">
<div class="formlinks"> &nbsp; </div>
<div class="formrechts">
<input type="radio" name="actief" id="actief" value="actief" <?=$actiefChecked?> onClick="document.location = '<?=$PHP_SELF?>?actief=actief&selectRapport=Fondsoverzicht'">
<label for="actief" title="actief"> Actieve fondsen  </label>
	<input type="radio" name="actief" id="positie" value="positie" <?=$positieChecked?> onClick="document.location = '<?=$PHP_SELF?>?actief=positie&selectRapport=Fondsoverzicht'">
	<label for="positie" title="actief"> In positie  </label>
<input type="radio" name="actief" id="inactief" value="inactief" <?=$inactiefChecked?> onClick="document.location = '<?=$PHP_SELF?>?actief=inactief&selectRapport=Fondsoverzicht'">
<label for="inactief" title="actief"> Alle fondsen </label>
</div>
</div>


<?if($bewaarder['OrderuitvoerBewaarder']==1){?>
<div class="formblock">
<div class="formlinks">Order voorkeur depotbank</div>
<div class="formrechts">
<select name="orderDepotbank" style="width:200px">
<?=$depotbankOptions?>
</select>
</div>
</div>
<?
}
?>


<div class="formblock">
<div class="formlinks"> Afronding </div>
<div class="formrechts">
<input type="text" name="afronding" value="1" size="5">
</div>
</div>

<div class="formblock">
<div class="formlinks"> Berekeningswijze </div>
<div class="formrechts">
<select name="berekeningswijze">
	<option value="Totaal vermogen">Totaal vermogen</option>
	<option value="Totaal belegd vermogen">Totaal belegd vermogen</option>
	<option value="Belegd vermogen per beleggingscategorie">Belegd vermogen per beleggingscategorie</option>
</select>
</div>
</div>

<div class="formblock">
<div class="formlinks"> Via norm</div>
<div class="formrechts">
	<input type="checkbox" value="1" name="berekeningswijzeViaNorm">
</div>
</div>


<div class="formblock">
<div class="formlinks"> Deposito's uitsluiten </div>
<div class="formrechts">
	<input type="checkbox" value="1" name="depositoUitsluiten">
</div>
</div>



<div class="formblock">
<div class="formlinks"> Opties weergeven </div>
<div class="formrechts">
	<input type="checkbox" value="1" name="optiesWeergeven">
</div>
</div>

<div class="formblock">
<div class="formlinks"> Portrait versie</div>
<div class="formrechts">
	<input type="checkbox" value="1" name="portraitVersie" <?if($layout == 13)echo "CHECKED";?> >
</div>
</div>

		<div class="formblock">
			<div class="formlinks"> Uitvoer op bewaarder </div>
			<div class="formrechts">
				<input type="checkbox" value="1" name="fondsenOpBewaarder">
			</div>
		</div>
</div>

</fieldset>

		<fieldset id="fondsenSelectieKader">
			<!--
			<table>
				<tr>
					<td>Percentage<input type="text" id="fondsPercentage" name="fondsPercentage" align="right" value="0.0"></td>
					<td rowspan=4><select id="mutatieVoorstelselectedFondsen" name="selectedFondsen[]" multiple size="8" style="width : 200px"></td>
				</tr>
				<tr><td><input type="checkbox" name="norm" value="1" onclick="editSmash();">Berekening volgens norm</td></tr>
				<tr><td><input type="button" value="Fonds toevoegen." onclick="javascript:appendFonds('mutatieVoorstelselectedFondsen');"></td></tr>
				<tr><td><input type="button" value="Fonds verwijderen." onclick="javascript:removeFonds('mutatieVoorstelselectedFondsen');"></td></tr>
			</table>
			-->
		</fieldset>

<div id="sm" style="visibility : hidden; position:absolute;">

<fieldset id="Smash" >
<legend accesskey="m">S<u>m</u>ash</legend>
<?

$DB = new DB();
$query = "SELECT ModelPortefeuilles.Portefeuille,
				 ModelPortefeuilles.Omschrijving
		  FROM ModelPortefeuilles
		  LEFT JOIN Portefeuilles on Portefeuilles.Portefeuille = ModelPortefeuilles.Portefeuille ".$join ." WHERE Portefeuilles.Einddatum>now() ORDER BY ModelPortefeuilles.Omschrijving";

$DB->SQL($query);
$DB->Query();
$aantal = $DB->records();
$t=0;

while($gb = $DB->NextRecord())
{
	$t++;
	$Modelportefeuilles .= "<option value=\"".$gb['Portefeuille']."\" >".$gb['Omschrijving']."</option>\n";
}

$query="SELECT Risicoklasse FROM Risicoklassen JOIN VermogensbeheerdersPerGebruiker ON Risicoklassen.Vermogensbeheerder=VermogensbeheerdersPerGebruiker.Vermogensbeheerder 
WHERE VermogensbeheerdersPerGebruiker.Gebruiker='$USR'";
$risicoklassen='';
$DB->SQL($query);
$DB->Query();
while($gb = $DB->NextRecord())
{
	$risicoklassen .= "<option value=\"".$gb['Risicoklasse']."\" >".$gb['Risicoklasse']."</option>\n";
}


if($DB->QRecords("SELECT CategorienPerHoofdcategorie.id FROM CategorienPerHoofdcategorie JOIN VermogensbeheerdersPerGebruiker ON CategorienPerHoofdcategorie.vermogensbeheerder=VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker='$USR' limit 1"))
  $hoofdcategorie='<input type="radio" name="modelcontrole_level" value="hoofdcategorie" >Hoofdcategorie<br>';
?>
<script>
	function moveCheckSmash()
	{
		if(document.getElementById('typeModel').checked==true)
		{
			return true;
		}
		else
		{
			alert('Alleen mogelijk bij "Via model".');
			return false;
		}

	}
	function moveBack()
	{
		moveItem(document.selectForm['modelportefeuille[]'],document.selectForm['inModelportefeuille[]'],true);
	}


</script>
<div class="formblock">
	<input type="radio" name="type" onclick="moveBack();" id="typeHandmatig" value="Handmatig" checked> Handmatig &nbsp;	Percentage: <input type="text" onChange="javascript:checkAndFixNumber(this);" name="percentage" value="0.0" size="4"> <input type="checkbox" value="1" name="nulUitlsuiten"> Aantal 0 niet tonen  <br><br>
	<input type="radio" name="type" id="typeModel" value="Model"> Via model &nbsp;
	Modelportefeuille:
	<!--
	<select name="modelportefeuille">
	<option value="">-</option>
<?
  if ($t <> 0)
    echo "<option value=\"Allemaal\">Allemaal</option>";
?>


	</select>
-->

	<table cellspacing="0" >
<tr>
  <td>
	  <select name="inModelportefeuille[]" multiple size="8" style="width : auto; margin-left: 8px;"> <?=$Modelportefeuilles?> </select>
  </td>
  <td width="10" >
	  <a href="javascript:if(moveCheckSmash()){moveItem(document.selectForm['inModelportefeuille[]'],document.selectForm['modelportefeuille[]'],false)};">
		  <img src="images/16/pijl_rechts.png" width="16" height="16" border="0" alt="toevoegen" align="absmiddle">
	  </a>
	  <br><br>
	  <a href="javascript:moveItem(document.selectForm['modelportefeuille[]'],document.selectForm['inModelportefeuille[]'],false);">
		  <img src="images/16/pijl_links.png" width="16" height="16" border="0" alt="verwijderen" align="absmiddle">
	  </a>
  </td>
  <td>
	  <select name="modelportefeuille[]" multiple size="8" style="width : 150px"></select>
  </td>
</tr>
</table>


</div>
</fieldset>
</div>

</div>

<div id="KostprijsMutatieverloop" style="visibility : hidden; position:absolute;">
<fieldset id="Selectie1">
<legend accesskey="e">S<u>e</u>lectie</legend>
<div class="formblock">
  <div class="formlinks"> Fonds
    <a href="javascript:select_kostprijsFonds('');getAjaxWaarden('<?=$getFonds?>','',document.getElementById('kostprijsFonds').name)">
    <img src="images/16/lookup.gif" border="0" height="18" align="middle"></a>
  </div>
  <div class="formrechts" id="div_kostprijsFonds">
  <select name="kostprijsFonds" id="kostprijsFonds" style="width:200px" onfocus="javascript:getAjaxWaarden('<?=$getFonds?>','',this.name);">
    <option value="">---</option>
  </select>
  </div>
</div>
<div class="formblock">
<div class="formlinks"> Vanaf beginpositie </div>
<div class="formrechts">
	<input type="checkbox" value="1" name="FondsBeginpositie" checked>
</div>
</div>
<div class="formblock">
<div class="formlinks"> Opties opnemen </div>
<div class="formrechts">
	<input type="checkbox" value="1" name="FondsOpties" checked>
</div>
</div>
<div class="formblock">
<div class="formlinks"> Kosten opnemen </div>
<div class="formrechts">
	<input type="checkbox" value="1" name="FondsKosten" checked>
</div>
</div>
</fieldset>
</div>

<div id="fondsverloop" style="visibility:hidden;position:absolute;">
<fieldset id="Selectie1">
<legend accesskey="e">S<u>e</u>lectie</legend>
<div class="formblock">
  <div class="formlinks"> Fonds
    <a href="javascript:select_fondsverloopFonds('');getAjaxWaarden('<?=$getFonds?>','',document.getElementById('fondsverloopFonds').name)">
    <img src="images/16/lookup.gif" border="0" height="18" align="middle"></a>
  </div>
  <div class="formrechts" id="div_fondsverloopFonds">
  <select name="fondsverloopFonds" id="fondsverloopFonds" style="width:200px" onfocus="javascript:getAjaxWaarden('<?=$getFonds?>','',this.name);">
    <option value="">---</option>
  </select>
  </div>
</div>
</fieldset>
</div>

<div id="Modelcontrole" style="visibility : hidden; position:absolute;">

<fieldset id="Modelportefeuille" >
<legend accesskey="m">M<u>o</u>delcontrole</legend>
<div class="formblock">
	Modelportefeuille
	<select name="modelcontrole_portefeuille">
	<option value="">-</option>
	<?
    echo "<option value=\"Allemaal\">Allemaal</option>";
  ?>
	<?=$Modelportefeuilles?>
	</select>
</div>

<div class="formblock">
	<u>Rapportsoort</u><br>
	<input type="radio" name="modelcontrole_rapport" value="gecomprimeerd" onclick="unsetVastBedrag()"> Gecomprimeerd op totaal<br>
	<input type="radio" name="modelcontrole_rapport" value="percentage" checked onclick="unsetVastBedrag()"> Modelcontrole in percentage<br>
	<input type="radio" name="modelcontrole_rapport" value="liquideren" onclick="unsetVastBedrag()"> Liquideren portefeuille<br>
	<input type="radio" name="modelcontrole_rapport" value="vastbedrag"> Mutatievoorstel Portefeuille<br>
	Vast bedrag: <input type="text" name="modelcontrole_vastbedrag" value="" size="4" onchange="$('input[name=modelcontrole_rapport][value=vastbedrag]').attr('checked',true);javascript:checkAndFixNumber(this);">  Incl rebalance: <input type="checkbox" name="modelcontrole_rebalance" value="1" size="4">
</div>

<div class="formblock">
	<u>Uitvoer soort</u><br>
	<input type="radio" name="modelcontrole_uitvoer" value="alles" checked> Alles<br>
	<input type="radio" name="modelcontrole_uitvoer" value="afwijkingen"> Alleen afwijkingen &nbsp;&nbsp;<input type="text" onChange="javascript:checkAndFixNumber(this);" name="modelcontrole_percentage" value="0.0" size="4">Afwijkingspercentage<br>
</div>

<div class="formblock">
	<u>Filter</u><br>
	<input type="radio" name="modelcontrole_filter" value="alles"> Alles<br>
	<input type="radio" name="modelcontrole_filter" value="gekoppeld" checked> Alleen gekoppelde depots<br>
</div>

<div class="formblock">
	<u>Niveau</u><br>
	<input type="radio" name="modelcontrole_level" value="fonds" checked> Fonds<br>
	<input type="radio" name="modelcontrole_level" value="beleggingscategorie" >Categorie<br>
	<?echo $hoofdcategorie;?>
	<input type="radio" name="modelcontrole_level" value="beleggingssector" >Sector<br>
	<input type="radio" name="modelcontrole_level" value="Regio" >Regio<br>
</div>

</fieldset>
</div>

	<div id="DivRapportDoorkijkFondsselectie" style="visibility : hidden; position:relative;top: 100px;">
	<fieldset id="fondsenSelectieKader">
		<table>
			<tr>
				<td>Percentage<input type="text" id="fondsPercentage" name="fondsPercentage" align="right" value="0.0"></td>
				<td rowspan=4><select id="selectedFondsen" name="selectedFondsen[]" multiple size="8" style="width : 200px"></td>
			</tr>
			<tr><td><input type="tekst" name="fondsPercentageSom" id="fondsPercentageSom" value="0" size="2" readonly>Totaal percentage</td></tr>
			<tr><td><input type="button" value="Fonds toevoegen." onclick="javascript:appendFonds('selectedFondsen');"></td></tr>
			<tr><td><input type="button" value="Fonds verwijderen." onclick="javascript:removeFonds('selectedFondsen');"></td></tr>
		</table>
	</fieldset>
	</div>

<div id="MutatievoorstelPortefeuille" style="visibility : hidden; position:absolute;">
<fieldset id="MutatievoorstelPortefeuille" >
<legend accesskey="m">M<u>u</u>tatievoorstel Portefeuille</legend>

  <div class="formblock">
<div class="formlinks"> Vast bedrag </div>
<div class="formrechts"> <input type="text" name="mutatieportefeuille_vastbedrag" value="" size="15">  Incl. AFM SD<input type="checkbox" name="mutatieportefeuille_afm" value="1" > </div>
</div>

<div class="formblock">
<div class="formlinks"> Naam </div>
<div class="formrechts"> <input type="text" name="mutatieportefeuille_customNaam" value="" size="25"> </div>
</div>
  
  
    <div class="formblock">
      <div class="formlinks"> Modelportefeuille </div>
      <div class="formrechts"> <select name="mutatieportefeuille_portefeuille"><option value="">-</option><?=$Modelportefeuilles?></select></div>
    </div>
  <div class="formblock">
  
  <table>
      <tr>
        <td>Percentage<input type="text" id="portefeuillePercentage" name="portefeuillePercentage" align="right" value="0.0"></td>
        <td rowspan=4><select id="selectedModelportefeuilles" name="selectedModelportefeuilles[]" multiple size="8" style="width : 200px"></td>
      </tr>
      <tr><td><input type="tekst" name="portefeuillePercentageSom" id="portefeuillePercentageSom" value="0" size="2" readonly>Totaal percentage</td></tr>
      <tr><td><input type="button" value="Portefeuille toevoegen." onclick="javascript:appendPortefeuille('selectedModelportefeuilles');"></td></tr>
      <tr><td><input type="button" value="Portefeuille verwijderen." onclick="javascript:removePortefeuille('selectedModelportefeuilles');"></td></tr>
    </table>
  </div>
  </fieldset>
</div>

	<script>

		function checkWaardeprognoseSettings()
		{
			if($("#waardeprognose_clientselectie").prop('checked')==true)
			{
				$("#waardeprognose_naam").prop("disabled", true);
				$("#waardeprognose_naam").css('background','#eee');
				$("#waardeprognose_naam").val('');
				$("#waardeprognose_bedrag").prop("disabled", true);
				$("#waardeprognose_bedrag").css('background','#eee');
				$("#waardeprognose_bedrag").val('');
			}
			else
			{
				$("#waardeprognose_naam").prop("disabled", false);
				$("#waardeprognose_naam").css('background','');
				$("#waardeprognose_bedrag").prop("disabled", false);
				$("#waardeprognose_bedrag").css('background','');
			}
		}
	</script>

	<div id="WaardeprognosePortefeuille" style="visibility : hidden; position:absolute;">

		<fieldset id="WaardeprognosePortefeuille" >
			<legend accesskey="m">W<u>a</u>ardeprognose Portefeuille</legend>

			<div class="formblock">
				<div class="formlinks"> Via clientselectie </div>
				<div class="formrechts"> <input type="checkbox" id="waardeprognose_clientselectie" name="waardeprognose_clientselectie" onclick="javascript:checkWaardeprognoseSettings();" value="1" checked size="25"> </div>
			</div>

			<div class="formblock">
				<div class="formlinks"> Naam </div>
				<div class="formrechts"> <input type="text" id="waardeprognose_naam" name="waardeprognose_naam" style="background:#ccc" value="" disabled size="25"> </div>
			</div>

			<div class="formblock">
				<div class="formlinks"> Bedrag </div>
				<div class="formrechts"> <input type="text" id="waardeprognose_bedrag" name="waardeprognose_bedrag" style="background:#ccc" value="" disabled size="15">  </div>
			</div>

			<div class="formblock">
				<div class="formlinks"> Invoer Profiel </div>
				<div class="formrechts"> <select name="waardeprognose_risicoklasse"><option value="">-</option><?=$risicoklassen?></select></div>
			</div>

			<div class="formblock">
				<div class="formlinks"> Eindjaar </div>
				<div class="formrechts"> <input type="text" name="waardeprognose_eindjaar" value="" size="4"> </div>
			</div>

			<div class="formblock">
				<div class="formlinks"> Kostencomponenten </div>
				<div class="formrechts"> <input type="text" name="waardeprognose_kosten_beheer" value="" size="2" > Beheerkosten <br>
					<input type="text" name="waardeprognose_kosten_transactie" value="" size="2"> Transactiekosten <br>
					<input type="text" name="waardeprognose_kosten_bank" value="" size="2"> Bankkosten <br>
					<input type="text" name="waardeprognose_kosten_indirect" value="" size="2" > Indirectekosten <br>
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
?>