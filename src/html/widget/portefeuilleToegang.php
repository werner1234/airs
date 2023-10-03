<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2017/05/29 07:49:33 $
    File Versie         : $Revision: 1.1 $

    $Log: portefeuilleToegang.php,v $
    Revision 1.1  2017/05/29 07:49:33  cvs
    no message



*/

$showPort = $cfg->getData($var_port);
$portValue = $cfg->getData($var_port);
$allePortefeuilles = $_SESSION['usersession']['gebruiker']["overigePortefeuilles"];

if ($allePortefeuilles == 1 AND $showPort == "alle")  // laat alle portefeuiles zien als toegestaan
{
  $exto_join  = "";
  $exto_where = "";
  $showPort   = "alle";
}
else
{
  $exto_join  = "INNER JOIN Gebruikers ON Gebruikers.Accountmanager = Portefeuilles.Accountmanager";
  $exto_where = "Gebruikers.Gebruiker='$USR' AND";
  $showPort   = "eigen";
}
