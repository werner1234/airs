<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2019/04/10 13:01:23 $
    File Versie         : $Revision: 1.6 $

    $Log: AE_cls_WidgetsFilter.php,v $
    Revision 1.6  2019/04/10 13:01:23  cvs
    call 7602

    Revision 1.5  2018/02/01 12:44:20  cvs
    update naar airsV2

    Revision 1.4  2017/12/15 13:52:21  cvs
    call 6257

    Revision 1.3  2017/10/20 11:43:21  cvs
    no message

    Revision 1.2  2017/09/29 14:39:01  cvs
    no message

    Revision 1.1  2017/09/27 15:42:30  cvs
    call 6159

    Revision 1.4  2017/05/30 14:15:55  cvs
    no message

    Revision 1.3  2017/05/29 07:51:23  cvs
    no message

    Revision 1.2  2017/01/13 09:31:11  cvs
    4 widgets werkend

    Revision 1.1  2016/12/22 09:42:24  cvs
    call 4830 eerste commit



*/

class AE_cls_WidgetsFilter
{
  var $db;
  var $cfg;
  var $user;
  var $selectie;
  var $session;
  var $extraQuery;
  var $extraJoin;


  function AE_cls_WidgetsFilter($selectie = "eigen")
  {
    $this->db       = new DB();
    $this->cfg      = new AE_config();
    $this->selectie = $selectie;
    $this->user     = $_SESSION["USR"];
    $this->initSession();
  }

  function showSettings()
  {
    debug($this,"filterclass",true,true);

  }

  function initSession()
  {
    global $_SESSION;
    $ses = $_SESSION["widgetFilter"];
    if ($ses["init"] == true)
    {
      $this->session = $ses;
      return true;
    }
    else
    {
      $ses = array();

      $gRec = $_SESSION["usersession"]["gebruiker"];
      $ses["init"]                      = true;
      $ses["user"]["naam"]              = $gRec["Naam"];
      $ses["user"]["crmLevel"]          = $gRec["CRMlevel"];
      $ses["user"]["crmEigenRecords"]   = $gRec["CRMeigenRecords"];
      $ses["user"]["crmRelatieSoorten"] = implode(",",unserialize($gRec["CRM_relatieSoorten"]));
      $ses["user"]["accountmanager"]    = $gRec["Accountmanager"];
      $ses["port"]["beperkingOpheffen"] = $gRec["beperkingOpheffen"];
      $ses["port"]["internePortefeuilles"] = $gRec["internePortefeuilles"];
      $ses["port"]["overigePortefeuilles"] = $gRec["overigePortefeuilles"];

      $_SESSION["widgetFilter"] =$ses;
      $this->session = $ses;
    }
  }

  function getPortefeuilleAccess()
  {
    $this->extraJoin = "";
    switch ($this->selectie)
    {
      case "alle":
        if( $this->session["port"]["overigePortefeuilles"] == 0 )
        {
          $this->selectie == "eigen";
          $this->extraQuery = " ( Portefeuilles.Accountmanager='".$this->session["user"]["accountmanager"]."' OR 
                                Portefeuilles.tweedeAanspreekpunt ='".$this->session["user"]["accountmanager"]."') AND ";
        }
        else
        {

          $this->extraQuery = " ( Portefeuilles.beperktToegankelijk = '0' OR  
                                Gebruikers.beperkingOpheffen = '1'         ) AND ";
          $this->extraJoin  = "
          INNER JOIN VermogensbeheerdersPerGebruiker ON
              Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND 
              VermogensbeheerdersPerGebruiker.Gebruiker = '".$this->user."'
          JOIN Gebruikers ON
              Gebruikers.Gebruiker = VermogensbeheerdersPerGebruiker.Gebruiker 
          ";
        }
        break;
      case "eigen":
        $this->extraQuery = " 
       ( Portefeuilles.Accountmanager='".$this->session["user"]["accountmanager"]."') AND ";
        break;
      default:
        $this->extraQuery = " 
       ( Portefeuilles.Accountmanager='".$this->session["user"]["accountmanager"]."' OR 
         Portefeuilles.tweedeAanspreekpunt ='".$this->session["user"]["accountmanager"]."') AND ";
    }

  }

  function portefeuilleAccessAllowed($id, $field="CRM_naw.id")
  {
    $this->selectie = "alle";
    $this->getPortefeuilleAccess();
    $access = false;
    $db = new DB();
    $query = "
    SELECT 
      CRM_naw.id
    FROM
      CRM_naw
    JOIN Portefeuilles ON 
      Portefeuilles.Portefeuille = CRM_naw.portefeuille
      ".$this->extraJoin."
    WHERE
    ".$this->extraQuery."
    $field = '$id' ";
    $pAccess = false;

    if( $db->lookupRecordByQuery($query))
    {
      $pAccess = true;
    }

    $this->getCRMaccess();
    $query = "
    SELECT 
      CRM_naw.id
    FROM
      CRM_naw
    WHERE
    ".$this->extraQuery." AND
    $field = '$id' ";

    $cAccess = false;
    if ($rec = $db->lookupRecordByQuery($query))
    {
      $cAccess = true;
    }

//    debug (array($pAccess,$cAccess,$query));

    return ($pAccess OR $cAccess);

  }

  function  getCombiAccess()
  {



    $this->extraQuery = " 
      (Portefeuilles.Accountmanager='".$_SESSION['usersession']['gebruiker']['Accountmanager']."' OR 
       Portefeuilles.Accountmanager IS NULL  OR 
       Portefeuilles.tweedeAanspreekpunt ='".$_SESSION['usersession']['gebruiker']['Accountmanager']."') AND 
       CRM_naw.aktief = 1
    AND 
    (   
    ";
    $relSoortArray = explode(",", $this->session["user"]["crmRelatieSoorten"] );
    $append = "";
    foreach ($relSoortArray as $rs)
    {
      if ($rs == "all" OR $rs == "inaktief")
      {
        continue;
      }
      $append .= "\n\t `CRM_naw`.`".$rs."` = 1 OR";
    }
    if ($append != "")
    {
       $append = substr($append,0, -2);
    }
    $this->extraQuery .= $append. "\n) AND ";


    $this->extraJoin  = "";

    switch ($this->selectie)
    {
      case "alle":
        if( $this->session["user"]["accountmanager"] <> '' AND $this->session["port"]["overigePortefeuilles"] == 0 AND $this->session["user"]["crmEigenRecords"] == 1 )
        {
          $this->selectie   = "eigen";
          $this->extraQuery .= "
          ( (Portefeuilles.Accountmanager='".$_SESSION['usersession']['gebruiker']['Accountmanager']."' OR 
            Portefeuilles.Accountmanager IS NULL  OR 
            Portefeuilles.tweedeAanspreekpunt ='".$_SESSION['usersession']['gebruiker']['Accountmanager']."') OR ( `Portefeuilles`.`Portefeuille` IS NULL AND (`CRM_naw`.`prospectEigenaar` = '".$this->user."' OR `CRM_naw`.`accountEigenaar` = '".$this->user."'))
          ) AND ";
        }
        elseif ( $this->session["user"]["accountmanager"] <> '' AND $this->session["port"]["overigePortefeuilles"] == 0 AND $this->session["user"]["crmEigenRecords"] == 0 )
        {

        }
        else
        {
          $this->extraJoin = "
           INNER JOIN VermogensbeheerdersPerGebruiker ON 
             Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker = '".$this->user."'
           JOIN Gebruikers ON 
             Gebruikers.Gebruiker = VermogensbeheerdersPerGebruiker.Gebruiker ";

          $this->extraQuery = " (Portefeuilles.Portefeuille IS NULL OR Portefeuilles.beperktToegankelijk = '0' OR  Gebruikers.beperkingOpheffen = '1' ) AND ";
        }
        break;
      case "eigen":
        $this->extraQuery .= "
        (
          (
            Portefeuilles.Accountmanager='".$_SESSION['usersession']['gebruiker']['Accountmanager']."'
          ) 
          OR
          (
            `Portefeuilles`.`Portefeuille` IS NULL  AND 
            (`CRM_naw`.`prospectEigenaar` = '".$this->user."' OR `CRM_naw`.`accountEigenaar`  = '".$this->user."' ) 
          ) 
        )
      AND
      ";
        break;
      default;
        $this->extraQuery .= "
        (
          (
            Portefeuilles.Accountmanager='".$_SESSION['usersession']['gebruiker']['Accountmanager']."' OR
            Portefeuilles.tweedeAanspreekpunt ='".$_SESSION['usersession']['gebruiker']['Accountmanager']."'
          ) 
          OR
          (
            `Portefeuilles`.`Portefeuille` IS NULL  AND 
            (`CRM_naw`.`prospectEigenaar` = '".$this->user."' OR `CRM_naw`.`accountEigenaar`  = '".$this->user."' ) 
          ) 
        )
      AND
      ";
        break;
    }


  }

  function getCRMaccess($prefix="")  // CRM records zonder portefeuille
  {

    $srtArray = explode(",",$this->session["user"]["crmRelatieSoorten"]);
    $this->extraJoin  = "";
    $this->extraQuery = "
    (CRM_naw.aktief = 1 OR ISNULL(CRM_naw.aktief) ) 
    AND `CRM_naw`.`Portefeuille` = '' ";

    $rsQuery = "\n AND (";
    $useRsQuery = false;

    $srtArray[] = "portefeuille"; // dummy test
    foreach ($srtArray as $relSoort)
    {
      if ($relSoort != "all" AND $relSoort != "inaktief" )
      {

        $rsQuery .= "\n\t `CRM_naw`.`".$relSoort."` = 1 OR";
        $useRsQuery = true;
      }
    }



    if ($useRsQuery)
    {
      $this->extraQuery .= substr($rsQuery,0, -2)."\n)";
    }


    if ($this->session["user"]["crmEigenRecords"] == 1)
    {
      $this->extraQuery .= " AND
      
      (
        `CRM_naw`.`prospectEigenaar` = '".$this->user."' OR
        `CRM_naw`.`accountEigenaar`  = '".$this->user."'
      )";
    }

    $this->extraQuery = $prefix." ".$this->extraQuery;

  }
  
}