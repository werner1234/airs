<?php
/*
    AE-ICT sourcemodule created 17 aug. 2022
    Author              : Chris van Santen
    Filename            : dd_inleesFilman.php


*/

include_once("wwwvars.php");
session_start();
$_SESSION["NAV"]='';
include_once "../classes/AE_cls_dd_helper.php";

$dd = new AE_cls_dd_helper(getVermogensbeheerderField("Vermogensbeheerder"));

if ($dd->error())
{
  echo "<h1> niet ingeregeld</h1>";
  exit;
}

$directory = $dd->path;

//debug($dd->showInfo(), $directory);


$content["style"] = '
<link rel="stylesheet" href="widget/css/font-awesome.min.css" >
<link rel="stylesheet" href="widget/css/jquery-ui-1.11.1.custom.css"  type="text/css" media="screen">
<link rel="stylesheet" href="style/workspace.css" type="text/css" media="screen">
<link rel="stylesheet" href="style/AIRS_default.css" type="text/css" media="screen">
<link rel="stylesheet" href="style/dropzone.css?cache='.$random.'"  type="text/css" media="screen">
';
$content['jsincludes'] = '
<script type="text/javascript" src="javascript/jquery-min.js"></script>
<script type="text/javascript" src="javascript/jquery-ui-min.js"></script>
<script type="text/javascript" src="javascript/algemeen.js"></script>
<script type="text/javascript" src="javascript/dropzone.js"></script>
  
';

echo template($__appvar["templateContentHeader"],$content);

if ($_POST)
{
  if ($_POST["action"] == "delete")
  {
    unlink($directory . "/" . $_POST["file"]);
  }
}

?>
  <style>
    .rowitem {
      float: left;
      width: 95%;
    }
    .done {
      color: red;
    }
    .mergeContainer{
      width: 95%;
    }
    .mergeHeader{
      background: rgba(20,60,90,1);
      color: white;
      margin: 0;
      left: ;
      padding: 5px;
    }
    .mergeContent{
      width: 100%;
      height: 200px;
    }
    #mergeframe{
      height: 250px;
      width: 100%;
    }
  </style>
  <script>

    function Delete(file)
    {
      $("#action").val("delete");
      $("#file").val(file);
      fm.submit();
    }
    $(document).ready(function ()
    {
      $("#btnGo").click(function(e)
      {
       e.preventDefault();
       location.href = "transaktieImport.php";
      });
    });

  </script>
  <br/>
  <h3>Documenten beheer voor automatische koppelen in CRM</h3>
  <br/>
  <section class="mergeContainer">
    <div class="mergeHeader">Bestanden uploaden</div>
    <div class="mergeContent" >
      <iframe src="dd_upload.php" frameborder="0"  id="mergeframe" name="mergeframe"></iframe>
    </div>
  </section><br/>

  <section class="mergeContainer">
  <div class="mergeHeader">Bestanden op de server</div>
    <br/>
  <iframe src="dd_fileList.php" frameborder="0" width="100%" height="100%" id="listframe" name="listframe"></iframe>
  </section>

<?

echo template($__appvar["templateRefreshFooter"], $content);
