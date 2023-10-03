<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2020/07/04 16:01:09 $
 		File Versie					: $Revision: 1.130 $

 		$Log: rapportFrontofficeFondsSelectie.php,v $
 		Revision 1.130  2020/07/04 16:01:09  rvv
 		*** empty log message ***


*/

//$AEPDF2=true;
include_once("wwwvars.php");
include_once("../classes/AE_cls_fpdf.php");
include_once("../classes/AE_cls_progressbar.php");
include_once("../classes/selectOptieClass.php");

$AETemplate = new AE_template();

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
  include_once("rapport/FondslijstDoorkijk.php");
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
    case "FondsenlijstDoorkijk" :
      $rapport = new FondsenlijstDoorkijk( $selectData );
      $rapport->USR = $USR;
      $rapport->progressbar = & $prb;
      $rapport->__appvar = $__appvar;
      $rapport->writeRapport();
      $rapportnaam = date('Y-m-d').'_'.$__appvar["bedrijf"]."_FLD_";
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

$selectie = new selectOptie($PHP_SELF);
$selectie->getInternExternActive();


$html='<form name="selectForm">';


  $html .= $selectie->getSelectieMethodeHTML($PHP_SELF);

  $selectieHtml = '';

  $selectieHtml .= $selectie->getHtmlInterneExternePortefeuille();
  $selectieHtml .= $selectie->getHtmlConsolidatie();

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

    function doStore()
    {
    }

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
      AEConfirm('<?=vt("Order validatie gelijk uitvoeren")?>?', '<?=vt("Order validatie")?>',
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
      selectedIndex = $('#rapportHolder').find('.active').attr('value');
      //check of velden gevuld
      if(selectedIndex == 2 && document.selectForm.fonds.value == '---')
      {
        if (document.selectForm.newFonds.value == '' ||
          document.selectForm.newFondsValutaCode.value == '' ||
          document.selectForm.newFondsEenheid.value == '' ||
          document.selectForm.newFondsKoers.value == '' ||
          document.selectForm.newFondsValutaKoers.value == '' )
        {
          alert('<?=vt("Niet alle vereiste velden zijn gevuld")?>.');
          return false;
        }
      }

      if(selectedIndex == 3)
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
      <?=$selectie->getJsPortefeuilleInternJava()?>
      <?
      if(method_exists($selectie,'getConsolidatieJava'))
        echo $selectie->getJsConsolidatieJava();
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


    function selectTab (selectedIndex = 0)
    {

      $('#rapportHolder').find('.active').removeClass('active');
      $('#rapportHolder .option-' + selectedIndex).addClass('active');

      $('#soortSelectie').val($('#rapportHolder .option-' + selectedIndex).attr('value'));

      $( "#databaseButton" ).hide();
      $( "#fondsverloop" ).hide();
      $( "#WaardeprognosePortefeuille" ).hide();
      $( "#DivRapportDoorkijkFondsselectie" ).hide();
      $( "#fondsButtonNieuw" ).hide();
      $( "#fondsButtonExtra" ).hide();
      $( "#FondsenlijstDoorkijk" ).hide();
      $( "#Fondsenlijst" ).hide();

      $( "#afdrukkenButton" ).show();
      $( "#opslaanButton" ).show();

      $('#Smash').hide();
      // $("#Smash :input").attr("disabled", true);
      $('#TransactieTypeDiv').hide();
      $('#fondsVerkoopSpan').hide();
      $('#allePortefeuillesTonen').hide();

      if(selectedIndex == 1 || selectedIndex== 2)
      {
        $( "#sm" ).hide();
        $( "#Modelcontrole" ).hide();
        $( "#MutatievoorstelPortefeuille" ).hide();
        $( "#KostprijsMutatieverloop" ).hide();

        $( "#Mutatievoorstel" ).show();
        $( "#fondsButtonNieuw" ).show();
        $( "#fondsButtonExtra" ).show();

        $( "#csvButton" ).show();
        $( "#xlsButton" ).show();
        $( "#PortefueilleSelectie" ).show();
        if(selectedIndex == 1)
        {
          $('#allePortefeuillesTonen').show();
        }

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


        if(selectedIndex == 2)
        {
          $( "#TransactieTypeDiv" ).show();
          $( "#sm" ).show();


          <?if (GetModuleAccess("ORDER") && ($_SESSION['usersession']['gebruiker']['ordersNietAanmaken']==0 || checkOrderAcces ('rapportages_aanmaken') == true)){?>
          $( "#orderButton" ).show();
          <?}?>
        }
        else
        {
          $( "#orderButton" ).hide();

          $( "#databaseButton" ).show();
        }

      }
      else if( selectedIndex== 3 )
      {

        $( "#sm" ).hide();
        $( "#Mutatievoorstel" ).hide();
        $( "#MutatievoorstelPortefeuille" ).hide();
        $( "#orderButton" ).hide();
        $( "#KostprijsMutatieverloop" ).hide();

        $( "#Modelcontrole" ).show();
        $( "#csvButton" ).show();
        $( "#xlsButton" ).show();
        $( "#PortefueilleSelectie" ).show();

        <?if (GetModuleAccess("ORDER") && ($_SESSION['usersession']['gebruiker']['ordersNietAanmaken']==0 || checkOrderAcces ('rapportages_aanmaken') == true)){?>
        $( "#orderButton" ).show();
        <?}?>
      }
      else if( selectedIndex== 4 )
      {
        $( "#Modelcontrole" ).hide();
        $( "#Mutatievoorstel" ).hide();
        $( "#orderButton" ).hide();
        $( "#PortefueilleSelectie" ).hide();
        $( "#KostprijsMutatieverloop" ).hide();
        $( "#sm" ).hide();

        $( "#MutatievoorstelPortefeuille" ).show();

        $( "#csvButton" ).show();
        $( "#xlsButton" ).show();
      }
      else if( selectedIndex== 5 || selectedIndex== 11 )
      {
        $( "#MutatievoorstelPortefeuille" ).hide();
        $( "#KostprijsMutatieverloop" ).hide();
        $( "#sm" ).hide();
        if(selectedIndex == 11)
        {
          $("#FondsenlijstDoorkijk").show();
        }
        else
        {
          $("#Fondsenlijst").show();
        }
        $( "#Modelcontrole" ).hide();
        $( "#Mutatievoorstel" ).hide();
        $( "#afdrukkenButton" ).hide();
        $( "#opslaanButton" ).hide();
        $( "#orderButton" ).hide();
        $( "#target" ).hide();

        $( "#csvButton" ).show();
        $( "#xlsButton" ).show();
        $( "#PortefueilleSelectie" ).show();
      }
      else if( selectedIndex== 6 )
      {
        $( "#MutatievoorstelPortefeuille" ).hide();
        $( "#sm" ).hide();
        $( "#Modelcontrole" ).hide();
        $( "#Mutatievoorstel" ).hide();
        $( "#orderButton" ).hide();

        $( "#KostprijsMutatieverloop" ).show();
        $( "#csvButton" ).show();
        $( "#xlsButton" ).show();
        $( "#PortefueilleSelectie" ).show();
      }
      else if( selectedIndex== 7 )
      {
        $( "#MutatievoorstelPortefeuille" ).hide();
        $( "#KostprijsMutatieverloop" ).hide();
        $( "#sm" ).hide();
        $( "#Modelcontrole" ).hide();
        $( "#Mutatievoorstel" ).hide();
        $( "#orderButton" ).hide();
        $( "#fondsverloop" ).show();
        $( "#csvButton" ).show();
        $( "#xlsButton" ).show();
        $( "#PortefueilleSelectie" ).show();
      }
      else if( selectedIndex== 8 )
      {

        $( "#MutatievoorstelPortefeuille" ).hide();
        $( "#KostprijsMutatieverloop" ).hide();
        $( "#fondsverloop" ).hide();
        $( "#sm" ).hide();
        $( "#Modelcontrole" ).hide();
        $( "#Mutatievoorstel" ).hide();
        $( "#afdrukkenButton" ).hide();
        $( "#opslaanButton" ).hide();
        $( "#orderButton" ).hide();

        $( "#csvButton" ).show();
        $( "#xlsButton" ).show();
        $( "#PortefueilleSelectie" ).show();

      }
      else if( selectedIndex== 9 )
      {
        $( "#MutatievoorstelPortefeuille" ).hide();
        $( "#KostprijsMutatieverloop" ).hide();
        $( "#fondsverloop" ).hide();
        $( "#sm" ).hide();
        $( "#Modelcontrole" ).hide();
        $( "#Mutatievoorstel" ).hide();
        $( "#orderButton" ).hide();

        $( "#csvButton" ).show();
        $( "#xlsButton" ).show();
        $( "#PortefueilleSelectie" ).show();
        $( "#WaardeprognosePortefeuille" ).show();
      }
      else if(selectedIndex== 10)
      {
        $( "#DivRapportDoorkijkFondsselectie" ).show();
        $( "#Mutatievoorstel" ).show();
        $( "#xlsButton" ).show();
        $( "#afdrukkenButton" ).show();
        $('#fondsenSelectieKader').show();
        $('#fondsAankoopSpan').hide();
      }
      else
      {

        if(selectedIndex == 0 )
        {
          $( "#databaseButton" ).show();
        }


        $( "#Modelcontrole" ).hide();
        $( "#sm" ).hide();
        $( "#Mutatievoorstel" ).hide();
        $( "#MutatievoorstelPortefeuille" ).hide();
        $( "#orderButton" ).hide();
        $( "#KostprijsMutatieverloop" ).hide();


        $( "#csvButton" ).show();
        $( "#xlsButton" ).show();
        $( "#PortefueilleSelectie" ).show();
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
      // $("#Smash :input").attr("disabled", false);
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
      // $("#Smash :input").attr("disabled", true);
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


  <?php

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


  $autocomplete->resetVirtualField('kostprijsFondsOmschrijving');
  $autoCompleteConfig['autocomplete']['actions']['select'] = '
    event.preventDefault();
    $("#kostprijsFonds").val(ui.item.value);
    $("#kostprijsFondsOmschrijving").val(ui.item.field_value);
    
    $(".kostprijsFondsIsin").html(ui.item.data.ISINCode);
    $(".kostprijsFondsValuta").html(ui.item.data.Valuta);
  ';
  $autoCompleteConfig['autocomplete']['actions']['change'] = '
    if ( ui.item === null ) {
      $("#kostprijsFonds").val("");
      $("#kostprijsFondsOmschrijving").val("");
      
      $(".kostprijsFondsIsin").html("");
      $(".kostprijsFondsValuta").html("");
    }
  ';
  $autoCompleteConfig['autocomplete']['conditions'] = array(
    'AND' => array(
      '(Fondsen.EindDatum >= now() OR Fondsen.EindDatum = "0000-00-00" OR 1 = "{$get:kostprijsfondsInactief}")', // or 1=0 of 1=1 voor het tonen van inactieve fondsen
    )
  );
  $kostprijsFondsField = $autocomplete->addVirtuelField('kostprijsFondsOmschrijving', $autoCompleteConfig);
  $content['script_voet'] .= $autocomplete->getAutoCompleteVirtuelFieldScript('kostprijsFondsOmschrijving');


  $autocomplete->resetVirtualField('fondsverloopFondsOmschrijving');
  $autoCompleteConfig['autocomplete']['actions']['select'] = '
    event.preventDefault();
    $("#fondsverloopFonds").val(ui.item.value);
    $("#fondsverloopFondsOmschrijving").val(ui.item.field_value);
    
    $(".fondsverloopIsin").html(ui.item.data.ISINCode);
    $(".fondsverloopaluta").html(ui.item.data.Valuta);
  ';
  $autoCompleteConfig['autocomplete']['actions']['change'] = '
    if ( ui.item === null ) {
      $("#fondsverloopFonds").val("");
      $("#fondsverloopFondsOmschrijving").val("");
      
      $(".fondsverloopIsin").html("");
      $(".fondsverloopaluta").html("");
    }
  ';
  $autoCompleteConfig['autocomplete']['conditions'] = array(
    'AND' => array(
      '(Fondsen.EindDatum >= now() OR Fondsen.EindDatum = "0000-00-00" OR 1 = "{$get:fondsverloopfondsInactief}")', // or 1=0 of 1=1 voor het tonen van inactieve fondsen
    )
  );
  $fondsverloopFondsField = $autocomplete->addVirtuelField('fondsverloopFondsOmschrijving', $autoCompleteConfig);
  $content['script_voet'] .= $autocomplete->getAutoCompleteVirtuelFieldScript('fondsverloopFondsOmschrijving');


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

  ?>





  <br />

  <div class="container-fluid">
    <form action="<?=$PHP_SELF?>" method="POST" target="_blank" name="selectForm">
      <input type="hidden" name="posted" value="true" />
      <input type="hidden" name="save" value="" />
      <input type="hidden" id="soortSelectie" name="soort" value="Managementoverzicht" />
      <input type="hidden" name="rapport_types" value="" />
      <input type="hidden" name="filetype" value="PDF" />
      <input type="hidden" name="portefeuilleIntern" value="" />
      <input type="hidden" name="metConsolidatie" value="<?=$_SESSION['metConsolidatie']?>" />
      <input type="hidden" name="extra" value="" />


      <div class="formHolder" >

        <div class="formTabGroup ">
          <?=$AETemplate->parseBlockFromFile('rapportFrontoffice/tabbuttons.html', array(
            'fondsen'      => 'active'
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
                <div class="btn btn-default" id="afdrukkenButton" style="width:130px" onclick="javascript:print();"><i style="color:red" class="fa fa-file-pdf-o fa-fw  " aria-hidden="true"></i> <?=vt("Afdrukken")?></div>
                <div class="btn btn-default" id="opslaanButton" style="width:130px" onclick="javascript:saveasfile();"><i style="color:blue"  class="fa fa-floppy-o fa-fw " aria-hidden="true"></i> <?=vt("Opslaan")?> </div>
                <div class="btn btn-default" id="csvButton" style="width:130px" onclick="javascript:csv();"><i style="color:green" class="fa fa-file-excel-o fa-fw" aria-hidden="true"></i> <?=vt("CSV-export")?> </div>
                <div class="btn btn-default" id="xlsButton" style="width:130px" onclick="javascript:xls();"><i style="color:green" class="fa fa-file-excel-o fa-fw" aria-hidden="true"></i> <?=vt("XLS-export")?> </div>
                <div class="btn btn-default" id="databaseButton" style="width:130px" onclick="javascript:database();"><i style="color:blue"  class="fa fa-table fa-fw " aria-hidden="true"></i> <?=vt("Reportbuilder")?> </div>
                <?
                if (checkOrderAcces('rapportages_aanmaken') == true || GetModuleAccess('ORDER') < 2)
                  echo '<div class="btn btn-default" id="orderButton" style="width:130px; display: none;" onclick="javascript:order();">&nbsp; '.vt("Genereer orders").'</div>';
                else
                  echo '<div class="btn btn-default" id="orderButton" style="width:150px; display: none;" >&nbsp; '.vt("Geen order rechten").'</div>';
                ?>

                <?php if ( checkOrderAcces ('rapportages_aanmaken') == true ) { ?>
                  <div class="btn btn-default" id = "orderButton" style = "width:130px; display: none;" onclick = "javascript:order();" >&nbsp; <?=vt("Genereer orders")?> </div >
                <?php } ?>
              </div>



            </div>

          </div>
        </div>
      </div>


      <div class="formHolder" id="rapportHolder" >
        <div class="formTitle textB"><?=vt("Rapport")?></div>
        <div class="formContent padded-10">

          <div class="btn-group-vertical btn-group-top btn-group-text-left col-sm-3 col-2">
            <span class="btn btn-hover btn-default option-10" onclick="selectTab(10);" data-toggle="tooltip" data-placement="top" title="<?=vt("Doorkijk fondsselectie")?>" value="doorkijkFondsselectie"><?=vt("Doorkijk fondsselectie")?></span>
            <span class="btn btn-hover btn-default option-5" onclick="selectTab(5);" data-toggle="tooltip" data-placement="top" title="<?=vt("Fondsenlijst")?>" value="Fondsenlijst"><?=vt("Fondsenlijst")?></span>
            <span class="btn btn-hover btn-default option-11" onclick="selectTab(11);" data-toggle="tooltip" data-placement="top" title="<?=vt("FondsenlijstDoorkijk")?>" value="FondsenlijstDoorkijk"><?=vt("Fondsenlijst incl. doorkijk")?></span>
            <span class="btn btn-hover btn-default option-1" onclick="selectTab(1);" data-toggle="tooltip" data-placement="top" title="<?=vt("Fondsoverzicht")?>" value="Fondsoverzicht" <?=($_GET['selectRapport']=="Fondsoverzicht"?"selected":"")?>><?=vt("Fondsoverzicht")?></span>
          </div>

          <div class="btn-group-vertical btn-group-top btn-group-text-left col-sm-3  col-2">

            <span class="btn btn-hover btn-default option-7" onclick="selectTab(7);" data-toggle="tooltip" data-placement="top" title="<?= vt('Fondsverloop'); ?>" value="Fondsverloop"><?= vt('Fondsverloop'); ?></span>
            <span class="btn btn-hover btn-default option-0" onclick="selectTab(0);" data-toggle="tooltip" data-placement="top" title="<?=vt("Geaggregeerd Portefeuille Overzicht")?>" value="Geaggregeerd Portefeuille Overzicht"><?=vt("Geaggregeerd Portefeuille Overzicht")?></span>
            <span class="btn btn-hover btn-default option-6" onclick="selectTab(6);" data-toggle="tooltip" data-placement="top" title="<?=vt("Kostprijs Mutatieverloop")?>" value="KostprijsMutatieverloop"><?=vt("Kostprijs Mutatieverloop")?></span>
            <span class="btn btn-hover btn-default option-3" onclick="selectTab(3);" data-toggle="tooltip" data-placement="top" title="<?=vt("Modelcontrole")?>" value="Modelcontrole"><?=vt("Modelcontrole")?></span>

          </div>

          <div class="btn-group-vertical btn-group-top btn-group-text-left col-sm-3  col-2">
            <span class="btn btn-hover btn-default option-2" onclick="selectTab(2);" data-toggle="tooltip" data-placement="top" title="<?=vt("Mutatievoorstel Fondsen")?>" value="Mutatievoorstel Fondsen"><?=vt("Mutatievoorstel Fondsen")?></span>
            <span class="btn btn-hover btn-default option-4" onclick="selectTab(4);" data-toggle="tooltip" data-placement="top" title="<?=vt("Mutatievoorstel Portefeuille")?>" value="MutatievoorstelPortefeuille"><?=vt("Mutatievoorstel Portefeuille")?></span>
            <span class="btn btn-hover btn-default option-8" onclick="selectTab(8);" data-toggle="tooltip" data-placement="top" title="<?=vt("Obligatie overzicht")?>" value="Obligatie overzicht"><?=vt("Obligatie overzicht")?></span>
            <span class="btn btn-hover btn-default option-9" onclick="selectTab(9);" data-toggle="tooltip" data-placement="top" title="<?=vt("Waardeprognose portefeuille")?>" value="WaardeprognosePortefeuille"><?=vt("Waardeprognose portefeuille")?></span>

          </div>


        </div>

      </div>

      <div class="baseRow">

        <?php
        $mainBlockSize = 'col-7 col-md-6 col col-xl-6';
        if($_SESSION['selectieMethode'] == 'portefeuille') {
          $mainBlockSize = 'col-7 col-md-6 col col-xl-6';
        }

        ?>

      <div class="<?=$mainBlockSize;?>" id="PortefueilleSelectie">
        <div class="formHolder"  >
          <div class="formTabGroup ">
            <?=$selectie->getHtmlSelectieMethode()?>
          </div>
          <div class="formTitle textB"><?=vt("Selectie")?></div>
          <div class="formContent formContentForm pl-1 pt-2 PB-2" id="">
          <?
            // portefeuille selectie
            if($_SESSION['selectieMethode'] == 'portefeuille') {
              $DB = new DB();
              $DB->SQL($selectie->queries['ClientPortefeuille']);
              $DB->Query();

              while( $gb = $DB->NextRecord() ) {
                $data[$gb['Portefeuille']]=$gb;
              }
              echo $selectie->createEnkelvoudigeSelctie($data,$_SESSION['backofficeSelectie']);
            }
            // end portefeuille selectie
            else {
              $DB = new DB();
              $maxVink=25;

              $opties=array('Vermogensbeheerder'=>'Vermogensbeheerder','Accountmanager'=>'accountmanager','TweedeAanspreekpunt'=>'tweedeAanspreekpunt','Client'=>'client','Portefeuille'=>'portefeuilles','Depotbank'=>'depotbank');
              foreach ($opties as $optie=>$omschrijving) {
                $data=$selectie->getData($optie);
                if($_SESSION['selectieMethode'] =='vink' && count($data) < $maxVink) {
                  echo $selectie->createCheckBlok($optie,$data,$_SESSION['backofficeSelectie'],$omschrijving);
                } else {
                  echo $selectie->createSelectBlok($optie,$data,$_SESSION['backofficeSelectie'],$omschrijving);
                }
              }

              $opties=array('Risicoklasse'=>'Risicoklasse','ModelPortefeuille'=>'ModelPortefeuille','AFMprofiel'=>'AFMprofiel','SoortOvereenkomst'=>'SoortOvereenkomst','Remisier'=>'Remisier','PortefeuilleClusters'=>'PortefeuilleClusters','selectieveld1'=>'Selectieveld1','selectieveld2'=>'Selectieveld2');
              foreach ($opties as $optie=>$omschrijving) {
                $data=$selectie->getData($optie);
                if(count($data) > 1)
                {
                  if($_SESSION['selectieMethode'] =='vink' && count($data) < $maxVink) {
                    echo $selectie->createCheckBlok($optie,$data,$_SESSION['backofficeSelectie'],$omschrijving);
                  } else {
                    echo $selectie->createSelectBlok($optie,$data,$_SESSION['backofficeSelectie'],$omschrijving);
                  }
                }
              }

            }
          ?>
          </div>
        </div>
        </div>


        <div class="col-6 col-md-6 col col-xl-6" >


          <div class="formHolder"  id="TransactieTypeDiv" style="display: none; ">
            <div class="formTitle textB"><?=vt("Order type")?></div>
            <div class="formContent formContentForm pl-4 pt-2 PB-2" id="TransactieTypeFieldset">
              <div class="formblock">
                <div class="formlinks">
                  <input type="radio" name="transactieType" value="enkelvoudig" checked onClick="javascript:mutatieEnkel();"> <?=vt("Enkelvoudige order ")?><br>
                  <input type="radio" name="transactieType" value="switch" onClick="javascript:mutatieSwitch();">  <?=vt("Switch order")?>  <br>
                  <!--			<input type="radio" name="transactieType" value="meervoudig" disabled onClick="javascript:mutatieMeer();"> Meervoudige order -->
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




      <div class="formHolder" id="Mutatievoorstel" style="display: none;">
        <div class="formTitle textB"><?=vt("Specificatie")?></div>
        <div class="formContent formContentForm pl-4 pt-2 PB-2" id="VoorstelSelectie">

          <div id="Fondsoverzicht">
            <div class="row">
              <div class="col-md-3"><strong> <span id="fondsVerkoopSpan"> <?=vt("Verkoop")?> </span> <?=vt("Fonds")?></strong></div>

              <div class="col-md-8"  id="div_fonds">
                <input type="hidden" id="fonds" name="fonds" value="">
                <?=$fondsField;?> <input type="checkbox" id="fondsInactief" name="fondsInactief" value="0">
                <label for="fondsInactief"> <?=vt("Inactief tonen")?></label><br>

                <script>
                  $("#fondsInactief").change(function() {
                    $('#fondsInactief').attr('value', '0');
                    if(this.checked) {
                      $('#fondsInactief').attr('value', '1');
                    }
                  });
                </script>
              </div>
            </div>
            <br />
            <div class="row">
              <div class="col-md-3"><strong><?=vt("ISIN-code")?>:</strong></div>
              <div class="col-md-8"><span class="verkoopIsin"></span></div>
            </div>
            <br />
            <div class="row">
              <div class="col-3"><strong><?=vt("Valuta")?>:</strong></div>
              <div class="col-8"><span class="verkoopValuta"></span></div>
            </div>
            <br />
            <div class="row" id="allePortefeuillesTonen">
              <div class="col-3"><strong><?=vt('Ook portefeuilles zonder posities tonen');?></strong></div>
              <div class="col-8"><input type="checkbox" id="allePortefeuillesOpnemen" name="allePortefeuillesOpnemen" value="1"></div>
            </div>
          </div>


          <span id="fondsAankoopSpan">
            <br /><br />
            <div class="row">
              <div class="col-md-3"><strong><?=vt("Aankoop Fonds")?></strong></div>

              <div class="col-md-8"  id="div_aankoopFonds">
                <input type="hidden" id="aankoopFonds" name="aankoopFonds" value="">
                <?=$aankoopFondsField;?>
                <input type="checkbox" id="aankoopfondsverloopfondsInactief" name="aankoopfondsverloopfondsInactief" value="0">
                <label for="aankoopfondsverloopfondsInactief"> <?=vt("Inactief tonen")?></label><br>

                <script>
                  $("#aankoopfondsverloopfondsInactief").change(function() {
                    $('#aankoopfondsverloopfondsInactief').attr('value', '0');
                    if(this.checked) {
                      $('#aankoopfondsverloopfondsInactief').attr('value', '1');
                    }
                  });
                </script>
              </div>
            </div>
            <br />
            <div class="row">
              <div class="col-md-3"><strong><?=vt("ISIN-code")?>:</strong></div>
              <div class="col-md-8"><span class="aankoopFondsIsin"></span></div>
            </div>
            <br />
            <div class="row">
              <div class="col-3"><strong><?=vt("Valuta")?>:</strong></div>
              <div class="col-8"><span class="aankoopFondsValuta"></span></div>
            </div>

          </span>

          <div class="formblock">
            <br><br>
            <div id="wrapper" style="overflow:hidden;width:=400px;">
              <div class="buttonDiv" id="fondsButtonNieuw" style="width:120px;float:left;text-align: center;" onclick="$('#newFondsDiv').toggle();"> <?=vt("Nieuw fonds")?> </div>
              <div class="buttonDiv" id="fondsButtonExtra" style="width:120px;float:left;text-align: center;" onclick="$('#mutatieVoorstelOptieDiv').toggle();"> <?=vt("Extra")?> </div>
            </div>
          </div>
          <div id="newFondsDiv" style="display: none;">
            <div class="formblock">
              <div class="formlinks"> <?=vt("Nieuwe fonds naam")?> </div>
              <div class="formrechts">
                <input type="text" name="newFonds" id="newFonds">
              </div>
            </div>

            <div class="formblock">
              <div class="formlinks"> <?=vt("Fonds ISIN code")?> </div>
              <div class="formrechts">
                <input type="text" name="newFondsISIN" id="newFondsISIN">
              </div>
            </div>

            <div class="formblock">
              <div class="formlinks"> <?=vt("Fonds koers")?> </div>
              <div class="formrechts">
                <input type="text" name="newFondsKoers" id="newFondsKoers" size="5">
              </div>
            </div>

            <div class="formblock">
              <div class="formlinks"> <?=vt("Fonds valuta koers")?> </div>
              <div class="formrechts">
                <input type="text" name="newFondsValutaKoers" id="newFondsValutaKoers" size="5" >
              </div>
            </div>

            <div class="formblock">
              <div class="formlinks"> <?=vt("Fonds valuta code")?> </div>
              <div class="formrechts">

                <select name="newFondsValutaCode" id="newFondsValutaCode" style="width:200px" >
                  <?=$valutaCode?>
                </select>
              </div>
            </div>

            <div class="formblock">
              <div class="formlinks"> <?=vt("Fonds eenheid")?> </div>
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
                <label for="actief" title="actief"> <?= vt('Actieve fondsen'); ?>  </label>
                <input type="radio" name="actief" id="positie" value="positie" <?=$positieChecked?> onClick="document.location = '<?=$PHP_SELF?>?actief=positie&selectRapport=Fondsoverzicht'">
                <label for="positie" title="actief"> <?= vt('In positie'); ?>  </label>
                <input type="radio" name="actief" id="inactief" value="inactief" <?=$inactiefChecked?> onClick="document.location = '<?=$PHP_SELF?>?actief=inactief&selectRapport=Fondsoverzicht'">
                <label for="inactief" title="actief"> <?= vt('Alle fondsen'); ?> </label>
              </div>
            </div>


            <?if($bewaarder['OrderuitvoerBewaarder']==1) {?>
              <div class="formblock">
                <div class="formlinks"><?=vt("Order voorkeur depotbank")?></div>
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
              <div class="formlinks"> <?=vt("Afronding")?> </div>
              <div class="formrechts">
                <input type="text" name="afronding" value="1" size="5">
              </div>
            </div>

            <div class="formblock">
              <div class="formlinks"> <?=vt("Berekeningswijze")?> </div>
              <div class="formrechts">
                <select name="berekeningswijze">
                  <option value="Totaal vermogen"><?=vt("Totaal vermogen")?></option>
                  <option value="Totaal belegd vermogen"><?=vt("Totaal belegd vermogen")?></option>
                  <option value="Belegd vermogen per beleggingscategorie"><?=vt("Belegd vermogen per beleggingscategorie")?></option>
                </select>
              </div>
            </div>

            <div class="formblock">
              <div class="formlinks"> <?=vt("Via norm")?></div>
              <div class="formrechts">
                <input type="checkbox" value="1" name="berekeningswijzeViaNorm">
              </div>
            </div>


            <div class="formblock">
              <div class="formlinks"> <?=vt("Deposito's uitsluiten")?> </div>
              <div class="formrechts">
                <input type="checkbox" value="1" name="depositoUitsluiten">
              </div>
            </div>



            <div class="formblock">
              <div class="formlinks"> <?=vt("Opties weergeven")?> </div>
              <div class="formrechts">
                <input type="checkbox" value="1" name="optiesWeergeven">
              </div>
            </div>

            <div class="formblock">
              <div class="formlinks"> <?=vt("Portrait versie")?></div>
              <div class="formrechts">
                <input type="checkbox" value="1" name="portraitVersie" <?if($layout == 13)echo "CHECKED";?> >
              </div>
            </div>

            <div class="formblock">
              <div class="formlinks"> <?=vt("Uitvoer op bewaarder")?> </div>
              <div class="formrechts">
                <input type="checkbox" value="1" name="fondsenOpBewaarder">
              </div>
            </div>
          </div>



        </div>
      </div>

          <!-- Smash -->
          <div class="formHolder" id="sm" style="display: none; ">
            <div class="formTitle textB"><?=vt("Smash")?></div>
            <div class="formContent formContentForm pl-1 pt-2 PB-2" id="Smash">

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
                <input type="radio" name="type" onclick="moveBack();" id="typeHandmatig" value="Handmatig" checked> <?=vt("Handmatig")?> &nbsp;	<?=vt("Percentage")?>:
                <input type="text" onChange="javascript:checkAndFixNumber(this);" name="percentage" value="0.0" size="4"> <input type="checkbox" value="1" name="nulUitlsuiten"> <?=vt("Aantal 0 niet tonen")?>  <br><br>
                <input type="radio" name="type" id="typeModel" value="Model"> <?=vt("Via model")?> &nbsp;
                <?=vt("Modelportefeuille")?>:
                <!--
	<select name="modelportefeuille">
	<option value="">-</option>
<?
                if ($t <> 0)
                  echo "<option value=\"Allemaal\">".vt("Allemaal")."</option>";
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









          <!-- KostprijsMutatieverloop -->
          <div class="formHolder" id="KostprijsMutatieverloop" style="display: none;">
            <div class="formTitle textB"><?=vt("Selectie")?></div>
            <div class="formContent formContentForm pl-4 pt-2 PB-2" id="Selectie1">
              <div class="formblock">
                <div class="formlinks"> <?=vt("Fonds")?>
                </div>
                <div class="formrechts" id="div_kostprijsFonds">
                  <input type="hidden" name="kostprijsFonds" id="kostprijsFonds" value="">
                  <?=$kostprijsFondsField;?><input type="checkbox" id="kostprijsfondsInactief" name="kostprijsfondsInactief" value="0">
                  <label for="kostprijsfondsInactief"> <?=vt("Inactief tonen")?></label><br>

                  <script>
                    $("#kostprijsfondsInactief").change(function() {
                      $('#kostprijsfondsInactief').attr('value', '0');
                      if(this.checked) {
                        $('#kostprijsfondsInactief').attr('value', '1');
                      }
                    });
                  </script>

                </div><br /><br />
                <div class="row">
                  <div class="col-md-3"><strong><?=vt("ISIN-code")?>:</strong></div>
                  <div class="col-md-8"><span class="verkoopIsin"></span></div>
                </div>
                <br />
                <div class="row">
                  <div class="col-md-3"><strong><?=vt("Valuta")?>:</strong></div>
                  <div class="col-md-8"><span class="verkoopValuta"></span></div>
                </div>
              </div>

              <div class="formblock">
                <div class="formlinks"> <?=vt("Vanaf beginpositie")?> </div>
                <div class="formrechts">
                  <input type="checkbox" value="1" name="FondsBeginpositie" checked>
                </div>
              </div>
              <div class="formblock">
                <div class="formlinks"> <?=vt("Opties opnemen")?> </div>
                <div class="formrechts">
                  <input type="checkbox" value="1" name="FondsOpties" checked>
                </div>
              </div>
              <div class="formblock">
                <div class="formlinks"> <?=vt("Kosten opnemen")?> </div>
                <div class="formrechts">
                  <input type="checkbox" value="1" name="FondsKosten" checked>
                </div>
              </div>
            </div>
          </div>

          <!-- Fondsenlijst -->
          <div class="formHolder" id="Fondsenlijst" style="display: none;">
            <div class="formTitle textB"><?=vt("Selectie Fondslijst")?></div>
            <div class="formContent formContentForm pl-4 pt-2 PB-2" id="Selectie1">
              <div class="formblock">
                <div class="formlinks"><?=vt("Inclusief Liquiditeiten")?></div>
                <div class="formrechts">
                  <input type="checkbox" value="1" name="fondslijst_Liq">
                </div>
              </div>
            </div>
          </div>

          <!-- FondsenlijstDoorkijk -->
          <div class="formHolder" id="FondsenlijstDoorkijk" style="display: none;">
            <div class="formTitle textB"><?=vt("Selectie categorieën")?></div>
            <div class="formContent formContentForm pl-4 pt-2 PB-2" id="Selectie1">
              <?php
              $morningstarCheck = getVermogensbeheerderField("morningstar ");
              $doorkijkSoorten=array('Beleggingscategorien','Beleggingssectoren','Regios');
              if ( $morningstarCheck == 2 || $morningstarCheck == 4 )
              {
                $doorkijkSoorten[]='Rating';
                $doorkijkSoorten[]='Looptijd';
                $doorkijkSoorten[]='Coupon';
              }
              foreach($doorkijkSoorten as $categorie)
              {
                echo '              <div class="formblock">
                <div class="formlinks"> '.vt($categorie).'</div>
                <div class="formrechts">
                  <input type="checkbox" value="1" name="fondslijstDK_'.$categorie.'">
                </div>
              </div>';
              }
              ?>
              <br>
              <div class="formblock">
                <div class="formlinks"><?=vt("Doorkijk MS-categorieen")?></div>
                <div class="formrechts">
                  <input type="checkbox" value="1" name="fondslijstDK_MScat">
                </div>
              </div>
              <div class="formblock">
                <div class="formlinks"><?=vt("Inclusief Liquiditeiten")?></div>
                <div class="formrechts">
                  <input type="checkbox" value="1" name="fondslijstDK_Liq">
                </div>
              </div>

             </div>
          </div>


          <!-- fondsverloop -->
          <div class="formHolder" id="fondsverloop" style="display: none;">
            <div class="formTitle textB"><?=vt("Selectie")?></div>
            <div class="formContent formContentForm pl-4 pt-2 PB-2" id="Selectie1">

              <div class="formblock">
                <div class="formlinks"> <?=vt("Fonds")?>
                </div>
                <div class="formrechts" id="div_fondsverloopFonds">

                  <input name="fondsverloopFonds" id="fondsverloopFonds" type="hidden">
                  <?=$fondsverloopFondsField;?>
                  <input type="checkbox" id="fondsverloopfondsInactief" name="fondsverloopfondsInactief" value="0">
                  <label for="fondsverloopfondsInactief"> <?=vt("Inactief tonen")?></label><br>

                  <script>
                    $("#fondsverloopfondsInactief").change(function() {
                      $('#fondsverloopfondsInactief').attr('value', '0');
                      if(this.checked) {
                        $('#fondsverloopfondsInactief').attr('value', '1');
                      }
                    });
                  </script>

                </div><br /><br />
                <div class="row">
                  <div class="col-12"><strong><?=vt("ISIN-code")?>:</strong> <span class="fondsverloopIsin"></span></div>
                  <div class="col-12"><strong><?=vt("Valuta")?>:</strong> <span class="fondsverloopaluta"></span></div>
                </div>
              </div>
          </div>
          </div>




          <!-- Modelcontrole -->
          <div class="formHolder" id="Modelcontrole" style="display: none;">
            <div class="formTitle textB"><?=vt("Modelcontrole")?></div>
            <div class="formContent formContentForm pl-4 pt-2 PB-2" id="Modelportefeuille">

              <div class="formblock">
                <?=vt("Modelportefeuille")?>
                <select name="modelcontrole_portefeuille">
                  <option value="">-</option>
                  <?
                  echo "<option value=\"Allemaal\">".vt("Allemaal")."</option>";
                  ?>
                  <?=$Modelportefeuilles?>
                </select>
              </div>

              <div class="formblock">
                <u><?=vt("Rapportsoort")?></u><br>
                <input type="radio" name="modelcontrole_rapport" value="gecomprimeerd" onclick="unsetVastBedrag()"> <?=vt("Gecomprimeerd op totaal")?><br>
                <input type="radio" name="modelcontrole_rapport" value="percentage" checked onclick="unsetVastBedrag()"> <?=vt("Modelcontrole in percentage")?><br>
                <input type="radio" name="modelcontrole_rapport" value="liquideren" onclick="unsetVastBedrag()"> <?=vt("Liquideren portefeuille")?><br>
                <input type="radio" name="modelcontrole_rapport" value="vastbedrag"> <?=vt("Mutatievoorstel Portefeuille")?><br>
                Vast bedrag: <input type="text" name="modelcontrole_vastbedrag" value="" size="4" onchange="$('input[name=modelcontrole_rapport][value=vastbedrag]').attr('checked',true);javascript:checkAndFixNumber(this);">  <?=vt("Incl rebalance")?>: <input type="checkbox" name="modelcontrole_rebalance" value="1" size="4">
              </div>

              <div class="formblock">
                <u><?=vt("Uitvoer soort")?></u><br>
                <input type="radio" name="modelcontrole_uitvoer" value="alles" checked> <?=vt("Alles")?><br>
                <input type="radio" name="modelcontrole_uitvoer" value="afwijkingen"> <?=vt("Alleen afwijkingen")?> &nbsp;&nbsp;<input type="text" onChange="javascript:checkAndFixNumber(this);" name="modelcontrole_percentage" value="0.0" size="4"><?=vt("Afwijkingspercentage")?><br>
              </div>

              <div class="formblock">
                <u><?=vt("Filter")?></u><br>
                <input type="radio" name="modelcontrole_filter" value="alles"> <?=vt("Alles")?><br>
                <input type="radio" name="modelcontrole_filter" value="gekoppeld" checked> <?=vt("Alleen gekoppelde depots")?><br>
              </div>

              <div class="formblock">
                <u><?=vt("Niveau")?></u><br>
                <input type="radio" name="modelcontrole_level" value="fonds" checked> <?=vt("Fonds")?><br>
                <input type="radio" name="modelcontrole_level" value="beleggingscategorie" ><?=vt("Categorie")?><br>
                <?echo $hoofdcategorie;?>
                <input type="radio" name="modelcontrole_level" value="beleggingssector" ><?=vt("Sector")?><br>
                <input type="radio" name="modelcontrole_level" value="Regio" ><?=vt("Regio")?><br>
              </div>

            </div>
          </div>







          <!-- DivRapportDoorkijkFondsselectie -->
      <div class="formHolder" id="DivRapportDoorkijkFondsselectie" style="display: none;">
        <div class="formTitle textB"><?=vt("Specificatie")?></div>
        <div class="formContent formContentForm pl-4 pt-2 PB-2" id="fondsenSelectieKader">
          <table>
            <tr>
              <td><?=vt("Percentage")?><input type="text" id="fondsPercentage" name="fondsPercentage" align="right" value="0.0"></td>
              <td rowspan=4><select id="selectedFondsen" name="selectedFondsen[]" multiple size="8" style="width : 200px"></td>
            </tr>
            <tr><td><input type="tekst" name="fondsPercentageSom" id="fondsPercentageSom" value="0" size="2" readonly><?=vt("Totaal percentage")?></td></tr>
            <tr><td><input type="button" value="<?=vt("Fonds toevoegen")?>." onclick="javascript:appendFonds('selectedFondsen');"></td></tr>
            <tr><td><input type="button" value="<?=vt("Fonds verwijderen")?>." onclick="javascript:removeFonds('selectedFondsen');"></td></tr>
          </table>
	      </div>
	    </div>


          <!-- MutatievoorstelPortefeuille -->
          <div class="formHolder" id="MutatievoorstelPortefeuille" style="display: none;">
            <div class="formTitle textB"><?=vt("Mutatievoorstel Portefeuille")?></div>
            <div class="formContent formContentForm pl-4 pt-2 PB-2" id="fondsenSelectieKader">

              <div class="formblock">
                <div class="formlinks"> <?=vt("Vast bedrag")?> </div>
                <div class="formrechts"> <input type="text" name="mutatieportefeuille_vastbedrag" value="" size="15">  Incl. AFM SD<input type="checkbox" name="mutatieportefeuille_afm" value="1" > </div>
              </div>

              <div class="formblock">
                <div class="formlinks"> <?=vt("Naam")?> </div>
                <div class="formrechts"> <input type="text" name="mutatieportefeuille_customNaam" value="" size="25"> </div>
              </div>


              <div class="formblock">
                <div class="formlinks"> <?=vt("Modelportefeuille")?> </div>
                <div class="formrechts"> <select name="mutatieportefeuille_portefeuille"><option value="">-</option><?=$Modelportefeuilles?></select></div>
              </div>
              <div class="formblock">

                <table>
                  <tr>
                    <td><?=vt("Percentage")?><input type="text" id="portefeuillePercentage" name="portefeuillePercentage" align="right" value="0.0"></td>
                    <td rowspan=4><select id="selectedModelportefeuilles" name="selectedModelportefeuilles[]" multiple size="8" style="width : 200px"></td>
                  </tr>
                  <tr><td><input type="tekst" name="portefeuillePercentageSom" id="portefeuillePercentageSom" value="0" size="2" readonly><?=vt("Totaal percentage")?></td></tr>
                  <tr><td><input type="button" value="Portefeuille toevoegen." onclick="javascript:appendPortefeuille('selectedModelportefeuilles');"></td></tr>
                  <tr><td><input type="button" value="Portefeuille verwijderen." onclick="javascript:removePortefeuille('selectedModelportefeuilles');"></td></tr>
                </table>
              </div>
              </fieldset>
            </div>
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

          <!-- MutatievoorstelPortefeuille -->
          <div class="formHolder" id="WaardeprognosePortefeuille" style="display: none;">
            <div class="formTitle textB"><?=vt("Waardeprognose Portefeuille")?></div>
            <div class="formContent formContentForm pl-4 pt-2 PB-2" id="WaardeprognosePortefeuille">

              <div class="formblock">
                <div class="formlinks"> <?=vt("Via clientselectie")?> </div>
                <div class="formrechts"> <input type="checkbox" id="waardeprognose_clientselectie" name="waardeprognose_clientselectie" onclick="javascript:checkWaardeprognoseSettings();" value="1" checked size="25"> </div>
              </div>

              <div class="formblock">
                <div class="formlinks"> <?=vt("Naam")?> </div>
                <div class="formrechts"> <input type="text" id="waardeprognose_naam" name="waardeprognose_naam" style="background:#ccc" value="" disabled size="25"> </div>
              </div>

              <div class="formblock">
                <div class="formlinks"> <?=vt("Bedrag")?> </div>
                <div class="formrechts"> <input type="text" id="waardeprognose_bedrag" name="waardeprognose_bedrag" style="background:#ccc" value="" disabled size="15">  </div>
              </div>

              <div class="formblock">
                <div class="formlinks"> <?=vt("Invoer Profiel")?> </div>
                <div class="formrechts"> <select name="waardeprognose_risicoklasse"><option value="">-</option><?=$risicoklassen?></select></div>
              </div>

              <div class="formblock">
                <div class="formlinks"> <?=vt("Eindjaar")?> </div>
                <div class="formrechts"> <input type="text" name="waardeprognose_eindjaar" value="" size="4"> </div>
              </div>

              <div class="formblock">
                <div class="formlinks"> <?=vt("Kostencomponenten")?> </div>
                <div class="formrechts"> <input type="text" name="waardeprognose_kosten_beheer" value="" size="2" > <?=vt("Beheerkosten")?> <br>
                  <input type="text" name="waardeprognose_kosten_transactie" value="" size="2"> <?=vt("Transactiekosten")?> <br>
                  <input type="text" name="waardeprognose_kosten_bank" value="" size="2"> <?=vt("Bankkosten")?> <br>
                  <input type="text" name="waardeprognose_kosten_indirect" value="" size="2" > <?=vt("Indirectekosten")?> <br>
                </div>
              </div>

              </fieldset>
            </div>
          </div>







</td>
</tr>
</table>
        </div>
</form>

    <div class="row">

      <div class="col-12">

<? echo progressFrame();?>
<iframe width="538" height="300" name="generateFrame" frameborder="0" scrolling="No" marginwidth="0" marginheight="0"></iframe>

      </div>
    </div>


<script type="text/javascript">

</script>
	<?
	if($__debug) {
		echo getdebuginfo();
	}
	echo template($__appvar["templateRefreshFooter"],$content);
}
