<?php
class updateHistorySync
{
  function updateHistorySync()
  {
    $this->db = new DB();
    $this->counter=0;
    $this->log = array();
  }
  
  function syncRecords()
  {
    $query = "SELECT * FROM updates";
    $DB_queue = new DB(2);
    $DB_queue->SQL($query);
    $DB_queue->Query();
    if($DB_queue->records() > 0)
    {
    	while($queue = $DB_queue->nextRecord())
    	{
    		$DB = new DB();
        $select = "SELECT exportId FROM UpdateHistory WHERE exportId = '".$queue['exportId']."' AND Bedrijf = '".$queue['Bedrijf']."' ";
		    $DB->SQL($select);
		    $DB->Query();
		    $records = $DB->records();
		    if($records >0)
			    $query = "UPDATE UpdateHistory SET ";
		    else
			    $query = "INSERT INTO UpdateHistory SET ";

    		$query .= "  Bedrijf = '".$queue['Bedrijf']."' ";
    		$query .= ", exportId = '".$queue['exportId']."' ";
	    	$query .= ", type = '".$queue['type']."' ";
		    $query .= ", filename = '".$queue['filename']."' ";
    		$query .= ", filesize = '".$queue['filesize']."' ";
	    	$query .= ", server = '".$queue['server']."' ";
    		$query .= ", username = '".$queue['username']."' ";
    		$query .= ", password = '".$queue['password']."' ";
    		$query .= ", complete = '".$queue['complete']."' ";
    		$query .= ", terugmelding = '".mysql_escape_string($queue['terugmelding'])."' ";
    		$query .= ", tableDef = '".mysql_escape_string($queue['tableDef'])."' ";
    		$query .= ", add_user = '".$queue['add_user']."' ";
    		$query .= ", add_date = '".$queue['add_date']."' ";
    		$query .= ", change_user = '".$queue['change_user']."' ";
    		$query .= ", change_date = '".$queue['change_date']."' ";
    
        $msg='';
        $lines=explode("\n",$queue['terugmelding']);
        foreach($lines as $line)
        {
          $begin=substr($line,20,6);
          if($begin=='Tabel ' || $begin=='Fonds ')
          {
            $msg.=$line."<br>\n";
          }
        }
        if($msg <> '')
        {
          $cfg=new AE_config();
          $mailserver=$cfg->getData('smtpServer');
          $emailAddesses=explode(";",$cfg->getData('fondsEmail'));
          if($mailserver !='')
          {
            $html='<table>
            <tr><td><b>Veld</b></td><td><b>Waarde</b></td></tr>
            <tr><td>Bedrijf</td><td>'.$queue['Bedrijf'].'</td></tr>
            <tr><td>exportId</td><td>'.$queue['exportId'].'</td></tr>
            <tr><td>type</td><td>'.$queue['type'].'</td></tr>
            <tr><td>filename</td><td>'.$queue['filename'].'</td></tr>
            <tr><td>Verschillen</td><td>'.$msg.'</td></tr>
            </table>';
            include_once('../classes/AE_cls_phpmailer.php');
            $mail = new PHPMailer();
            $mail->IsSMTP();
            $mail->From     = 'info@airs.nl';
            $mail->FromName = "Airs";
            $mail->Body    = $html;
            $mail->AltBody = html_entity_decode(strip_tags($html));
            foreach ($emailAddesses as $id=>$emailadres)
             $mail->AddAddress($emailadres);
            $mail->Subject = "AIRS update database verschillen ".$queue['Bedrijf'].".";
            $mail->Host=$mailserver;
            storeControleMail('updateVerschil',"AIRS update database verschillen ".$queue['Bedrijf'].".",$html);
            if(!$mail->Send())
              $this->log[]="Verzenden van e-mail ".$queue['Bedrijf']." mislukt.";
            else
              $this->log[]="Verschillenmail ".$queue['Bedrijf']." verzonden.";
          }
        }
    

	    	if($records >0)
    			$query .= " WHERE exportId = '".$queue['exportId']."' AND Bedrijf = '".$queue['Bedrijf']."' ";

    		$DB->SQL($query);
    		if($DB->Query())
    		{
	    		if($queue['complete'] == 1)
		    	{
				    // remove from queue
			    	$DB_queue2 = new DB(2);
				    $query = "DELETE FROM updates WHERE exportId = '".$queue['exportId']."' AND Bedrijf = '".$queue['Bedrijf']."' ";
				    $DB_queue2->SQL($query);
				    $DB_queue2->Query();
		    	}

			    $this->counter++;
		    }
      }
    	$this->log[]=$this->counter." records overgehaald.<br>";
    }
    else
    {
	    $this->log[]="Geen nieuwe data in queue log.";
    }
  }
  
  function checkQueue()
  {
    $query = "SELECT id,complete,add_date,change_date,Bedrijf,filename FROM updates ORDER BY add_date";
    $DB_queue = new DB(2);
    $DB_queue->SQL($query);
    $DB_queue->Query();
    $msh='';
    if($DB_queue->records() > 0)
    {
    	while($data = $DB_queue->nextRecord())
    	{
    	  if($data['complete'] <> 1 && (time()-db2jul($data['c'])) > 2700)//45 min
        {
          $msg.="Update ".$data['filename']." voor ".$data['Bedrijf']." staat vanaf ".$data['add_date']." in de queue. Status (".$data['complete'].").<br>\n";
        }
      }
    }
    if($msg <> '')
    {
      $cfg=new AE_config();
      $mailserver=$cfg->getData('smtpServer');
      $emailAddesses=explode(";",$cfg->getData('fondsEmail'));
      //$emailAddesses=array('rvv@aeict.nl');
      if($mailserver !='')
      {
        $html=$msg;
        include_once('../classes/AE_cls_phpmailer.php');
        $mail = new PHPMailer();
        $mail->IsSMTP();
        $mail->From     = 'info@airs.nl';
        $mail->FromName = "Airs";
        $mail->Body    = $html;
        $mail->AltBody = html_entity_decode(strip_tags($html));
        foreach ($emailAddesses as $id=>$emailadres)
          $mail->AddAddress($emailadres);
        $mail->Subject = "AIRS updates in queue om ".date('d-m-Y H:i');
        $mail->Host=$mailserver;
        if(!$mail->Send())
           $this->log[]="Verzenden van checkQueue e-mail mislukt.";
        else
          $this->log[]="CheckQueue e-mail verzonden.";
       }
     }
    
    //echo $msg;
  }   
     
  function savelog()
  {
    print_r($this->log);
  }
}
?>