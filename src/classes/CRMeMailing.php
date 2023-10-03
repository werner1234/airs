<?php
/*
    AE-ICT source module
    Author  						: $Author: rm $
 		Laatste aanpassing	: $Date: 2020/05/08 14:45:40 $
 		File Versie					: $Revision: 1.32 $

 		$Log: CRMeMailing.php,v $
 		Revision 1.32  2020/05/08 14:45:40  rm
 		8541 emailings eMail opmaak: via nieuwe templates
 		
 		Revision 1.31  2018/12/21 17:46:29  rvv
 		*** empty log message ***
 		
 		Revision 1.30  2018/12/12 16:15:37  rvv
 		*** empty log message ***
 		
 		Revision 1.29  2018/12/01 19:47:09  rvv
 		*** empty log message ***
 		
 		Revision 1.28  2017/12/03 10:31:39  rvv
 		*** empty log message ***
 		
 		Revision 1.27  2017/10/04 16:06:28  rvv
 		*** empty log message ***
 		
 		Revision 1.26  2016/08/17 15:56:02  rvv
 		*** empty log message ***
 		
 		Revision 1.25  2016/04/16 17:09:33  rvv
 		*** empty log message ***
 		
 		Revision 1.24  2016/01/30 16:19:39  rvv
 		*** empty log message ***
 		
 		Revision 1.23  2015/04/22 15:27:39  rvv
 		*** empty log message ***
 		
 		Revision 1.22  2015/04/19 08:40:22  rvv
 		*** empty log message ***
 		
 		Revision 1.21  2014/10/02 10:45:13  rvv
 		*** empty log message ***
 		
 		Revision 1.20  2014/10/02 05:48:38  rvv
 		*** empty log message ***
 		
 		Revision 1.19  2014/10/01 16:03:39  rvv
 		*** empty log message ***
 		
 		Revision 1.18  2014/07/09 16:11:12  rvv
 		*** empty log message ***
 		
 		Revision 1.17  2014/07/02 16:01:36  rvv
 		*** empty log message ***
 		
 		Revision 1.16  2014/03/22 15:50:01  rvv
 		*** empty log message ***
 		
 		Revision 1.15  2014/02/05 15:54:21  rvv
 		*** empty log message ***
 		
 		Revision 1.14  2013/10/12 15:48:00  rvv
 		*** empty log message ***
 		
 		Revision 1.13  2013/08/24 15:45:43  rvv
 		*** empty log message ***
 		
 		Revision 1.12  2013/05/19 10:57:43  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2012/11/17 15:59:38  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2012/10/31 16:56:33  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2012/10/21 10:01:01  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2012/09/19 16:51:59  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2012/09/09 17:33:15  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2012/06/03 09:41:10  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2011/10/23 13:18:43  rvv
 		*** empty log message ***

 		Revision 1.4  2011/06/25 14:23:50  rvv
 		*** empty log message ***

 		Revision 1.3  2011/06/25 14:13:00  rvv
 		*** empty log message ***

 		Revision 1.2  2011/06/13 14:33:38  rvv
 		*** empty log message ***

 		Revision 1.1  2011/06/02 14:59:15  rvv
 		*** empty log message ***


*/

class CRMeMailing
{
	function CRMeMailing()
	{
	  global $__appvar,$USR;
	}

	function prepareMails($xlsData,$extra)
	{
	  global $__appvar,$USR;
	  $this->_POST=$extra;
    $db=new DB();

	  $idsToUse=array();
    $this->templateData=array();
	  foreach ($extra as $key=>$value)
	  {
	    if(substr($key,0,6)=='check_')
	    {
	      $idsToUse[]=$value;
	    }
	  }

	

	  foreach ($xlsData as $relatie=>$row)
	  {
	    $selectie=array();
      $tmp=array();
     

	    foreach ($row as $colId=>$colData)
	    {
	      switch ($colData[1])
	      {
	        case "header":
	          if(!isset($header[$colId]))
	            $header[$colId]=$colData[0];
 	        break;
	        case "body":
            $tmp[$header[$colId]]=$colData[0];
	        break;
	      }
        
 	    }
      if($tmp['id'] > 0)
      {
        $query="SELECT CRM_naw.id as crm_id, CRM_naw.* ,Portefeuilles.* FROM CRM_naw LEFT JOIN Portefeuilles ON CRM_naw.portefeuille=Portefeuilles.Portefeuille WHERE CRM_naw.id='".$tmp['id']."'";
        $db->SQL($query); 
        $tmp=$db->lookupRecord();
        $tmp['id']=$tmp['crm_id'];
      }
      $selectie=unserialize($tmp['rapportageVinkSelectie']);
   
            
      if(count($tmp) > 0)
      {
        if($extra['rapporteFilter']==1)
        {
          $toevoegen=false;
          if($extra['rapporteEmail']==1 && $selectie['verzending']['rap_k']['email']==1)
            $toevoegen=true;
          if($extra['rapportePapier']==1 && $selectie['verzending']['rap_k']['papier']==1)
            $toevoegen=true;
          if(($extra['rapportePapier']==0 && $selectie['verzending']['rap_k']['papier']==0)&&($extra['rapporteEmail']==0 && $selectie['verzending']['rap_k']['email']==0))
            $toevoegen=true;  
          if($toevoegen==true)
					{
						$this->templateData[$relatie] = $tmp;
						if($extra['extraeAdressen']==1)
						{
							$extraAddressen=$this->AddExtraAdres($tmp);
							if(is_array($extraAddressen))
							{
								foreach($extraAddressen as $index=>$adres)
								{
									$newId=$relatie . "." . $index;
									$this->templateData[$newId] = $adres;
								}
							}
						}
					}
        }
        else
				{
					$this->templateData[$relatie] = $tmp;
					if($extra['extraeAdressen']==1)
					{
						$extraAddressen=$this->AddExtraAdres($tmp);
						if(is_array($extraAddressen))
						{
							foreach($extraAddressen as $index=>$adres)
							{
								$newId=$relatie . "." . $index;
								$this->templateData[$newId] = $adres;
							}
						}
					}
				}
      }
	  }

 	  $cfg=new AE_config();
    
	  if(!$this->newBody)
      if($this->_POST['body'] && $this->_POST['gebruikHandtekening']=="1")
        $this->newBody=$this->_POST['body'];
	    elseif($this->_POST['mailing'] && $this->_POST['gebruikHandtekening']=="0")
        $this->newBody=$cfg->getData($this->_POST['mailing']);
	    else
	      $this->newBody=$cfg->getData('mailingBody');


	  if(count($idsToUse) > 0)
	  {
	    $newTemplateData=array();
	    foreach ($this->templateData as $relatieData)
	    {
	      if(in_array($relatieData['id'],$idsToUse))
	      {
	        $newTemplateData[]=$relatieData;
	      }
	    }
	    $this->templateData=$newTemplateData;
	  }

    $customTemplate = new AE_CustomTemplate();
    $templateParser = new AE_cls_TemplateParser();

    $db=new DB();
	  foreach ($this->templateData as $relatieData)
	  {
	    if($relatieData['email'] <> '')
	    {
        $body=$this->newBody;
 	      $relatieData=$this->getAllFields($relatieData);

        $templateParser->setData($relatieData);
	      foreach ($relatieData as $key=>$val)
          $body = str_replace("{".$key."}", $val, $body);

        $body = $templateParser->ParseData($body);

	      $senderName = $_SESSION['usersession']['gebruiker']['Naam'];
	      if ( isset ($this->_POST['senderName']) && ! empty ($this->_POST['senderName']) ) {
          $senderName = $this->_POST['senderName'];
        }
        
	      $senderEmail = $_SESSION['usersession']['gebruiker']['emailAdres'];
        if ( isset ($this->_POST['senderEmail']) && ! empty ($this->_POST['senderEmail']) ) {
          $senderEmail = $this->_POST['senderEmail'];
        }
	      

        $fields=array('crmId'=>$relatieData['id'],
            'status'=>'aangemaakt',
            'senderName'=>mysql_escape_string($senderName),
            'senderEmail'=>mysql_escape_string($senderEmail),
            'receiverName'=>mysql_escape_string($relatieData['naam']),
            'receiverEmail'=>mysql_escape_string($relatieData['email']),
            'subject'=>mysql_escape_string($this->_POST['onderwerp']),
            'ccEmail'=>mysql_escape_string($this->_POST['ccEmail']),
            'bccEmail'=>mysql_escape_string($this->_POST['bccEmail']),
            'bodyHtml'=>mysql_escape_string($body));
        $query="INSERT INTO emailQueue SET add_date=now(),add_user='$USR',change_date=now(),change_user='$USR'";
        foreach ($fields as $key=>$value)
          $query.=",$key='$value'";

	      $db->SQL($query);
	      $db->Query();
        
     	  if($extra['estoreDD']) //Losse RTF bestanden opslaan.
	      {
	        include_once("../classes/AE_cls_digidoc.php");
          $dd = new digidoc();
          if($relatieData['portefeuille'])
    	     $filename=$relatieData['portefeuille'].date("_Y-m-d_H:i").".html";
  	      elseif($relatieData['id'])
  	       $filename="id".$relatieData['id'].date("_Y-m-d_H:i").".html";
        
          $rec ["filename"] = $filename;
          $rec ["filesize"] = strlen($body);
          $rec ["filetype"] = "text/html";
          $rec ["description"] = rtrim("eMailing ".$extra['eDDnaam'])." aangemaakt op ".date("d-m-Y H:i");
          $rec ["blobdata"] = $body;
          $rec ["keywords"] = rtrim("emailing ".$extra['eDDnaam']);
          $rec ["module"] = "CRM_naw";
          $rec ["module_id"] = $relatieData['id'];
          $dd->useZlib = true;
          $dd->addDocumentToStore($rec);
	      }
	    }
	  }

	  if($extra['evenement'])
	  {
	    foreach ($this->templateData as $relatieData)
	    {
	      $query="SELECT id FROM CRM_evenementen WHERE rel_id='".$relatieData['id']."' AND evenement='".$extra['evenement']."'";
	      $db->SQL($query);
	      $evenementData=$db->lookupRecord($query);
	      if($evenementData['id'] > 0)
	        $query="UPDATE CRM_evenementen SET change_date=NOW(),change_user='$USR' WHERE id='".$evenementData['id']."'";
	      else
	        $query="INSERT INTO CRM_evenementen SET rel_id='".$relatieData['id']."' , evenement='".$extra['evenement']."', add_date=NOW(), add_user='$USR', change_date=NOW(),change_user='$USR'";
				$db->SQL($query);
				$db->Query();
	    }
	  }

	  //include('emailqueueList.php');
    header("Location: emailqueueList.php");
	  exit;
	}

	function AddExtraAdres($data)
	{
		$db=new DB();
		$query="SELECT CRM_naw_adressen.* FROM CRM_naw_adressen WHERE (CRM_naw_adressen.rapportage=1 or CRM_naw_adressen.evenement='rapportage') AND CRM_naw_adressen.rel_id='".$data['crm_id']."'";
		$db->SQL($query);
		$db->Query();
		if($db->records())
		{
			$adresData=array();
			while($extra=$db->nextRecord())
			{

				$extra['id']=$data['id'];
				$adresData[]=array_merge($data,$extra);
			}
			return $adresData;
		}
		else
			return 0;
		$tmp=$db->lookupRecord();
		$tmp['id']=$tmp['crm_id'];
	}

	function getAllFields($keyValue)
	{
	  $db=new DB();
	  $data=array();
	  global $__appvar,$USR;
	  if($keyValue['Vermogensbeheerder'])
	  {
	    $query="SELECT Naam as `*Vermogensbeheerder` FROM Vermogensbeheerders WHERE Vermogensbeheerder='".$keyValue['Vermogensbeheerder']."'";
	    $db->SQL($query);
	    $data=$db->lookupRecord();
	    $keyValue['*Vermogensbeheerder']=$data['*Vermogensbeheerder'];
	  }
	  if($keyValue['Client'])
	  {
	    $query="SELECT Naam as `*Client` FROM Clienten WHERE Client='".$keyValue['Client']."'";
	    $db->SQL($query);
	    $data=$db->lookupRecord();
	    $keyValue['*Client']=$data['*Client'];
	  }
	  if($keyValue['Depotbank'])
	  {
	    $query="SELECT Omschrijving as `*Depotbank` FROM Depotbanken WHERE Depotbank='".$keyValue['Depotbank']."'";
	    $db->SQL($query);
	    $data=$db->lookupRecord();
	    $keyValue['*Depotbank']=$data['*Depotbank'];
	  }
	  if($keyValue['custodian'])
	  {
	    $query="SELECT Omschrijving as `*custodian` FROM Depotbanken WHERE Depotbank='".$keyValue['custodian']."'";
	    $db->SQL($query);
	    $data=$db->lookupRecord();
      $keyValue['*custodian']=$data['*custodian'];
	  }    
	  if($keyValue['Accountmanager'])
	  {
	    $query="SELECT Naam as `*Accountmanager`, Titel as AccountmanagerTitel, Titel2 as AccountmanagerTitel2 FROM Accountmanagers WHERE Accountmanager='".$keyValue['Accountmanager']."'";
	    $db->SQL($query);
	    $data=$db->lookupRecord();
	    $keyValue['*Accountmanager']=$data['*Accountmanager'];
      $keyValue['AccountmanagerTitel']=$data['AccountmanagerTitel'];
      $keyValue['AccountmanagerTitel2']=$data['AccountmanagerTitel2'];
	  }
	  if($keyValue['tweedeAanspreekpunt'])
	  {
	    $query="SELECT Naam as `*tweedeAanspreekpunt` FROM Accountmanagers WHERE Accountmanager='".$keyValue['tweedeAanspreekpunt']."'";
	    $db->SQL($query);
	    $data=$db->lookupRecord();
	    $keyValue['*tweedeAanspreekpunt']=$data['*tweedeAanspreekpunt'];
	  }
	  if($keyValue['Remisier'])
	  {
	    $query="SELECT Naam as `*Remisier` FROM Remisiers WHERE Remisier='".$keyValue['Remisier']."'";
	    $db->SQL($query);
	    $data=$db->lookupRecord();
	    $keyValue['*Remisier']=$data['*Remisier'];
	  }
 	  if($keyValue['accountEigenaar'])
	  {
	    $query="SELECT Naam as `*accountEigenaar` FROM Gebruikers WHERE Gebruiker='".$keyValue['accountEigenaar']."'";
	    $db->SQL($query);
	    $data=$db->lookupRecord();
	    $keyValue['*accountEigenaar']=$data['*accountEigenaar'];
	  }
    if($keyValue['RapportageValuta'])
	  {
	    $query="SELECT Omschrijving as `*RapportageValuta` FROM Valutas WHERE Valuta='".$keyValue['RapportageValuta']."'";
	    $db->SQL($query);
	    $data=$db->lookupRecord();
	    $keyValue['*RapportageValuta']=$data['*RapportageValuta'];
	  }
	  $keyValue['huidigeDatum']=date("j")." ".$__appvar["Maanden"][date("n")]." ".date("Y");
	  $keyValue['huidigeGebruiker']=$USR;
	  return $keyValue;
	}

	function verzendMails($ids='',$toDdb=false,$categorie='email')
	{
	  global $USR;
    $db=new DB();
    $db2=new DB();
    if(is_array($ids))
    {
      $idFilter="AND id IN('".implode("','",$ids)."')";
    }
    else
      $idFilter='';
    $emailvelden=array('senderEmail','receiverName','receiverEmail');
    $query="SELECT id,senderName,senderEmail,receiverName,receiverEmail,subject,bodyHtml,ccEmail,bccEmail,crmId FROM emailQueue WHERE status='aangemaakt' $idFilter";
    $db->SQL($query);
    $db->Query();
    while($data=$db->nextRecord())
    {
      foreach($emailvelden as $veld)
        $data[$veld]=trim($data[$veld]);
      
      logScherm("Mail voor ".$data['receiverEmail']." klaarmaken.",true);
      $mail = new PHPMailer();
      $mail->IsSMTP();
      if($_POST['debug']==1)
        $mail->SMTPDebug=9;
      else
        $mail->SMTPDebug=1;
      $mail->From     = $data['senderEmail'];
      $mail->FromName = $data['senderName'];
      $data['bodyHtml']=$mail->encodeInlineImages($data['bodyHtml']);
      $mail->Body    = $data['bodyHtml'];
      $mail->AltBody = html_entity_decode(strip_tags($data['bodyHtml']));
      $mail->AddAddress($data['receiverEmail'],$data['receiverName']);
      if($data['ccEmail'] <> '' && $this->valid_email_quick($data['ccEmail']))
        $mail->AddCC($data['ccEmail']);
      if($data['bccEmail'] <> '' && $this->valid_email_quick($data['bccEmail']))
        $mail->AddBCC($data['bccEmail']);
      $mail->Subject = $data['subject'];

      $query="SELECT id,filename,Attachment FROM emailQueueAttachments WHERE emailQueueId='".$data['id']."'";
      $db2->SQL($query);
      $db2->Query();
      $fileDataIds=array();
      while($fileData=$db2->nextRecord())
      {
        $mail->AddStringAttachment($fileData['Attachment'],$fileData['filename']);
        $fileDataIds[]=$fileData['id'];
      }

      if (!$this->valid_email_quick($data['receiverEmail']))
      {
        echo "Fout bij het zenden naar " .$data['receiverEmail'].". Geen geldig emailadres ingesteld.<br>\n";
      }
      elseif(!$mail->Send())
      {
        echo "Fout bij het zenden  naar " .$data['receiverEmail']. "<br>\n";
        echo $mail->ErrorInfo;
        listarray($mail->smtp);
      }
      else
      {
         echo "email is verzonden naar ".$data['receiverEmail'].". <br>\n";

         if($toDdb==1)
         {
           if($data['crmId'] > 0)
           {
             $fullmail = $mail->MIMEHeader . "\n\n" . $mail->MIMEBody;
             $dd = new digidoc();
             $rec ["filename"] = preg_replace('/[^A-Za-z0-9_.-]/', "_", $data['subject']) . '.eml';
             $rec ["filesize"] = strlen($fullmail);
             $rec ["filetype"] = "text/plain";
             $rec ["description"] = $data['subject'];
             $rec ["blobdata"] = $fullmail;
             $rec ["keywords"] = 'email';
             if ($categorie == '')
             {
               $categorie = 'email';
             }
             $rec ["categorie"] = $categorie;
             $rec ["module"] = 'CRM_naw';
             $rec ["module_id"] = $data['crmId'];
             $dd->useZlib = false;
             $dd->addDocumentToStore($rec);
             echo "Email is opgeslagen als CRM document.<br>\n";
           }
           else
           {
             echo "Email niet opgeslagen als CRM document. Geen CRM record gevonden.<br>\n";
           }
         }
         
         $mail->ClearAddresses();
         $query="DELETE FROM emailQueue WHERE id='".$data['id']."'";
         $db2->SQL($query);
         $db2->Query();
         $query="DELETE FROM emailQueueAttachments WHERE id IN('".implode("','",$fileDataIds)."')";
         $db2->SQL($query);
         $db2->Query();
      }
      if($_POST['debug']==1)
        listarray($mail->smtp);
    }
	}


  function valid_email_quick($address)
  {
    $multipleEmail=explode(";",$address);
    foreach ($multipleEmail as $address)
    {
      $address=trim($address);
      if(!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*$", $address) || (strlen($address)==0))
        return false;
    }
    return true;
  }
}
?>