<?php

class AE_ShortPositions extends AE_AjaxHelper {
  
  function AE_ShortPositions()
  {
    parent::AE_AjaxHelper(array_merge($_POST, $_GET));
  }
  
  
  function getShortPositionAjax () {
    $this->itemsExist(array('form.Fonds', 'form.Transactietype', 'form.Aantal'));
  }
  
  
  function getParticipantShortPositionAjax () {
    $this->itemsExist(array('postData.datum', 'postData.aantal', 'postData.transactietype', 'postData.crm_id', 'postData.participanten_id'));
    
    if ( empty ($this->data['postData']['datum']) ) {$this->data['postData']['datum'] = date('Y-m-d');}
    
    $AEParticipants = new AE_Participants();
    $inPossession = $AEParticipants->oneRegistrationOneClientStartPosition($this->data['postData']['crm_id'], $this->data['postData']['participanten_id'], $this->data['postData']['datum'], null);
    
    if ( $inPossession['aantal'] == NULL) {$inPossession['aantal'] = 0;}
    $this->returnJson($inPossession);
  }
  
}