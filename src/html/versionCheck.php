<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2011/09/14 09:26:56 $
 		File Versie					: $Revision: 1.3 $

 		$Log: versionCheck.php,v $
 		Revision 1.3  2011/09/14 09:26:56  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2009/08/26 13:11:17  rvv
 		*** empty log message ***

 		Revision 1.1  2009/01/20 17:49:20  rvv
 		*** empty log message ***


*/
if($_GET['pass'] <> 'verm0gen')
 exit;

include_once("wwwvars.php");

$kleuren=array('8888FF','88FF88','88FFFF','FF8888','FF88FF','FFFF88','888888','0000FF','00FF00','00FFFF','FF0000','FF00FF','FFFF00','8888DD','88DD88','88DDDD','DD8888','DD88DD','DDDD88','DDDDDD','8888AA','88AA88','88AAAA','AA8888','88AAAA','AAAA88','AAAAAA');

$db=new DB(2);
$query = "SELECT
DISTINCT(bedrijven.bedrijf),
(SELECT terugmelding.fileinfo FROM terugmelding WHERE bedrijven.bedrijf = terugmelding.bedrijf ORDER BY datum DESC LIMIT 1 ) as fileinfo,
max(terugmelding.datum) as datum
FROM
 bedrijven
 LEFT JOIN terugmelding ON bedrijven.bedrijf = terugmelding.bedrijf
WHERE bedrijven.vink = '1'
GROUP BY bedrijven.bedrijf
ORDER BY datum DESC";
$db->SQL($query);
$db->Query();
while($data = $db->nextRecord())
{
  $bedrijven[$data['bedrijf']] = $data['fileinfo'];
  $bedrijvenReportDate[$data['bedrijf']] = $data['datum'];
}
  $bedrijvenKeys = array_keys($bedrijven);

foreach ($bedrijven as $bedrijf=>$fileInfo)
{
 $infoArray = unserialize($fileInfo);
 foreach ($infoArray as $info)
 {
   $data = explode("|",$info);
   $allFiles[$data[0]]=1;
   $bedrijvenFiles[$bedrijf][$data[0]]['fileNaam']=$data[0];
   $bedrijvenFiles[$bedrijf][$data[0]]['changeDate']=$data[1];
   $bedrijvenFiles[$bedrijf][$data[0]]['size']=$data[2];
   $bedrijvenFiles[$bedrijf][$data[0]]['versie']=$data[3];
   $versie=explode(":",$data[3]);
   $bedrijvenFiles[$bedrijf][$data[0]]['versieNr']=$versie[0];
   $bedrijvenFiles[$bedrijf][$data[0]]['versieDatum']=$versie[1];
   $bedrijvenFiles[$bedrijf][$data[0]]['md5']=$data[4];

   $aantalVersies = count($bestandVersieKleur[$data[0]]);
   $bestandAantalVersies[$data[0]] = $aantalVersies;

   if(!isset($bestandVersieKleur[$data[0]][$data[4]]))
     $bestandVersieKleur[$data[0]][$data[4]]=$kleuren[$aantalVersies];
 }
}


$allFiles = array_keys($allFiles);
sort($allFiles);

$html="
<table width=3000px>
<tr>
  <td  style=\"width:250px\">File</td>
  <td  style=\"width:50px\">Versies</td>  ";
foreach ($bedrijvenKeys as $bedrijf)
{
  $html.="<td align=\"center\"  style=\"width:150px\" ><b>$bedrijf</b><br>".substr($bedrijvenReportDate[$bedrijf],0,10)."</td>\n";
}
$html.="</tr>";
foreach ($allFiles as $file)
{
  if($file=='../classes/records/Portefeuilles.php' || 1)
  {
    $html.="<tr><td>$file </td><td align=\"center\">(".$bestandAantalVersies[$file].")</td>";
    foreach ($bedrijvenKeys as $bedrijf)
    {
      $html.="<td bgcolor=\"#".$bestandVersieKleur[$file][$bedrijvenFiles[$bedrijf][$file]['md5']]."\" title=\"".$bedrijvenFiles[$bedrijf][$file]['versieNr']."\">".
      $bedrijvenFiles[$bedrijf][$file]['versieDatum']." </td>\n";
    }
    $html.="</tr>";
  }

}
$html.="</table> ";



arsort($bestandAantalVersies);

$html.="<br>Verschillende versie aantal:";
$html.="
<table>
<tr>
  <td>File</td>
  <td>aantal > 5</td>
</tr>";
foreach ($bestandAantalVersies as $bestand=>$aantal)
{
  if($aantal >5)
  {
    $html.= "<tr> <td>$bestand</td><td>$aantal</td> </tr>";
  }

}
$html.="</table> ";
echo $html;




?>