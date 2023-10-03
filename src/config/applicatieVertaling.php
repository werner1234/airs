<?php
/*
    AE-ICT sourcemodule created 18 May 2020
    Author              : Chris van Santen
    Filename            : applicatieVertaling.php

    $Log: applicatieVertaling.php,v $
    Revision 1.2  2020/05/25 13:58:40  cvs
    call 6055

    query om dubbele invoer te tellen
    select count(veld) as aantal, veld FROM appVertaling  GROUP BY veld HAVING aantal > 1  ORDER BY aantal

*/

// Check if translations is loaded if not load it.

if ( ! isset ($appTranslation) ) {
  $appTranslation = new AE_cls_ApplicatieVertaling();
}

/**
 * Vtb voor het vertalen van een string met variablen
 * Voorbeeld: vtb("pagina %s van %s", array(4, 25));
 * @param string $veld veldnaam
 * @param array $data data array
 * @return string vertaling
 */

function vtb ( $veld = '', $data = array() )
{
  global $appTranslation;
  if ( ! isset ($appTranslation) ) {
    $appTranslation = new AE_cls_ApplicatieVertaling();
  }
  return $appTranslation->vtb($veld, $data);
}

function vtbv ( $veld = '', $data = array() )
{
  global $appTranslation;
  if ( ! isset ($appTranslation) ) {
    $appTranslation = new AE_cls_ApplicatieVertaling();
  }
  return $appTranslation->vtbv($veld, $data);
}


/**
 * @param $veld
 * @param bool $capital
 * @return string
 */
function vt($veld = '', $capital=true, $clean=true) // vertaal routine
{
  global $appTranslation;
  if ( ! isset ($appTranslation) ) {
    $appTranslation = new AE_cls_ApplicatieVertaling();
  }
  return $appTranslation->vt($veld, $capital, $clean);
}

