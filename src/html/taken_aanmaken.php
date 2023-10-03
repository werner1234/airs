<?php
/*
    AE-ICT CODEX source module versie 1.6, 31 mei 2006
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2020/07/22 13:31:35 $
    File Versie         : $Revision: 1.1 $

    $Log: taken_aanmaken.php,v $


*/

include_once("wwwvars.php");
$db = new DB();



class taken_aanmaken
{
  
  function taken_aanmaken()
  {
    $this->nawIdFilter='';
  
  }
  
  function getInput()
  {
  
    $input = array_merge($_GET, $_POST);
  
    $input['ids']=array();
    if (isset($_SESSION['lastListQuery']) && $_SESSION['lastListQuery'] <> '')
    {
      $db = new DB();
      $tmp = explode("LIMIT", $_SESSION['lastListQuery']);
      $db->SQL($tmp[0]);
      $db->Query();
      while ($data = $db->nextRecord())
      {
        $input['ids'][] = $data['id'];
      }
      $this->nawIdFilter = " AND CRM_naw.id IN('" . implode("','", $input['ids']) . "')";
      
    }

    $input['verwerken'] = array();
    $input['taakIds'] = array();
    foreach ($input as $key => $value)
    {
      if (substr($key, 0, 6) == 'check_')
      {
        if ($value == 1)
        {
          $input['verwerken'][] = substr($key, 6);
        }
      }
      if (substr($key, 0, 9) == 'aanmaken_')
      {
        if ($value == 1)
        {
          $input['taakIds'][] = substr($key, 9);
        }
      }
    }
    
    return $input;
  }
  
  
  function createSelect($name, $values, $selectedValue, $changeSubmit = false)
  {
    $html = "<select name='$name' " . (($changeSubmit == true)?'onchange=\'selectForm.submit();\'':'') . ">";
    
    foreach ($values as $key => $value)
    {
      if ($key == $selectedValue)
      {
        $html .= "<option selected value='$key'>$value</option>";
      }
      else
      {
        $html .= "<option value='$key'>$value</option>";
      }
    }
    $html .= "</select>\n";
    
    return $html;
  }
  
  function getVeld($db,$query,$veld)
  {
    $db->SQL($query);
    $data=$db->lookupRecord();
    return $data[$veld];
  }
  
  function maakTaken($input)
  {
    global $USR;
    $db=new DB();
    
    $taken=array();
    foreach ($input['taakIds'] as $taakId)
    {

      $taakVelden = array('gebruiker'        => $input['gebruiker_' . $taakId],
                          'soort'            => $input['soort_' . $taakId],
                          'taak'             => $input['taak_' . $taakId],
                          'zichtbaarna'      => $input['zichtbaarna_' . $taakId],
                          'gebruikDatumveld' => $input['gebruikDatumveld_' . $taakId],
                          'taakId'           => $taakId,
                          'datumveld'        => $input['datumveld_' . $taakId]);
      $taken[]=$taakVelden;

    }

    $queries=array();
    foreach($input['verwerken'] as $crmId)
    {
      foreach($taken as $taakDetails)
      {
        if($taakDetails['gebruikDatumveld']<>0 && $taakDetails['datumveld']<>'')
        {
          $beginDatum=$this->getVeld($db,"SELECT ".$taakDetails['datumveld']." FROM CRM_naw WHERE id='$crmId'",$taakDetails['datumveld']);
          $taakDetails['zichtbaarna']=date('Y-m-d',db2jul($beginDatum)+$taakDetails['gebruikDatumveld']*86400);
        }
        else
        {
          $taakDetails['zichtbaarna']=formdate2db($taakDetails['zichtbaarna']);
        }
        if($taakDetails['gebruiker']=='accountmanagerGekoppeld')
        {
          $gebruiker=$this->getVeld($db,"SELECT Portefeuilles.Accountmanager,Gebruikers.Gebruiker FROM Portefeuilles INNER JOIN CRM_naw ON Portefeuilles.Portefeuille = CRM_naw.portefeuille INNER JOIN Gebruikers ON Portefeuilles.Accountmanager = Gebruikers.Accountmanager WHERE CRM_naw.id='$crmId'",'gebruiker');
          if($gebruiker<>'')
            $taakDetails['gebruiker']=$gebruiker;
          else
            $taakDetails['gebruiker']=$USR;
        }
        $relatie=$this->getVeld($db,"SELECT naam FROM CRM_naw WHERE id='$crmId'",'naam');
        $query="INSERT INTO taken SET rel_id='$crmId',
                                      gebruiker='".mysql_real_escape_string($taakDetails['gebruiker'])."',
                                      kop='".mysql_real_escape_string($taakDetails['taak'])."',
                                      relatie='".mysql_real_escape_string($relatie)."',
                                      txt='',
                                      soort='".mysql_real_escape_string($taakDetails['soort'])."',
                                      zichtbaar='".mysql_real_escape_string($taakDetails['zichtbaarna'])."',
                                      standaardtaakId='".mysql_real_escape_string($taakDetails['taakId'])."',
                                      add_date=now(),
                                      add_user='".mysql_real_escape_string($USR)."',
                                      change_date=now(),
                                      change_user='".mysql_real_escape_string($USR)."'";
        $queries[]=$query;
      }
    }

    $n=0;
    foreach($queries as $query)
    {
      $db->SQL($query);
      if($db->query())
      {
        $n++;
      }
    }
    return "$n taak/taken aangemaakt.";
  }
  
  function standaardTaak()
  {
    
    global $USR, $input;
    $db = new DB();
  
    $naw=new NAW();
    $typen=array();
    $datumVelden=array(''=>'---');
    foreach($naw->data['fields'] as $veld=>$details)
    {
      $typen[$details['form_type']]=$details['form_type'];
      if($details['form_type']=='calendar')
      {
        $datumVelden[$veld]=$details['description'];
      }
    }
  
   
    
    $query = "SELECT waarde,omschrijving FROM CRM_selectievelden WHERE module = 'standaardTaken' ORDER BY omschrijving";
    $db->SQL($query);
    $db->Query();
    $typeOptions = '';
    $selected = '';
    while ($data = $db->nextRecord())
    {
      $typeOptions .= "<option $selected value=\"" . $data['waarde'] . "\">" . $data['omschrijving'] . " </option>\n";
    }
    
    $query = "SELECT Gebruiker,Naam FROM Gebruikers ORDER BY Gebruiker";
    $db->SQL($query);
    $db->Query();
    $taakGebruikers = array('accountmanagerGekoppeld' => 'Accountmanager gekoppelde gebruiker');
    while ($data = $db->nextRecord())
    {
      $taakGebruikers[$data['Gebruiker']] = $data['Gebruiker'] . ' - ' . $data['Naam'];
    }
    
    $query = "SELECT if(waarde<>'',waarde,omschrijving) as waarde ,omschrijving FROM CRM_selectievelden WHERE module IN('agenda afspraak','standaardTaken') ORDER BY omschrijving";
    $db->SQL($query);
    $db->Query();
    $taakSoorten = array();
    while ($data = $db->nextRecord())
    {
      $taakSoorten[$data['waarde']] = $data['omschrijving'];
    }
    
    
    $query = "SELECT id,taak, hoofdtaak as categorie, dagenTotZichtbaar FROM standaardTaken ORDER BY categorie,taak";
    $db->SQL($query);
    $db->Query();
    $lastcategorie = '';
    $output = '';
    
    $tableHeader = "<tr><td>Selectie</td><td>Wie</td><td>Betreft</td><td>Soort</td><td>Zichtbaar vanaf</td></tr>";
    while ($data = $db->nextRecord())
    {
      if ($data['categorie'] != $lastcategorie)
      {
        if ($lastcategorie <> '')
        {
          $output .= "</table></div>\n";
          $output .= "<br/><br/><a href=\"javascript:openDiv('" . $data['id'] . "')\"><b>" . $data['categorie'] . "</b> </a>\n";
        }
        else
        {
          $output .= "<br/><br/><a href=\"javascript:openDiv('" . $data['id'] . "')\"><b>" . $data['categorie'] . "</b> </a>\n";
        }
        $output .= "<div style='display: none' id='kop_" . $data['id'] . "'>\n <table> $tableHeader";
      }
  
      $datumveldSelect=$this->createSelect('datumveld_'. $data['id'],$datumVelden,$input['datumveld'],false);
      
      
      $output .= "<tr><td><input type=\"checkbox\" name=\"aanmaken_" . $data['id'] . "\" value=\"1\"></td>\n";
      $output .= "<td>" . $this->createSelect("gebruiker_" . $data['id'], $taakGebruikers, ($input["gebruiker_" . $data['id']] <> ''?$input["gebruiker_" . $data['id']]:$USR)) . "</td> \n";
      $output .= "<td><input type=\"text\" size=\"60\" name=\"taak_" . $data['id'] . "\" value=\"" . $data['taak'] . "\" ></td>";
      $output .= "<td>" . $this->createSelect("soort_" . $data['id'], $taakSoorten, $data['categorie']) . "</td> \n";
      $output .= "<td><input type=\"text\" size=\"8\" name=\"zichtbaarna_" . $data['id'] . "\" value=\"" . ($input["zichtbaarna_" . $data['id']] <> ''?$input["zichtbaarna_" . $data['id']]:date('d-m-Y', time() - 86400 + ($data['dagenTotZichtbaar'] * 86400))) . "\" >
of ".$datumveldSelect." + <input name='gebruikDatumveld_".$data['id']."' size='4' value='".($input["gebruikDatumveld_".$data['id']]<>''?$input["gebruikDatumveld_".$data['id']]:'')."'> dagen
</td>\n";
      $output .= "</tr>\n";
      
      $lastcategorie = $data['categorie'];
    }
    if ($lastcategorie <> '')
    {
      $output .= "</table></div>\n<br><br><br>";
    }
    
    return $output;
  }
  
  function toonSelectie()
  {
    $db=new DB();
  
    
    $taak = $this->standaardTaak();
    $query = 'SELECT id,Naam,Zoekveld,Portefeuille FROM CRM_naw WHERE 1 '.$this->nawIdFilter;
    $db->SQL($query);
    $db->Query();
    $crmTabel = "<table><tr><td><b><a href='javascript:checkAll(true);'>Alles</a>/<a href='javascript:checkAll(false);'>Niets</a></b></td><td><b>Naam</b></td><td><b>Zoekveld</b></td><td><b>Portefeuille</b></td></tr>";
    while ($data = $db->nextRecord())
    {
       $crmRegels[] = $data;
       $crmTabel .= "<tr><td><input type='checkbox' name='check_" . $data['id'] . "' value='1' onclick='checkStatus();'></td>
<td>" . $data['Naam'] . "</td><td>" . $data['Zoekveld'] . "</td><td>" . $data['Portefeuille'] . "</td><td>" . $data['datumveld'] . "</td><td>" . $data['filterdag'] . "</td></tr>";
    }
    $crmTabel .= "</table>";
    
  
    echo "<form name='selectForm' method='post'>";
    echo "<input type='hidden' name='ophalen' value='1'>\n";
    echo "<table>";
    echo "<tr><td>&nbsp</td><td><br><input type='submit' id='submitKnop' value='Relaties ophalen'> <!--<input type='checkbox' name='debug' value='1' checked>debug  --> <br> <br></td></tr>";
    echo "</table>";
    echo $taak;
    echo $crmTabel;
    echo "</form>";
  }
  
}

$content['javascript'] = 'function checkStatus()
{
  var theForm = document.selectForm.elements, z = 0, toonVerwerken=0 ;
  for(z=0; z<theForm.length;z++)
  {
   if(theForm[z].type == \'checkbox\' && theForm[z].name.substr(0,6) == \'check_\')
   {
      if(theForm[z].checked==true)
      {
        toonVerwerken++;
      }
   }
  }

  if(toonVerwerken>0)
  {
    $(\'#submitKnop\').val(\'Taken aanmaken\');
  }
  else
  {
    $(\'#submitKnop\').val(\'Relaties ophalen\');
  }
}

function openDiv(field)
{
  $(\'#kop_\'+field).toggle();
}

 function checkAll(optie)
{
  var theForm = document.selectForm.elements, z = 0;
  for(z=0; z<theForm.length;z++)
  {
    if(theForm[z].type == \'checkbox\' && theForm[z].name.substr(0,6) == \'check_\')
    { console.log(theForm[z].name);
      if(optie == -1)
      {
        if(theForm[z].checked == true)
          theForm[z].checked=false;
        else
          theForm[z].checked=true;
      }
      else
      {
        theForm[z].checked = optie;
      }
    }
  }
  checkStatus();
}

';
$content['pageHeader'] = "<br><div class='edit_actionTxt'>
  <b>Produceer taken</b>
</div><br><br>";

echo template($__appvar["templateContentHeader"], $content);

$taak=new taken_aanmaken();
$input=$taak->getInput();
if (count($input['verwerken']) > 0)
{
  $txt=$taak->maakTaken($input);
  echo $txt;
}
else
{
  $taak->toonSelectie($input);
}

echo template($__appvar["templateRefreshFooter"], $content);
?>