<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2013/02/03 09:03:11 $
File Versie					: $Revision: 1.2 $

$Log: CRM_pwdGen.php,v $
Revision 1.2  2013/02/03 09:03:11  rvv
*** empty log message ***

Revision 1.1  2011/03/23 16:57:38  rvv
*** empty log message ***

Revision 1.1  2011/03/13 18:36:37  rvv
*** empty log message ***

*/

include_once("wwwvars.php");

$content['pageHeader'] = "<br><div class='edit_actionTxt'><b>" . vt('CRM wachtwoord generator') . "</b></div><br>";
echo template($__appvar["templateContentHeader"],$content);

$selectVelden=array(''=>'Leeg',
                    'CRM_naw.naam'=>vt('Naam'),
                    'CRM_naw.geboortedatum'=>vt('Geboortedatum'),
                    'CRM_naw.voornamen'=>vt('Voornamen'),
                    'CRM_naw.tel1'=>vt('Telefoon 1'),
                    'CRM_naw.pc'=>vt('Postcode'),
                    'CRM_naw.verzendPc'=>vt('Verzend Postcode'),
                    'CRM_naw.portefeuille'=>vt('portefeuille'));
$veldOpties=array('CRM_naw.naam'=>array('type'=>'text','info'=>'0 is eerste teken'),
                  'CRM_naw.geboortedatum'=>array('type'=>'datum','info'=>'0,2=dd, 2,2=mm 4,4=YYYY 0,4=ddmm 2,6=mmYYYY '),
                  'CRM_naw.voornamen'=>array('type'=>'text','info'=>''),
                  'CRM_naw.tel1'=>array('type'=>'text','info'=>''),
                  'CRM_naw.pc'=>array('type'=>'text','info'=>''),
                  'CRM_naw.verzendPc'=>array('type'=>'text','info'=>''),
                  'CRM_naw.portefeuille'=>array('type'=>'text','info'=>''));

function createSelect($naam,$selectVelden,$selectedField)
{
  $txt='<select name="'.$naam.'">';
  foreach ($selectVelden as $key=>$omschrijving)
  {
    if($key==$selectedField)
      $selected='selected';
    else
      $selected='';
    $txt.='<option value="'.$key.'" '.$selected.' >'.$omschrijving."\n";
  }
  $txt.='</select>';
  return $txt;
}

function createVeld($naam,$selectVelden,$data,$extraInfo)
{
  $txt='<tr>';
  $txt.='<td>'.createSelect($naam.'Naam',$selectVelden,$data[$naam.'Naam']).'</td>
         <td><input name="'.$naam.'StartPos" size="3" type="text" value="'.$data[$naam.'StartPos'].'"></td>
         <td><input name="'.$naam.'AantalPos" size="3" type="text" value="'.$data[$naam.'AantalPos'].'"></td>
         <td>'.$extraInfo[$data[$naam.'Naam']]['info'].'</td>'.
  $txt='</tr>';
  return $txt;
}
$data=$_POST;
$veld=array();
if($_POST)
  foreach ($data as $key=>$value)
  {
    if(substr($key,0,4)=='veld')
      $veld['veld'.substr($key,4,1)][substr($key,5)]=$value;
  }


$db=new DB();
$query="SELECT ";
foreach ($veld as $veldNaam=>$waarden)
{
  if($waarden['Naam'] <> '')
  {
    $query.= $waarden['Naam']." as $veldNaam, ";
    $veldnaamConversie[$veldNaam]=$waarden['Naam'];
  }
  else
    unset($veld[$veldNaam]);
}

?>
<form method="POST" name="editForm">
<input type="hidden" name="action" value="">
<div class="formblock">
<table>
<tr><td width="100"><?= vt('Veldnaam'); ?></td><td width="100" ><?= vt('Start positie'); ?></td><td width="100" ><?= vt('Aantal posities'); ?></td><td width="100"><?= vt('Extra info'); ?></td></tr>
<?
for($n=1;$n<5+count($veld);$n++)
  echo createVeld('veld'.$n,$selectVelden,$data,$veldOpties);
?>
</table>
</div>
<div class="formblock">
<div class="formlinks"><input type="submit" value="Verwerk"> </div>
<div class="formrechts"> <input type="radio" name="actie" value="voorbeeld" checked> <?= vt('Voorbeeld'); ?> <input type="radio" name="actie" value="aanvullen"> <?= vt('Aanvullen'); ?> <input type="radio" name="actie" value="vernieuwen"> <?= vt('Alles vervangen'); ?></div>
</div>
</form>
<?

if($_POST)
{

if($_POST['actie']=='voorbeeld')
 $limit="limit 10";

$query.=" CRM_naw.wachtwoord,CRM_naw.id,CRM_naw.Portefeuille FROM CRM_naw WHERE CRM_naw.Portefeuille <> '' order by CRM_naw.Portefeuille $limit";
$db->SQL($query);
$db->Query();
while($data=$db->nextRecord())
  $nawRecords[$data['id']]=$data;

foreach ($nawRecords as $recordId=>$nawData)
{
  $newWachtwoord='';
  foreach ($veld as $veldNaam=>$veldPosities)
  {
    if($veldOpties[$veldnaamConversie[$veldNaam]]['type']=='datum')
    {
      $jul=db2jul($nawData[$veldNaam]);
      if($jul <> 0)
        $nawData[$veldNaam]=date("dmY",db2jul($nawData[$veldNaam]));
      else
        $nawData[$veldNaam]='00000000';
    }

    $newWachtwoord.=substr($nawData[$veldNaam],$veldPosities['StartPos'],$veldPosities['AantalPos']);
  }
  $nawRecords[$recordId]['nieuwWachtwoord']=$newWachtwoord;
}

echo "<table class=\"list_tabel\" cellspacing=\"0\">";
echo "<tr class=\"list_kopregel\">
<td class=\"list_kopregel_data\" width=\"150\">" . vt('Portefeuille') . "</td>
<td class=\"list_kopregel_data\" width=\"150\">" . vt('Huidige wachtwoord') . "</td>
<td class=\"list_kopregel_data\" width=\"150\">" . vt('Nieuwe wachtwoord') . "</td>
<td class=\"list_kopregel_data\" width=\"150\">" . vt('Info') . "</td>
</tr>";
foreach ($nawRecords as $id=>$data)
{
  $info='&nbsp';
  $update=0;
  if($_POST['actie']=='voorbeeld')
    $update=0;
  elseif($_POST['actie']=='vernieuwen')
    $update=1;
  elseif($_POST['actie']=='aanvullen' && $data['wachtwoord'] == '')
    $update=1;
  else
  {
    $info=vt("Wachtoord is al aanwezig.");
    $update=0;
  }
  if(strlen($data['nieuwWachtwoord']) < 6)
  {
    $info=vt("Wachtoord is te kort.");
    $update=0;
  }

  if($update==1)
  {

    $query="UPDATE CRM_naw SET wachtwoord='".$data['nieuwWachtwoord']."' WHERE id='$id' ";
    $db->SQL($query);
    $db->Query();
    $data['wachtwoord']=$data['nieuwWachtwoord'];
  }

  echo "<tr class=\"list_dataregel\">
  <td class=\"listTableData\">".$data['Portefeuille']."</td>
  <td class=\"listTableData\">".$data['wachtwoord']."</td>
  <td class=\"listTableData\">".$data['nieuwWachtwoord']."</td>
  <td class=\"listTableData\">$info</td></tr>";


}
echo "</table>";
}
echo template($__appvar["templateRefreshFooter"],$content);

?>