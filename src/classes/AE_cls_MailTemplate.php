<?php

class AE_MailTemplate
{
  /**
   * class veriables
   */
  var $messages       = array();
  var $messageTypes   = array('info', 'error', 'success');
  var $messageWrapper = '<div class="alert alert-%s">%s</div>';
  
  var $mailData       = array();

  function AE_MailTemplate()
  {
    $this->date = new AE_datum();
  }


  function setData ($data) {
    $this->mailData = $data;
    
  }
  
  function ParseData ($dataToParse) {
    preg_match_all('/\$\[(.*?)\]/',$dataToParse, $replaceValues);
  
    if ( ! empty ($replaceValues) ) {
      
      foreach ($replaceValues[1] as $replaceKey => $replaceString) {
        $newVar = '';
        $stringVars = explode('|', $replaceString);
        
        switch ($stringVars[0]) {
          case 'datum':
            $newVar = $this->formatDate($stringVars);
            break;
          case 'tijd':
            $newVar = $this->formatTime($stringVars);
            break;
          case 'link':
            $thisValue = '';
            $thisUrl = '';
            
            $thisValue = $stringVars[2];
            if ( isset ($this->mailData[$stringVars[2]]) ) {
              $thisValue = $this->mailData[$stringVars[2]];
            }
  
            $thisUrl = $stringVars[1];
            if ( isset ($this->mailData[$stringVars[1]]) ) {
              $thisUrl = $this->mailData[$stringVars[1]];
            }
            
            if (strpos($thisUrl, 'http') !== false) {
              $newVar = '<a href="' . $thisUrl . '" target="_blank">' . $thisValue . '</a>';
            }
            break;
        }
  
        $dataToParse  = str_replace($replaceValues[0][$replaceKey], $newVar, $dataToParse );
      }
    }
    
    
    foreach ( $this->mailData as $key => $val ) {
      $dataToParse  = str_replace("[" . $key . "]", $val, $dataToParse );
    }
  
    return $dataToParse;
  }
  

  
  function formatDate ($dateVars) {
    $dateVars[1] = strip_tags ($dateVars[1]);
  
    $thisDate = $this->date->formatDate($dateVars[2], $this->__getdateTimeFormat($dateVars));
    
    return $thisDate;
  }
  
  function formatTime ($timeVars) {
    $dateVars[1] = strip_tags ($timeVars[1]);
  
    $thisDate = $this->date->formatTime($timeVars[2], $this->__getdateTimeFormat($timeVars));
  
    return $thisDate;
  }
  
  function __getdateTimeFormat ($data) {
    $returnData = '';
  
    if ( isset ($this->mailData[$data[1]]) ) {
      $returnData = $this->mailData[$data[1]];
    } else {
      switch ($data[1]) {
        case 'now':
          $returnData = date('Y-m-d H:i:s');
          break;
        case 'next_month':
          $returnData = date('Y-m-d H:i:s', strtotime('+1 month'));
          break;
      }
    }
    
    return $returnData;
  }
  
  


  function getExtraFields ($keyValue)
  {
    $db=new DB();
    $data=array();
    global $__appvar,$USR;

    $velden=array('Vermogensbeheerder','Client','Depotbank','Accountmanager','tweedeAanspreekpunt','Remisier','RapportageValuta','accountEigenaar');
    foreach($velden as $veld)
      $keyValue['*'.$veld]='';

    if($keyValue['Vermogensbeheerder'])
    {
      $query="SELECT Naam as `*Vermogensbeheerder` FROM Vermogensbeheerders WHERE Vermogensbeheerder='".$keyValue['Vermogensbeheerder']."'";
      $db->SQL($query);
      $value=$db->lookupRecord();
      if(is_array($value))
        $keyValue = array_merge($keyValue,$value);
    }
    if($keyValue['Client'])
    {
      $query="SELECT Naam as `*Client` FROM Clienten WHERE Client='".$keyValue['Client']."'";
      $db->SQL($query);
      $value=$db->lookupRecord();
      if(is_array($value))
        $keyValue = array_merge($keyValue,$value);
    }
    if($keyValue['Depotbank'])
    {
      $query="SELECT Omschrijving as `*Depotbank` FROM Depotbanken WHERE Depotbank='".$keyValue['Depotbank']."'";
      $db->SQL($query);
      $value=$db->lookupRecord();
      if(is_array($value))
        $keyValue = array_merge($keyValue,$value);
    }
    if($keyValue['custodian'])
    {
      $query="SELECT Omschrijving as `*custodian` FROM Depotbanken WHERE Depotbank='".$keyValue['custodian']."'";
      $db->SQL($query);
      $value=$db->lookupRecord();
      if(is_array($value))
        $keyValue = array_merge($keyValue,$value);
    }
    if($keyValue['accountEigenaar'])
    {
      $query="SELECT Naam as `*accountEigenaar` FROM Gebruikers WHERE Gebruiker='".$keyValue['accountEigenaar']."'";
      $db->SQL($query);
      $data=$db->lookupRecord();
      $keyValue['*accountEigenaar']=$data['*accountEigenaar'];
    }
    if($keyValue['Accountmanager'])
    {
      $query="SELECT Naam as `*Accountmanager` FROM Accountmanagers WHERE Accountmanager='".$keyValue['Accountmanager']."'";
      $db->SQL($query);
      $value=$db->lookupRecord();
      if(is_array($value))
        $keyValue = array_merge($keyValue,$value);
    }
    if($keyValue['tweedeAanspreekpunt'])
    {
      $query="SELECT Naam as `*tweedeAanspreekpunt` FROM Accountmanagers WHERE Accountmanager='".$keyValue['tweedeAanspreekpunt']."'";
      $db->SQL($query);
      $value=$db->lookupRecord();
      if(is_array($value))
        $keyValue = array_merge($keyValue,$value);
    }
    if($keyValue['Remisier'])
    {
      $query="SELECT Naam as `*Remisier` FROM Remisiers WHERE Remisier='".$keyValue['Remisier']."'";
      $db->SQL($query);
      $value=$db->lookupRecord();
      if(is_array($value))
        $keyValue = array_merge($keyValue,$value);
    }
    if($keyValue['RapportageValuta'])
    {
      $query="SELECT Omschrijving as `*RapportageValuta` FROM Valutas WHERE Valuta='".$keyValue['RapportageValuta']."'";
      $db->SQL($query);
      $value=$db->lookupRecord();
      if(is_array($value))
        $keyValue = array_merge($keyValue,$value);
    }

    $keyValue['huidigeDatum'] = date("j")." ".$__appvar["Maanden"][date("n")]." ".date("Y");
    $keyValue['huidigeGebruiker'] = $USR;

    return $keyValue;
  }

}