<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2016/12/22 09:40:30 $
    File Versie         : $Revision: 1.1 $

    $Log: welcomeMessages.php,v $
    Revision 1.1  2016/12/22 09:40:30  cvs
    call 4830 eerste commit



*/

switch ($_GET["msg"])
{
  case "wwSave":
    $txt = "Uw wachtwoord is opgeslagen!";
    break;
  case "wwSettings":
    $txt = "Het wachtwoordbeleid is opgeslagen";
    break;
  default:
}
?>
<style>

  .msg{

    padding: 10px;
    padding-left:10%;
    margin-bottom: 5px;
    background-image: -webkit-gradient(linear, top, bottom, color-stop(0, #B0E0E6), color-stop(1, #F5F5F5));
    background-image: -o-linear-gradient(top, #B0E0E6, #F5F5F5);
    background-image: -moz-linear-gradient(top, #B0E0E6, #F5F5F5);
    background-image: -webkit-linear-gradient(top, #B0E0E6, #F5F5F5);
    background-image: linear-gradient(to bottom, #B0E0E6, #F5F5F5);
    font-weight: bold;
  }

</style>
<div class="msg col-xs-12">
  <?=$txt?>
</div>
<div class="wl_clear"></div>
