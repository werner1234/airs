<?php

class AE_cls_ApplicatieVertaling
{
  private $__vt               = array();
  private $__vtv              = array();
  private $__curLanguage      = 'NL';

  // All allowd languages
  private $allowedLanguages   = array(
    'NL',
    'EN',
    'FR',
    'DU'
  );

  // Language conversion from vertalingen table
  private $rapportTaal        = array(
    'NL'    => null,
    'EN'    => 1,
    'DU'    => 2,
    'FR'    => 3,
    'IT'    => 4,
    'PL'    => 5,
  );

  public function __construct ()
  {
    global $__appvar;
    if ( is_session_started() === false ) {
      session_start();
    }
    $this->db = new DB();

    if ( isset ($_SESSION["appTaal"]) && in_array($_SESSION["appTaal"], $this->allowedLanguages) ) {
      $this->__curLanguage = $_SESSION["appTaal"];
    }

    $this->checkVT();

    // Current languages
    $__appvar["vtTaal"] = array(
      'NL'    => $this->vt('Nederlands'),
      'EN'    => $this->vt('Engels'),
      'DU'    => $this->vt('Duits'),
      'FR'    => $this->vt('Frans'),
    );
  }

  /**
   * hoofd vertaal functie
   *
   * @param $veld
   * @param bool $capital
   * @return string
   */
  function vt($veld = '', $capital=true, $clean=true)
  {
    $veld = trim($veld);

    if (empty ($veld))
    {
      return $veld;
    }

    if ( isset ($__vtVars["firstCap"]) )
    {
      $capital = $__vtVars["firstCap"];
    }

    $v = strtolower(trim($veld));
    if ( ! isset ($this->__vt[$v]) || empty ($this->__vt[$v]) )
    {
      $this->addVT($v);
    }

    if ($capital)
    {
      $out = ($this->__vt[$v]<>"")?ucfirst($this->__vt[$v]):ucfirst($veld);
    }
    else
    {
      $out = ($this->__vt[$v]<>"")?$this->__vt[$v]:$veld;
    }

    $returnValue = $out;
    return $this->__cleanVt($returnValue, $clean);
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
    $veld = trim($veld);
    if (empty ($veld)) {
      return $veld;
    }

    if ( ! isset ($this->__vt[$veld]) || empty ($this->__vt[$veld]) ) {
      $this->addVT(trim($veld));
    }

    $returnValue = vsprintf(($this->__vt[$veld] ? $this->__vt[$veld] : $veld),$data);
    return $this->__cleanVt($returnValue);
  }

  /**
   * Get db/variable translations, first from the appvertalingen then from the vertalingen
   * These translations are never added to the translation table
   * @param $veld
   * @param $data
   * @return string
   */
  function vtbv ($veld = '', $data = array())
  {
    $veld = trim($veld);

    if (empty ($veld)) {
      return $veld;
    }

    // First check if in appVertalingen then check vertalingen
    $translation = $veld;
    if ( isset ($this->__vt[$veld]) && ! empty ($this->__vt[$veld]) ) {
      $translation = $this->__vt[$veld];
    } elseif ( isset ($this->__vtv[$veld]) && ! empty ($this->__vtv[$veld]) ) {
      $translation = $this->__vtv[$veld];
    }
    return $this->__cleanVt(vsprintf($translation,$data));
  }

  /**
   * @return void
   */
  function checkVT()
  {
    // check of de vertaalarray al bestaat en de vertaling niet gewijzigd is
    if (
      ( ! isset($_SESSION["appVertaling"]) || empty ($_SESSION["appVertaling"]) )
      || $_SESSION["appTaal"] != $_SESSION["appVertaling"]["__TAAL"]
    ) {
      $this->fillVT();
    }
    $this->__vt = $_SESSION["appVertaling"];

    if (
      ( ! isset($_SESSION["appReportVertaling"]) || empty ($_SESSION["appReportVertaling"]) )
      || $_SESSION["appTaal"] != $_SESSION["appVertaling"]["__TAAL"]
    ) {
      $this->fillReportVT();
    }
    $this->__vtv = isset ($_SESSION["appReportVertaling"]) ? $_SESSION["appReportVertaling"] : array();

  }

  function fillVT()
  {
    $query = "
      SELECT
        `veld`
        , `" . mysql_real_escape_string(strtolower($this->__curLanguage)) . "` as vertaling
      FROM
        `appVertaling`
      ORDER BY
        `veld`
    ";

    $this->db->executeQuery($query);
    $_SESSION["appVertaling"]["__INIT"] = date("j-m-Y H:i");
    $_SESSION["appVertaling"]["__TAAL"] = $this->__curLanguage;
    while ($tRec = $this->db->nextRecord())
    {
      $_SESSION['appVertaling'][$tRec["veld"]] = $tRec["vertaling"];
    }
    $this->__vt = $_SESSION["appVertaling"];
  }

  /**
   * Fill a session with the report translation
   * @return void
   */
  function fillReportVT()
  {
    $reportTaal = $this->rapportTaal[$this->__curLanguage];

    if ( ! empty ($reportTaal) ) {
      $vertaalObj = new Vertaling();
      $_SESSION["appReportVertaling"]["__TAAL"] = $this->__curLanguage;
      $this->__vtv = $_SESSION["appReportVertaling"] = $vertaalObj->getList(array('Taal' => $reportTaal), 'Term', 'Vertaling');
    }
  }


  /**
   * Cleans translated strings.
   * Quotes to entities
   *
   * @param $string
   * @return string
   */
  private function __cleanVt ($string, $clean=true)
  {
    if ( $clean === true ) {
      $string = htmlspecialchars($string);
      $string = str_replace(array('\'', '"'), array('&#39;', '&#34;'), $string);
    }
    return $string;
  }

  function addVT($v = '')
  {
    global $__appvar, $__vt, $__vtVars, $USR;
    if( $__appvar["bedrijf"] !== "HOME" ) {return false;}

    if ( empty($v) ) {
      return false;
    }

    // Backtrace bepalen wanneer 0 de addvt functie is regel 1 pakken
    $bt = debug_backtrace();
    $vtOrigin         = $bt[0]["file"];
    $vtOriginLine     = $bt[0]["line"];

    if ( count ($bt) > 1 && $bt[0]["function"] === 'addVT' ) {
      foreach ( $bt as $value ) {
        if  ( $value['function'] === 'vt' || $value['function'] === 'vtb' ) {
          $vtOrigin         = $value["file"];
          $vtOriginLine     = $value["line"];
        }
      }
    }


    $veldValue = addslashes($v);
//    $veldValue = mysql_real_escape_string($v);
    $query = "SELECT * FROM `appVertaling` WHERE `veld` = '{$veldValue}'"; // voorkom dubbele vertalingen
    if ( ! $chkRec = $this->db->lookupRecordByQuery($query) ) {
      $query = "
        INSERT INTO
          `appVertaling`
        SET
            `add_user`    = '$USR'
          , `add_date`    = NOW()
          , `change_user` = '$USR'
          , `change_date` = NOW()
          , `veld`        = '" . $veldValue . "'
          , `nl`          = '" . $veldValue . "'
          , `en`          = '(en)" . $veldValue . "'
          , `orgin`       = '" . $vtOrigin . ":" . $vtOriginLine . "'
      ";
      $this->db->executeQuery($query);
    }

    $this->fillVT();
  }

}