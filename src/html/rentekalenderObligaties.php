<?php
/*
    AE-ICT CODEX source module versie 1.1.1.1, 10 november 2005
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2019/12/07 17:46:01 $
    File Versie         : $Revision: 1.8 $

    $Log: rentekalenderObligaties.php,v $
    Revision 1.8  2019/12/07 17:46:01  rvv
    *** empty log message ***


*/
include_once("wwwvars.php");
include_once("../classes/AE_cls_xls.php");


$_SESSION['NAV']='';
$mainHeader = vt('Rentekalender Obligaties');

if($_GET['filter']=='onvolledig')
  $onvolledig='selected';
else
  $alles="selected";
  
if($_GET['datum']=='')
  $datum=date('d-m-Y');
else  
  $datum=$_GET['datum'];
  
$julDatum=form2jul($datum);
$dbdatum=date('Y-m-d',$julDatum);  

$content['pageHeader'] = "<br><div class='edit_actionTxt'>
  <b>$mainHeader</b> $subHeader
</div><br>

<form method=\"GET\" name=\"editForm\">
<input type=\"text\" name=\"datum\" value='".$datum."'>
<br>


</form>


";


$content['javascript'] .= "";
echo template($__appvar["templateContentHeader"],$content);

$db=new DB();
$query="SELECT
Fondsen.Fonds,
Fondsen.FondsImportCode,
Fondsen.ISINCode,
Fondsen.fondssoort,
Fondsen.Valuta,
Fondsen.Fondseenheid,
Fondsen.inflatieGekoppeld,
Fondsen.Renteperiode,
Fondsen.Rente30_360,
Fondsen.variabeleCoupon,
Fondsen.Lossingsdatum,
Fondsen.EindDatum,
Fondsen.Rentedatum,
Fondsen.Rentepercentage,
Fondsen.EersteRentedatum,
Fondsen.OblSoortFloater,
Fondsen.OblFloaterJaar,
if(ActieveFondsen.actief is NULL,'N',if(ActieveFondsen.actief=1,'J','N')) as actief,
if(ActieveFondsen.portefeuilleAantal is NULL,0,ActieveFondsen.portefeuilleAantal) as portefeuilleAantal,
if(ActieveFondsen.InPositie is NULL,0,ActieveFondsen.InPositie) as InPositie,
if(ActieveFondsen.Aantal is NULL,0,ActieveFondsen.Aantal) as Aantal,
Fondsen.OblMemo
FROM
Fondsen
LEFT JOIN ActieveFondsen ON ActieveFondsen.Fonds=Fondsen.Fonds
WHERE 
Fondsen.fondssoort IN('OBL','OVERIG') AND 
(Fondsen.EindDatum>'$dbdatum' OR Fondsen.EindDatum='0000-00-00' OR Fondsen.EindDatum IS NULL) AND 
(Fondsen.Lossingsdatum > '$dbdatum' OR Fondsen.Lossingsdatum='0000-00-00' OR Fondsen.Lossingsdatum IS NULL)
ORDER BY Fonds";
$db->SQL($query);// echo $query;
$db->Query();
while($data=$db->nextRecord())
{
  $fondsen[$data['Fonds']]=$data;
}

$headerVelden=array('Fonds'=>'Fonds',
'FondsImportCode'=>'FondsImportCode',
'ISINCode'=>'ISINCode',
'fondssoort'=>'fondssoort',
'Valuta'=>'Valuta',
'Fondseenheid'=>'Fondseenheid',
'inflatieGekoppeld'=>'inflatieGekoppeld',
'Renteperiode'=>'Renteperiode',
'Rente30_360'=>'Rente30_360',
'variabeleCoupon'=>'variabeleCoupon',
'Frequentie'=>'Frequentie',
'Lossingsdatum'=>'Lossingsdatum',
'EindDatum'=>'Einddatum',
'Rentedatum'=>'Coupondatum',
'Rentepercentage'=>'Actuele rentepercentage',
'OblSoortFloater'=>'OblSoortFloater',
'OblFloaterJaar'=>'OblFloaterJaar',
'VolgendeRentedatum'=>'Volgende Coupondatum',
'actief'=>'Actief J/N',
'portefeuilleAantal'=>'portefeuilleAantal',
'InPositie'=>'InPositie',
'Aantal'=>'AantalInPositie',
'OblMemo'=>'Memo');

$xlsData=array();
$header=array();
$html="<table>";
$html.="<tr class=\"list_kopregel\">";
foreach($headerVelden as $veld)
{
  $header[]=array($veld,'header');
  $html.="<td class=\"list_kopregel_data\" title=\"".vt($veld)."\">".vt($veld)."</td>";
}
$xlsData[]=$header;
$html.="</tr>"; 

foreach($fondsen as $fonds=>$fondsData)
{
  $rente=getRenteParameters($fonds, $dbdatum);
  foreach($rente as $key=>$value)
    $fondsData[$key]=$value;

  $koers=getRentePercentage($fonds,$dbdatum);
  if($koers['Rentepercentage'])
    $fondsen[$fonds]['Rentepercentage']=$koers['Rentepercentage'];
    
  $fondsen[$fonds]['Frequentie']=$fondsData['Renteperiode']/12;
  $rentedatumJul=db2jul($fondsData['Rentedatum']);
  $renteDag=date('d',$rentedatumJul);
  $renteMaand=date('m',$rentedatumJul);
  $fondsen[$fonds]['Rentedatum']=$renteDag.'-'.$renteMaand;
  $jaar=substr($fondsData['EersteRentedatum'],0,4);

  $timer=0;
  if($fondsData['Renteperiode']>0)
  {
     $start= db2jul($fondsData['EersteRentedatum']);// mktime(0,0,0,$renteMaand,$renteDag,$jaar-1);
     $eind=$julDatum;//mktime(0,0,0,$renteMaand,$renteDag,$jaar);
     $timer=$start;
     $maanden=0;
     //if($julDatum>$start)
     //{
       while($timer<=$eind)
       {
        $maanden+=$fondsData['Renteperiode'];
        $timer=mktime(0,0,0,$renteMaand+$maanden,$renteDag,$jaar);
        //echo date('d-m-Y',$eind)." ".date('d-m-Y',$timer)."<br>\n";
       }
     //}
     //else
     //{
     // $timer=$start;
     //}
     
  }
  $fondsen[$fonds]['VolgendeRentedatum']=date('d-m-Y',$timer);
  $fondsen[$fonds]['EindDatum']=date('d-m-Y',db2jul($fondsen[$fonds]['EindDatum']));
  $fondsen[$fonds]['Lossingsdatum']=date('d-m-Y',db2jul($fondsen[$fonds]['Lossingsdatum']));

}

foreach($fondsen as $fonds=>$fondsData)
{
  $html.="<tr>\n";
  $row=array();
  foreach($headerVelden as $key=>$value)
  {
    if(isNumeric($fondsData[$key]))
      $align='right';
    else
      $align='left';  
    $html.="<td class=\"listTableData\" align=\"$align\" >".$fondsData[$key]."</td>\n";
    $row[]=$fondsData[$key];

  }
  $html.="</tr>\n"; 
  $xlsData[]=$row;
}
echo $html;



$xls = new AE_xls();
$xls->excelOpmaak['header']=array('setAlign'=>'centre','setBgColor'=>'26','setBorder'=>'1');

/*
$xlsData=array();
$xls->excelOpmaak['datum']=array('setNumFormat'=>'DD-MM-YYYY');
$xlsData[]=array(array('10-10-2010','datum'));
$xlsData[]=array(array('11-10-2010','header'));
$xlsData[]=array('12-10-2010');
*/

$xls->setData($xlsData);
$xls->OutputXls($__appvar['tempdir'].'RentekalenderObligaties.xls',true);
echo "<br>\n<a href='showTempfile.php?show=1&filename=RentekalenderObligaties.xls&unlink=1' >Download XLS file.</a>";

?>

</table>



<?



echo template($__appvar["templateRefreshFooter"],$content);
