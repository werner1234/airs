<?php
/*
    AE-ICT sourcemodule created 27 Mar 2020
    Author              : Chris van Santen
    Filename            : index.php

    $Log: index.php,v $
    Revision 1.2  2020/06/19 10:49:30  cvs
    call 3205

*/

include_once("wwwvars.php");

$tmpl = new AE_template();
$fmt  = new AE_cls_formatter();
$rowTemplate = <<<EOB
  <tr>
    <td><button class="btnSelect" data-id="{id}"><i class="fa fas fa-clone"></i> </button> </td>
    <td>{vb}</td>
    <td>{add_date}</td>
  </tr>
EOB;
$tmpl->loadTemplateFromString($rowTemplate,"row");
session_start();
$_SESSION["submenu"] = "";
$_SESSION["NAV"] = "";



$db = new DB(2); // connect to updateserver
$dbH = new DB();

$query = "SELECT bedrijf, updatesAfter, exportId FROM `integrityCheckHome` GROUP BY exportId ORDER BY bedrijf, exportId DESC";
$dbH->executeQuery($query);
$vbs = array("");
while($rec = $dbH->nextRecord())
{
  $vbsHome[$rec["bedrijf"]][] = $rec["updatesAfter"];
}

$query = "SELECT DISTINCT vb FROM `integrityCheck` ORDER BY vb";
$db->executeQuery($query);
$vbs = array("");
while($rec = $db->nextRecord())
{
  $vbs[] = $rec["vb"];
}

$filter = $_GET["filterVb"];

$where = ( $filter != "")?" WHERE vb = '{$filter}' ":"";


$query = "SELECT * FROM `integrityCheck` $where ORDER BY vb, add_date DESC";
$db->executeQuery($query);

$content = array();
$editcontent['style']  .= '<link href="../style/workspace.css" rel="stylesheet" type="text/css" media="screen">';
$content['jsincludes'] .= '<link rel="stylesheet" href="../style/smoothness/jquery-ui-1.11.1.custom.css">';
$content['jsincludes'] .= "<script type=\"text/javascript\" src=\"../javascript/jquery-min.js\"></script>";
$content['jsincludes'] .= "<script type=\"text/javascript\" src=\"../javascript/jquery-ui-min.js\"></script>";
$content['jsincludes'] .= "<link rel=\"stylesheet\" href=\"../style/fontAwesome/font-awesome.min.css\">";
echo template("../".$__appvar["templateContentHeader"],$content);

?>
<style>
  body{
    box-sizing: border-box!important;
  }
  thead td{
    background: #0a246a;
    color: white;
    padding: 3px 10px;
  }
  #outputFrame{
    position: relative;
    width: 90%;
    border:1px solid #999;
    padding:0;
    margin-top: 2em;

  }
  #headerFrame{

    padding: 5px;
    background: #333;
    color:white;
    height: 25px;
  }
  #dataFrame{
    min-height: 50px;
    width: 100%;
    padding: .5em;
  }
</style>

<pre>
  Home sets
  <?print_r($vbsHome);?>
</pre>

<fieldset>
  <legend>filter</legend>
  <form id="filterForm">
  Filter op vermogensbeheerder: <select id='filter' name="filterVb">

    <?
    foreach ($vbs as $vb)
    {
      $selected = ($vb == $filter)?"SELECTED":"";
      $desc = ($vb == "")?"-----":$vb;
      echo "<option  value='{$vb}' {$selected}>{$desc}</option>\n";
    }
    ?>
  </select>
  </form>
</fieldset>
<table>
  <thead>
    <td>&nbsp;</td>
    <td>Bedrijf</td>
    <td>Datum</td>
  </thead>

<?php
while ($rec = $db->nextRecord())
{
    $rowData = array(
      "add_date" => $fmt->format("@D{d}-{m}-{Y} om {H}:{i}", $rec["add_date"]),
      "vb"       => $rec["vb"],
      "id"       => $rec["id"]
    );
    echo $tmpl->parseBlock("row", $rowData);
}
?>
</table>
<div id="outputFrame">
  <div id="headerFrame"><button id="btnDelete"><i class="fa fas fa-remove"></i> </button></div>
  <div id="dataFrame">...</div>
</div>

  <script>
  $(document).ready(()=>{
    $( "#filter" ).change(function() {
      $("#filterForm").submit();
    });
    $(".btnSelect").click(function(e){
      e.preventDefault();
      const v = $(this).data("id");
      $("#dataFrame").load("dataFrameLoad.php?id="+v);
    });
    $("#btnDelete").click(function(e){
      $("#dataFrame").html("");
    });
  });
</script>
<?
echo template("../".$__appvar["templateRefreshFooter"],$content);
