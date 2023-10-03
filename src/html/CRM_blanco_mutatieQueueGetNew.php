<?php
/*
  Author  						: $Author: cvs $
  Laatste aanpassing	: $Date: 2020/06/15 13:13:38 $
  File Versie					: $Revision: 1.1 $

  $Log: CRM_blanco_mutatieQueueGetNew.php,v $
  Revision 1.1  2020/06/15 13:13:38  cvs
  call 8380


 */

include_once("wwwvars.php");
include_once "../classes/AE_cls_OTconnect.php";

$error = array();
$succes = array();
$ot = new AE_cls_OTconnect();

$ot->getRequest();

$ot->setAction("credits");
$req = $ot->getResult();
echo template($__appvar["templateContentHeader"],$content);
$verbinding = ($ot->getHttpCode() == 200)?"succesvol":"mislukt";

?>
<h1>Ophalen nieuwe Blanco berichten</h1>
<h3>Verbinding <?=$verbinding?></h3>
<h3>Gevonden items <?=(int)$req["items"]?></h3>
<style>
  fieldset{
    width: 80%;
  }
  .btn{

    padding: 10px 15px 10px 15px;
    background: rgba(20,60,90,1);
    color: white;
    border: 0px;
    cursor: pointer;
  }
  a:link, a:visited{
    color:white;
  }
  a:hover{
    opacity: .8;
  }
</style>
<?php

if ($ot->errorFlag)
{
  echo "API errors:<hr/>".implode("<br/>", $ot->error)."<hr/>";
}


if (count($req["data"]) > 0)
{
  $db = new DB();
  foreach ($req["data"] as $rec )
  {
    $item = (array) $rec;
    $blancoId = $item["blancoId"];
    $add_date = $item["createdAt"];
    $blob = json_encode($item);
    $query = "
    INSERT INTO `CRM_blanco_mutatieQueue` SET
        `add_date`     = '$add_date',
        `add_user`     = 'blanco',
        `change_date`  = NOW(),
        `change_user`  = 'blanco',
        `jsonData`     = '{$blob}',
        `blancoId`     = '{$blancoId}',
        `verwerkt`     = 0,
        `afgewerkt`    = 0
  ";

    if (!$db->executeQuery($query))
    {
      $error[] = "insert failed for id {$blancoId}";
    }
    else
    {
      $succes[] = $blancoId;
    }
  }


}

if (count($succes) > 0)
{
  ?>
  <fieldset style="background: beige; color: black">
    <legend style="background: rgba(20,60,90,1); color: white; padding: 5px">Items ingelezen</legend>
    <ul>
      <?
      foreach ($succes as $i)
      {
        echo "<li>Item met BlancoId: {$i} </li>";
      }
      ?>
    </ul>
  </fieldset>
  <?php
}

if (count($error) > 0)
{
?>
  <fieldset style="background: maroon; color: white">
    <legend style="background: red; color: white; padding: 5px">Inlees fouten</legend>
    <ul>
      <?
      foreach ($error as $i)
      {
        echo "<li>Item met BlancoId: {$i} </li>";
      }
      ?>
    </ul>
  </fieldset>
<?php
}

?>
   <br/><br/><br/><br/>
   <a href="CRM_blanco_mutatieQueueList.php" class="btn"> terug naar Blanco wachtrij </a>
<?php

echo template($__appvar["templateRefreshFooter"],$content);
