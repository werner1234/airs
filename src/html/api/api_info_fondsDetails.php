<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2019/05/14 15:07:29 $
    File Versie         : $Revision: 1.2 $

    $Log: api_info_fondsDetails.php,v $
    Revision 1.2  2019/05/14 15:07:29  cvs
    call 7630

    Revision 1.1  2019/04/24 14:13:04  cvs
    call 7630

    Revision 1.2  2018/10/22 14:20:49  cvs
    call 7228

    Revision 1.1  2018/07/11 10:01:19  cvs
    call 6812



*/

$fonds = rawurldecode($__ses["data"]["fonds"]);

$portefeuille = rawurldecode($__ses["data"]["portefeuille"]);

/////////////////////////////////////

$ms = new AE_cls_Morningstar($portefeuille);

$db = new DB();
$query = "
  SELECT 
    _htmlRapport_VOLK.*,
    Fondsen.KIDformulier
  FROM 
    `_htmlRapport_VOLK` 
  INNER JOIN Fondsen ON Fondsen.Fonds = _htmlRapport_VOLK.fonds
  WHERE 
    `_htmlRapport_VOLK`.fonds = '$fonds' AND 
    `_htmlRapport_VOLK`.portefeuille = '$portefeuille'
";

$output = $db->lookupRecordByQuery($query);
if (!$ms->allowed(3,4))
{
  $output["KIDformulier"] = "no";
}
$output["msLevel"] = $ms->level;

