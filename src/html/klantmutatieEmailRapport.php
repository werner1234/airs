<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2014/03/22 15:48:26 $
 		File Versie					: $Revision: 1.3 $

 		$Log: klantmutatieEmailRapport.php,v $
 		Revision 1.3  2014/03/22 15:48:26  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2011/12/31 18:16:36  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2011/04/30 16:23:58  rvv
 		*** empty log message ***




*/

$disable_auth = true;
include_once("wwwvars.php");
include_once('../classes/AE_cls_phpmailer.php');

$cfg=new AE_config();
$mailserver=$cfg->getData('smtpServer');

$date=date("Y-m-d");

$db=new DB();
$query="SELECT
klantMutaties.tabel,
klantMutaties.recordId,
klantMutaties.veld,
klantMutaties.oudeWaarde,
klantMutaties.nieuweWaarde,
klantMutaties.Vermogensbeheerder,
klantMutaties.verwerkt,
klantMutaties.change_date
FROM
klantMutaties
WHERE
klantMutaties.verwerkt=1 AND
date(klantMutaties.change_date)='$date'
ORDER BY Vermogensbeheerder,change_date
";
$db->SQL($query);
$db->Query();
while($data=$db->nextRecord())
{
  $mutatiesPerVermogensbeheer[$data['Vermogensbeheerder']][]=$data;
}

foreach ($mutatiesPerVermogensbeheer as $vermogensbeheerder=>$data)
{
  $query="SELECT Vermogensbeheerders.Naam,Vermogensbeheerders.Email FROM Vermogensbeheerders WHERE Vermogensbeheerders.Vermogensbeheerder='$vermogensbeheerder'";
  $db->SQL($query);
  $vermogensbeheerderData=$db->lookupRecord();
  if($vermogensbeheerderData['Email'] <> '')
  {
    echo "$vermogensbeheerder <br>\n";
    $html="
    Hieronder ontvangt u de verwerkte mutaties van $date. <br><br>\n
    <table><tr><td>Tabel</td><td>Veld</td><td>Oude waarde</td><td>Nieuwe waarde</td><td>Verwerk tijd</td></tr>\n";
    foreach ($data as $id=>$mutatie)
    {
      if($mutatie['tabel']=='Rekeningmutaties')
      {
        $db->SQL("SELECT Rekening FROM Rekeningmutaties WHERE id='".$mutatie['recordId']."'");
        $tmpRekening=$db->lookupRecord();
        $mutatie['tabel'].=" rekening:".$tmpRekening['Rekening'];
      }

     $html.="<tr><td>".$mutatie['tabel']."</td><td>".$mutatie['veld']."</td><td>".$mutatie['oudeWaarde']."</td><td>".$mutatie['nieuweWaarde']."</td><td>".$mutatie['change_date']."</td></tr>\n";
    }
    $html.="</table>";
    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->From     = 'info@airs.nl';
    $mail->FromName = "Airs";
    $mail->Body    = $html;
    $mail->AltBody = html_entity_decode(strip_tags($html));
    $mail->AddAddress($vermogensbeheerderData['Email'],$vermogensbeheerderData['naam']);
    //$mail->AddBCC('info@airs.nl','Airs');
    $mail->Subject = "Verwerkte AIRS klanmutaties op $date.";
    $mail->Host=$mailserver;
    if(!$mail->Send())
      echo "Verzenden van e-mail aan $vermogensbeheerder mislukt.<br>\n";
    else
      echo "Mutatiemail verzonden aan $vermogensbeheerder.<br>\n";
  }
}
?>