<?php
/*
    AE-ICT CODEX source module versie 1.1.1.1, 10 november 2005
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2015/07/08 15:40:15 $
    File Versie         : $Revision: 1.3 $

    $Log: taakVoortgang.php,v $
    Revision 1.3  2015/07/08 15:40:15  rvv
    *** empty log message ***

    Revision 1.2  2014/04/27 07:07:22  rvv
    *** empty log message ***

    Revision 1.1  2014/04/26 16:40:33  rvv
    *** empty log message ***


*/
include_once("wwwvars.php");
include_once("../classes/AE_cls_xls.php");



  $_SESSION['NAV']='';

$mainHeader=vt('Taak voortgang bij') . ' ' . $_GET['categorie'];
$filter=$_GET['filter'];
if($filter=='')
  $filter='alles';

if($_GET['filter']=='onvolledig')
  $onvolledig='selected';
else
  $alles="selected";

$content['pageHeader'] = "<br><div class='edit_actionTxt'>
  <b>$mainHeader</b> $subHeader
</div><br>

<form method=\"GET\" name=\"controleForm\">
<input type=\"hidden\" name=\"categorie\" value='".$_GET['categorie']."'>
" . vt('Filter') . " :
<select name=\"filter\" onChange=\"document.controleForm.submit();\">
<option $alles value=\"alles\">" . vt('alles') . "</option>
<option $onvolledig value=\"onvolledig\">" . vt('Alleen onvolledig') . "</option>
</select>
<br>


</form>


";


$content['javascript'] .= "";
echo template($__appvar["templateContentHeader"],$content);

$db=new DB();
$query="SELECT omschrijving FROM CRM_selectievelden WHERE module = 'standaardTaken' AND omschrijving='".mysql_real_escape_string($_GET['categorie'])."'";
$db->SQL($query);
$categorie=$db->lookupRecord();
$query="SELECT id,taak FROM standaardTaken WHERE hoofdtaak='".mysql_real_escape_string($categorie['omschrijving'])."' order by taak";
$db->SQL($query);
$db->Query();
while($data=$db->nextRecord())
{
  $taken[$data['id']]=$data['taak'];
  $takenLeeg[$data['id']]='';
}

$query="SELECT
taken.standaardtaakId,
taken.afgewerkt,
taken.soort,
taken.zichtbaar,
CRM_naw.naam,
date(taken.add_date) as add_date,
concat(CRM_naw.naam,' ',date(taken.add_date)) as groepering
FROM
taken
INNER JOIN CRM_naw ON taken.rel_id = CRM_naw.id
WHERE taken.standaardtaakId IN('".implode("','",array_keys($taken))."')
ORDER BY groepering,taken.standaardtaakId";
$db->SQL($query);
$db->Query();
$onvolledig=array();
$relatieData=array();
while($data=$db->nextRecord())
{
  if(!isset($relatieData[$data['groepering']]))
  {
    $relatieData[$data['groepering']]=$takenLeeg;
  } 
  $relatieData[$data['groepering']][$data['standaardtaakId']]=$data;
  if($data['afgewerkt']==0)
    $onvolledig[$data['groepering']]=1;
    

    
}

$xlsData=array();
$row=array(array('Relatie','header'),array('Toegevoegd op','header'));
$html="<table>";
$html.="<tr class=\"list_kopregel\"><td class=\"list_kopregel_data\" >" . vt('Relatie') . "</td><td class=\"list_kopregel_data\" >" . vt('Toegevoegd op') . "</td>";
foreach($taken as $taakId=>$taak)
{
  $html.="<td class=\"list_kopregel_data\" title=\"".$taak."\">".substr($taak,0,10)."</td>";
  $row[]=array($taak,'header');
}
$html.="</tr>";  
$xlsData[]=$row;

foreach($relatieData as $relatie=>$takenData)
{
  if($filter=='alles' || $onvolledig[$relatie]==1)
  {
    $relatieNaam=substr($relatie,0,-10);
    $relatieDatum=substr($relatie,-10);
    $html.="<tr><td class=\"listTableData\">".$relatieNaam."</td><td class=\"listTableData\">".$relatieDatum."</td>";
    $row=array($relatieNaam,$relatieDatum);

    foreach($taken as $taakId=>$taak)
    {
      if($takenData[$taakId]['afgewerkt']==1)
      {
        $html.="<td class=\"listTableData\" style=\"background-color:#00AA00;\">&nbsp;</td>";
        $row[]=array('X','groen');
      }
      elseif(isset($takenData[$taakId]['afgewerkt']))
      {
        $html.="<td class=\"listTableData\" style=\"background-color:#E9AB17;\">&nbsp;</td>";
        $row[]=array('X','geel');
      }
      else
      {
        $html.="<td class=\"listTableData\" style=\"background-color:#AAAAAA;\">&nbsp;</td>";
        $row[]=array('X','grijs');
      }
    }
    $xlsData[]=$row;
  }
  $html.="</tr>";
}
$html.="</table>";
echo $html;

$xls = new AE_xls();
$xls->excelOpmaak['header']=array('setAlign'=>'centre','setBgColor'=>'26','setBorder'=>'1');
$xls->excelOpmaak['grijs']=array('setAlign'=>'centre','setBgColor'=>'23','setBorder'=>'1');
$xls->excelOpmaak['geel']=array('setAlign'=>'centre','setBgColor'=>'5','setBorder'=>'1');
$xls->excelOpmaak['groen']=array('setAlign'=>'centre','setBgColor'=>'50','setBorder'=>'1');
/*
for($i=0;$i<50;$i++)
{
$xls->excelOpmaak['kleur'.$i]=array('setAlign'=>'centre','setBgColor'=>$i,'setBorder'=>'1');
$xlsData[]=array(array('x'.$i,'kleur'.$i));
}
*/
$xls->setData($xlsData);
$xls->OutputXls($__appvar['tempdir'].'taakVoortgang.xls',true);
echo "<br>\n<a href='showTempfile.php?show=1&filename=taakVoortgang.xls&unlink=1' >" . vt('Download XLS file') . ".</a>";

?>

</table>



<?



echo template($__appvar["templateRefreshFooter"],$content);
?>