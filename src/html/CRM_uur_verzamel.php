<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2012/08/05 10:45:21 $
 		File Versie					: $Revision: 1.2 $

 		$Log: CRM_uur_verzamel.php,v $
 		Revision 1.2  2012/08/05 10:45:21  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2012/06/06 10:05:12  cvs
 		factuurregels uit CRM_uren

*/

include_once("wwwvars.php");
session_start();

$templateRow = <<< EOB
  <tr>
    <td><input type="checkbox" name="uurRow_{id}"></td>
    <td style='text-align: right'>{datum}</td>
    <td style='text-align: right'>{tijd}</td>
    <td style='text-align: right'>{wn_code}</td>
    <td>{code} - {omschrijving}</td>
    <td>{memo}</td>
  </tr>
EOB;

$templateInput = <<< EOB
  <tr>
    <td style="background: #DDDDDD;">{tijd} uur x &euro; {uurtarief} &nbsp;&nbsp;</td>
    <td>{omschrijving}</td>
    <td><input name="R_{id}[bedrag]" value="{bedrag}" size="10" style="text-align:right"/></td>
  </tr>
EOB;

echo template($__appvar["templateContentHeader"],$content);

$data = array_merge($_GET, $_POST);

$db = new DB();


if ($data["process"] == "step1")
{

  echo template($__appvar["templateRefreshFooter"],$content);
?>
  <h1>factuurregels samenstellen voor:</h1>
  <h3><?=$data["klantnaam"]?></h3>

  <form name="editForm" method="POST">
    <input type="hidden" name="deb_id" value="<?=$data["deb_id"]?>" />
    <input type="hidden" name="klantnaam" value="<?=addslashes($data["klantnaam"])?>" />
    <input type="hidden" name="process" value="step2" />
<?
  while ( list( $key, $val ) = each( $data ) )
  {
    if (substr($key,0,7) == "uurRow_")
    {
      $recordArray[] = substr($key,7);
    }

  }
  if (count($recordArray) < 1)
  {
    echo "geen selectie om te verwerken!";
    exit();
  }
  $query = "SELECT * FROM CRM_uur_registratie WHERE id IN (".implode(",",$recordArray).")";
?>
  <input type="hidden" name="uurRows" value="<?=implode(",",$recordArray)?>" />
<?
  $db->executeQuery($query);
  while ($uurRec = $db->nextRecord())
  {
    $factuurRows[$uurRec["act_id"]] += $uurRec["tijd"];
  }

?>
<table>
<?
  reset($factuurRows);
  while (list( $key, $value ) = each( $factuurRows ))
  {
    $query = "SELECT * FROM CRM_uur_activiteiten WHERE id = $key";
    $db->executeQuery($query);
    $actRec = $db->lookupRecord();
    $bedrag = $value * $actRec["uurtarief"];
    $actRec["uurtarief"] = number_format($actRec["uurtarief"],2);
    $actRec["bedrag"] = number_format($bedrag,2);
    $actRec["tijd"]  = $value;
    echo ArrayTemplate($templateInput,$actRec);
    $id = $actRec["id"];
?>
    <input type='hidden' name="R_<?=$id?>[code]"    value='<?=$actRec["code"]?>' />
    <input type='hidden' name="R_<?=$id?>[oms]" value='<?=$actRec["omschrijving"]?>' />

<?
  }
?>
  </table>
  <input type="submit" value="Genereer factuurRegels in TEMP_factuurvoorstel">
  </form>
<?
  exit;
}

if ($data["process"] == "step2")
{
  $db = new DB();
  while ( list( $key, $val ) = each( $data ) )
  {
    if (substr($key,0,2) == "R_")
    {
      $ind = substr($key,2);
      if ((float)$data[$key]["bedrag"] <> 0)
      {
        $queries[] = "
        INSERT into TEMP_factuurvoorstel SET
          factuurtekst = '".$data[$key]["oms"]."'
        , act_id       = $ind
        , actCode      = '".$data[$key]["code"]."'
        , bedrag       = '".(float)$data[$key]["bedrag"]."'
        , klantnaam    = '".$data["klantnaam"]."'
        , deb_id       = '".$data["deb_id"]."'
        , add_date     = NOW()
        ";
      }
    }
  }

  if (count($queries) > 0)
  {
    for ($x=0 ; $x < count($queries); $x++)
    {
      $db->executeQuery($queries[$x]);
    }
    $query = "update CRM_uur_registratie SET verwerkt = 1, change_date = NOW() WHERE id IN (".$data["uurRows"].")";
    $db->executeQuery($query);
    echo "factuurregels zijn aangemaakt in TEMP_factuurvoorstel";
  }
  else
  {
    echo "geen items om op te slaan";
  }
  exit();

}
$query = "SELECT * FROM CRM_naw WHERE id = ".$data["deb_id"];
$db->executeQuery($query);
$nawRec = $db->lookupRecord();

$query = "
SELECT
 CRM_uur_registratie.*,
 CRM_uur_activiteiten.omschrijving,
 CRM_uur_activiteiten.code
FROM
  CRM_uur_registratie
LEFT JOIN CRM_uur_activiteiten ON CRM_uur_registratie.act_id = CRM_uur_activiteiten.id
  WHERE deb_id = ".$data["deb_id"]." AND verwerkt <> 1 ORDER BY CRM_uur_activiteiten.code, CRM_uur_registratie.datum";
$db->executeQuery($query);
while ($uurRec = $db->nextRecord())
{
  $tblRows .= ArrayTemplate($templateRow,$uurRec);
}

if($__debug)
{
	echo getdebuginfo();
}
echo template($__appvar["templateRefreshFooter"],$content);
?>
  <h1>factuurregels samenstellen voor:</h1>
  <h3><?=$nawRec["naam"]?></h3>

<form name="editForm" method="POST">
  <input type="hidden" name="deb_id" value="<?=$data["deb_id"]?>" />
  <input type="hidden" name="klantnaam" value="<?=addslashes($nawRec["naam"])?>" />
  <input type="hidden" name="process" value="step1" />

<table>
  <tr style="background: #DDDDDD;">
    <td> </td>
    <td style="width: 80px;">datum</td>
    <td style="width: 50px;">uren</td>
    <td style="width: 80px;">werknemer</td>
    <td style="width: 300px;">activiteit</td>
    <td style="width: 300px;">memo</td>
  </tr>
  <?=$tblRows?>
  <tr style="background: #DDDDDD;">
    <td> </td>
    <td> </td>
    <td> </td>
    <td> </td>
    <td> </td>
    <td> </td>
  </tr>
</table>

<input type="submit" value="Genereer voorstel"> ::
<input type="button" value="Alles aanzetten" onclick="checkAll();"> ::
<input type="button" value="Alles uitzetten" onclick="uncheckAll();">
</form>

<script>
function uncheckAll()
{
  var theForm = document.editForm.elements, z = 0;
  for(z=0; z<theForm.length;z++)
  {
   if(theForm[z].type == 'checkbox' && theForm[z].name != 'checkall')
   {
    theForm[z].checked = false;
   }
  }
}
function checkAll()
{
  var theForm = document.editForm.elements, z = 0;
  for(z=0; z<theForm.length;z++)
  {
   if(theForm[z].type == 'checkbox' && theForm[z].name != 'checkall')
   {
    theForm[z].checked = true;
   }
  }
}

</script>

<?

function ArrayTemplate($template,$record)
{
  $data = $template;
  while ( list( $key, $val ) = each( $record) )
  {
    if ($key == "datum") $val = dbdate2form($val);
    if ($key == "tijd")  $val = number_format($val,2);

    $data = str_replace( "{".$key."}", $val, $data);
 	}

	$data = eregi_replace( "\{[a-zA-Z0-9_-]+\}", "", $data);
  return $data;
}

?>