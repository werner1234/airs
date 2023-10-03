<?php
 foreach ( $this->AEParticipant->getParticipant() as $client ) {
    $rows[$client['crm_id']]['CRM'] = $this->crmNaw->parseById($client['crm_id']);
    $rows[$client['crm_id']]['participate'] = $this->AEParticipant->getParticipant($client['crm_id']);
    $rows[$client['crm_id']]['rows'] = $this->AEParticipant->allFondsOneClient($client['crm_id'], $data['DateStart'], $data['DateEnd']);
  }
  $this->__makePdfBulk($rows, 'naam',$data);