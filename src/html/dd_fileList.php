<?php
/*
    AE-ICT sourcemodule created 28 jun. 2021
    Author              : Chris van Santen
    Filename            : transaktie_CS_fileList.php

    call 9615
*/

include_once("wwwvars.php");
session_start();

//debug($_SESSION["dd_path"]);

if ($_SESSION["dd_path"] != "")
{
  $directory = $_SESSION["dd_path"];
}
else
{
  echo "<h1> DD map niet ingeregeld </h1>";
  exit;
}
//include_once("../config/advent_functies.php");

if (!file_exists($directory))
{
  mkdir($directory);
}

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
      height: 280px;

    }
    .btnDelete{
      background: Maroon;
      padding: 2px 10px;
      margin-bottom: 5px;
    }
    .btnGo{
      background: rgba(20,60,90,1);
      color: white;
      margin: 0;
      padding: 5px;
    }
  </style>
  <script>
    $(document).ready(function ()
    {
    });


    function Download(file)
    {
      $("#action").val("push");
      $("#file").val(file);
      fm.submit();
    }

    function Delete(file)
    {
      $("#action").val("delete");
      $("#file").val(file);
      fm.submit();
    }

  </script>
<?

    if (!is_dir($directory))
    {
      $error[] = "FOUT: uitvoermap is geen geldige map op de server";
    }
    else
    {
      if (!is_writable($directory))
      {
        $error[] = "FOUT: geen rechten om te schrijven in uitvoermap";
      }
    }
    if (count($error) > 0)
    {
      echo "meldingen <hr />";
      for ($x = 0; $x < count($error); $x++)
      {
        echo "<li>" . $error[$x] . "</li>";
      }

      exit;
    }

    if ($handle = opendir("$directory"))
    {
      while ($file = readdir($handle))
      {
        $files[] = $file;
      }
      sort($files);
      if (count($files) > 2)  // zijn er bestanden?
      {
        for ($x = 0; $x < count($files); $x++)
        {
          $file = $files[$x];
          if (is_dir($full_path . "/" . $file) OR $file == "verwerkt")
          {
            continue;
          }
          else
          {
            $delete = "<button class='btnDelete' onClick='Delete(\"$file\")' ><i class='fa fa-trash-o' aria-hidden='true'></i> verwijder </button>";
            echo "<div>{$delete} {$file}</div>";
          }
        }
      }
      else
      {
        echo "Geen bestanden gevonden!";
      }
    }
    ?>

  <script>
    $(document).ready(function ()
    {
    });

    function Download(file)
    {
      $("#action").val("push");
      $("#file").val(file);
      fm.submit();
    }

    function Delete(file)
    {
      $("#action").val("delete");
      $("#file").val(file);
      fm.submit();
    }

  </script>
  <form action="<?=$PHP_SELF?>?depot=<?=$depot?>" method="POST" name="fm">
    <input type="hidden" name="file" id="file" value=""/>
    <input type="hidden" name="action" id="action" value=""/>
  </form>
<?php
echo template($__appvar["templateRefreshFooter"], $content);
