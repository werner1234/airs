<?php


include_once("wwwvars.php");
include_once("../config/ordersVars.php");
include_once("../classes/mysqlList.php");


echo template($__appvar["templateContentHeader"],$editcontent);

if ( ! isset ($_GET['PortefeuilleVan']) ) {
  $_GET['PortefeuilleVan'] = '';
}

class AIRS_rapportEmt {
  
  var $fondsChecks = array();
  var $clientChecks = array();
  
  var $reportChecks = array (
    1,2,3,4,5,6
  );
  
  var $toCheckGroups = array (
    1 =>  'Clientclassificatie',
    2 =>  'Kennis',
    3 =>  'Risicohouding',
    4 =>  'Clientprofiel',
    5 =>  'Soort dienstverlening',
    6 =>  'Risicotolerantie',
    7 =>  'Risicotolerantie2',
    8 =>  'Horizon',
    9 =>  'Risicotolerantie',
  );
  
  var $toCheckGroupFields = array ();
  var $toCheckFields = array(
    'ClientTypeRetail'                  => array('check'  => 1, 'checkGroup' => 1),
    'ClientTypeProfessional'            => array('check'  => 1, 'checkGroup' => 1),
    'ClientTypeEligibleCounterparty'    => array('check'  => 1, 'checkGroup' => 1),
    
    'ExpertiseBasic'                    => array('check'  => 1, 'checkGroup' => 2),
    'ExpertiseInformed'                 => array('check'  => 1, 'checkGroup' => 2),
    'ExpertiseAdvanced'                 => array('check'  => 1, 'checkGroup' => 2),
    
    'CapitalLossNone'                   => array('check'  => 2, 'checkGroup' => 3),
    'CapitalLossLimited'                => array('check'  => 2, 'checkGroup' => 3),
    'CapitalLossTotal'                  => array('check'  => 2, 'checkGroup' => 3),
    'CapitalLossBeyondInvestment'       => array('check'  => 2, 'checkGroup' => 3),
    
    'ProfilePreservation'               => array('check'  => 1, 'checkGroup' => 4),
    'ProfileGrowth'                     => array('check'  => 1, 'checkGroup' => 4),
    'ProfileIncome'                     => array('check'  => 1, 'checkGroup' => 4),
    'ProfileHedging'                    => array('check'  => 1, 'checkGroup' => 4),
    'ProfileOptionsLeverage'            => array('check'  => 1, 'checkGroup' => 4),
    'ProfileOther'                      => array('check'  => 1, 'checkGroup' => 4),
    
    'ServiceExecOnly'                   => array('check'  => 3, 'checkGroup' => 5),
    'ServiceExecOnlyAppTest'            => array('check'  => 3, 'checkGroup' => 5),
    'ServiceAdvice'                     => array('check'  => 3, 'checkGroup' => 5),
    'ServiceManagement'                 => array('check'  => 3, 'checkGroup' => 5),
    
    'RiskSRRI'                          => array('check'  => 4, 'checkGroup' => 6),
    'RiskPRIIPSRI'                      => array('check'  => 4, 'checkGroup' => 7),
    
    'ClientHorizon'                     => array('check'  => 5, 'checkGroup' => 8),
    
    'ClientRiskTolerance'               => array('check'  => 6, 'checkGroup' => 9),
  
  );
  
  
  var $checkedChecks;
  var $checkedCheckGroups;
  var $checkedCheckFields;
  
  
  function AIRS_rapportEmt () {
    // array voor do check opbouwen zodat we hier doorheen kunnen lopen
    foreach ( $this->toCheckFields as $field => $data ) {
      $this->toCheckGroupFields[$data['checkGroup']][$data['check']]['fields'][] = $field;
      $this->toCheckGroupFields[$data['checkGroup']][$data['check']]['check'] = $data['check'];
    }
  }
  
  
  function getGroups () {
    return $this->toCheckGroups;
  }
  
  
  function fillFondsData ( $fondsData = array() ) {
    
    if ( ! empty ($fondsData) ) {
      foreach ( $fondsData as $key => $fields ) {
        $this->fondsChecks[$fields['fondsenFonds']]['fields'] =  $fields;
        foreach ( $fields as $field => $fieldValue ) {
         
          if ( isset ($this->toCheckFields[$field]) ) {
            $this->fondsChecks[$fields['fondsenFonds']]['checks'][$this->toCheckFields[$field]['checkGroup']][$field]['fondsValue'] =  $fieldValue;
            $this->fondsChecks[$fields['fondsenFonds']]['checks'][$this->toCheckFields[$field]['checkGroup']]['fondsValue'][$field] =  $fieldValue;
          }
        }
      }
    }
    
  }
  
  function fillClientData ( $clientData = array() ) {
    if ( ! empty ($clientData) ) {
      foreach ( $clientData as $key => $fields ) {
        foreach ( $fields as $field => $fieldValue ) {
          $field = str_replace('Ptf_', '', $field);
          if ( isset ($this->toCheckFields[$field]) ) {
            $this->clientChecks[$fields['portefeuille']]['fields'][$field] =  $fieldValue;
            
            $this->clientChecks[$fields['portefeuille']]['checks'][$this->toCheckFields[$field]['checkGroup']][$field]['clientValue'] =  $fieldValue;
            $this->clientChecks[$fields['portefeuille']]['checks'][$this->toCheckFields[$field]['checkGroup']]['clientValue'][$field] =  $fieldValue;
          } elseif ( $field == 'id' || $field == 'portefeuille' ) {
            $this->clientChecks[$fields['portefeuille']]['fields'][$field] =  $fieldValue;
          }
        }
      }
    }
  }
  
  
  function doCheck ($portefeuille = '', $fonds = '') {
    $validated = array();
    $this->checkedCheckGroups = null;
    
    if ( empty ($portefeuille) || empty ($fonds) ) {
      return null;
    } else {
      //$fondsData[$key]['VKM']
      foreach ( $this->fondsChecks[$fonds]['checks'] as $key => $value ) {
        $this->checkedCheckGroups[$key] = array_merge ($value, $this->clientChecks[$portefeuille]['checks'][$key]);
        $this->checkedCheckGroups[$key]['Fonds'] = $fonds;
        $this->checkedCheckGroups[$key]['Portefeuille'] = $portefeuille;
      }
    }
    

    foreach ( $this->toCheckGroupFields as $group => $checkData ) {
      foreach ( $checkData as $data ) {
        $callCheck = 'check'.$data['check'];
        if (method_exists($this,$callCheck)) {
          if ( $data['check'] === 2 ) {
          $test = $this->$callCheck ($data['fields'], $group);
          $validated[$group] = $test;//$this->$callCheck ($data['fields'], $group);
          } else {
            $validated[$group] =$this->$callCheck ($data['fields'], $group);
          }
        }
      }
    }
    
    return $validated;
  }
  /**
   * 1 = Akkoord
   * 2 = Niet akkoord
   * 3 = Niet te controleren
   */
  
  /** Check 1 */
  function check1 ($data, $group) {
    //tellen van waarden
    $clientCount = array_count_values($this->checkedCheckGroups[$group]['clientValue']);
    $fondsCount = array_count_values($this->checkedCheckGroups[$group]['fondsValue']);
    $fondsData = $this->checkedCheckGroups[$group]['fondsValue'];
    $clientData = $this->checkedCheckGroups[$group]['clientValue'];

    $returnStatus = 3;
  
    /**
     * Ja mag maar een keer voorkomen bij Client
     * En Client Ja moet gelijk zijn aan Fonds ja of neutraal
     */
    if ( isset ($clientCount['Ja']) && (int) $clientCount['Ja'] === 1 ) {
      foreach ( $clientData as $field => $check ) {
        if ( $check === 'Ja' && ( $fondsData[$field] === 'Ja' || $fondsData[$field] === 'Neutraal') ) {
          return 1;
        } elseif ( $check === 'Ja' && ( $fondsData[$field] !== 'Ja' && ! $fondsData[$field] !== 'Neutraal') )  {
          return 2;
        }
      }
    }
    
    return $returnStatus;
  }

    /**
     * 1 = Akkoord
     * 2 = Niet akkoord
     * 3 = Niet te controleren
     */

    /** Check 2 */
    function check2 ($data, $group)
    {
      //tellen van waarden
      $clientCount = array_count_values($this->checkedCheckGroups[$group]['clientValue']);
      $fondsCount = array_count_values($this->checkedCheckGroups[$group]['fondsValue']);
      $clientCountTotal = count ($this->checkedCheckGroups[$group]['clientValue']);
      $fondsCountTotal = count ($this->checkedCheckGroups[$group]['fondsValue']);

      $fondsData = $this->checkedCheckGroups[$group]['fondsValue'];
      $clientData = $this->checkedCheckGroups[$group]['clientValue'];

      $returnStatus = 3;

      $checkSum =
          ( isset ($fondsCount['Ja']) ? $fondsCount['Ja'] :0 )
          + ( isset ($fondsCount['Neutraal']) ? $fondsCount['Neutraal'] :0 )
          + ( isset ($fondsCount['Nee']) ? $fondsCount['Nee'] :0 );

      if ( (int) $checkSum != (int) $fondsCountTotal) {
          return 3;
      }

      // Client LossBeyondInvestment = Ja is akkoord
      if ( $clientData['CapitalLossBeyondInvestment'] === 'Ja' ) {
        return 1;
      }
      // Client CapitalLossTotal
      elseif ( $clientData['CapitalLossTotal'] === 'Ja' ) {
        if ( in_array($fondsData['CapitalLossBeyondInvestment'], array('Ja', 'Neutraal')) ) {
          return 2;
        }
        return 1;
      }
      // Client CapitalLossLimited
      elseif ( $clientData['CapitalLossLimited'] === 'Ja' ) {
        if (
          in_array($fondsData['CapitalLossBeyondInvestment'], array('Ja', 'Neutraal'))
          || in_array($fondsData['CapitalLossTotal'], array('Ja', 'Neutraal'))
        ) {
          return 2;
        }
        return 1;
      }
      // Client CapitalLossLimited
      elseif ( $clientData['CapitalLossNone'] === 'Ja' ) {
        if (
          ! in_array($fondsData['CapitalLossNone'], array('Ja', 'Neutraal'))
        ) {
          return 2;
        }
        return 1;
      }

      return $returnStatus;
    }
  
  /**
   * 1 = Akkoord
   * 2 = Niet akkoord
   * 3 = Niet te controleren
   */
  
  /** Check 3 */
  function check3 ($data, $group)
  {
    //tellen van waarden
    $clientCount = array_count_values($this->checkedCheckGroups[$group]['clientValue']);
    $fondsCount = array_count_values($this->checkedCheckGroups[$group]['fondsValue']);
    $clientCountTotal = count ($this->checkedCheckGroups[$group]['clientValue']);
  

    $fondsData = $this->checkedCheckGroups[$group]['fondsValue'];
    $clientData = $this->checkedCheckGroups[$group]['clientValue'];
  
    $returnStatus = 3;

    /**
     * Retail mag maar een keer voorkomen bij Client en geen Professional bevat
     *
     */
    if (
      ( isset ($clientCount['Retail']) && (int) $clientCount['Retail'] === 1 ) ||
      ( isset ($clientCount['Professional']) && (int) $clientCount['Professional'] === 1 )
    ) {
      foreach ( $clientData as $field => $check ) {
        if ( ($check === 'Professional' || $check === 'Retail') && $fondsData[$field] === 'Neither' ) {
          return 2;
        } elseif ( $check === 'Professional' || $check === 'Retail') {
          if ( $check === 'Retail' && ($fondsData[$field] === 'Both' || $fondsData[$field] === 'Retail') ) {
            return 1;
          } elseif ( $check === 'Professional' && ($fondsData[$field] === 'Both' || $fondsData[$field] === 'Professional') ) {
            return 1;
          } elseif ( $check === 'Neither' || $check === '' && ($fondsData[$field] === 'Both' || $fondsData[$field] === 'Professional' || $fondsData[$field] === 'Retail') ) {
            return 2;
          }
        }
      }
    }

//    if (
//        ( isset ($clientCount['Retail']) && (int) $clientCount['Retail'] === 1 ) &&
//        ! isset($clientCount['Professional'])
//      ) {
//      foreach ( $clientData as $field => $check ) {
//
//        if ( $fondsData[$field] === 'Retail' || $fondsData[$field] === 'Both' ) {
//          return 1;
//        } elseif ( $fondsData[$field] !== 'Retail' && $fondsData[$field] !== 'Both' ) {
//          return 2;
//        }
//
//      }
//    }
//
//    /**
//     * Professional mag maar een keer voorkomen bij Client en geen Retail bevat
//     * En Client Ja moet gelijk zijn aan Fonds ja of Both
//     */
//    else if (
//        ( isset ($clientCount['Professional']) && (int) $clientCount['Professional'] === 1 ) &&
//        ! isset($clientCount['Retail'])
//    ) {
//
//
//      foreach ( $clientData as $field => $check ) {
//
//
//
//        if ( $fondsData[$field] === 'Professional' || $fondsData[$field] === 'Both' ) {
//          return 1;
//        } elseif ( $fondsData[$field] !== 'Professional' && $fondsData[$field] !== 'Both' ) {
//          return 2;
//        }
//
//      }
//    }
  
    return $returnStatus;
  }
  
  
  /**
   * 1 = Akkoord
   * 2 = Niet akkoord
   * 3 = Niet te controleren
   */
  
  /** Check 4 */
  function check4 ($data, $group)
  {
    //tellen van waarden
    $clientCount = array_count_values($this->checkedCheckGroups[$group]['clientValue']);
    $fondsCount = array_count_values($this->checkedCheckGroups[$group]['fondsValue']);
    $clientCountTotal = count ($this->checkedCheckGroups[$group]['clientValue']);
  
    $clientValue = (int) array_shift(array_slice($this->checkedCheckGroups[$group]['clientValue'], 0, 1));
    $fondsValue = (int) array_shift(array_slice($this->checkedCheckGroups[$group]['fondsValue'], 0, 1));
    
    if ( $clientValue >= $fondsValue && $fondsValue > 0 ) {
      return 1;
    }
    elseif ( $clientValue < $fondsValue && $fondsValue > 0 ) {
      return 2;
    }
  
    return 3;
  }
  
  
  
  /**
   * 1 = Akkoord
   * 2 = Niet akkoord
   * 3 = Niet te controleren
   */
  
  /** Check 5 */
  function check5 ($data, $group)
  {
    //tellen van waarden
    $clientCount = array_count_values($this->checkedCheckGroups[$group]['clientValue']);
    $fondsCount = array_count_values($this->checkedCheckGroups[$group]['fondsValue']);
    $clientCountTotal = count ($this->checkedCheckGroups[$group]['clientValue']);

    if ( ( ! empty ($clientCount) && ! isset ($clientCount['']) && ! isset ($clientCount['Neutral']) ) && isset ($fondsCount['Neutral']) ) {
      return 1;
    }
    //Klant Long --> Akkoord
    elseif ( isset ($clientCount['Long'])  ) {
      return 1;
    }
    //Klant Medium --> Fonds long --> niet Akkoord
    elseif ( isset ($clientCount['Medium']) &&  isset ($fondsCount['Long']) ) {
      return 2;
    }
    elseif ( isset ($clientCount['Medium']) && ! isset ($fondsCount['Long']) && ! isset($fondsCount['']) ) {
      return 1;
    }
    //Klant Short > Fonds long of medium --> Niet akkoord
    elseif ( isset ($clientCount['Short']) ) {
      if (  isset ($fondsCount['Short']) ) {
        return 1;
      } elseif (  isset ($fondsCount['Long']) || isset ($fondsCount['Medium'])) {
        return 2;
      }
    }
    
    return 3;
  }
  
  
  /**
   * 1 = Akkoord
   * 2 = Niet akkoord
   * 3 = Niet te controleren
   */
  
  /** Check 6 */
  function check6 ($data, $group)
  {
    //tellen van waarden
    $clientCount = array_count_values($this->checkedCheckGroups[$group]['clientValue']);
    $fondsCount = array_count_values($this->checkedCheckGroups[$group]['fondsValue']);
    $clientCountTotal = count ($this->checkedCheckGroups[$group]['clientValue']);
  
    if ( isset ($clientCount['High'])  ) {
      return 1;
    }
    //Klant Medium --> Fonds long --> niet Akkoord
    elseif ( isset ($clientCount['Medium']) &&  isset ($fondsCount['High']) ) {
      return 2;
    }
    elseif ( isset ($clientCount['Medium']) && ! isset ($fondsCount['High']) && ! isset($fondsCount['']) ) {
      return 1;
    }
    //Klant Short > Fonds long of medium --> Niet akkoord
    elseif ( isset ($clientCount['Low']) ) {
      if (  isset ($fondsCount['Low']) ) {
        return 1;
      } elseif (  isset ($fondsCount['High']) || isset ($fondsCount['Medium'])) {
        return 2;
      }
    }
  
    return 3;
  }
  
  
}



$emt = new AIRS_rapportEmt();

$db = new DB();



$portefeuille = $_GET['PortefeuilleVan'];

$inFondsCrmFields = array();
$inFondsCrmFieldstest = '';
foreach ( $emt->toCheckFields as $fieldName => $fieldValue ) {
  $inFondsCrmFields[] = 'Ptf_' . $fieldName;
  $inFondsFondsFields[] =  'FondsenEMTdata.' . $fieldName;
  $inFondsCrmFieldstest .= 'CRM_naw.Ptf_' . $fieldName . ', ';
}

$portefeuilleInFondsQuery = "
  SELECT
    Rekeningen.Portefeuille,
    SUM(Rekeningmutaties.aantal) as aantal,
    Rekeningmutaties.Fonds
    
    FROM
      Rekeningmutaties
    
      INNER JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
            WHERE Rekeningen.Portefeuille = '".mysql_real_escape_string($portefeuille)."'

      AND  year(Rekeningmutaties.Boekdatum)='" . date('Y') . "'
      AND Grootboekrekening='FONDS'
      
      GROUP BY Rekeningen.Portefeuille, Rekeningmutaties.Fonds

";

$db->executeQuery($portefeuilleInFondsQuery);
while ($portefeuilleInFondsData = $db->nextRecord()) {
  if ($portefeuilleInFondsData['aantal'] <> 0 ) {
    $fondsList[$portefeuilleInFondsData['Fonds']] = $portefeuilleInFondsData['Fonds'];
    $portefeuilleList[$portefeuilleInFondsData['Portefeuille']] = $portefeuilleInFondsData['Portefeuille'];

    $portefeuilleInFondsList[] = $portefeuilleInFondsData;
  }
}


$fondsObj = new Naw();
$testdatacrm = $fondsObj->parseByArray('all', array (
  'fields' => array_merge( array(
      'id',
      'naam',
      'zoekveld',
      'portefeuille',
  ), $inFondsCrmFields ),
  'conditions' => array (
    'portefeuille' => trim($portefeuille)
  )
));
$emt->fillClientData($testdatacrm);



$fondsObj = new Fonds();
$testdata = $fondsObj->parseByArray('first', array (
  'fields' => array_merge( array (
    'Fondsen.Fonds AS `fondsenFonds`',
    'Fondsen.id AS `fondsId`',
    'FondsenEMTdata.id AS `fondsinfoId`',
    'Fondsen.VKM'
  ), $inFondsFondsFields) ,
  'conditions' => array (
    'Fondsen.Fonds' => $fondsList,
  ),
  'joins' => array (
    'FondsenEMTdata' => 'FondsenEMTdata.Fonds = Fondsen.Fonds'
  )
));

$emt->fillFondsData($testdata);

?>
  
  
  <style>
  
    .table {
      width: 90%;
      margin-bottom: 1rem;
      border-collapse: collapse;
      margin-top: 60px;
      margin-left: 25px;
    }
    .table td, .table th {
      padding: 0px;
      vertical-align: top;
    }
    .table td {
      border: 1px solid #dee2e6;
    }
    
    th.rotate {
      /* Something you can count on */
      height: 85px;
      white-space: nowrap;
    }
    
    th.rotate > div {
      transform:
        /* Magic Numbers */
        translate(25px, 51px)
          /* 45 is really 360 - 45 */
        rotate(315deg);
      width: 30px;
    }
    th.rotate > div > span {
      padding: 0px;
    }

    table>thead>tr>th, .table>tfoot>tr>td {
      padding: 0px!important;
      border: none!important;
      background: white!important;
    }
  
  </style>


<?php

$extraJoin=array('Portefeuille'=>'','PortefeuilleClusters'=>'');
$extraWhere=array('Portefeuille'=>'AND Portefeuilles.Portefeuille NOT IN(SELECT ModelPortefeuilles.Portefeuille FROM ModelPortefeuilles WHERE ModelPortefeuilles.Fixed=1) ','PortefeuilleClusters'=>'');
if(!checkAccess('portefeuille'))
{
  if(isset($_SESSION['usersession']['gebruiker']['internePortefeuilles']) && $_SESSION['usersession']['gebruiker']['internePortefeuilles'] == '1')
    $internDepotToegang="OR Portefeuilles.interndepot=1";
  else
    $internDepotToegang='';
  
  if($_SESSION['usersession']['gebruiker']['Accountmanager'] <> '' && $_SESSION['usersession']['gebruiker']['overigePortefeuilles'] == 0)
  {
    $extraWhere['Portefeuille']  .= " AND(Portefeuilles.Accountmanager='".$_SESSION['usersession']['gebruiker']['Accountmanager']."' OR Portefeuilles.tweedeAanspreekpunt ='".$_SESSION['usersession']['gebruiker']['Accountmanager']."' $internDepotToegang) ";
  }
  else
  {
    $extraJoin['Portefeuille'] .= "INNER JOIN VermogensbeheerdersPerGebruiker ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND  VermogensbeheerdersPerGebruiker.Gebruiker = '".$USR."' JOIN Gebruikers ON Gebruikers.Gebruiker = VermogensbeheerdersPerGebruiker.Gebruiker ";
    $extraWhere['Portefeuille']  .=" AND (Portefeuilles.beperktToegankelijk = '0' OR  Gebruikers.beperkingOpheffen = '1' )";
  }
}

$portefeuilleQuery =  "SELECT Client,Portefeuille FROM Portefeuilles ".$extraJoin['Portefeuille']."
WHERE Portefeuilles.Einddatum  >=  NOW() ".$extraWhere['Portefeuille']."
ORDER BY Portefeuille";
$db->executeQuery($portefeuilleQuery);
$portefeuilleSelect = '';
while ($portefeuilleData = $db->nextRecord()) {
  $portefeuilleSelect .= '<option value="' . ($portefeuilleData['Portefeuille']) . '" ' . ($portefeuilleData['Portefeuille'] === $_GET['PortefeuilleVan'] ? 'selected':'')  .'>' . trim($portefeuilleData['Portefeuille']) . '</option>';
}


echo '

<form action="controlerapportEmt.php" method="get">
  <select autocomplete="autocomplete_off_hack_xfr4!k" name="PortefeuilleVan" style="width:200px" >' . $portefeuilleSelect . '</select>
  <input type="submit" value="Submit">
</form>

';


//<div class="formrechts" id="div_PortefeuilleVan">
//<select autocomplete="autocomplete_off_hack_xfr4!k" name="PortefeuilleVan" style="width:200px" onfocus="javascript:loadOptions('Portefeuille');">
//<option value="0004620">0004620</option>
//
//</select>
//</div>

echo '<div id="AETest" style="display:none">';
echo '<pre>'; print_r($emt); echo '</pre>';
echo '<pre>-----------------</pre>';
echo '<pre>'; print_r($portefeuilleInFondsList); echo '</pre>';
echo '</div>';

echo '<table class="table">';
echo '<thead><tr>';

echo '<th style="vertical-align: bottom; text-align: left;" class=""><div><span>Fonds</span></div></th>';
echo '<th style="vertical-align: bottom; text-align: left;" class=""><div><span>Portefeuille</span></div></th>';
foreach ( $emt->getGroups() as $groupName ) {
  echo '<th class="rotate"><div><span>' . $groupName . '</span></div></th>';
}
echo '</tr></thead>';
echo '<tbody>';

foreach ( $portefeuilleInFondsList as $portefeuilleInFondsData ) {
  if ( (int) $emt->fondsChecks[$portefeuilleInFondsData['Fonds']]['fields']['VKM'] === 1 ) {
  
  $test = $emt->doCheck($portefeuilleInFondsData['Portefeuille'], $portefeuilleInFondsData['Fonds']);


  echo '<tr>';

  echo '<td>' . $portefeuilleInFondsData['Fonds'] . '</td>';
  echo '<td>' . $portefeuilleInFondsData['Portefeuille'] . '</td>';
  
  if ( $emt->fondsChecks[$portefeuilleInFondsData['Fonds']]['fields']['fondsinfoId'] == NULL ) {
    echo '<td colspan="'.count($test).'">Geen fondsinformatie aanwezig</td>';
  } elseif(
    ! isset($emt->clientChecks[$portefeuilleInFondsData['Portefeuille']]) ||
    $emt->clientChecks[$portefeuilleInFondsData['Portefeuille']]['fields']['id'] == NULL ) {
    echo '<td colspan="'.count($test).'">Geen clientinformatie aanwezig</td>';
  } else {
    foreach ( $test as $key => $value ) {
      if ( $value === 1 ) {
        echo '<td style="color:green"><i class="fa fa-check" aria-hidden="true"></i></td>';
      } elseif ( $value === 2 ) {
        echo '<td style="color:red"><i class="fa fa-times" aria-hidden="true"></i></td>';
      } elseif ( $value === 3 )  {
        echo '<td style="color:orange"><i class="fa fa-question" aria-hidden="true"></i></td>';
      } else {
        echo '<td>'.$value.'</td>';
      }
    }
  }
  echo '</tr>';
  }
}
echo '</tbody>';
echo '</table>';





