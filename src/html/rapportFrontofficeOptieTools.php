<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2020/05/20 17:09:45 $
 		File Versie					: $Revision: 1.36 $

 		$Log: rapportFrontofficeOptieTools.php,v $
 		Revision 1.36  2020/05/20 17:09:45  rvv
 		*** empty log message ***
 		

*/

//$AEPDF2=true;
include_once("wwwvars.php");
define('FPDF_FONTPATH',$__appvar["basedir"]."/html/font/");
include_once("../classes/AE_cls_progressbar.php");
include_once("../classes/selectOptieClass.php");
include_once("../classes/portefeuilleSelectieClass.php");

$AETemplate = new AE_template();

//$type='portefeuille';
$maxVink=25;
$content["javascript"] .= " ";
$content["body"] = " onLoad=\"javascript:selectTab()\"";

$content["calendarinclude"] = "<script language=JavaScript src=\"javascript/algemeen.js\" type=text/javascript></script> <script language=JavaScript src=\"javascript/selectbox.js\" type=text/javascript></script>";
$kal = new DHTML_Calendar();
$content["calendar"] = $kal->get_load_files_code();

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


$query = "SELECT OptieTools FROM Vermogensbeheerders WHERE OptieTools = 1 ";
$DB = new DB();
$DB->SQL($query);
$DB->Query();
if ($DB->records() == 0)
{
$notActivated = true;
}

if($_POST['posted'])
{
	$start = getmicrotime();
	include_once("rapport/rapportVertaal.php");
	include_once("rapport/rapportRekenClass.php");
	include_once("rapport/PDFOptieOverzicht.php");
	include_once("rapport/PDFRapport.php");
	include_once("rapport/OptieExpiratieLijst.php");
	include_once("rapport/OptieGeschrevenPositie.php");
	include_once("rapport/OptieOngedektePositie.php");
	include_once("rapport/OptieLiquideRuimte.php");
	include_once("rapport/OptieVrijePositie.php");

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
	$selectData['selectedPortefeuilles']  = $_POST['selectedFields'];


	// maak progressbar
	$prb = new ProgressBar(536,8);	// create new ProgressBar
	$prb->color = 'maroon';	// bar color
	$prb->bgr_color = '#ffffff';	// bar background color
	$prb->brd_color = 'Silver';
	$prb->left = 0;	                  // Frame position from left
	$prb->top = 	0;
	$prb->show();	                             // show the ProgressBar

	switch($selectData['soort'])
	{
		case "OptieExpiratieLijst" :
			$rapport = new OptieExpiratieLijst( $selectData );
			$rapport->USR = $USR;
			$rapport->progressbar = & $prb;
			$rapport->__appvar = $__appvar;
			$rapport->writeRapport();
			$rapportnaam = $__appvar["bedrijf"]."_OptExp";
		break;
		case "OptieGeschrevenPositie" :
			$rapport = new OptieGeschrevenPositie( $selectData );
			$rapport->USR = $USR;
			$rapport->progressbar = & $prb;
			$rapport->__appvar = $__appvar;
			$rapport->writeRapport();
			$rapportnaam = $__appvar["bedrijf"]."_OptGesPos";
		break;
		case "OptieOngedektePositie" :
			$rapport = new OptieOngedektePositie( $selectData );
			$rapport->USR = $USR;
			$rapport->progressbar = & $prb;
			$rapport->__appvar = $__appvar;
			$rapport->writeRapport();
			$rapportnaam = $__appvar["bedrijf"]."_OptOngPos";
		break;
		case "OptieVrijePositie":
			$rapport = new OptieVrijePositie( $selectData );
			$rapport->USR = $USR;
			$rapport->progressbar = & $prb;
			$rapport->__appvar = $__appvar;
			$rapport->writeRapport();
			$rapportnaam = $__appvar["bedrijf"]."_OptVrij";
		break;
		case "OptieLiquideRuimte":
    case "OptiePutExposure":
			$rapport = new OptieLiquideRuimte( $selectData );
			$rapport->USR = $USR;
			$rapport->progressbar = & $prb;
			$rapport->__appvar = $__appvar;
			$rapport->writeRapport();
			$rapportnaam = $__appvar["bedrijf"]."_OptLiq";
		break;

	}

	if($selectData['expitatieVerwerken'] == 1)
	{
	  $rapport->expitatieVerwerken();
	  ?>
	<script type="text/javascript">
		parent.document.location = 'tijdelijkerekeningmutatiesList.php';
	</script>
<?
	  exit;
	}

	switch($_POST['filetype'])
	{
		case "PDF" :
			$filename = $rapportnaam.".pdf";
			$filetype = "pdf";
			$rapport->pdf->Output($__appvar["tempdir"].$filename,"F");
		break;
		case "cvs" :
			$filename =  $rapportnaam.".csv";
			$filetype = "csv";
			$rapport->pdf->OutputCSV($__appvar["tempdir"].$filename,"F");
		break;
		case "xls" :
			if(class_exists('XMLWriter'))
				$xlsuitvoer = "xlsx";

			if($xlsuitvoer == "xlsx")
				$filename =  $rapportnaam.".xlsx";
			else
				$filename =  $rapportnaam.".xls";

			$rapport->pdf->OutputXLS($__appvar['tempdir'].$filename,"F",$xlsuitvoer);
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
  $selectie=new selectOptie($PHP_SELF);
// selecteer laatst bekende valutadatum
$totdatum = getLaatsteValutadatum();

$jr = substr($totdatum,0,4);

//$html='<form name="selectForm">';
$selectie->getInternExternActive();
//$html.=$selectie->getSelectieMethodeHTML($PHP_SELF);
//$html.=$selectie->getInternExternHTML($PHP_SELF);
//$html .="<br>";
//$html.=$selectie->getConsolidatieHTML($PHP_SELF);
//$html.='</form>';
//
//$_SESSION[NAV] = "";
//$_SESSION[submenu] = New Submenu();
//$_SESSION[submenu]->addItem($html,"");
//
//session_write_close();
  
  
  $selectieHtml = '';
  
  $selectieHtml .= $selectie->getHtmlInterneExternePortefeuille();
  $selectieHtml .= $selectie->getHtmlConsolidatie();
  
  


?>
<script type="text/javascript">

<?=$selectie->getSelectJava();?>



function print()
{
	document.selectForm.target = "generateFrame";
	document.selectForm.filetype.value = "PDF";
	document.selectForm.save.value = "0";
	selectSelected();
	document.selectForm.submit();
}


function saveasfile()
{
	document.selectForm.target = "generateFrame";
	document.selectForm.filetype.value = "PDF";
	document.selectForm.save.value = "1";
	selectSelected();
	document.selectForm.submit();
}

function csv()
{
	document.selectForm.target = "generateFrame";
	document.selectForm.filetype.value = "cvs";
	document.selectForm.save.value = "1";
	selectSelected();
	document.selectForm.submit();
}

function xls()
{
  document.selectForm.target = "generateFrame";
	document.selectForm.filetype.value="xls";
	document.selectForm.save.value="1";
	selectSelected();
	document.selectForm.submit();
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
	  <?=$selectie->getJsPortefeuilleInternJava();?>
  	<?=$selectie->getJsConsolidatieJava();?>
}

function selectTab (selectedIndex = 0)
{
  
  console.log(selectedIndex);
  $('#rapportHolder').find('.active').removeClass('active');
  $('#rapportHolder .option-' + selectedIndex).addClass('active');
  
  $('#soortSelectie').val($('#rapportHolder .option-' + selectedIndex).attr('value'));
<?if($notActivated == true)
{
?>
 $( "#knopPDF" ).hide();
 $( "#knopSAVE" ).hide();
 $( "#knopCSV" ).hide();
 $( "#knopXLS" ).hide();
<?
}
else
{
?>
 $( "#knopPDF" ).show();
 $( "#knopSAVE" ).show();
 $( "#knopCSV" ).show();
 $( "knopXLS" ).show();
<?
}
?>
		if(selectedIndex == 0 )
		{
		$( "#ExpiratieDatum" ).show();
		}
		else
		{
		$( "#ExpiratieDatum" ).hide();
		}

		if(selectedIndex == 3 )
		{
		$( "#vrijePositie" ).show();
		}
		else
		{
		$( "#vrijePositie" ).hide();
		}

		if(selectedIndex == 2 )
		{
		$( "#ongedektePositie" ).show();
		}
		else
		{
		$( "#ongedektePositie" ).hide();
		}
   
   	if(selectedIndex == 5)
		{
		$( "#liquideRuimte" ).show();
		} 
    else
    {
     $( "#liquideRuimte" ).hide();
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
	    //div_a += '<option value="">---</option>';
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
  
  <br />
  
  <div class="container-fluid">
  
  
<form action="<?=$PHP_SELF?>" method="POST" target="_blank" name="selectForm">
<input type="hidden" name="posted" value="true" />
<input type="hidden" id="soortSelectie" name="soort" value="OptieExpiratieLijst" />
<input type="hidden" name="save" value="" />
<input type="hidden" name="rapport_types" value="" />
<input type="hidden" name="filetype" value="PDF" />
<input type="hidden" name="portefeuilleIntern" value="" />
<input type="hidden" name="metConsolidatie" value="<?=$_SESSION['metConsolidatie']?>" />
  
  
  <div class="formHolder" >
    
    <div class="formTabGroup ">
      <?=$AETemplate->parseBlockFromFile('rapportFrontoffice/tabbuttons.html', array(
        'optietools'      => 'active'
      ))?>
    </div>
    
    <div class="formTitle textB"><?=vt("Selectie")?></div>
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
  
          <?
          if($notActivated == true)
            echo '<br><input id="nietGeactiveerd" type="button" value="Niet geactiveerd."  style="width:130px"><br><br> ';
          ?>
  
            <div class="btn btn-default" id="knopPDF" style="width:130px" onclick="javascript:print();"><i style="color:red" class="fa fa-file-pdf-o fa-fw  " aria-hidden="true"></i> <?=vt("Afdrukken")?></div>
            <div class="btn btn-default" id="knopSAVE" style="width:130px" onclick="javascript:saveasfile();"><i style="color:blue"  class="fa fa-floppy-o fa-fw " aria-hidden="true"></i> <?=vt("Opslaan")?> </div>
            <div class="btn btn-default" id="knopCSV" style="width:130px" onclick="javascript:csv();"><i style="color:green" class="fa fa-file-excel-o fa-fw" aria-hidden="true"></i> <?=vt("CSV-export")?> </div>
            <div class="btn btn-default" id="knopXLS" style="width:130px" onclick="javascript:xls();"><i style="color:green" class="fa fa-file-excel-o fa-fw" aria-hidden="true"></i> <?=vt("XLS-export")?> </div>


          </div>
        </div>
      </div>
    </div>
  </div>
  
  
  <div class="formHolder" id="rapportHolder" >
    <div class="formTitle textB"><?=vt("Rapport")?></div>
    <div class="formContent padded-10">
      
      
      <div class="btn-group-vertical btn-group-top btn-group-text-left  col-sm-3 col-2">
        <span class="btn btn-hover btn-default option-0" onclick="selectTab(0);" data-toggle="tooltip" data-placement="top" title="<?=vt("Expiratie lijst")?>" value="OptieExpiratieLijst"><?=vt("Expiratie lijst")?></span>
        <span class="btn btn-hover btn-default option-1" onclick="selectTab(1);" data-toggle="tooltip" data-placement="top" title="<?=vt("Geschreven positie")?>" value="OptieGeschrevenPositie"><?=vt("Geschreven positie")?></span>
        <span class="btn btn-hover btn-default option-2" onclick="selectTab(2);" data-toggle="tooltip" data-placement="top" title="<?=vt("Ongedekte positie")?>" value="OptieOngedektePositie"><?=vt("Ongedekte positie")?></span>
      </div>
        <div class="btn-group-vertical btn-group-top btn-group-text-left  col-sm-3 col-2">
        <span class="btn btn-hover btn-default option-3" onclick="selectTab(3);" data-toggle="tooltip" data-placement="top" title="<?=vt("Vrije positie")?>" value="OptieVrijePositie"><?=vt("Vrije positie")?></span>
        <span class="btn btn-hover btn-default option-4" onclick="selectTab(4);" data-toggle="tooltip" data-placement="top" title="<?=vt("Overzicht put-exposure")?>" value="OptiePutExposure"><?=vt("Overzicht put-exposure")?></span>
        <span class="btn btn-hover btn-default option-5" onclick="selectTab(5);" data-toggle="tooltip" data-placement="top" title="<?=vt("Liquide ruimte geschreven puts")?>" value="OptieLiquideRuimte"><?=vt("Liquide ruimte geschreven puts")?></span>
      </div>
    
    
    </div>
  
  </div>
  
<iframe width="538" height="15" name="generateFrame" frameborder="0" scrolling="No" marginwidth="0" marginheight="0"></iframe>
  
  
  <div class="baseRow">
  
    <?php
    $mainBlockSize = 'col-7 col-md-6 col col-xl-6';
    if($_SESSION['selectieMethode'] == 'portefeuille') {
      $mainBlockSize = 'col-7 col-md-6 col col-xl-6';
    }
  
    ?>
    
    <div class="<?=$mainBlockSize;?>" id="PortefueilleSelectie">
  
      <!-- Selectie -->
      <div class="formHolder"  id="Selectie" >
        <div class="formTabGroup ">
          <?=$selectie->getHtmlSelectieMethode()?>
        </div>
        
        <div class="formTitle textB"><?=vt("Selectie")?></div>
  
       
        
        
        <div class="formContent formContentForm pl-1 pt-2 PB-2" id="">
          <?
          // portefeuille selectie
          if($_SESSION['selectieMethode'] == 'portefeuille')
          {
          ?>
          <table cellspacing="0" border = 0>
            <?
            $DB = new DB();
            if(checkAccess($type))
              $join = "";
            else
            {
              $join = "INNER JOIN VermogensbeheerdersPerGebruiker ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker = '".$USR."'
	JOIN Gebruikers ON Gebruikers.Gebruiker = VermogensbeheerdersPerGebruiker.Gebruiker";
          
              if($_SESSION['usersession']['gebruiker']['internePortefeuilles'] == '1')
                $internDepotToegang="OR Portefeuilles.interndepot=1";
          
              if($_SESSION['usersession']['gebruiker']['Accountmanager'] <> '' && $_SESSION['usersession']['gebruiker']['overigePortefeuilles'] == 0)
                $beperktToegankelijk = " AND (Portefeuilles.Accountmanager='".$_SESSION['usersession']['gebruiker']['Accountmanager']."' OR Portefeuilles.tweedeAanspreekpunt ='".$_SESSION['usersession']['gebruiker']['Accountmanager']."' $internDepotToegang) ";
              else
                $beperktToegankelijk = " AND (Portefeuilles.beperktToegankelijk = '0' OR  Gebruikers.beperkingOpheffen = '1' ) ";
            }
            $query = "SELECT Portefeuille, Client FROM Portefeuilles ".$join. " WHERE Portefeuilles.Einddatum  >=  NOW() $beperktToegankelijk ORDER BY Client ";
        
            $DB->SQL($query);
            $DB->Query();
            
            while($gb = $DB->NextRecord())
              $data[$gb['Portefeuille']]=$gb;

              echo $selectie->createEnkelvoudigeSelctie($data);

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
        </div>
      </div>
    </div>
  
  
  
    <div class="col-6 col-md-6 col col-xl-6" >
  
  
  
    <!-- Expiratie Datum -->
    <div class="formHolder"  id="ExpiratieDatum" style="display: none; ">
      <div class="formTitle textB"><?=vt("Expiratie Datum")?></div>
      <div class="formContent formContentForm pl-4 pt-2 PB-2" id="">
      
        <div class="formblock">
          <div class="formlinks"> <?=vt("Expiratie Maand")?> </div>
          <div class="formrechts">
          
            <select class="" type="select"  name="expiratieMaand" >
              <option value=""> --- </option>
              <?
              $huidigeMaand= date(n);
              for($i=1; $i<13; $i++)
              {
                if ($huidigeMaand == $i)
                  echo "<option value=\"$i\" SELECTED>".$__appvar["Maanden"][$i]." </option>";
                else
                  echo "<option value=\"$i\" >".$__appvar["Maanden"][$i]." </option>";
              }
              ?>
            </select>
          </div>
        </div>
      
        <div class="formblock">
          <div class="formlinks"> <?=vt("Expiratie Jaar")?> </div>
          <div class="formrechts">
            <select class="" type="select"  name="expiratieJaar" >
              <option value=""> --- </option>
              <?
              $huidigeJaar = date(Y);
              for ($i=-5;$i<10;$i++)
              {
                $expJaar = $huidigeJaar + $i;
                if ($i == 0)
                  echo "<option value=\"".$expJaar."\" SELECTED>".$expJaar."</option>";
                else
                  echo "<option value=\"".$expJaar."\" >".$expJaar."</option>";
              }
              ?>
            </select>
          </div>
        </div>
      
        <?if(checkAccess()){?>
          <div class="formblock">
            <div class="formlinks"> <?=vt("Expitatie Verwerken")?> </div>
            <div class="formrechts">
              <input type="checkbox" name="expitatieVerwerken" value="1" >
            </div>
          </div>
        <?}?>
      </div>
    </div>
  
  
  
  
  
  
  
    <!-- Fonds selectie -->
    <div class="formHolder"  id="vrijePositie" style="display: none; ">
      <div class="formTitle textB"><?=vt("Selectie")?></div>
      <div class="formContent formContentForm pl-4 pt-2 PB-2" id="Selectie">
        <?
        $alleenActief = " AND (Fondsen.EindDatum  >=  NOW() OR Fondsen.EindDatum = '0000-00-00') ";
        ?>
        <div class="formblock">
          <div class="formlinks"> <?=vt("Fonds")?> </div>
          <div class="formrechts" >
            <div id="div_fonds">
              <select name="fonds" id='fonds' style="width:200px" onfocus="javascript:getAjaxWaarden('<?=urlencode(base64_encode(gzcompress("SELECT Fonds, Omschrijving FROM Fondsen WHERE 1=1 ".$alleenActief." ORDER BY Omschrijving")))?>','',this.name);" >
                <option value="" >---</option>
              </select>
            </div>
            </br>
            <input type="checkbox" name="geaccordeerd" value="1" > <?=vt("Geaccordeerde portefeuilles")?>
          </div>
        </div>
      </div>
    </div>
    <!-- end Fonds selectie -->
  
    <!-- Fonds selectie -->
    <div class="formHolder" id="ongedektePositie" style="display: none; ">
      <div class="formTitle textB"><?=vt("Selectie")?></div>
      <div class="formContent formContentForm pl-4 pt-2 PB-2" id="Selectie">
        <div class="formblock">
          <div class="formlinks"> <?=vt("Tonen boven")?></div>
          <div class="formrechts">
            <input type="text" name="ongedektePositiePercentage" value="100" size="5"> <?=vt("% geschreven")?>.
          </div>
        </div>
      </div>
    </div>
  
    <!-- liquideRuimte -->
    <div class="formHolder"  id="liquideRuimte" style="display: none; ">
      <div class="formTitle textB"><?=vt("Selectie")?></div>
      <div class="formContent formContentForm pl-4 pt-2 PB-2" id="Selectie">
        <div class="formblock">
          <div class="formlinks"> <?=vt("Alleen tekorten weergeven")?></div>
          <div class="formrechts">
            <input type="checkbox" name="liquideRuimteTekort" value="1">
          </div>
        </div>
      </div>
    </div>

</form>
  
  </div>
  </div>
  
  <div class="row">
    
    
    <div class="col-12" >
    
    
<?php
	if($__debug) {
		echo getdebuginfo();
	}
	echo template($__appvar["templateRefreshFooter"],$content);
}
