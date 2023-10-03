<?php
/*
    AE-ICT source module
    Author  						: $Author: rm $
 		Laatste aanpassing	: $Date: 2019/11/13 15:43:38 $
 		File Versie					: $Revision: 1.1 $

 		$Log: rapportFrontofficeOptieToolsold.php,v $
 		Revision 1.1  2019/11/13 15:43:38  rm
 		7929
 		
 		Revision 1.33  2019/02/13 16:46:47  rvv
 		*** empty log message ***
 		
 		Revision 1.32  2018/10/13 17:16:37  rvv
 		*** empty log message ***
 		
 		Revision 1.31  2018/08/27 17:17:09  rvv
 		*** empty log message ***
 		
 		Revision 1.30  2018/01/07 14:04:01  rvv
 		*** empty log message ***
 		
 		Revision 1.29  2018/01/06 19:03:24  rvv
 		*** empty log message ***
 		
 		Revision 1.28  2017/07/09 11:56:18  rvv
 		*** empty log message ***
 		
 		Revision 1.27  2015/03/25 14:47:52  rvv
 		*** empty log message ***
 		
 		Revision 1.26  2014/09/13 14:37:42  rvv
 		*** empty log message ***
 		
 		Revision 1.25  2014/09/03 15:55:22  rvv
 		*** empty log message ***
 		
 		Revision 1.24  2013/09/01 13:31:16  rvv
 		*** empty log message ***
 		
 		Revision 1.23  2013/08/14 15:57:30  rvv
 		*** empty log message ***
 		
 		Revision 1.22  2013/05/26 13:52:44  rvv
 		*** empty log message ***
 		
 		Revision 1.21  2013/05/12 11:18:26  rvv
 		*** empty log message ***
 		
 		Revision 1.20  2012/12/19 17:00:08  rvv
 		*** empty log message ***
 		
 		Revision 1.19  2012/11/25 13:15:50  rvv
 		*** empty log message ***
 		
 		Revision 1.18  2012/08/11 13:05:20  rvv
 		*** empty log message ***
 		
 		Revision 1.17  2012/04/04 16:07:40  rvv
 		*** empty log message ***

 		Revision 1.16  2011/12/11 10:57:35  rvv
 		*** empty log message ***

 		Revision 1.15  2011/04/17 09:11:14  rvv
 		*** empty log message ***

 		Revision 1.14  2010/11/14 10:49:33  rvv
 		*** empty log message ***

 		Revision 1.13  2010/07/28 17:19:16  rvv
 		*** empty log message ***

 		Revision 1.12  2010/05/30 11:50:01  rvv
 		*** empty log message ***

 		Revision 1.11  2010/04/18 10:39:40  rvv
 		*** empty log message ***

 		Revision 1.10  2010/03/22 10:51:03  cvs
 		$type='portefeuille'; uitgeschakeld

 		Revision 1.9  2010/03/10 19:55:29  rvv
 		*** empty log message ***

 		Revision 1.8  2009/04/05 09:36:28  rvv
 		*** empty log message ***

 		Revision 1.7  2009/04/05 09:23:36  rvv
 		*** empty log message ***

 		Revision 1.6  2008/06/30 06:53:04  rvv
 		*** empty log message ***

 		Revision 1.5  2008/05/29 07:32:37  rvv
 		*** empty log message ***

 		Revision 1.4  2007/08/02 14:42:19  rvv
 		*** empty log message ***

 		Revision 1.3  2007/04/03 13:25:22  rvv
 		*** empty log message ***

 		Revision 1.2  2007/02/21 10:57:56  rvv
 		Client / consolidatie toevoeging

 		Revision 1.1  2006/12/05 12:19:00  rvv
 		Optie tool toevoeging


*/

//$AEPDF2=true;
include_once("wwwvars.php");
define('FPDF_FONTPATH',$__appvar["basedir"]."/html/font/");
include_once("../classes/AE_cls_progressbar.php");
include_once("../classes/selectOptieClass.php");
include_once("../classes/portefeuilleSelectieClass.php");
$selectie=new selectOptie();
//$type='portefeuille';
$maxVink=25;
$content[javascript] .= " ";
$content[body] = " onLoad=\"javascript:selectTab()\"";

$content[calendarinclude] = "<script language=JavaScript src=\"javascript/algemeen.js\" type=text/javascript></script> <script language=JavaScript src=\"javascript/selectbox.js\" type=text/javascript></script>";
$kal = new DHTML_Calendar();
$content[calendar] = $kal->get_load_files_code();

echo template($__appvar["templateContentHeader"],$content);
flush();


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
  $selectData['selectedPortefeuilles']  = $_POST['selectedFields'];
  
  
  // maak progressbar
  $prb = new ProgressBar(536,8);	// create new ProgressBar
  $prb->color = 'maroon';	// bar color
  $prb->bgr_color = '#ffffff';	// bar background color
  $prb->brd_color = 'Silver';
  $prb->left = 0;	                  // Frame position from left
  $prb->top = 	0;
  $prb->show();	                             // show the ProgressBar
  
  switch($selectData[soort])
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
      $rapport->pdf->Output($__appvar[tempdir].$filename,"F");
      break;
    case "cvs" :
      $filename =  $rapportnaam.".csv";
      $filetype = "csv";
      $rapport->pdf->OutputCSV($__appvar[tempdir].$filename,"F");
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
// selecteer laatst bekende valutadatum
  $totdatum = getLaatsteValutadatum();
  
  $jr = substr($totdatum,0,4);
  
  $html='<form name="selectForm">';
  $selectie->getInternExternActive();
  $html.=$selectie->getSelectieMethodeHTML($PHP_SELF);
  $html.=$selectie->getInternExternHTML($PHP_SELF);
  $html .="<br>";
  $html.=$selectie->getConsolidatieHTML($PHP_SELF);
  $html.='</form>';
  
  $_SESSION[NAV] = "";
  $_SESSION[submenu] = New Submenu();
  $_SESSION[submenu]->addItem($html,"");
  
  session_write_close();
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
      <?=$selectie->getPortefeuilleInternJava();?>
      <?=$selectie->getConsolidatieJava();?>
    }
    
    function selectTab()
    {
      <?if($notActivated == true)
      {
      ?>
      document.getElementById('knopPDF').style.visibility="hidden";
      document.getElementById('knopSAVE').style.visibility="hidden";
      document.getElementById('knopCSV').style.visibility="hidden";
      document.getElementById('knopXLS').style.visibility="hidden";
      <?
      }
      else
      {
      ?>
      document.getElementById('knopPDF').style.visibility="visible";
      document.getElementById('knopSAVE').style.visibility="visible";
      document.getElementById('knopCSV').style.visibility="visible";
      document.getElementById('knopXLS').style.visibility="visible";
      <?
      }
      ?>
      if(document.selectForm.soort.selectedIndex == 0 )
      {
        document.getElementById('ExpiratieDatum').style.visibility="visible";
      }
      else
      {
        document.getElementById('ExpiratieDatum').style.visibility="hidden";
      }
      
      if(document.selectForm.soort.selectedIndex == 3 )
      {
        document.getElementById('vrijePositie').style.visibility="visible";
      }
      else
      {
        document.getElementById('vrijePositie').style.visibility="hidden";
      }
      
      if(document.selectForm.soort.selectedIndex == 2 )
      {
        document.getElementById('ongedektePositie').style.visibility="visible";
      }
      else
      {
        document.getElementById('ongedektePositie').style.visibility="hidden";
      }
      
      if(document.selectForm.soort.selectedIndex == 5)
      {
        document.getElementById('liquideRuimte').style.visibility="visible";
      }
      else
      {
        document.getElementById('liquideRuimte').style.visibility="hidden";
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
  
  <br><br>
  <div class="tabbuttonRow">
    <input type="button" class="tabbuttonInActive" onclick="javascript:document.location = 'rapportFrontofficeClientSelectieold.php';" id="tabbutton0" value="Clienten">
    <input type="button" class="tabbuttonInActive" onclick="javascript:document.location = 'rapportFrontofficeFondsSelectieold.php';" id="tabbutton1" value="Fondsen">
    <input type="button" class="tabbuttonInActive" onclick="javascript:document.location = 'rapportFrontofficeManagementSelectieold.php';" id="tabbutton2" value="Management info">
    <input type="button" class="tabbuttonActive" onclick="" id="tabbutton3" value="Optie tools">
    <input type="button" class="tabbuttonInActive" onclick="javascript:document.location = 'rapportFrontofficeConsolidatieSelectieold.php';" id="tabbutton4" value="Consolidatie tool">
  </div>
  
  <form action="<?=$PHP_SELF?>" method="POST" target="_blank" name="selectForm">
    <input type="hidden" name="posted" value="true" />
    <input type="hidden" name="save" value="" />
    <input type="hidden" name="rapport_types" value="" />
    <input type="hidden" name="filetype" value="PDF" />
    <input type="hidden" name="portefeuilleIntern" value="" />
    <input type="hidden" name="metConsolidatie" value="<?=$_SESSION['metConsolidatie']?>" />
    
    <iframe width="538" height="15" name="generateFrame" frameborder="0" scrolling="No" marginwidth="0" marginheight="0"></iframe>
    
    <table border="0">
      <tr>
        <td width="540">
          
          <div class="form">
            
            <fieldset id="Rapport" >
              <legend accesskey="R"><u>R</u>apport</legend>
              
              <div class="formblock">
                <div class="formlinks"> Rapport </div>
                <div class="formrechts">
                  
                  <select name="soort" style="width:200px" onChange="javascript:selectTab();">
                    <option value="OptieExpiratieLijst">Expiratie lijst</option>
                    <option value="OptieGeschrevenPositie">Geschreven positie</option>
                    <option value="OptieOngedektePositie">Ongedekte positie</option>
                    <option value="OptieVrijePositie">Vrije positie</option>
                    <option value="OptiePutExposure">Overzicht put-exposure</option>
                    <option value="OptieLiquideRuimte">Liquide ruimte geschreven puts</option>
                  </select>
                </div>
              </div>
              
              <?
              echo $selectie->createDatumSelectie();
              ?>
            </fieldset>
            
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
                ?>
                <br><br>
                <?
                while($gb = $DB->NextRecord())
                  $data[$gb['Portefeuille']]=$gb;
                echo "<br><br>";
                echo $selectie->createEnkelvoudigeSelctie($data);
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
        <td valign="top">
          
          <?
          if($notActivated == true)
            echo '<br><input id="nietGeactiveerd" type="button" value="Niet geactiveerd."  style="width:130px"><br><br> ';
          ?>
          
          <div class="buttonDiv" id="knopPDF" style="width:130px" onclick="javascript:print();">&nbsp;&nbsp;<?=maakKnop('pdf.png',array('size'=>16))?> Afdrukken</div><br>
          <div class="buttonDiv" id="knopSAVE" style="width:130px" onclick="javascript:saveasfile();">&nbsp;&nbsp;<?=maakKnop('disk_blue.png',array('size'=>16))?> Opslaan </div><br>
          <div class="buttonDiv" id="knopCSV" style="width:130px" onclick="javascript:csv();">&nbsp;&nbsp;<?=maakKnop('csv.png',array('size'=>16))?> CSV-export </div><br>
          <div class="buttonDiv" id="knopXLS" style="width:130px" onclick="javascript:xls();">&nbsp;&nbsp;<?=maakKnop('xls.png',array('size'=>16))?> XLS-export </div><br>
          
          <fieldset id="ExpiratieDatum">
            <legend accesskey="X">E<u>x</u>piratie Datum</legend>
            
            <div class="formblock">
              <div class="formlinks"> Expiratie Maand </div>
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
              <div class="formlinks"> Expiratie Jaar </div>
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
                <div class="formlinks"> Expitatie Verwerken </div>
                <div class="formrechts">
                  <input type="checkbox" name="expitatieVerwerken" value="1" >
                </div>
              </div>
            <?}?>
          </fieldset>
          
          <!-- Fonds selectie -->
          <div id="vrijePositie" style="visibility : hidden; position:absolute;">
            
            <fieldset id="Selectie">
              <legend accesskey="e">S<u>e</u>lectie</legend>
              <?
              $alleenActief = " AND (Fondsen.EindDatum  >=  NOW() OR Fondsen.EindDatum = '0000-00-00') ";
              
              ?>
              <div class="formblock">
                <div class="formlinks"> Fonds </div>
                <div class="formrechts" >
                  <div id="div_fonds">
                    <select name="fonds" id='fonds' style="width:200px" onfocus="javascript:getAjaxWaarden('<?=urlencode(base64_encode(gzcompress("SELECT Fonds, Omschrijving FROM Fondsen WHERE 1=1 ".$alleenActief." ORDER BY Omschrijving")))?>','',this.name);" >
                      <option value="" >---</option>
                    </select>
                  </div>
                  </br>
                  <input type="checkbox" name="geaccordeerd" value="1" > Geaccordeerde portefeuilles
                </div>
              </div>
            
            </fieldset>
            <!-- end Fonds selectie -->
            
            <!-- Fonds selectie -->
            <div id="ongedektePositie" style="visibility : hidden; position:absolute;">
              
              <fieldset id="Selectie">
                <legend accesskey="e">S<u>e</u>lectie</legend>
                
                <div class="formblock">
                  <div class="formlinks"> Tonen boven </div>
                  <div class="formrechts">
                    <input type="text" name="ongedektePositiePercentage" value="100" size="5"> % geschreven.
                  
                  </div>
                </div>
              
              </fieldset>
              
              <div id="liquideRuimte" style="visibility : hidden; position:absolute;">
                
                <fieldset id="Selectie">
                  <legend accesskey="e">S<u>e</u>lectie</legend>
                  
                  <div class="formblock">
                    <div class="formlinks"> Alleen tekorten weergeven</div>
                    <div class="formrechts">
                      <input type="checkbox" name="liquideRuimteTekort" value="1">
                    
                    </div>
                  </div>
                
                </fieldset>
                <!-- end Fonds selectie -->
        
        
        </td>
      </tr>
    </table>
  
  </form>
  <?php
  if($__debug) {
    echo getdebuginfo();
  }
  echo template($__appvar["templateRefreshFooter"],$content);
}
?>