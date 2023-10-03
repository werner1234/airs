<?php

class AE_Portal {
  var $messages = null;
  var $error = false;
  

  function AE_Portal() 
  {
    $this->AE_Validate = new AE_Validate();
    $this->AE_Message = new AE_Message();
    $this->AE_Sanitize = new AE_Sanitize();
    $this->AE_Date = new AE_datum();
  }
  
  function validateCrmDataForPortal ($crmData) {
    $error = false;
    if ( ! $this->AE_Validate->isValidEmail($crmData['email']) ) {
      $this->AE_Message->setMessage(date("d-m-Y H:i:s") . ' - Klaarzetten rapportage voor ' . $crmData['email'] . ' ('. $crmData['zoekveld'] .') mislukt. Geen geldig emailadres ingesteld.', 'info');
      $error = true;
    }

    if ( empty ($crmData['wachtwoord']) ) {
      $this->AE_Message->setMessage(date("d-m-Y H:i:s") . ' - Klaarzetten rapportage voor ' . $crmData['email'] . ' mislukt. Geen geldig wachtwoord ingesteld.', 'info');
      $error = true;
    }
    $this->messages .= $this->AE_Message->getMessage();
    return $error;
  }
  
  
  function addToPortalqueue($crmData, $data, $pdfFile, $fileName)
  {
    $USR = $_SESSION['usersession']['user'];
    $afbreken = false;

    if ( ! $this->AE_Validate->isValidEmail($crmData['email']) ) {
      $this->AE_Message->setMessage(date("d-m-Y H:i:s") . ' - Klaarzetten rapportage voor ' . $crmData['email'] . ' ('. $crmData['zoekveld'] .') mislukt. Geen geldig emailadres ingesteld.', 'info');
      $this->error = true;
    }

    if ( empty ($crmData['wachtwoord']) ) {
      $this->AE_Message->setMessage(date("d-m-Y H:i:s") . ' - Klaarzetten rapportage voor ' . $crmData['email'] . ' mislukt. Geen geldig wachtwoord ingesteld.', 'info');
      $this->error = true;
    }
    
    if( $this->error === false )
    {
      $db=new DB();
      $fields = array(
        'crmId'             => $crmData['id'],
        'status'            => 'aangemaakt',
        'naam'              => $crmData['naam'],
        'naam1'             => $crmData['naam1'],
        'email'             => $crmData['email'],
        'portefeuille'      => $data['portefeuille'],
        'periode'           => $data['periode'],
        'raportageDatum'    => $this->AE_Date->formToDb($data['raportageDatum']),
        'crmWachtwoord'     => $crmData['wachtwoord']
      );

      /** build query **/
      $query = 'INSERT INTO portaalQueue 
        SET filename = "' . $fileName . '.pdf", 
        pdfData=unhex("' . bin2hex($pdfFile) . '"), 
        add_date=now(),
        add_user="' . $USR . '",
        change_date=now(),
        change_user="' . $USR . '",
      ';
      /** set $fields to `field` = "value", **/
      array_walk($fields, array($this, 'formatKeyValue'));
      $query .= implode(", \n", $fields);

      /** save query **/
      $db->SQL($query);
      if($db->Query()) {
        $this->AE_Message->setMessage(date("d-m-Y H:i:s") . ' - Rapportage voor ' . $crmData['email'] . ' ('. $crmData['zoekveld'] .') in de portaal wachtrij geplaatst.', 'success');
      }
    }
    $this->messages .= $this->AE_Message->getMessage();
  }
  
  
  function formatKeyValue (&$value, $key) { $value = sprintf( "`%s` = '%s'" , $key , $this->AE_Sanitize->escape($value) ); }
  
}