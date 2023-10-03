<?php
include_once("wwwvars.php");
include_once('../classes/AE_cls_progressbar.php');

if($__appvar['master'] == false)
  exit;

$updateSoort=array(''=>'---',
                   'dagelijks'=>'Dagelijkse update',
                   'vanafLaatste'=>'Vanaf laatste update',
                   'correctie'=>'Correctie update',
                   'tabel'=>'Alleen tabel',
                   'database'=>'Complete database');
                   
if($_GET['lookup']==1)
{
  $DB = new DB();
 	$query="SELECT SUM(koersExport) as koersExport FROM Vermogensbeheerders, VermogensbeheerdersPerBedrijf WHERE VermogensbeheerdersPerBedrijf.Bedrijf = '".$_GET['form']['Bedrijf']."' AND VermogensbeheerdersPerBedrijf.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder ";
	$DB->SQL($query);
  $koersExport=$DB->lookupRecord();
  if($koersExport['koersExport'] > 0)
  {
    include_once('queueExportQueryKoers.php');
    unset($updateSoort['correctie']);
    unset($updateSoort['tabel']);
    unset($updateSoort['database']);
  }
  else  
  {
    include_once('queueExportQuery.php');
    $disableTable=0;
  }
  unset($exportQuery['afmCategorien']);
  $tabellen=array_keys($exportQuery);
  natcasesort($tabellen);
  
  if($_GET['form']['updateSoort']=='correctie')
    $default=array('---');
  else
    $default=array('---');
  $tabellen=array_merge($default,$tabellen);
  echo json_encode(array('tabellen' => $tabellen,'updateSoort'=>$updateSoort));
  exit;
}
elseif($_GET['lookup']==2)
{
 
  $DB = new DB();
 	$query="SELECT LaatsteUpdate,laatsteDagelijkeUpdate FROM Bedrijfsgegevens WHERE Bedrijf = '".$_GET['form']['Bedrijf']."'";  
	$DB->SQL($query);
  $laatsteUpdate=$DB->lookupRecord();
  if($_GET['form']['updateSoort']=='dagelijks')
  {
    $melding='';
    if(db2jul($laatsteUpdate['laatsteDagelijkeUpdate']) > db2jul(date('Y-m-d')))
      $melding .='Er is al een dagelijkse update om '.$laatsteUpdate['laatsteDagelijkeUpdate']." klaargezet.";
       
    $dag=date('w');
    if($dag==1)
      $dagenTerug=3;
    else
      $dagenTerug=1;   
    $laatsteValutaDatum=getLaatsteValutadatum();
    if(db2jul($laatsteValutaDatum)<db2jul(date('Y-m-d',time()-86400*$dagenTerug)))
      $melding .='Er zijn nog geen koersen voor '.date('d-m-Y',time()-86400*$dagenTerug).' beschikbaar.';
      
    if($melding <> '')
      echo json_encode(array('fout' => 1,'melding'=>$melding." Toch doorgaan?"));
  }
  elseif(db2jul($laatsteUpdate['laatsteDagelijkeUpdate']) < db2jul(date('Y-m-d')) && $_GET['form']['updateSoort']=='vanafLaatste')
    echo json_encode(array('fout' => 1,'melding'=>'Er is nog geen dagelijkse update klaargezet. Laatste dagelijkse update was '.$laatsteUpdate['laatsteDagelijkeUpdate'].'. Toch doorgaan?'));  
  elseif($_GET['form']['updateSoort']=='correctie')
  {
    if($DB->QRecords("SELECT koersExport FROM Vermogensbeheerders INNER JOIN VermogensbeheerdersPerBedrijf ON Vermogensbeheerders.Vermogensbeheerder = VermogensbeheerdersPerBedrijf.Vermogensbeheerder WHERE koersExport=1 AND VermogensbeheerdersPerBedrijf.Bedrijf='".$_GET['form']['Bedrijf']."'"))
      echo json_encode(array('fout' => 2,'melding'=>'Correctie export bij koersonly niet mogelijk.'));  
    else
      echo json_encode(array('fout' => 0,'melding'=>'geen problemen'));
  }
  else
    echo json_encode(array('fout' => 0,'melding'=>'geen problemen'));
  exit;
}  
  
session_start();
$_SESSION[NAV] = "";
session_write_close();

$DB = new DB();
$DB->SQL("SELECT * FROM Bedrijfsgegevens ORDER BY Bedrijf");
$DB->Query();

$bedrijven = array();

while($bedrijfdata = $DB->NextRecord())
{
	$bedrijven[] = $bedrijfdata['Bedrijf'];
}



//$content = array();
$content['javascript'].='

function buildQueryArray(theFormName) {
  var theForm = document.forms[theFormName];
  var qs = new Object();
  for (e = 0; e < theForm.elements.length; e++) {
    if (theForm.elements[e].name != \'\') {
      qs[theForm.elements[e].name] = theForm.elements[e].value;
    }
  }
  return qs;
}

function toonVelden()
{
  $(\'#tabelSelectie\').hide();
  
  if($(\'#updateSoort\').val()==\'database\')
  {
    $(\'#naarBestandSelectie\').show();
  }
  else
  {
    $(\'#naarBestandSelectie\').hide();
    $(\'#radioQueueExport\').prop(\'checked\',true);
  }
}

function bedrijfChanged()
{
  toonVelden();

  $.ajax({
    type: "GET",
    url: "queueExport.php?lookup=1&bedrijf="+$(\'#Bedrijf\').val(),
    dataType: "json",
    async: false,
    data: {
      type: \'bedrijf\',
      form: buildQueryArray(\'selectForm\')
  
    },
    success: function(data, textStatus, jqXHR)
    {
      $(\'select[name="tabel"]\').html(\'\');
      $.each(data.tabellen, function(index, value) {
        $(\'select[name="tabel"]\').append($(\'<option>\').text(value).attr(\'value\', value));
      });
 
       $(\'select[name="updateSoort"]\').html(\'\');
      $.each(data.updateSoort, function(index, value) {
        $(\'select[name="updateSoort"]\').append($(\'<option>\').text(value).attr(\'value\', index));
      });     
    },
    error: function(jqXHR, textStatus, errorThrown)
    {
    }
  });
}

function updateSoortChanged()
{
  toonVelden();
  
  if($(\'#updateSoort\').val()==\'tabel\' || $(\'#updateSoort\').val()==\'correctie\')
  {
    
    $(\'#tabelSelectie\').show();
  $.ajax({
    type: "GET",
    url: "queueExport.php?lookup=1&bedrijf="+$(\'#Bedrijf\').val(),
    dataType: "json",
    async: false,
    data: {
      type: \'bedrijf\',
      form: buildQueryArray(\'selectForm\')
  
    },
    success: function(data, textStatus, jqXHR)
    {
      $(\'select[name="tabel"]\').html(\'\');
      $.each(data.tabellen, function(index, value) {
        $(\'select[name="tabel"]\').append($(\'<option>\').text(value).attr(\'value\', value));
      });
    },
    error: function(jqXHR, textStatus, errorThrown)
    {
    }
  });
  }
}

function checkDagUpdate()
{
  var ret=false;
  
 $.ajax({
    type: "GET",
    url: "queueExport.php?lookup=2&soort="+$(\'#updateSoort\').val(),
    dataType: "json",
    async: false,
    data: {
      type: \'bedrijf\',
      form: buildQueryArray(\'selectForm\')
    },
    success: function(data, textStatus, jqXHR)
    {
      if(data.fout==1)
      {
        ret=confirm(data.melding);
      }
      else if(data.fout==2)
      {
        alert(data.melding);
        ret=false;
      }
      else
      {
        ret=true; 
      }
    },
    error: function(jqXHR, textStatus, errorThrown)
    {
      alert("Geen laatste update datum kunnen ophalen.");
      ret=false;
    }
  });

  return ret;
}

$(function() {
    $(\'#selectForm\').submit(function() {

   if($(\'#updateSoort\').val()==\'\')
   {
     alert("Nog geen update soort gekozen.");
     return false;
   }
   if($(\'#updateSoort\').val()==\'dagelijks\' || $(\'#updateSoort\').val()==\'vanafLaatste\' || $(\'#updateSoort\').val()==\'correctie\')
   {
     if(checkDagUpdate()==1)
     {
      
       return true; 
     }
     else
     {
       return false;
     } 
   }
   if($(\'#updateSoort\').val()==\'database\')
   {
     return confirm("Weet u zeker dat u een volledige database wilt exporteren?");
   }
   
        return true; 
    });
});

';


echo template($__appvar["templateContentHeader"],$content);
?>

<form action="queueExportData.php" method="GET" name='selectForm' id='selectForm' >
<input type="hidden" name="posted" value="true" />

<b><?= vt('Export data'); ?></b><br><br>
<?php
if($_error) echo "<b style=\"color:red;\">".$_error."</b>";
?>
<div class="form">
<div class="formblock">
<div class="formlinks"> <?= vt('Bedrijf'); ?></div>
<div class="formrechts">
<select name="Bedrijf" id="Bedrijf" onchange="javascript:bedrijfChanged();">
<?=SelectArray($_GET["bedrijf"],$bedrijven)?>
</select>
</div>
</div>


<div class="form">
<div class="formblock">
<div class="formlinks"> <?= vt('Soort update'); ?></div>
<div class="formrechts">
<select id="updateSoort" name="updateSoort" onchange="javascript:updateSoortChanged();">
<?=SelectArray($_GET["updateSoort"],$updateSoort,true)?>
</select>
</div>
</div>

<div class="form" id="tabelSelectie" style="display: none;">
<div class="formblock">
<div class="formlinks"> <?= vt('Tabel'); ?></div>
<div class="formrechts">
<select name="tabel">
</select>
</div>
</div>
</div>


<div class="formblock" id="naarBestandSelectie" style="display: none;">
<div class="formlinks"> &nbsp;</div>
<div class="formrechts">
<input type="radio" name="exportType" id="radioFileExport" value="file"> <?= vt('Naar bestand'); ?>
</div>
</div>


<div class="formblock">
<div class="formlinks"> &nbsp;</div>
<div class="formrechts">
<input type="radio" name="exportType" id="radioQueueExport" value="queue" checked> <?= vt('Naar Queue'); ?>
</div>
</div>

  <div class="formblock">
    <div class="formlinks"> &nbsp;</div>
    <div class="formrechts">
      <input type="checkbox" name="telTotaal" id="telTotaal" value="1" checked> <?= vt('Tel totaal aantal records. (consistentie check)'); ?>
    </div>
  </div>

<div class="formblock">
<div class="formlinks"> &nbsp;</div>
<div class="formrechts">
<input type="submit" value="Exporteren" >
</div>
</div>
</form>

</div>

<?


echo template($__appvar["templateRefreshFooter"],$content);
?>