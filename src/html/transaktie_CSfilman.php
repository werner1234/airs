<?php
/*
    AE-ICT sourcemodule created 28 jun. 2021
    Author              : Chris van Santen
    Filename            : transaktie_CSfilman.php

  call 9615
*/

include_once("wwwvars.php");

$depot = $_GET["depot"];
$pathInit = false;
switch ($depot)
{
  case "cs":
    $pathInit = ( isset($__credswissImportMap) AND trim($__credswissImportMap) != "" );
    $directory = $__credswissImportMap."/import/";
    break;
  case "ubs":
    $pathInit = ( isset($__ubsImportMap) AND trim($__ubsImportMap) != "" );
    $directory = $__ubsImportMap."/import/";
    break;
  default:
}


if (!$pathInit)
{
  echo "<h1> niet ingeregeld</h1>";
  exit;
}

if (!file_exists($directory))
{
  $dp = explode("/", substr($directory,0,-1));
  array_pop($dp);
  $dir3 =  implode("/", $dp);
  array_pop($dp);
  $dir2 =  implode("/", $dp);
  array_pop($dp);
  $dir1 =  implode("/", $dp);

//  debug(array(
//          $dir1,
//          $dir2,
//          $dir3,
//          $directory
//        ));

  //mkdir($dir1);  // IMPORTDATA
  mkdir($dir2);  // cs
  mkdir($dir3);  // cs
  mkdir($directory);  //import
}


session_start();

$cfg = new AE_config();

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

echo template($__appvar["templateContentHeader"], $editcontent);

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
  <button id="btnGo">Ga naar rekeningmutatie import</button>
  <br/>
  <br/>
  <section class="mergeContainer">
    <div class="mergeHeader">Bestanden uploaden</div>
    <div class="mergeContent" >
      <iframe src="transaktie_CS_upload.php?depot=<?=$depot?>" frameborder="0"  id="mergeframe" name="mergeframe"></iframe>
    </div>
  </section><br/>

  <section class="mergeContainer">
  <div class="mergeHeader">Bestanden op de server</div>
    <br/>
  <iframe src="transaktie_CS_fileList.php?depot=<?=$depot?>" frameborder="0" width="100%" height="100%" id="listframe" name="listframe"></iframe>
  </section>

<?

echo template($__appvar["templateRefreshFooter"], $content);
