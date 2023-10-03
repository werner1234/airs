<?php
/*
    AE-ICT sourcemodule created 10 jul. 2020
    Author              : Chris van Santen
    Filename            : mylo_foutBestand.php

    $Log: mylo_foutBestand.php,v $
    Revision 1.2  2020/07/29 09:59:10  cvs
    call 8750

naar RVV 20201123

*/

include_once("wwwvars.php");
session_start();


foreach ($_SESSION["myloFoutBestand"] as $row)
{
  $out .= implode(",", $row)."\n";
}

unset($_SESSION["myloFoutBestand"]);
$file = "Moka_foutBestand_".date("Ymd-Hi").".csv";


header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename='.basename($file));
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
echo $out;