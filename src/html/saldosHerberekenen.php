<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2019/07/20 16:31:57 $
 		File Versie					: $Revision: 1.15 $

 		$Log: saldosHerberekenen.php,v $
 		Revision 1.15  2019/07/20 16:31:57  rvv
 		*** empty log message ***
 		
 		Revision 1.14  2018/10/13 17:16:37  rvv
 		*** empty log message ***
 		
 		Revision 1.13  2015/06/15 06:48:49  cvs
 		*** empty log message ***
 		
 		Revision 1.12  2015/06/11 16:03:49  cvs
 		*** empty log message ***
 		
 		Revision 1.11  2014/11/26 16:46:01  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2014/11/15 18:41:19  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2014/11/15 18:29:20  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2010/10/09 14:51:38  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2008/05/16 08:04:51  rvv
 		*** empty log message ***

 		Revision 1.6  2006/01/19 07:40:24  cvs
 		test


*/
if($_SERVER['argv'][0]=='saldosHerberekenen.php')
{
  $disable_auth=true;
  $_POST['posted']=true;
  $_POST['vanRekening']='';
  $_POST['tmRekening']='';
  $_POST['jaar']=date('Y');
  $commandline=true;
  $USR='sys';
}
else
{
  $commandline=false;
}

include_once("wwwvars.php");
include_once('../classes/AE_cls_progressbar.php');

session_start();
$_SESSION['NAV'] = "";
session_write_close();

if($commandline==true)
  $saldi = new saldosHerberekenen(false);
else
{
  $saldi = new saldosHerberekenen();
  $saldi->header();
}

if($_POST['posted'] == true)
  $saldi->processPost();
else  
  $saldi->createHtmlBody();

if($commandline==true)
  $saldi->sendEmailLog();

exit;

class saldosHerberekenen
{

  function saldosHerberekenen($verbose=true)
  {
    $this->verbose=$verbose;
    $this->log=array();
  }
  
  function header()
  {
    global $__appvar;
$content['jsincludes'] .= '<link rel="stylesheet" href="style/smoothness/jquery-ui-1.11.1.custom.css">';
$content['jsincludes'] .= "<script type=\"text/javascript\" src=\"javascript/jquery-min.js\"></script>";
$content['jsincludes'] .= "<script type=\"text/javascript\" src=\"javascript/jquery-ui-min.js\"></script>";
    echo template($__appvar["templateContentHeader"],$content);    
  }

  function processPost()
  {
    global $USR;
    if($this->verbose==true)
    {
	    $prb = new ProgressBar();	// create new ProgressBar
	    $prb->pedding = 2;	// Bar Pedding
	    $prb->brd_color = "#404040 #dfdfdf #dfdfdf #404040";	// Bar Border Color
	    $prb->setFrame();          	                // set ProgressBar Frame
	    $prb->frame['left'] = 50;	                  // Frame position from left
	    $prb->frame['top'] = 	80;	                  // Frame position from top
	    $prb->addLabel('text','txt1','Bezig ...');	// add Text as Label 'txt1' and value 'Please wait'
	    $prb->addLabel('procent','pct1');	          // add Percent as Label 'pct1'
	    $prb->show();	                              // show the ProgressBar

	    $prb->moveStep(0);
	    $prb->setLabelValue('txt1','Bezig met herberekenen saldo \'s');
	    $pro_step = 0;
    }
 
	  // selecteer alle rekeningen
  
  	if(!empty($_POST['jaar']))
  	{
	  	$jaar = $_POST['jaar'];
	  }
  	else
  	{
		  echo "geen jaar opgegeven!";
		  exit;
	  }
    
    $query = "SELECT DISTINCT(Rekeningafschriften.Rekening) FROM Rekeningafschriften WHERE ";
    if($_POST['vanRekening'] <> '')
      $query .=" Rekeningafschriften.Rekening >= '".$_POST['vanRekening']."' AND ";
    if($_POST['tmRekening'] <> '')
  	  $query .=" Rekeningafschriften.Rekening <= '".$_POST['tmRekening']."' AND ";
		$query.= "YEAR(Rekeningafschriften.Datum) = '".$jaar."' ORDER BY Rekeningafschriften.Rekening ";

  	$DB = new DB();
  	$DB->SQL($query);
  	$DB->Query();

	  $pro_multiplier = (100 / $DB->Records());

	  while($rekening = $DB->NextRecord())
	  {  
 	  	$pro_step += $pro_multiplier;
 		  if($this->verbose==true)
        $prb->moveStep($pro_step);

 	  	// loop alle afschriften, check saldi

		  // set YEAR!

  		$query = "SELECT ROUND(SUM(Rekeningmutaties.Bedrag),2) AS controle , ".
	  	" ROUND((Rekeningafschriften.NieuwSaldo - Rekeningafschriften.Saldo),2) AS mutatie , ".
	  	" Rekeningafschriften.* ".
	  	" FROM Rekeningafschriften ".
	  	" LEFT JOIN Rekeningmutaties ON Rekeningafschriften.Rekening = Rekeningmutaties.Rekening ".
	  	" AND Rekeningafschriften.Afschriftnummer = Rekeningmutaties.Afschriftnummer ".
	  	" WHERE Rekeningafschriften.Rekening = '".$rekening['Rekening']."' ".
	  	" AND YEAR(Rekeningafschriften.Datum) = '".$jaar."' ".
	  	" GROUP BY Rekeningafschriften.Afschriftnummer ".
	  	" ORDER BY Afschriftnummer ";

  
  		$DB3 = new DB();
	  	$DB3->SQL($query);
		  $DB3->Query();
	  	while($afschrift = $DB3->NextRecord())
	  	{
			// extra controle op vorige afschriftsaldo is substr laatste 3 > 000
			$digits = substr($afschrift['Afschriftnummer'],-3);

			$saldoOK = true;
			if($digits > 0)
			{
				// SELECT Afschriftnummer, NieuwSaldo FROM Rekeningafschriften WHERE YEAR(Datum) = '2005' AND Rekening = '035569EUR' AND Afschriftnummer < 11  ORDER BY Afschriftnummer DESC LIMIT 1
				$query = "SELECT Afschriftnummer, NieuwSaldo FROM Rekeningafschriften WHERE YEAR(Datum) = '".$jaar."' AND Rekening = '".$rekening['Rekening']."' AND Afschriftnummer < ".$afschrift['Afschriftnummer']."  ORDER BY Afschriftnummer DESC LIMIT 1";
				$DB4 = new DB();
				$DB4->SQL($query);
				$DB4->Query();
				if($DB4->records() > 0)
				{
					$check = $DB4->nextRecord();
					if(round($check['NieuwSaldo'],2) <> round($afschrift['Saldo'],2))
					{
		  		  $msg="<br>Saldo komt komt niet overeen met vorige eindsaldo! ".$afschrift['Rekening']."(vorige eindsaldo ".round($check['NieuwSaldo'],2)." beginsaldo ".round($afschrift['Saldo'],2).") ";
            if($this->verbose==true)
              echo $msg;
            else
              $this->log[]=$msg;
						$saldoOK = false;
					}
					else
					{
						$saldoOK = true;
					}
				}

			}

			//listarray($afschrift);
			//listarray($check);
			// check of controle gelijk is aan nieuw Saldo!
			if((round($afschrift['controle'],2) <> round($afschrift['mutatie'],2)) || $saldoOK == false)
			{
				if($saldoOK == false)
				{
					// zet begin saldo
					$afschrift['Saldo'] = $check['NieuwSaldo'];
				}
				else
				{
					// fout , mutatie verschil.
      		$msg="<br>Mutatieverschilgevonden van ".($afschrift['controle'] -$afschrift['mutatie'])." op Rekening ".$afschrift['Rekening']." , afschrift ".$afschrift['Afschriftnummer'];
          if($this->verbose==true)
            echo $msg;
          else
            $this->log[]=$msg;
				}
				$nieuwSaldo = $afschrift['Saldo'] + $afschrift['controle'];


				$DB4 = new DB();
				// update dit afschrift met goede nieuwSaldo
				$query = " UPDATE Rekeningafschriften SET ".
								 " Rekeningafschriften.NieuwSaldo = '".$nieuwSaldo."' ".
								 ", Rekeningafschriften.Saldo = '".$afschrift['Saldo']."' ".
								 ", Rekeningafschriften.change_user = '".$USR."' ".
								 ", Rekeningafschriften.change_date = NOW() ".
								 " WHERE Rekeningafschriften.Rekening = '".$afschrift['Rekening']."' AND ".
								 " Rekeningafschriften.Afschriftnummer = '".$afschrift['Afschriftnummer']."'";
				$DB4->SQL($query);
				$DB4->Query();
   		  $msg="<br>Update afschrift ".$afschrift['Afschriftnummer']." nieuw saldo: ".$nieuwSaldo;
        if($this->verbose==true)
          echo $msg;
        else
          $this->log[]=$msg;
				// update volgende afschrift met nieuw begin saldo.
				// maak een loop over de rest van de rekeningenafschriften en update waarden.
				$query = "SELECT ROUND(SUM(Rekeningmutaties.Bedrag),2) AS controle , ".
				" ROUND((Rekeningafschriften.NieuwSaldo - Rekeningafschriften.Saldo),2) AS mutatie , ".
				" Rekeningafschriften.* ".
				" FROM Rekeningafschriften ".
				" JOIN Rekeningmutaties ON Rekeningafschriften.Rekening = Rekeningmutaties.Rekening ".
				" AND Rekeningafschriften.Afschriftnummer = Rekeningmutaties.Afschriftnummer ".
				" WHERE Rekeningafschriften.Rekening = '".$afschrift['Rekening']."' AND ".
				" Rekeningafschriften.Afschriftnummer > '".$afschrift['Afschriftnummer']."' AND ".
				" YEAR(Rekeningafschriften.Datum) = '".$jaar."'".
				" GROUP BY Rekeningafschriften.Afschriftnummer ".
				" ORDER BY Afschriftnummer ";
				$DB4->SQL($query);
				$DB4->Query();

				$msg="<br>loop over de rest van de afschriften bij rekening ".$afschrift['Rekening'];
        if($this->verbose==true)
          echo $msg;
        else
          $this->log[]=$msg;
          
				while($rekdata = $DB4->nextRecord())
				{
					$saldo = $nieuwSaldo;
					$nieuwSaldo = $saldo + $rekdata['controle'];
					$query = " UPDATE Rekeningafschriften SET Rekeningafschriften.Saldo = '".$saldo."' ".
									 ", Rekeningafschriften.NieuwSaldo = '".$nieuwSaldo."' ".
									 ", Rekeningafschriften.change_user = '".$USR."' ".
									 ", Rekeningafschriften.change_date = NOW() ".
									 " WHERE Rekeningafschriften.Rekening = '".$rekdata['Rekening']."' AND ".
									 " Rekeningafschriften.Afschriftnummer = '".($rekdata['Afschriftnummer'])."' ".
									 " ";

					$DB5 = new DB();
					$DB5->SQL($query);
					$DB5->Query();

					$msg="<br>Update afschrift ".($rekdata['Afschriftnummer'])." begin saldo: ".$saldo." nieuw saldo ".$nieuwSaldo;
          if($this->verbose==true)
            echo $msg;
          else
            $this->log[]=$msg;
				}

				$DB4->SQL($query);
				$DB4->Query();


				flush();
			}
			$vorigeSaldo = $afschrift['NieuwSaldo'];

		}
	}
  if($this->verbose==true)
	  $prb->hide();
}

function createHtmlBody()
{
	// selecteer alle rekeningen
//	$query = "SELECT DISTINCT(Rekeningafschriften.Rekening) FROM Rekeningafschriften ".
//					 " ORDER BY Rekeningafschriften.Rekening ";
//
//	$query = "SELECT Rekening FROM Rekeningen ORDER BY Rekening LIMIT 100";
	$DB = new DB();
//	$DB->SQL($query);
	//$DB->Query($query);
  $rec = $DB->lookupRecordByQuery("SELECT Rekening FROM Rekeningen WHERE consolidatie=0 ORDER BY Rekening");
  $eersteRekening = $rec["Rekening"];
  
  $rec = $DB->lookupRecordByQuery("SELECT Rekening FROM Rekeningen WHERE consolidatie=0 ORDER BY Rekening DESC");
  $laatsteRekening = $rec["Rekening"];
  
//	while($rek = $DB->NextRecord())
//	{
//		$options[] = $rek['Rekening'];
//		$laatstSelect = $rek['Rekening'];
//	}

	$jaar = date("Y",mktime());
	for($a=0; $a < 10; $a++)
	{
		$JaarOptions[] = $jaar-$a;
	}
  
?>
<style>
  .ui-menu {
     width:400px;
     height: 400px;
  }
  .ui-autocomplete {
    font-size: .8em;
    max-height: 500px;
    overflow-y: auto;
    /* prevent horizontal scrollbar */
    overflow-x: hidden;
  }
  
</style>
<form action="<?=$PHP_SELF?>" method="POST" target="importFrame" name="controleForm">
<!-- MAX_FILE_SIZE must precede the file input field -->
<input type="hidden" name="posted" value="true" />
<input type="hidden" name="aanvullen" value="0" />
<!-- Name of input element determines name in $_FILES array -->
<b><?= vt("Saldo's herberekenen"); ?></b><br><br>
<?php
if($_error) echo "<b style=\"color:red;\">".$_error."</b>";
?>

<div class="form">
  <div class="formblock">
    <div class="formlinks"> <?= vt('Van rekening'); ?>: </div>
    <div class="formrechts">
      <input name="vanRekening" id="vanRekening" value="<?=$eersteRekening?>" />
    </div>
  </div>
</div>  

<div class="form">
  <div class="formblock">
    <div class="formlinks"> <?= vt('T/m rekening'); ?>: </div>
    <div class="formrechts">
      <input name="tmRekening" id="tmRekening" value="<?=$laatsteRekening?>" />
    </div>
  </div>
</div>  

<div class="form">
  <div class="formblock">
    <div class="formlinks"> <?= vt('Jaar'); ?>: </div>
    <div class="formrechts">
      <select name="jaar">
      <?=SelectArray(date("Y",mktime()),$JaarOptions)?>
      </select>
    </div>
  </div>
</div>
  

<div class="formblock">
    <div class="formlinks"> &nbsp;</div>
    <div class="formrechts">
      <input type="button" value="Verwerken" onClick="document.controleForm.submit();">
      &nbsp;&nbsp;&nbsp;&nbsp;
    </div>
</div>


</form>

<div class="formblock">
  <div class="formlinks"> &nbsp;</div>
  <div class="formrechts">
    <iframe width="600" height="400" name="importFrame"></iframe>
  </div>
</div>


<script>
   $(document).ready(function(){
     
      $("#vanRekening").focus().select();
      $("#vanRekening").change(function(){
        $("#tmRekening").val($("#vanRekening").val());
      });
      $("#vanRekening").autocomplete(
      {
        source: "lookups/jq_Rekening.php",
        mustMatch:true,
        select: function( event, ui ) 
        {   
          $("#vanRekening").val(ui.item.Rekening);
          return false;
        },
        minLength: 2,
        autoFocus: true,
        delay : 0 
      });
      
      $("#tmRekening").autocomplete(
      {
        source: "lookups/jq_Rekening.php",
        mustMatch:true,
        select: function( event, ui ) 
        {   
          $("#tmRekening").val(ui.item.Rekening);
          return false;
        },
        minLength: 2,
        autoFocus: true,
        delay : 0 
      });
   });
</script>


<?
global $__appvar;
$content = array();
echo template($__appvar["templateRefreshFooter"],$content);


}



function sendEmailLog()
{
  
  
  $cfg=new AE_config();
  $mailserver=$cfg->getData('smtpServer');
  $fondsEmail=$cfg->getData('fondsEmail');
  //$fondsEmail='rvv@aeict.nl';
  
  $html='';
  foreach($this->log as $msg)
  {
    $html.="$msg<br>\n";
  }
  if($fondsEmail !="" && $mailserver !='' && $html <> '')
  {
    include_once('../classes/AE_cls_phpmailer.php');
    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->From     = $fondsEmail;
    $mail->FromName = "Airs";
    $mail->Body    = $html;
    $mail->AltBody = html_entity_decode(strip_tags($html));
    $mail->AddAddress($fondsEmail,$fondsEmail);
    $mail->Subject = "Saldo herberekening ".date('d-m-Y H:i:s');
    storeControleMail('SaldoHerberekening',$mail->Subject,$html);
    $mail->Host=$mailserver;
    if(!$mail->Send())
      echo "Verzenden van e-mail mislukt.";
    else
      echo "Email verzonden.";  
  }
}

}
?>