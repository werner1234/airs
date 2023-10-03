<?php
/*
    AE-ICT sourcemodule created 05 okt. 2020
    Author              : Chris van Santen
    Filename            : CRM_portaalVragenQueue.php


*/

include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$fmt = new AE_cls_formatter();

$subHeader     = "";
$mainHeader    = vt("CRM portaalvragenlijsten wachtrij");
$_SESSION['NAV'] = "";

$extraJoin  = "";
$extraWhere = "";

$db = new DB();
$query = "
SELECT
  CRM_naw.id,
  CRM_naw.naam,
  VragenLijstenPerRelatie.omschrijving,
  VragenLijstenPerRelatie.portaalDatumIngevuld,
  Portefeuilles.Portefeuille,
  Portefeuilles.Accountmanager,
	Portefeuilles.tweedeAanspreekpunt,
	Portefeuilles.Client
FROM
  VragenLijstenPerRelatie
INNER JOIN CRM_naw on 
  CRM_naw.id = VragenLijstenPerRelatie.nawId
INNER JOIN Portefeuilles ON 
  CRM_naw.portefeuille = Portefeuilles.Portefeuille
  $extraJoin  
WHERE
  $extraWhere
  VragenLijstenPerRelatie.portaalStatus = 'ingevuld'
ORDER BY
  VragenLijstenPerRelatie.portaalDatumIngevuld
";

$content['pageHeader'] = "<br><br><div class='edit_actionTxt'>
  <b>$mainHeader</b> $subHeader
</div><br><br>";
$content["style"] = '
<link rel="stylesheet" href="widget/css/font-awesome.min.css" >
<link rel="stylesheet" href="widget/css/jquery-ui-1.11.1.custom.css"  type="text/css" media="screen">
<link rel="stylesheet" href="style/workspace.css" type="text/css" media="screen">
<link rel="stylesheet" href="style/AIRS_default.css" type="text/css" media="screen">

';
$content['jsincludes'] = '
<script type="text/javascript" src="javascript/jquery-min.js"></script>
<script type="text/javascript" src="javascript/jquery-ui-min.js"></script>
<script src="javascript/algemeen.js" type=text/javascript></script>

';

echo template($__appvar["templateContentHeader"],$content);
?>
<style>
  #mutaties{
    width: 90%;
    min-height: 500px;
    border: #999 2px solid;
    padding: 10px;
  }
  button{
    padding:4px 6px 4px 6px!important;
  }
</style>

<table class="listTable" cellspacing="0">
  <colgroup>
    <col style="width: 40px;"/>
    <col style="width: 100px;"/>
    <col style="width: 200px;"/>
    <col style="width: 200px;"/>
    <col style="width: 150px;"/>
    <col style="width: 200px;"/>
  </colgroup>
  <tr>
  <td class="listKop"></td>
  <td class="listKop"><?= vt('Portefeuille'); ?></td>
  <td class="listKop"><?= vt('Client'); ?></td>
  <td class="listKop"><?= vt('Naam'); ?></td>
  <td class="listKop"><?= vt('Ingevuld d.d.'); ?></td>
  <td class="listKop"><?= vt('Vragenlijst'); ?></td>
  </tr>
<?

  $db->executeQuery($query);
  while ($rec = $db->nextRecord())
  {
    $portaalDatumIngevuld = $fmt->format("@D {d}-{m}-{Y} om {H}:{i}", $rec["portaalDatumIngevuld"]);
    $link = "<a target='content' href='CRM_nawEdit.php?action=edit&id={$rec["id"]}&lastTab=9&frameSrc=".base64_encode("vragenlijstenperrelatieList.php?rel_id=".$rec["id"])."'><button><i class='fa fa-vcard' aria-hidden='true'></i></button></a> ";
    echo "
    <tr>
      <td>{$link} </td>
      <td>{$rec["Portefeuille"]}</td>
      <td>{$rec["Client"]}</td>
      <td>{$rec["naam"]}</td>
      <td>{$portaalDatumIngevuld}</td>
      <td>{$rec["omschrijving"]}</td>
    </tr>";
  }

?>
</table>
<br/>
<br/>
<br/>
<br/>


<script>
  $(document).ready(function ()
  {
    $(".btnPortefeuille").click(function ()
    {
      var portefeuille = $(this).data("id");
      $("#mutaties").load("CRM_mutatieQueueSelecteer.php?p="+portefeuille);
    })

  });
</script>
<?
if($__debug) 
{
	echo getdebuginfo();
}
echo template($__appvar["templateRefreshFooter"],$content);
