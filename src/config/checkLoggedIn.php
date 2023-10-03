<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/07/24 06:35:13 $
    File Versie         : $Revision: 1.1 $

    $Log: checkLoggedIn.php,v $
    Revision 1.1  2018/07/24 06:35:13  cvs
    call 7041



*/

session_start();
if (!isset($_SESSION["USR"]))
{
  header("HTTP/1.0 404 Not Found");
  exit;
}
