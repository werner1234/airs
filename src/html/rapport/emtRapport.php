<?php
/**
 * 1 = Akkoord
 * 2 = Niet akkoord
 * 3 = Niet te controleren
 */
class emtRapport
{
  var $selectData           = '';
  var $excelData            = '';

  var $showOnly             = array();
  
  var $fondsWidth           = 50;
  var $portefeuilleWidth    = 40;
  
  var $headerWidths         = array();
  var $headerAligns         = array();
  
  var $dbWaarden            = array();
  
  var $fondsChecks          = array();
  var $clientChecks         = array();
  
  var $reportChecks         = array (
    1,2,3,4,5,6
  );
  
  var $toCheckGroupsShort   = array (
    1 =>  'CCF',//'Clientclassificatie',
    2 =>  'Kennis',//'Kennis',
    3 =>  'RH',//'Risicohouding',
    4 =>  'CP',//'Clientprofiel',
    5 =>  'SD',//'Soort dienstverlening',
    6 =>  'RT4020',//'Risicotolerantie',
    7 =>  'RT4010',//'Risicotolerantie2',
    8 =>  'HR',//'Horizon',
    9 =>  'RT4030',//'Risicotolerantie',
  );
  
  var $toCheckGroups        = array (
    1 =>  'Clientclassificatie',
    2 =>  'Kennis',
    3 =>  'Risicohouding',
    4 =>  'Clientprofiel',
    5 =>  'Soort dienstverlening',
    6 =>  'SRRI Cijfer',
    7 =>  'PRIIPS SRI',
    8 =>  'Horizon',
    9 =>  'Cl Risk Tolerance',
  );
  
  var $toCheckGroupsDB       = array (
    1 =>  'clientclassificatie',
    2 =>  'kennis',
    3 =>  'rRisicohouding',
    4 =>  'clientprofiel',
    5 =>  'soort_dienstverlening',
    6 =>  'risicotolerantie',
    7 =>  'risicotolerantie2',
    8 =>  'horizon',
    9 =>  'client_risicotolerantie',
  );
  
  var $toCheckGroupFields   = array ();
  var $toCheckFields        = array(
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
  
  function emtRapportSet( $selectData )
  {
    $this->selectData = $selectData;
    $this->pdf->excelData = array();
    
    $this->pdf = new PDFOverzicht('L','mm');
    $this->pdf->rapport_type = "orderValidatie";
    $this->pdf->SetAutoPageBreak(true,15);
    $this->pdf->pagebreak = 190;
    
    $this->pdf->marge = 10;
    $this->pdf->SetLeftMargin($this->pdf->marge);
    $this->pdf->SetRightMargin($this->pdf->marge);
    $this->pdf->SetTopMargin($this->pdf->marge);
    $this->pdf->SetFont("Times","",10);
    
    $this->db=new DB();
  
    $this->checkImg=base64_decode('iVBORw0KGgoAAAANSUhEUgAAADIAAAAyCAMAAAAp4XiDAAAAe1BMVEUAAAAAgAAAgAAAgAAAgAAAgAAAgAAAgAAAgAAAgAAAgAAAgAAAgAAAgAAAgAAAgAAAgAAAgAAAgAAAgAAAgAAAgAAAgAAAgAAAgAAAgAAAgAAAgAAAgAAAgAAAgAAAgAAAgAAAgAAAgAAAgAAAgAAAgAAAgAAAgAAAgADAcKSoAAAAKHRSTlMAAQMEBQYHExQXGBwdHh8gKC83OUNNYmmUl6OyvMXHyMrR6e/z9/n7UuJ97QAAAMdJREFUSMft0NcSwiAQheFoii323rue939CyUQNILC7F97lv4OdbwY2iur+VDqdxDIxvAKXjkg8oLp1pUJiPoJvKqFMiyMGmgDOYgFkYvFsUKJvCczFYlnN0kwommtgk4jErjifEqkAjqbJaWEZljBMfvfvaqXfH2KGiM3J24SETbAvTC8krIeVhhDG90szJoTDgBKkcQjCOEXQeETAeIXXBITHBIXTEMJhSPFjGMIyLGEYptAMW3yNQCizVWIhEarRrC0UdeFe/1FtPZhynq4AAAAASUVORK5CYII=');
    $this->deleteImg=base64_decode('iVBORw0KGgoAAAANSUhEUgAAABEAAAARCAMAAAAMs7fIAAAAhFBMVEX/AAj/Agr/CBD/ChL/DBT/GyL/HST/Iyr/JSz/Q0n/VVr/V1z/WV7/Wl//XGH/ZWr/Z2z/aG3/eX3/fID/hIj/iIz/k5b/lpn/urz/vsD/v8H/1db/19j/2dr/29z/3N3/4+T/5OX/5eb/5uf/5+j/6On/7/D/8PD//Pz//f3//v7///+1cU3fAAAAjElEQVQYV1XOxxKCQBRE0TsMBswBFQOiOAam////XLwpSu/yLLoaqavesuLxKaFPQR4MJriL6AowilPA1VRgFGcADHl5o8aAPWqNUqXQH5US+qVSJromWCpJXCTJg0kP4IOE4rwH8A+RjrGyeR84GezUZgCMCc5AumcAG3R2BtItg3VEqgcHWe1oG/UFUVQT2zZ8HjQAAAAASUVORK5CYII=');
//    $this->deleteImg=base64_decode('iVBORw0KGgoAAAANSUhEUgAAABkAAAAZCAMAAADzN3VRAAAAV1BMVEUAAAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AAD/AABpj0HLAAAAHHRSTlMAAgQGCAkKGx84OUVHVVZXWGZroqrR2tze4OT5E/F0bQAAAKRJREFUKJF10NsSQ0AQBNB2WSwhBCvo///OPGB2JpJ9mq1TUzXdAJCNW4P48mn1x5TNJJ8CLpB8CERygTwoOYDsNJAenlQkwBWVCFsFnIAh0itCyGEoggN+0gl3EvgmBZYMoNjVhRpUDpKtgsVe0P0DoTuczRdv+e+G0rgRCnV8g9rkiLShvMbFmcgj0GsQmjOctEglgwDQawAGAaCsU91V5RMAHyWHKe5lhpgMAAAAAElFTkSuQmCC');
    $this->questionImg=base64_decode('iVBORw0KGgoAAAANSUhEUgAAABkAAAAZCAMAAADzN3VRAAAAqFBMVEUAAAD/pQD/pQD/pQD/pQD/pQD/pQD/pQD/pQD/pQD/pQD/pQD/pQD/pQD/pQD/pQD/pQD/pQD/pQD/pQD/pQD/pQD/pQD/pQD/pQD/pQD/pQD/pQD/pQD/pQD/pQD/pQD/pQD/pQD/pQD/pQD/pQD/pQD/pQD/pQD/pQD/pQD/pQD/pQD/pQD/pQD/pQD/pQD/pQD/pQD/pQD/pQD/pQD/pQD/pQD/pQAjeu6NAAAAN3RSTlMAAQIDBQYHCAoLDA0OFBYYGRofICorLzg5OkJMUmtsbXSVmqCytLW8wcPHzNHT1dna5Obo7fn968xtmQAAAKBJREFUGBltwYUCgjAABNBjdndjYSM2eP//Z24DURjvIZbvjqfDGgydDTVvjKQVY4cK/jj886giNmfCyUKkwZQJIqUrpUvPstpHKnt85dbkrQCpSCXAz+xZh2JRg8mmckZa1aFmI8V+U7vnkLRgyCsjacDQUiBlR8VvwhBQ6cP0ouQjQ2cktZBFSMjiUrohAzUBEzUBEzUBEzUB05aSi9gHsM4pXy+/dXIAAAAASUVORK5CYII=');
  
    // set checks
    if ( isset ($_POST['toCheckGroup']) && is_array ($_POST['toCheckGroup']) ) {
      $this->showOnly = array_flip($_POST['toCheckGroup']);
    }
    
    $this->AIRS_rapportEmt();
  }
  
  function createTempTable ($USR, $table) {
    global $USR;
    //Checks columns
    $checksFields = '';
    foreach ( $this->toCheckGroupsDB as $key => $name ) {
      $checksFields .= "`" . $name . "` VARCHAR( 50 ) NOT NULL , \n";
    }
    
    
    $this->dbTable="CREATE TABLE `reportbuilder_$USR` (
      `id` INT NOT NULL AUTO_INCREMENT ,
      `Rapport` VARCHAR( 20 ) NOT NULL ,
      `add_date` datetime ,
      `Portefeuille` VARCHAR( 24 ) NOT NULL ,
      `fonds` VARCHAR( 24 ) NOT NULL ,
      `noFonds` VARCHAR( 50 ) NOT NULL,
      `noClient` VARCHAR( 50 ) NOT NULL
      
      " . (! empty ($checksFields) ? ','. $checksFields : '') . "
      
      PRIMARY KEY ( `id` ),
      KEY `Portefeuille` (`Portefeuille`)
      )";
  
  
    if($this->dbTable)
    {
      $db = new DB();
      $db->SQL($this->dbTable);
      $db->Query();
      $query="show variables like 'character_set_database'";
      $db->SQL($query);
      $db->Query();
      $charset=$db->lookupRecord();
      $charset=$charset['Value'];
      $query="ALTER TABLE `$table` CONVERT TO CHARACTER SET $charset";
      $db->SQL($query);
      $db->Query();
    }
  }
  
  
  
  function writeRapport()
  {
    global $__appvar,$__ORDERvar;
  
    $inFondsCrmFields = array();
    $inFondsCrmFieldstest = '';
    foreach ( $this->toCheckFields as $fieldName => $fieldValue ) {
      $inFondsCrmFields[] = 'Ptf_' . $fieldName;
      $inFondsFondsFields[] =  'FondsenEMTdata.' . $fieldName;
      $inFondsCrmFieldstest .= 'CRM_naw.Ptf_' . $fieldName . ', ';
    }
    
    
    $begindatum = jul2sql($this->selectData['datumVan']);
    $einddatum = jul2sql($this->selectData['datumTm']);
    
    $this->pdf->__appvar = $__appvar;
    
    $selectie = new portefeuilleSelectie($this->selectData,$this->orderby);
    $records = $selectie->getRecords();
    $portefeuilles = $selectie->getSelectie();
    
    if($records <= 0)		{
      echo "<b>Fout: geen portefeuilles binnen selectie!</b>";
      $this->progressbar->hide();
      exit;
    }
    
    if($this->progressbar)
    {
      $this->progressbar->moveStep(0);
      $pro_step = 0;
      $pro_multiplier = 100 / $records;
    }
    
    // voor kopjes
    
    
    $this->pdf->AddPage();
    
    $portefeuillesKeys=array_keys($portefeuilles);
    
    $extraFilters=array(
      'Fonds'=>'FondsenKeyActiefVKM',
      'Rekeningmutaties.add_date'=>'datumDb'
    
    );
    $extraWhere='';
    foreach($extraFilters as $dbKey=>$filerKey)
    {
      if($this->selectData[$filerKey."Van"] <> '' && $this->selectData[$filerKey."Tm"])
      {
        $extraWhere .= "AND ( $dbKey >= '" . $this->selectData[$filerKey . "Van"] . "' ";
        $extraWhere .= "AND  $dbKey <= '" . $this->selectData[$filerKey . "Tm"] . "' ) \n ";
      }
    }
  
  
  
    $query = "
      SELECT
        Rekeningen.Portefeuille,
        SUM(Rekeningmutaties.aantal) as aantal,
        Rekeningmutaties.Fonds
        
        FROM
          Rekeningmutaties
        
          INNER JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
          WHERE Rekeningen.Portefeuille IN('".implode("','",$portefeuillesKeys)."')
    
          
          AND Grootboekrekening='FONDS'
          " . $extraWhere . "
          GROUP BY Rekeningen.Portefeuille, Rekeningmutaties.Fonds
    ";
    
    
    $this->db->SQL($query);
    $this->db->Query();
    $orderRegels=array();
    $headerRegel = array();
  
  
    $this->headerWidths[] = $this->fondsWidth;
    $this->headerAligns[] = 'L';
    $headerRegel[] = 'Fonds';
    $this->pdf->excelData[0][] = 'Fonds';
  
    $this->headerWidths[] = $this->portefeuilleWidth;
    $this->headerAligns[] = 'L';
    $headerRegel[] = 'Portefeuille';
    $this->pdf->excelData[0][] = 'Portefeuille';
  
    $this->pdf->excelData[0][] = 'Geen informatie';
    
    foreach ( $this->getGroups() as $key => $groupName ) {
      if ( $this->selectData['soort'] !== 'emtDetail' || empty ($this->showOnly) || isset ($this->showOnly[$key])) {
        $headerRegel[] = $groupName;
        $this->pdf->excelData[0][] = $this->toCheckGroups[$key];
        $this->headerWidths[] = '20';
        $this->headerAligns[] = 'L';
      }
    }
  
    
  
    $this->pdf->setWidths($this->headerWidths);
    $this->pdf->setAligns($this->headerAligns);
    
    $this->pdf->row($headerRegel);
  
  
  
    while ( $portefeuilleInFondsData = $this->db->nextRecord() ) {
      if ($portefeuilleInFondsData['aantal'] <> 0 ) {
        $fondsList[$portefeuilleInFondsData['Fonds']] = $portefeuilleInFondsData['Fonds'];
        $portefeuilleList[$portefeuilleInFondsData['Portefeuille']] = $portefeuilleInFondsData['Portefeuille'];
      
        $portefeuilleInFondsList[] = $portefeuilleInFondsData;
      }
    }
  
  
    $nawObj = new Naw();
    $testdatacrm = $nawObj->parseByArray('all', array (
      'fields' => array_merge( array(
                                 'id',
                                 'naam',
                                 'zoekveld',
                                 'portefeuille',
                               ), $inFondsCrmFields ),
      'conditions' => array (
        'portefeuille' => $portefeuilleList
      )
    ));
    $this->fillClientData($testdatacrm);
  
  
  
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
  
    $this->fillFondsData($testdata);
  
    foreach ( $portefeuilleInFondsList as $portefeuilleInFondsData ) {
      $regelData = array();
      if ( (int) $this->fondsChecks[$portefeuilleInFondsData['Fonds']]['fields']['VKM'] === 1 ) {

        $test = $this->doCheck($portefeuilleInFondsData['Portefeuille'], $portefeuilleInFondsData['Fonds']);
        $occurences = array_count_values($test);

        $regelData['fonds'] = $portefeuilleInFondsData['Fonds'];
        $regelData['Portefeuille'] = $portefeuilleInFondsData['Portefeuille'];
        
          if ( $_POST['emtValidatieFilter'] === 'afwijkingen' ) {
  
            if ( $this->fondsChecks[$portefeuilleInFondsData['Fonds']]['fields']['fondsinfoId'] == NULL ) {
              $regelData = $this->makeNoDataRow('fonds', $regelData);
            }
  
            elseif (
              ! isset($this->clientChecks[$portefeuilleInFondsData['Portefeuille']]) ||
              $this->clientChecks[$portefeuilleInFondsData['Portefeuille']]['fields']['id'] == NULL
            ) {
              $regelData=$this->makeNoDataRow('client', $regelData);
            }
  
            else {
              if ( ! isset ($occurences[1]) || (int) $occurences[1] <  (int) count($test) ) {
                $regelData = $this->makeRow($test, $regelData);
              } else {
                $regelData = array();
              }
            }
          }
  
        
          elseif ( $_POST['emtValidatieFilter'] === 'nietcontroleren' ) {
  
            if ( $this->fondsChecks[$portefeuilleInFondsData['Fonds']]['fields']['fondsinfoId'] == NULL ) {
              $regelData=$this->makeNoDataRow('fonds', $regelData);
            }
  
            elseif (
              ! isset($this->clientChecks[$portefeuilleInFondsData['Portefeuille']]) ||
              $this->clientChecks[$portefeuilleInFondsData['Portefeuille']]['fields']['id'] == NULL
            ) {
              $regelData=$this->makeNoDataRow('client', $regelData);
            }
  
            else {
              if (  isset ($occurences[3]) || (int) $occurences[3] > 0 ) {
                $regelData = $this->makeRow($test, $regelData);
              }
            }
          
          }
          
          elseif ( $_POST['emtValidatieFilter'] === 'nietakkoord' ) {
  
            $occurences = array_count_values($test);

            if ( $this->fondsChecks[$portefeuilleInFondsData['Fonds']]['fields']['fondsinfoId'] == NULL ) {
//              $regelData = $this->makeNoDataRow('fonds', $regelData);
            }
            elseif (
              ! isset($this->clientChecks[$portefeuilleInFondsData['Portefeuille']]) ||
              $this->clientChecks[$portefeuilleInFondsData['Portefeuille']]['fields']['id'] == NULL
            ) {
//              $regelData = $this->makeNoDataRow('client', $regelData);
            }
            elseif ( isset ($occurences[2]) && $occurences[2] > 0 ) {
              $regelData = $this->makeRow($test, $regelData);
            }
          }
          
          elseif ( $_POST['emtValidatieFilter'] === 'all' ) {
  
            if ( $this->fondsChecks[$portefeuilleInFondsData['Fonds']]['fields']['fondsinfoId'] == NULL ) {
              $regelData = $this->makeNoDataRow('fonds', $regelData);
            }
            
            elseif (
              ! isset($this->clientChecks[$portefeuilleInFondsData['Portefeuille']]) ||
              $this->clientChecks[$portefeuilleInFondsData['Portefeuille']]['fields']['id'] == NULL
            ) {
              $regelData = $this->makeNoDataRow('client', $regelData);
            }
            
            else {
              $regelData = $this->makeRow($test, $regelData);
            }
          }
  
  
        $this->pdf->excelData[] = array_values($regelData);
        $this->dbWaarden[] = $regelData;
      }
      
      
    }
  
    if($this->progressbar)
      $this->progressbar->hide();
  }
  
  
  /**
   * Wanneer er geen fonds gegevens beschikbaar zijn.
   * @return array
   */
  function makeNoDataRow ($type = '',$regelData) {
    
    if ( ! empty ($type) && ($type === 'fonds' || $type === 'client') ) {
      
      $this->pdf->setWidths(array($this->fondsWidth, $this->portefeuilleWidth, '200'));
      $this->pdf->setAligns(array('L','L','L'));
      if ( $type === 'fonds' ) {
        $regelData['noFonds'] = 'Geen fondsinformatie aanwezig';
      } else {
        $regelData['noClient'] = 'Geen clientinformatie aanwezig';
      }
  
      $this->pdf->row(array_values($regelData));
      return $regelData;
    }
    
    return '';
  }
  
  /**
   * Create an row
   * @param $test test array
   * @param $regelData previous column data
   * @return array
   */
  function makeRow($test, $regelData) {
    $this->pdf->setWidths($this->headerWidths);
    $this->pdf->setAligns($this->headerAligns);

    $this->pdf->row(array_values($regelData));
    $regelData['nofonds'] = '';
    $indentIndex = 1;
    foreach ($test as $key => $value) {
      if ( $this->selectData['soort'] !== 'emtDetail' || empty ($this->showOnly) || isset ($this->showOnly[$key])) {
        $pos = (81 + ($indentIndex * 20));
        $indentIndex++;
        if ($value === 1) {
          $regelData[$this->toCheckGroupsDB[$key]] = 'Akkoord';
          $this->pdf->MemImage($this->checkImg, $pos, $this->pdf->getY() - 3, 3, 3);
        }
        elseif ($value === 2) {
          $this->pdf->MemImage($this->deleteImg, $pos, $this->pdf->getY() - 3, 3, 3);
          $regelData[$this->toCheckGroupsDB[$key]] = 'Niet akkoord';
        }
        elseif ($value === 3) {
          $this->pdf->MemImage($this->questionImg, $pos, $this->pdf->getY() - 3, 3, 3);
          $regelData[$this->toCheckGroupsDB[$key]] = 'Niet te controleren';
        }
      }
    }
    
    return $regelData;
  }
  
  /**
   * 1 = Akkoord
   * 2 = Niet akkoord
   * 3 = Niet te controleren
   */




function OutputDatabase()
{
  global $USR;
  $db=new DB();
  $table="reportbuilder_$USR";
  $query="SHOW TABLES like '$table'";
  
  if($db->QRecords($query) > 0) {
    $db->SQL("DROP table $table");
    $db->Query();
  }
  $this->createTempTable ($USR, $table);
  
//  $this->dbWaarden = $this->pdf->excelData;
  

  if(is_array($this->dbWaarden))
  {
    foreach ($this->dbWaarden as $rege=>$waarden)
    {
      $query="INSERT INTO $table SET add_date=now() ";
      foreach ($waarden as $key=>$value)
      {
        $query.=", `$key` = '".addslashes($value)."' ";
      }
  
      $db->SQL($query);
      $db->Query();
    }
  }


}
  
  
  
  
  
  
  
  
  
  
  
  function AIRS_rapportEmt () {
    // array voor do check opbouwen zodat we hier doorheen kunnen lopen
    foreach ( $this->toCheckFields as $field => $data ) {
      $this->toCheckGroupFields[$data['checkGroup']][$data['check']]['fields'][] = $field;
      $this->toCheckGroupFields[$data['checkGroup']][$data['check']]['check'] = $data['check'];
    }
  }
  
  
  function getGroups () {
    return $this->toCheckGroupsShort;
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

      if (
        $this->selectData['soort'] !== 'emtDetail'
        || ( $this->selectData['soort'] === 'emtDetail' && $_POST['emtValidatieFilter'] === 'all' )
        || ($_POST['emtValidatieFilter'] === 'afwijkingen' && $this->selectData['soort'] === 'emtDetail' && isset ($this->showOnly[$group]) )
        || ( $this->selectData['soort'] === 'emtDetail' && isset ($this->showOnly[$group]) )
      ) {
        foreach ( $checkData as $data ) {
          $callCheck = 'check'.$data['check'];

          if (method_exists($this,$callCheck)) {

            $validated[$group] = $this->$callCheck ($data['fields'], $group);
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

   // Oude code mag later weg

//    /**
//     * Retail mag maar een keer voorkomen bij Client en geen Professional bevat
//     * En Client Ja moet gelijk zijn aan Fonds ja of Both
//     */
//    if (
//      ( isset ($clientCount['Retail']) && (int) $clientCount['Retail'] === 1 ) &&
//      ! isset($clientCount['Professional'])
//    ) {
//
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
//      ( isset ($clientCount['Professional']) && (int) $clientCount['Professional'] === 1 ) &&
//      ! isset($clientCount['Retail'])
//    ) {
//
//      foreach ( $clientData as $field => $check ) {
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
?>