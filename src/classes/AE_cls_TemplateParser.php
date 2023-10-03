<?php

class AE_cls_TemplateParser
{
  /**
   * class veriables
   */
  var $encapsulationTypeList = array(
    1 => array('[',']'),
    2 => array('{','}')
  );
  var $encapsulationType = 2;
  var $messages       = array();
  var $messageTypes   = array('info', 'error', 'success');
  var $messageWrapper = '<div class="alert alert-%s">%s</div>';
  
  var $parseData       = array();
  
  function AE_cls_TemplateParser()
  {
    $this->date = new AE_datum();
  }
  
  function _enStart(){return (isset($this->encapsulationTypeList[$this->encapsulationType][0]) ? $this->encapsulationTypeList[$this->encapsulationType][0] : '');}
  function _enStop(){return (isset($this->encapsulationTypeList[$this->encapsulationType][1]) ? $this->encapsulationTypeList[$this->encapsulationType][1] : '');}
  
  
  function setData ($data) {
    $this->parseData = $data;
  }
  
  function ParseData ($dataToParse) {
    
    $pattern = '/\$\[(.*?)\]/';
    preg_match_all($pattern,$dataToParse, $replaceValues);
  
    if ( ! empty ($replaceValues) ) {
      foreach ($replaceValues[1] as $replaceKey => $replaceString) {
        $newVar = '';
        $stringVars = explode('|', $replaceString);
        
        switch ($stringVars[0]) {
          case 'checkbox':
            $newVar = $this->formatCheckbox($stringVars);
            break;
          case 'getal':
            $newVar = $this->formatGetal($stringVars);
            break;
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
            if ( isset ($this->parseData[$stringVars[2]]) ) {
              $thisValue = $this->parseData[$stringVars[2]];
            }
            
            $thisUrl = $stringVars[1];
            if ( isset ($this->parseData[$stringVars[1]]) ) {
              $thisUrl = $this->parseData[$stringVars[1]];
            }
            
            if (strpos($thisUrl, 'http') !== false) {
              $newVar = '<a href="' . $thisUrl . '" target="_blank">' . $thisValue . '</a>';
            }
            break;
        }
        
        $dataToParse  = str_replace($replaceValues[0][$replaceKey], $newVar, $dataToParse );
      }
    }

    //    $dataToParse = str_replace('&#9633;', '<input type="checkbox" id="" name="check'.rand(10, 100).'" value="1">', $dataToParse);
    $dataToParse = $this->em($dataToParse);
//    $dataToParse = iconv('UTF-8', 'ASCII//TRANSLIT', $dataToParse);
    foreach ( $this->parseData as $key => $val ) {
      $dataToParse  = str_replace($this->_enStart() . $key . $this->_enStop(), $val, $dataToParse );
    }


    // leeg niet tonen
    // Check of de variable voorkomt in de tekst
    // Loop door de tekst om de regel te controleren.
    $leegNietTonen=0;
    $leegNietTonen=strpos($dataToParse,'leegNietTonen');

    if($leegNietTonen > 0)
    {

      $fp = fopen("php://memory", 'r+');
      $fp1 = fopen("php://memory", 'r+');
      $test = '';
      fputs($fp, $dataToParse);
      rewind($fp);
      while($line = fgets($fp)){

        if (strpos($line, 'leegNietTonen') >= 1)
        {
          $clone = $line;
          $pattern = '/\\'.$this->_enStart() .'(.*?)\\'.$this->_enStop().'/';
          $clone = preg_replace($pattern, '', $clone);
          $clone = str_replace (array('<br />', ' '), '', $clone);
          $clone = trim($clone);

          if (!  empty ($clone) ) {
            fwrite($fp1,$line); //write to txtfile
            $test.=$line;
          }

        } else {
          $test.=$line;
        }
      }
      rewind($fp1);
      $dataToParse = $test;
      fclose($fp);
      fclose($fp1);


      $parts = explode("\par ", $dataToParse);
//      debug($parts);
      foreach ($parts as $nr => $line)
      {
        $strippedLine = trim($line, chr(160) . chr(32));
        $strippedLine = trim($strippedLine);

        if (strpos($strippedLine, 'leegNietTonen') >= 1)
        {
          $lines = explode("\line ", $strippedLine);
          foreach ($lines as $lineNr => $linePart)
          {
            if (strpos($linePart, 'leegNietTonen') == 3)
            {
              unset($lines[$lineNr]);
            }
          }
          $parts[$nr] = implode("\line ", $lines);
        }
        if (strpos($strippedLine, 'leegNietTonen') == 1 || strpos($strippedLine, 'leegNietTonen') == 2)
        {
          unset($parts[$nr]);
        }
      }
      $dataToParse = implode("\par ", $parts);
      $dataToParse = str_replace($this->_enStart() . 'leegNietTonen' . $this->_enStop(), '', $dataToParse);
      $dataToParse = str_replace('<<leegNietTonen>>', '', $dataToParse);
    }
  

    $dataToParse = preg_replace( "|\\".$this->_enStart()."[a-zA-Z0-9_-]+\\".$this->_enStop()."|", "", $dataToParse);

    return $dataToParse;
  }
  
  function em($word) {
    
   
  
    $map = array(
      chr(0x8A) => chr(0xA9),
      chr(0x8C) => chr(0xA6),
      chr(0x8D) => chr(0xAB),
      chr(0x8E) => chr(0xAE),
      chr(0x8F) => chr(0xAC),
      chr(0x9C) => chr(0xB6),
      chr(0x9D) => chr(0xBB),
      chr(0xA1) => chr(0xB7),
      chr(0xA5) => chr(0xA1),
      chr(0xBC) => chr(0xA5),
      chr(0x9F) => chr(0xBC),
      chr(0xB9) => chr(0xB1),
      chr(0x9A) => chr(0xB9),
      chr(0xBE) => chr(0xB5),
      chr(0x9E) => chr(0xBE),
      chr(0x80) => '&euro;',
      chr(0x82) => '&sbquo;',
      chr(0x84) => '&bdquo;',
      chr(0x85) => '&hellip;',
      chr(0x86) => '&dagger;',
      chr(0x87) => '&Dagger;',
      chr(0x89) => '&permil;',
      chr(0x8B) => '&lsaquo;',
      chr(0x91) => '&lsquo;',
      chr(0x92) => '&rsquo;',
      chr(0x93) => '&ldquo;',
      chr(0x94) => '&rdquo;',
      chr(0x95) => '&bull;',
      chr(0x96) => '&ndash;',
      chr(0x97) => '&mdash;',
      chr(0x99) => '&trade;',
      chr(0x9B) => '&rsquo;',
      chr(0xA6) => '&brvbar;',
      chr(0xA9) => '&copy;',
      chr(0xAB) => '&laquo;',
      chr(0xAE) => '&reg;',
      chr(0xB1) => '&plusmn;',
      chr(0xB5) => '&micro;',
      chr(0xB6) => '&para;',
      chr(0xB7) => '&middot;',
      chr(0xBB) => '&raquo;',
    );
  
//    $map1 = array_flip($map);
    $word = str_replace(array_values($map), array_keys($map), $word);
  
    $pattern = '/&#9633;/';
    preg_match_all($pattern,$word, $replaceValues);

    if ( ! empty ($replaceValues[0]) ) {
      foreach ( $replaceValues[0] as $value ) {
        $word = preg_replace('/&#9633;/', '<input type="checkbox" name="check'.rand(1,100).'" value="1">', $word, 1);
      }
    }
    return $word;
  }
//($number , $decimals = 0 , $dec_point = '.' , $thousands_sep = ',' ) {}
  function formatGetal ($checkboxVars) {
    $dec_point = ',';
    $thousands_sep = '.';
    $decimals = 2;

    $thisValue = $checkboxVars[1];
    if ( isset ($this->parseData[$checkboxVars[1]]) ) {
      $thisValue = $this->parseData[$checkboxVars[1]];
    }

    $checkVar = $checkboxVars[2];

    $thisValue = str_replace(",",".",$thisValue);
    $thisValue = preg_replace('/\.(?=.*\.)/', '', $thisValue);
    $thisValue =  floatval($thisValue);

    if ( ! empty ($checkVar) ) {
      // zowel decimalen als duizendtallen zijn ingesteld
      if (
        ($checkVar[0] === '.' || $checkVar[0] === ',') &&
        ($checkVar[1] === '.' || $checkVar[1] === ',')
      ) {
        $dec_point = $checkVar[1];
        $thousands_sep = $checkVar[0];
      } elseif (
        ($checkVar[0] === '.' || $checkVar[0] === ',')
      ) {
        if ($checkVar[0] === '.') {
          $dec_point = '.';
          $thousands_sep = '';
        } else {
          $dec_point = ',';
          $thousands_sep = '';
        }
        // $checkVar[0] === ',' dus de default
      } else {
        $thousands_sep = '';
      }

      $checkVar = str_replace(array('.',','), '', $checkVar);

      if ( ! empty($checkVar) ) {
        $decimals = (int) $checkVar;
      }
    } else {
      return number_format($thisValue, 0, '', '');
    }

    return number_format($thisValue, $decimals, $dec_point, $thousands_sep);
  }

  function formatCheckbox ($checkboxVars) {
    $thisValue = '';
    $thisUrl = '';

    $thisValue = (int) $checkboxVars[1];
    if ( isset ($this->parseData[$checkboxVars[1]]) ) {
      $thisValue = $this->parseData[$checkboxVars[1]];
    }


    $checkResult = null;
//    debug($checkboxVars);
    if ( isset ($checkboxVars[2]) ) {
      $toCheckData = html_entity_decode($checkboxVars[2]);

//      debug(strpos(html_entity_decode($toCheckData), '>='), html_entity_decode($toCheckData));

      // >=
      if (strpos($toCheckData, '>=') !== false) {
        $toCheck = str_replace('>=', '', $toCheckData);
//        debug('default >=');
        $checkResult = true;
        if ($thisValue >= $toCheck) {
          $checkResult = true;
        }
      }
      // >
      elseif (strpos($toCheckData, '>') !== false) {
        $toCheck = str_replace('>', '', $toCheckData);
//        debug('default >');
        $checkResult = true;
        if ($thisValue > $toCheck) {
          $checkResult = true;
        }
      }
      // <=
      elseif (strpos($toCheckData, '<=') !== false) {
        $toCheck = str_replace('<=', '', $toCheckData);
//        debug('default <=', '-->'. ((int) $thisValue <= (int) $toCheck));
        $checkResult = true;
//        debug((int) $thisValue);
//        debug((int) $toCheck);
//        debug(htmlspecialchars(((int) $thisValue <= (int) $toCheck)));
        if ((int) $thisValue <= (int) $toCheck) {
          $checkResult = true;
        }
      }
      // <
      elseif (strpos($toCheckData, '<') !== false) {
        $toCheck = str_replace('<', '', $toCheckData);
//        debug('default <');
        $checkResult = true;
        if ($thisValue < $toCheck) {
          $checkResult = true;
        }
      }
      elseif (strpos($toCheckData, '!') !== false) {
        $toCheck = str_replace('!', '', $toCheckData);
//        debug('default !=');
        $checkResult = true;
        if ($thisValue != $toCheck) {
          $checkResult = true;
        }
      } else {
//        debug('default 1');
        $checkResult = false;
        if ((int)$thisValue == (int) $toCheckData) {
          $checkResult = true;
        }
      }
    } else {
//      debug('default');
      $checkResult = false;
      if ((int)$thisValue == (int) 1) {
        $checkResult = true;
      }

    }
$random = rand(999,9999);
    if ( $checkResult == true ) {
      return ' 
      <img src="
      
      data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAABLUlEQVQ4T6WSwWmEUBCGZ1BUPLhBEA9e1AY2YAO7HaSEbSEdmA6SCraFlGA6SAM+gnjwILgLHhTxTRghopuscRNv+ub7Zv55Ivzzwb/wURRtpJQJIr7fLGC467oEAO6J6OMmQRiGG8MwBvhr8tUChlVVncEAEM8EQRBsEXEnhHiZ7oZhAODMY2d+T9N0PwoY5sUAwB0RPWdZ9sgShvu+v+x8UhTFF0KcB4HneVs2MjzpfNB1/bVt20sYeMo8z9+4dhC4rlsh4hReut24KIqn2RIdxzkS0WHFP5GUZbmf1o07sG37KKVckpwQ0a+q6vyjgD9alnV1EiLa1XU95L4q4APTNL9JiChummbMvSjgQ03TppKk67pZ7l8FXKAoCksepJQ+AMxyrxKsuJGh5BOyB3QaZ2L+4AAAAABJRU5ErkJggg==
      
      ">
';
    } else {
      return ' 
      <img style="height:16px" src="
      
      data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAYAAABXAvmHAAADpklEQVRoQ9Waa04iQRCAu0GcgaADxrdREaOo+GM2HkBvoHsCuYEeZfcGuydY9ga7BzDyQwQ0Imp8G0UlMIRHb9qVGtGa/rU7BZPwh0fq+/pRPVQNZ11+8S7nZ44CKysrBmMsIV9CCJNKlHOeYox9k6+dnZ3H9xyogGma65xz+aMQFTgStyiESKRSqZ9vP/sgYJrm5qtxB7G3oUiJ76132gTkyAshkp1KDtCcb7RmAgTkmq/VaoUOWzZOY1n0+XwRuSdAYHl5eYsx9qXTR/8N3/be3t5XEIjH47uMMcdswzlPvm7soguSIblhhRAbilipdDr9CQSWlpaE05c554l0Og0bxwWBlxDxeHxTCCGzIXrt7+/zF4FoNGpomuY0sslMJvPZLej3cRYXF38wxtCZyGQytkBvby8qIKcxl8u15V43ZWKxmDyT0MyYzWb/CsgrFouhS4hzvpbNZn+7Cf021sLCwqoQ4hcWP5fL2QLz8/OoQL1eD+Xz+Q9HuFtCcnn39PSgq+Pg4MAWmJubQwUODw/Jb/hUbAA3OzuLChwdHZELqNgALhqNogL5fJ5cQMUGcDMzM6jA8fExuYCKDeAikQgqUCgUyAVUbAA3PT2NCpycnJALqNgAbmpqChU4PT0lF1CxAdzk5CQqcHZ2Ri6gYgO4iYkJVOD8/JxcQMUGcOPj46jAxcUFuYCKDeDGxsZQgcvLS3IBFRvAjY6OogJXV1fkAio2gBsZGUEFrq+vyQVUbAA3PDyMCtzc3JALqNgAbmhoCBW4vb0lF1CxAdzg4CAqcHd3Ry6gYgO4gYEBVOD+/p5cQMUGcOFwGBV4eHggF1CxAVwoFEIFisUiuYCKDeAMw0AFHh/t6p1b/4Pfx1GxgUB/fz8q8PT0RD4DKjaA6+vrQwWen59JBcLhsFGv19GqhGQDuGAw6FRaXCuVSmR1oWAwuMoYQ+tCpVLJFggEAk4CG+VymawyFwgE1hljaGWuXC7bAn6/30kgWalUyGqjfr/fsTZaqVRsAV3XHavTstFnWZbr1Wld15XtLsuybAFN05T9ATmNstTt8XiKlmXJzuH/KDcauq6bzWYzJEv6TlXp1zSbqlardn9A07QtIUTXdGg459vVatXu0DDGDJ/P1zU9slqtFpGroC3He71ex1o81SmMxZU9i0aj8ZIZPxxSXq+34/vEjUYD7xO3bOVMNJvNjuvUezyeRGvkW6yq2wR4VkLVvXRhacGzEljmI73P+RfyfwDYz4dAkx33LwAAAABJRU5ErkJggg==
      
      ">
';
    }





//    if (strpos($thisUrl, 'http') !== false) {
//      $newVar = '<a href="' . $thisUrl . '" target="_blank">' . $thisValue . '</a>';
//    }



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
    
    if ( isset ($this->parseData[$data[1]]) ) {
      $returnData = $this->parseData[$data[1]];
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