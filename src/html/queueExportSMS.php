<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2019/08/07 15:29:44 $
    File Versie         : $Revision: 1.2 $

    $Log: queueExportSMS.php,v $
    Revision 1.2  2019/08/07 15:29:44  rvv
    *** empty log message ***

    Revision 1.1  2019/08/07 12:24:15  rvv
    *** empty log message ***



*/

include_once("wwwvars.php");


if($__appvar['master'] == false)
  exit;

$DB = new DB();
$DB->SQL("SELECT * FROM Bedrijfsgegevens ORDER BY Bedrijf");
$DB->Query();

$bedrijven=array();
if($_GET['posted']<>'true')
  $bedrijven[] = 'alles';

while($bedrijfdata = $DB->NextRecord())
{
  $bedrijven[] = $bedrijfdata['Bedrijf'];
}

$exportLog=array();
if($_GET['posted']=='true')
{
  $updateBedrijven=array();
  if($_GET['Bedrijf']=='alles')
  {
    $updateBedrijven=$bedrijven;
  }
  else
  {
    $updateBedrijven[]=$_GET['Bedrijf'];
  }
  $db2=new DB();
  $exportId = date("YmdHis");

  foreach($updateBedrijven as $bedrijf)
  {
  
    $query = "INSERT INTO updates SET ".
      "  exportId = '".$exportId."' ".
      ", Bedrijf = '".$bedrijf."' ".
      ", type = 'smsStatus' ".
      ", jaar = '".$_GET['exportType']."' ".
      ", filename = '' ".
      ", filesize = '' ".
      ", server = '' ".
      ", username = '' ".
      ", password = '' ".
      ", consistentie = '' ".
      ", add_date = NOW() ".
      ", add_user = '".$USR."' ".
      ", change_date = NOW() ".
      ", change_user = '".$USR."' ";
  
    $DB = new DB(2);
    
    $DB->SQL($query);
    if($DB->Query())
    {
      $exportLog[]="SMS uitschakelstatus naar  '".$_GET['exportType']."' update voor $bedrijf klaargezet.";
    }
    else
    {
       $exportLog[]="<b>SMS uitschakelstatus naar  '".$_GET['exportType']."' update voor $bedrijf mislukt om in de queue te plaatsen!</b>";
    }
  }
}


echo template($__appvar["templateContentHeader"],$content);
?>
  
  <form action="queueExportSMS.php" method="GET" name='selectForm' id='selectForm' >
    <input type="hidden" name="posted" value="true" />
    
    <b><?= vt('Klaarzetten van een update om de sms wachtwoord verificatie uit te schakelen.'); ?></b><br><br>
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
        
        <div class="formblock">
          <div class="formlinks"> &nbsp;</div>
          <div class="formrechts">
            <input type="radio" name="exportType" id="deactiveren" value="0" checked> <?= vt('SMS uitschakeling deactiveren'); ?>
            <input type="radio" name="exportType" id="activeren" value="1"> <?= vt('SMS uitschakeling activeren'); ?>
          </div>
        </div>
        
        <div class="formblock">
          <div class="formlinks"> &nbsp;</div>
          <div class="formrechts">
            <input type="submit" value="Exporteren" >
          </div>
        </div>
  
      <div class="formblock">
        <div class="formlinks"> &nbsp;</div>
        <div class="formrechts">
      <?php
       foreach($exportLog as $message)
       {
         logscherm($message);
       }
      ?>
        </div>
      </div>
  </form>


<?


echo template($__appvar["templateRefreshFooter"],$content);



?>