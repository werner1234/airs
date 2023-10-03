<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2019/01/09 07:39:13 $
    File Versie         : $Revision: 1.3 $

    $Log: blocked.php,v $
    Revision 1.3  2019/01/09 07:39:13  cvs
    no message

    Revision 1.2  2018/07/24 06:34:32  cvs
    call 7041

    Revision 1.1  2017/01/04 13:06:53  cvs
    call 5542, uitrol WWB en TGC



*/

error_reporting(0);
$ref = htmlspecialchars((($_GET["ref"] <> 1)?$_GET["ref"]:"").(($_GET["ref2"] <> 1)?$_GET["ref2"]:""));

switch ($ref)
{
  case "blockLogin":
    $txt = "U probeert in te loggen buiten de toegestane tijdsinterval";
    break;
  case "blockPeriod":
    $txt = "De toegestane periode voor uw locatie is verlopen.";
    break;
  case "blockBlacklist":
    $txt = "U heeft te veel loginpogingen gedaan.";
    break;
  default:
    $txt = "U heeft of te veel loginpogingen gedaan of<br/>
    de toegestane periode voor uw locatie is verlopen.<br/>
    ref=$ref";
    break;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Afvangpagina</title>
  <style>
    *{
      font-family: sans-serif;
      margin:0;
      padding:0;
    }
    body{
      margin: 0 auto;

    }
    .container{
      margin:0 auto;
      text-align: center;
    }
    h1{

      padding: 20px;
      color: red;
      text-shadow: #333 2px 2px 2px;

    }
    article{
      margin: 0 auto;
      width: 500px;
      border:1px #333 solid;

      padding: 20px;
    }
    .kop{
      background: Maroon;
      margin: 0 auto;
      width: 500px;
      border-radius:15px 15px 0 0;
      font-size: 2em;
      color: white;
      text-shadow: #333 2px 2px 2px;
      border:1px Maroon solid;
    }
  </style>

</head>
<body>
<div class="container">
  <br/><br/><br/>
  <article class="kop">Blokkade</article>
  <article>
    Als u deze pagina ziet dan is uw IP adres geblokkeerd.<br/><br/>
    <?=$txt?>
    <br/><br/>
    Neem aub contact op met uw beheerder voor meer informatie.<hr/>
    <br/>
    uw IP adres is <b><?=$_SERVER["REMOTE_ADDR"]?></b>
  </article>
</div>
</body>
</html>

