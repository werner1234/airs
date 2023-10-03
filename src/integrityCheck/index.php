<?php
/*
    AE-ICT sourcemodule created 23 Mar 2020
    Author              : Chris van Santen
    Filename            : index.php

    $Log: index.php,v $
    Revision 1.4  2020/03/25 13:24:12  cvs
    call 3205

    Revision 1.3  2020/03/25 13:17:49  cvs
    call 3205

    Revision 1.2  2020/03/25 13:16:52  cvs
    call 3205

    Revision 1.1  2020/03/23 13:04:14  cvs
    call 3205

*/

include_once "wwwvars.php";

$ic = new AE_cls_integrityCheck(true);
//$ic->checkDays = 7;
$ic->loadTable("Fondsen");
$ic->loadTable("Fondskoersen");
$ic->loadTable("Portefeuilles");
$ic->loadTable("Rekeningafschriften");
$ic->loadTable("Rekeningen");
$ic->loadTable("Rekeningmutaties");
debug($ic->getResults());


