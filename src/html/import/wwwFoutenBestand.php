<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/08/31 14:20:28 $
    File Versie         : $Revision: 1.2 $

    $Log: wwwFoutenBestand.php,v $
    Revision 1.2  2018/08/31 14:20:28  cvs
    call 6550

    Revision 1.1  2018/07/17 12:26:38  cvs
    call 6734



*/

include_once("wwwvars.php");

$output = $_SESSION["importFoutFile"];
if (is_array($output))
{
  $output = implode("",$output);
}

unset($_SESSION["importFoutFile"]);
session_write_close();
$filename = $_GET["bank"]."_FOUTEN_bestand_".date("Ymd_Hi").".csv";
header('Content-Type: application/csv');
header('Content-Disposition: attachment; filename="'.$filename.'";');
echo $output;