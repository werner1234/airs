<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2016/02/20 15:16:26 $
 		File Versie					: $Revision: 1.3 $

 		$Log: rapportBackofficeMerge.php,v $
 		Revision 1.3  2016/02/20 15:16:26  rvv
 		*** empty log message ***


*/
//$AEPDF2=true;
//
include_once('wwwvars.php');
include_once("../classes/AE_cls_fpdf.php");
include_once('../classes/fpdi/fpdi.php');
include_once('../classes/portefeuilleSelectieClass.php');
define('FPDF_FONTPATH',$__appvar["basedir"]."/html/font/");

class mergeDigiDoc 
{
  function mergeDigiDoc($selectie)
  {
    $this->selectie=$selectie;
    $this->startdatum = form2jul($selectie['datumVan']);
	  $this->einddatum  = form2jul($selectie['datumTm']);
    $this->selectie['datumVan']   = ($this->startdatum );
    $this->selectie['datumTm'] 	  = ($this->einddatum);
  	$this->selectie['backoffice']	= true;
    $this->db = new DB();

    $this->initPdf();
  }
  
  function initPdf()
  {
    $this->pdf = new FPDI('L');
  }

  function getPortefeuilles()
  {
    $this->portefeuilleSelectie= new portefeuilleSelectie($this->selectie,$order,false); //$afdrukSortering['AfdrukSortering']
    if($this->selectie['consolidatieToevoegen']>0)
      $this->portefeuilleSelectie->consolidatieAanmaken(true,true);
    $this->portefeuilles=$this->portefeuilleSelectie->getSelectie(false);
    if(count($this->portefeuilles) < 1)
    {
		  logScherm("<b>Fout: geen portefeuilles binnen selectie!</b>");
		  exit;
	  }
  }
  
  function getDocuments($portefeuille,$filter='.*')
  {
    global $__appvar;
    $dd=new digidoc();
    $documenten=array();
    

    $query="SELECT id FROM CRM_naw WHERE portefeuille='".$portefeuille."'";
    $this->db->SQL($query);
    $crmRecord=$this->db->lookupRecord();
    
    if($crmRecord['id']=='')
    {
      logscherm("Portefeuille $portefeuille niet als CRM record gevonden");
      return $documenten;
    }
    
    $query="SELECT id,module_id, module, dd_id, datastore,filename
            FROM dd_reference 
            WHERE 
            dd_reference.module='CRM_naw' AND 
            dd_reference.filename like '%".date("Ym",$this->einddatum)."%' AND 
            dd_reference.module_id='".$crmRecord['id']."' 
            ORDER by filename";
    $this->db->SQL($query);
    $this->db->Query();
    
    while($data=$this->db->nextRecord())
    {
      if($dd->retrieveDocumentToFile($data['id'],$__appvar['tempdir'])!==false)
        $documenten[]=$data['filename'];
    }
    if(count($documenten)==0)
      logscherm(vt("Voor portefeuille")." $portefeuille ".vt("geen documenten gevonden met")." '".date("Ym",$this->einddatum)."' ".vt("in de bestandsnaam").".");
    return $documenten;

  }
  
  function verwijderTijdelijkeBestanden($documenten)
  {
    global $__appvar;
    
    foreach($documenten as $document)
      unlink($__appvar['tempdir'].'/'.$document);
  }
  
  function getPages($documents)
  {
    global $__appvar;
    $volgorde=array(array('name'=>'p01'),
                    array('name'=>'p02'),
                    array('name'=>'airs','title'=>'Vermogensoverzicht'),
                    array('name'=>'p04'),
                    array('name'=>'p05'),
                    array('name'=>'p06'),
                    array('name'=>'airs','title'=>'Vermogensontwikkeling'),
                    array('name'=>'airs','title'=>'Mutaties'),
                    array('name'=>'p10'));
   
    $geladenDocumenten=array();
    $n=1;   
    if(count($documents)==0)
      return false;         
    foreach($volgorde as $zoekDocument)
    {
      logscherm("P$n Zoeken naar documentnaam ".$zoekDocument['name']."");
      foreach($documents as $document)
      {
        if(strpos($document,$zoekDocument['name'])!==false)
        {
          logscherm("P$n Zoeken naar pagina ".$zoekDocument['title']."");
          if(isset($zoekDocument['title']))
          {
            $pageCount = $this->pdf->setSourceFile($__appvar['tempdir'].'/'.$document);
            for($i=1;$i<=$pageCount;$i++)
            {
              $tplIdx = $this->pdf->importPage($i);
              if(strpos($this->pdf->tpls[$tplIdx]['buffer'],$zoekDocument['title'])!==false)
              { 
                 logscherm("P$n (\"$document\" p$i) = ".$zoekDocument['title']."");
                 $size =   $this->pdf->getTemplateSize($tplIdx);
                 if ($size['w'] > $size['h']) 
                 {
                   $this->pdf->AddPage('L');
                   $size=array('w'=>297,'h'=>210);
                 }
                 else 
                 {
                   $this->pdf->AddPage('P');
                   $size=array('w'=>210,'h'=>297);
                 }
                 $n++;
                 $this->pdf->useTemplate($tplIdx,0,0,$size['w'],$size['h']);
                // if($size['w']==210)
              //     $this->pdf->Rotate(45);
              }
            }  
          }
          else
          {
            $pageCount = $this->pdf->setSourceFile($__appvar['tempdir'].'/'.$document);
            for($i=1;$i<=$pageCount;$i++)
            {
              logscherm("P$n (\"$document\" p$i) = ".$zoekDocument['name']."");
              $tplIdx = $this->pdf->importPage($i);
              
              $size =   $this->pdf->getTemplateSize($tplIdx);
              $this->pdf->AddPage('L');
              if ($size['w'] > $size['h'])
              {
                $size=array('w'=>297,'h'=>210);
                $this->pdf->Rotate(0);
                $this->pdf->useTemplate($tplIdx,0,0,$size['w'],$size['h']); //
              }
              else 
              {
                $size=array('w'=>210,'h'=>297);
                $this->pdf->Rotate(-90);
                $this->pdf->useTemplate($tplIdx,-8,-297+20,$size['w'],$size['h']); //
              }
              $n++;
                     
              
             // if($size['w']==210)
                 //  $this->pdf->Rotate(45);
            }
          }
          $geladenDocumenten[$document]=$document;
        }
      }
    }  
  }
  
  
  function genereerPdf($file)
  {
    global $__appvar;
    
    $this->pdf->Output($file);
    $this->initPdf();
    
  }
  
  function pushPdf($filename)
  {
    
    ?>
    <script type="text/javascript">
function pushpdf(file,save)
{
        var width='800';
        var height='600';
        var target = '_blank';
        var location = 'pushFile.php?file=' + file;
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
pushpdf('<?=$filename?>',1);
</script>
  <?

  }
  
  function createZip($bestanden)
  {
     global $__appvar;
     include_once($__appvar["basedir"]."/classes/pclzip.lib.php");
     $zipfile=$__appvar['tempdir']."/export.zip";
     $zip=new PclZip($zipfile);
     $zip->create($bestanden,PCLZIP_OPT_REMOVE_ALL_PATH);
     foreach($bestanden as $file)
       unlink($file);
     return $zipfile;  
  } 
  
  
  
}


if($_POST['type']=='pdf')
{
  $afdruk = new mergeDigiDoc($_POST);
  
  $afdruk->getPortefeuilles();
  if($_POST['save']==1)
    $lossePdfs=true;
  else
    $lossePdfs=false;  
  $uitvoerPdfs=array();
  foreach($afdruk->portefeuilles as $portefeuille=>$portefeuilleData)
  {
    $documents=$afdruk->getDocuments($portefeuille);//$crmId
    $afdruk->getPages($documents);
    $afdruk->verwijderTijdelijkeBestanden($documents);
    if($lossePdfs==true)
    {
      $uitvoerFile=$__appvar['tempdir'].'/'.$portefeuille.'.pdf';
      $afdruk->genereerPdf($uitvoerFile);
      $uitvoerPdfs[]=$uitvoerFile;
    }
  }
  
  if($lossePdfs==true)
  {
    $afdruk->createZip($uitvoerPdfs);
    $afdruk->pushPdf('export.zip');
  }
  else
  {  
    $afdruk->genereerPdf($__appvar['tempdir'].'/samenstelling.pdf');
    $afdruk->pushPdf('samenstelling.pdf');
  }
  exit;
}



include_once("../classes/selectOptieClass.php");
$type='portefeuille';
// selecteer de 1e vermogensbeheerder uit de tabel vermogensbeheerders voor de selectie vakken.

$selectie=new selectOptie();
$selectie->getInternExternActive();
$html .= $selectie->getSelectieMethodeHTML($PHP_SELF);
$html .= $selectie->getInternExternHTML($PHP_SELF);
$html.='</form>';

$_SESSION['submenu'] = New Submenu();
$_SESSION['submenu']->addItem($html,"");
$_SESSION['submenu']->onLoad = " onLoad=\"parent.frames['content'].selectTab()\" ";
$_SESSION['NAV'] = "";
$content['javascript'] .= "";
$content['calendarinclude'] = "<script language=JavaScript src=\"javascript/algemeen.js\" type=text/javascript></script>";
$kal = new DHTML_Calendar();
$content['calendar'] = $kal->get_load_files_code();
$content['jsincludes'] .= "\n<script language=JavaScript src=\"javascript/sack/tw-sack.js\" type=text/javascript></script>\n";
$content['body']='onload="javascript:periodeSelected();"';
echo template($__appvar["templateContentHeader"],$content);
$totdatum = getLaatsteValutadatum();
?>
<script type="text/javascript">

function merge(zip)
{
  document.selectForm.action = "rapportBackofficeMerge.php";
	document.selectForm.target = "generateFrame";
	document.selectForm.save.value=zip;
	document.selectForm.type.value="pdf";
	selectSelected();
	document.selectForm.submit();
	document.selectForm.target = "";
	document.selectForm.action = "";
}

<?=$selectie->getSelectJava();?>

function periodeSelected()
{
    var theForm = document.selectForm.elements, z = 0;
    var waarde='';
    var CRM_rapport_vink = 0;
    for(z=0; z<theForm.length;z++)
    {
     if(theForm[z].name == "periode")
     {
        if(theForm[z].checked)
        {
          waarde=theForm[z].value;
        }
     }
     if(theForm[z].name == "inclFactuur" && theForm[z].type=='checkbox'){checkIndex=z;}
    }
    if(waarde=='Kwartaalrapportage'){$('#factuurinfo').show();}
    else{$('#factuurinfo').hide();theForm[checkIndex].checked=false;document.selectForm.factuurnummer.value='';}

    checkRapportageInstelling();
}



function checkRapportageInstelling()
{
 <?if($rdata['check_portaalCrmVink']==0){?>
    var theForm = document.selectForm.elements, z = 0;
    var waarde='';
    var CRM_rapport_vink = 0;
    for(z=0; z<theForm.length;z++)
    {
     if(theForm[z].name == "periode")
     {
        if(theForm[z].checked)
        {
          waarde=theForm[z].value;
        }
     }
     if(theForm[z].name == "CRM_rapport_vink" && theForm[z].type=='checkbox' && theForm[z].checked){CRM_rapport_vink=1;checkIndex=z;}
    }
    if(waarde=='Clienten' && CRM_rapport_vink==1)
    {
      alert("<?=vt("Gebruikte selectie `Alle clienten` en `CRM rapportage instellingen` is niet mogelijk")?>.");
      theForm[checkIndex].checked=false;
    }
  <?} ?>  
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
	}
</script>

<br><br>

<form method="POST" name="selectForm">
<input type="hidden" name="posted" value="true" />
<input type="hidden" name="stap" value="" />
<input type="hidden" name="save" value="" />
<input type="hidden" name="type" value="" />
<input type="hidden" name="rapport_types" value="" />
<input type="hidden" name="portefeuilleIntern" value="" />
<input type="hidden" name="exportRap" value="" />


<table border="0">
<tr>
<td width="540" valign="top">
<fieldset id="Selectie" >
<legend accesskey="S"><?=vt("Selectie")?></legend>
<?
echo $selectie->createDatumSelectie($_SESSION['backofficeSelectie']);
if($_SESSION['selectieMethode'] == 'portefeuille')
{
?>
<script language="Javascript">

</script>
<table cellspacing="0" border = 1>

<?
  $DB = new DB();
  if(checkAccess($type))
  {
  	$join = "";
  	$beperktToegankelijk = '';
  }
  else
  {
  	$join = "INNER JOIN VermogensbeheerdersPerGebruiker ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker = '".$USR."'
  	         JOIN Gebruikers ON Gebruikers.Gebruiker = VermogensbeheerdersPerGebruiker.Gebruiker ";
  	$beperktToegankelijk = " AND (Portefeuilles.beperktToegankelijk = '0' OR  Gebruikers.beperkingOpheffen = '1' ) ";
  }


	//$query = "SELECT Portefeuille, Client FROM Portefeuilles ".$join. " WHERE Portefeuilles.Einddatum  >=  NOW() $beperktToegankelijk ORDER BY Client ";

	$query=$selectie->queries['ClientPortefeuille'];
  $DB->SQL($query);
  $DB->Query();
  while($gb = $DB->NextRecord())
    $pData[$gb['Portefeuille']]=$gb;
  echo "<br><br>";
  echo $selectie->createEnkelvoudigeSelctie($pData,$_SESSION['backofficeSelectie']);
  echo "<br><br>";
}
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
  $opties=array('Risicoklasse'=>'Risicoklasse','AFMprofiel'=>'AFMprofiel','SoortOvereenkomst'=>'SoortOvereenkomst','Remisier'=>'Remisier');
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
<div id="factuurinfo">
<div class="formblock">
<div class="formlinks"> <?=vt("Factuur toevoegen")?> </div>
<div class="formrechts">
<input type="hidden" value="0" name="inclFactuur">
<input type="checkbox" name="inclFactuur" value="1" <?if($_SESSION['backofficeSelectie']['inclFactuur'] > 0) echo "checked";?>>
<input type="text" name="factuurnummer" size="4" id="inclFactuurCheck" value="<?=$_SESSION['backofficeSelectie']['factuurnummer']?>">
</div>
</div>
</fieldset>
</td>
<td valign="top">
<br><br><div class="buttonDiv" style="width:150px" onclick="javascript:merge('0');">&nbsp;&nbsp;<?=maakKnop('pdf.png',array('size'=>16))?> <?=vt("Samenvoegen")?> </div>
<br>
<div class="buttonDiv" style="width:150px" onclick="javascript:merge('1');">&nbsp;&nbsp;<?=maakKnop('pdf.png',array('size'=>16))?> <?=vt("Export")?> </div><br>
</div>

</td>
</tr>
</table>
</form>

<?echo progressFrame();?>
<?
if($__debug) {
	echo getdebuginfo();
}
echo template($__appvar["templateRefreshFooter"],$content);
