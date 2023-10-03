<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2011/12/04 12:53:49 $
 		File Versie					: $Revision: 1.3 $

 		$Log: AE_tableSync.php,v $
 		Revision 1.3  2011/12/04 12:53:49  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2011/07/16 13:30:37  rvv
 		*** empty log message ***

 		Revision 1.1  2008/12/30 15:30:20  rvv
 		*** empty log message ***


*/
class AE_tableSync
{

  function AE_tableSync($table)
  {
    $this->sDB = new DB(2);
    $this->dDB = new DB();
    $this->table = $table;
  }


  function copyRecords()
  {
    $query = "SHOW fields FROM ".$this->table."";
    $this->dDB->SQL($query);
    $this->dDB->Query();
    while($data = $this->dDB->nextRecord())
    {
      $dVelden[] = $data['Field'];
    }

    $query = "SELECT * FROM $this->table ";
    $this->sDB->SQL($query);
    $this->sDB->Query();
    while($data = $this->sDB->nextRecord())
    {
      $query = "INSERT INTO $this->table SET ";
      foreach ($dVelden as $n=>$veld)
      {
        if($n > 0)
          $query .= ", ";
        $query .= " $veld = '".addslashes($data[$veld])."' " ;
      }
      $this->dDB->SQL($query);
      if(!$this->dDB->Query())
      {
        $this->errors .= "Kopiereren van Record mislukt.<br>\n";
        $recordControleren[]=$data['id'];
      }
      else
      {
        $recordsOK[] = $data['id'];
      }
    }

    foreach ($recordControleren as $id)
    {
      $query = "SELECT id FROM ".$this->table." WHERE id = '$id'";
      if($this->dDB->QRecords($query) > 0) //record bestaat al in doeltabel. Ook in brontabel opruimen.
       $recordsOK[] = $id;
    }

    $this->recordNr = count($recordsOK);

    if($this->recordNr > 0)
    {
      $in = implode("','",$recordsOK);
      $query = "DELETE FROM ".$this->table." WHERE id in('$in') ";
      $this->sDB->SQL($query);
      if(!$this->sDB->Query())
        echo "$query miskukt.";
    }
  }

  function copyRecordsNoId()
  {
    $query = "SHOW fields FROM ".$this->table."";
    $this->dDB->SQL($query);
    $this->dDB->Query();
    while($data = $this->dDB->nextRecord())
    {
      if($data['Field'] <> 'id')
        $dVelden[] = $data['Field'];
    }

    $query = "SELECT * FROM $this->table ";
    $this->sDB->SQL($query);
    $this->sDB->Query();
    while($data = $this->sDB->nextRecord())
    {
      $where='';
      $query = "INSERT INTO $this->table SET ";
      foreach ($dVelden as $n=>$veld)
      {
        if($n > 0)
          $query .= ", ";
        $query .= " $veld = '".addslashes($data[$veld])."' " ;
        $where .= " AND $veld = '".addslashes($data[$veld])."' " ;
      }


      if($this->dDB->QRecords("SELECT id FROM ".$this->table." WHERE 1 $where") == 0)
      {
        $this->dDB->SQL($query);
        if(!$this->dDB->Query())
        {
          $this->errors .= "Kopiereren van Record mislukt.<br>\n";
        }
        else
        {
          $recordsOK[] = $data['id'];
        }
      }
      else
      {
        $this->errors .= "Record (met $where) al aanwezig.<br>\n";
        $recordsOK[] = $data['id'];
      }
    }
    $this->recordNr = count($recordsOK);
    if($this->recordNr > 0)
    {
      $in = implode("','",$recordsOK);
      $query = "DELETE FROM ".$this->table." WHERE id in('$in') ";
      $this->sDB->SQL($query);
      if(!$this->sDB->Query())
        echo "$query miskukt.";
    }
  }


  function getRecordNr()
  {
    return $this->recordNr;
  }

  function getErrors()
  {
    return $this->errors ;
  }

  function emailErrors()
  {
    if($this->errors <> '')
    {
    $cfg=new AE_config();
    $mailserver=$cfg->getData('smtpServer');
    $fondsEmail=$cfg->getData('fondsEmail');
    if($fondsEmail !="" && $mailserver !='')
    {
      include_once('../classes/AE_cls_phpmailer.php');
      $mail = new PHPMailer();
      $mail->IsSMTP();
      $mail->From     = $fondsEmail;
      $mail->FromName = "Airs";
      $mail->Body    = str_replace("\n","<br>\n",$this->errors)."<br>\nVerzonden om: ".date("d-m-Y H:i")."";
      $mail->AltBody = $this->errors;
      $mail->AddAddress($fondsEmail,$fondsEmail);
      $mail->Subject = "Database sync error";
      $mail->Host=$mailserver;
      if(!$mail->Send())
      {
        echo "Verzenden van e-mail mislukt.";
      }
    }
    }
  }


}

?>