<?php
/**
 * Created by PhpStorm.
 * User: bdl
 * Date: 23-10-2017
 * Time: 10:17
 */

include_once("../../config/local_vars.php");
include_once("../../config/applicatie_functies.php");
include_once("../../classes/AE_cls_mysql.php");

require("../../config/checkLoggedIn.php");

if (trim($_POST['action']) == "")
{
  exit;
}

$USR = $_SESSION["USR"];
$data = array();
$db = new DB();

$action = $_POST['action'];
$systeem = $_POST['systeem'];

foreach ($_POST as $k=>$v)
{
  $_POST[$k] = mysql_real_escape_string($v);
}

switch($action)
{
    case 'catSoort':
        $query = "
                  SELECT DISTINCT
                     doorkijkCategoriesoort 
                  FROM 
                    `doorkijk_categoriePerVermogensbeheerder` 
                  WHERE 
                    `vermogensbeheerder` = '" .$_POST['vermogensbeheerder'] . "' ORDER BY doorkijkCategoriesoort"
                  ;
        //debug($query);
        $db->executeQuery($query);
        while ($rec = $db->nextRecord())
        {
            $data[] = array(
                "id"   => $rec["doorkijkCategoriesoort"],
                "desc" => $rec["doorkijkCategoriesoort"]
            );
        }
    break;
    case 'categorie':
            $query = "SELECT doorkijkCategorie
                          FROM doorkijk_categoriePerVermogensbeheerder
                          WHERE vermogensbeheerder = '" . $_POST['vermogensbeheerder'] . "' AND doorkijkCategoriesoort = '" . $_POST['doorkijkCategoriesoort'] . "' ORDER BY doorkijkCategorie
                     ";
            //debug($query);
            $db->executeQuery($query);
            while ($rec = $db->nextRecord())
            {
                $data[] = array(
                    "id"   => $rec["doorkijkCategorie"],
                    "desc" => $rec["doorkijkCategorie"]
                );
            }
    break; // --- end of case categorie ---
    case 'bronkoppeling':
        //debug($systeem);
        switch($systeem)
        {
            case 'AIRS':
            {
              if($_POST['doorkijkCategoriesoort']=='Rating')
                $query = "SELECT rating as bronkoppeling FROM Rating ORDER BY Rating";
              elseif($_POST['doorkijkCategoriesoort']=='Valutas')
                $query = "SELECT Valuta as bronkoppeling FROM Valutas ORDER BY Valuta";
              else
                $query = "SELECT waarde as bronkoppeling
                          FROM KeuzePerVermogensbeheerder
                          WHERE vermogensbeheerder = '" . $_POST['vermogensbeheerder'] . "' and categorie = '" . $_POST['doorkijkCategoriesoort'] . "'";
            }
            break;
            case 'MS':
                $query = "SELECT msCategorie as bronkoppeling
                          FROM doorkijk_msCategoriesoort
                          WHERE msCategoriesoort = '" . $_POST['doorkijkCategoriesoort'] . "'";
            break;
        } // -- end switch system ---
        //debug($query);
        $db->executeQuery($query);
        $usedCategorie=array();
        while ($rec = $db->nextRecord())
        {
           $data[] = array("id"=>$rec["bronkoppeling"],"desc" => $rec["bronkoppeling"]);
           $usedCategorie[]=$rec["bronkoppeling"];
        }
        
        if($systeem=='AIRS' && in_array($_POST['doorkijkCategoriesoort'],array('Coupon','Looptijd','Rating')))
        {
          $data[] = array("id"=>'Geldrekeningen', "desc"=>'Geldrekeningen');
          if($_POST['doorkijkCategoriesoort']=='Rating')
          {
            if(!in_array('NR',$usedCategorie))
              $data[] = array("id" => 'NR', "desc" => 'NR');
          }
          else
          {
            if(!in_array('Overig',$usedCategorie))
              $data[] = array("id" => 'Overig', "desc" => 'Overig');
          }
        }
        elseif($systeem=='AIRS' && $_POST['doorkijkCategoriesoort'] == 'Beleggingssectoren')
        {
            $data[] = array("id" => '', "desc" => '--- standaard ---');
            $query = 'SELECT Beleggingssector,Omschrijving FROM Beleggingssectoren WHERE standaard=1 ORDER BY Beleggingssector';
            $db->executeQuery($query);
            while ($rec = $db->nextRecord())
            {
              if(!in_array($rec["Beleggingssector"],$usedCategorie ))
                $data[] = array("id"=> $rec["Beleggingssector"],"desc" => $rec["Beleggingssector"]);
            }
        }
        elseif($systeem=='AIRS' && $_POST['doorkijkCategoriesoort'] == 'Beleggingssectoren')
        {
          $data[] = array("id" => '', "desc" => '--- standaard ---');
          $query = 'SELECT Beleggingssector,Omschrijving FROM Beleggingssectoren WHERE standaard=1 ORDER BY Beleggingssector';
          $db->executeQuery($query);
          while ($rec = $db->nextRecord())
          {
            if(!in_array($rec["Beleggingssector"],$usedCategorie ))
              $data[] = array("id"=> $rec["Beleggingssector"],"desc" => $rec["Beleggingscategorie"]);
          }
        }
        elseif($systeem=='AIRS' && $_POST['doorkijkCategoriesoort'] == 'Beleggingscategorien')
        {
          $data[] = array("id"=>'Geldrekeningen', "desc"=>'Geldrekeningen');
        }
    break; //  -- end of 'bronkoppeling'---
} // --- end of switch action ---

foreach($data as $index=>$dataArray)
{
  foreach($dataArray as $key=>$value)
  {
    if(!is_array($value))
      $data[$index][$key] = utf8_encode($value);
  }
}
echo json_encode($data);