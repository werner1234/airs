<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/03/07 13:31:08 $
    File Versie         : $Revision: 1.2 $

    $Log: CRM_naw_importVerslagCsv.php,v $
    Revision 1.2  2018/03/07 13:31:08  cvs
    call 6713

    Revision 1.1  2017/11/08 07:31:09  cvs
    call 6145



*/

include_once("wwwvars.php");

//debug($_SESSION["crmImportCSVdata"]);
$out[] = "'id', 'externId', 'actie', 'zoekveld'";
foreach ($_SESSION["crmImportCSVdata"] as $row)
{
  $out[] = "'".implode("','",$row)."'";
}
$out[] = " ";
$filename="crmImportVerslag_".date("Ymd-his").".csv";

header('Content-type: application/csv');
header("Content-Disposition: inline; filename=".$filename);
echo implode("\r\n", $out);