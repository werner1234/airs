<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2020/07/01 14:01:44 $
 		File Versie					: $Revision: 1.44 $

 		$Log: klantMutatiesVerwerken.php,v $
 		Revision 1.44  2020/07/01 14:01:44  rvv
 		*** empty log message ***
 		
 		Revision 1.43  2020/06/06 15:44:57  rvv
 		*** empty log message ***
 		
 		Revision 1.42  2020/06/03 15:37:49  rvv
 		*** empty log message ***
 		
 		Revision 1.41  2020/05/30 15:26:05  rvv
 		*** empty log message ***
 		
 		Revision 1.40  2020/05/16 15:52:52  rvv
 		*** empty log message ***
 		
 		Revision 1.39  2020/05/09 13:11:46  rvv
 		*** empty log message ***
 		
 		Revision 1.38  2020/05/06 14:55:45  rvv
 		*** empty log message ***
 		
 		Revision 1.37  2020/03/12 05:26:01  rvv
 		*** empty log message ***
 		
 		Revision 1.36  2020/03/11 13:21:31  rvv
 		*** empty log message ***
 		
 		Revision 1.35  2019/09/21 16:26:52  rvv
 		*** empty log message ***
 		
 		Revision 1.34  2019/07/05 16:35:47  rvv
 		*** empty log message ***
 		
 		Revision 1.33  2019/04/27 18:36:41  rvv
 		*** empty log message ***
 		
 		Revision 1.32  2019/04/27 08:24:53  rvv
 		*** empty log message ***
 		
 		Revision 1.31  2018/08/18 12:40:13  rvv
 		php 5.6 & consolidatie
 		
 		Revision 1.30  2018/04/25 16:33:20  rvv
 		*** empty log message ***
 		
 		Revision 1.29  2017/10/22 07:02:27  rvv
 		*** empty log message ***
 		
 		Revision 1.28  2017/10/21 17:27:55  rvv
 		*** empty log message ***
 		
 		Revision 1.27  2017/06/14 16:05:18  rvv
 		*** empty log message ***
 		
 		Revision 1.26  2017/06/11 09:56:38  rvv
 		*** empty log message ***
 		
 		Revision 1.25  2017/06/11 09:46:53  rvv
 		*** empty log message ***
 		
 		Revision 1.24  2016/12/14 17:03:58  rvv
 		*** empty log message ***
 		
 		Revision 1.23  2016/12/10 19:38:17  rvv
 		*** empty log message ***
 		
 		Revision 1.22  2016/12/10 19:37:28  rvv
 		*** empty log message ***
 		
 		Revision 1.21  2016/12/10 19:25:50  rvv
 		*** empty log message ***
 		
 		Revision 1.20  2016/12/07 16:27:25  rvv
 		*** empty log message ***
 		
 		Revision 1.19  2016/10/19 15:31:40  rvv
 		*** empty log message ***
 		
 		Revision 1.18  2016/10/16 14:55:51  rvv
 		*** empty log message ***
 		
 		Revision 1.17  2014/12/03 17:10:46  rvv
 		*** empty log message ***
 		
 		Revision 1.16  2014/11/30 13:03:37  rvv
 		*** empty log message ***
 		
 		Revision 1.15  2014/03/22 15:50:01  rvv
 		*** empty log message ***
 		
 		Revision 1.14  2013/12/18 17:02:55  rvv
 		*** empty log message ***
 		
 		Revision 1.13  2013/11/23 17:18:39  rvv
 		*** empty log message ***
 		
 		Revision 1.12  2013/01/27 13:58:08  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2013/01/09 17:03:48  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2012/07/25 15:57:24  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2011/08/03 14:41:40  rvv
 		*** empty log message ***

 		Revision 1.8  2011/05/22 11:43:32  rvv
 		*** empty log message ***

 		Revision 1.6  2010/10/17 09:27:40  rvv
 		sleutelvelden toegevoegd

 		Revision 1.5  2010/07/31 16:03:06  rvv
 		*** empty log message ***

 		Revision 1.4  2010/05/02 10:17:48  rvv
 		*** empty log message ***

 		Revision 1.3  2010/02/24 20:15:57  rvv
 		*** empty log message ***

 		Revision 1.2  2010/02/03 17:13:54  rvv
 		*** empty log message ***

 		Revision 1.1  2008/12/30 15:30:20  rvv
 		*** empty log message ***


*/

class klantMutatiesVerwerken
{
  function klantMutatiesVerwerken()
  {
    $this->db = new DB();
    $this->counter=0;
    $this->log = array();
    $this->newRecords = array();
    $this->fakeId='';
    $this->extraChecks=$this->getExtraChecks();
    $this->fouten ='';
    $this->keyFields = array();
    $this->tableObject = array();
    $this->getKeyFields();
   // $this->fondsaanvraagExport =array(); // Werkt via verwerkFondsAanvraag uit de applicatie_functies.
  }
  
  function getKeyFields()
  {
    global $__appvar;
    $done=array();
    $checkFields=array();

    foreach ($__appvar['tabelObjecten'] as $objectNaam)
    {
      $tmpObject = new $objectNaam;
      $this->tableObject[$tmpObject->data['table']]=$objectNaam;
      foreach ($tmpObject->data['fields'] as $field=>$fieldData)
      {
        if($fieldData["key_field"]==true)
        {
          $this->keyFields[$tmpObject->data['table']][]=$field;
          $checkFields[$tmpObject->data['table']][]=$field;
          $done[]=$tmpObject->data['table'];
        }
      }
    
      foreach ($tmpObject->data['fields'] as $field=>$fieldData)
      {
        if(!in_array($tmpObject->data['table'],$done) && $fieldData["keyIn"] !='')
        {
          $checkFields[$tmpObject->data['table']][]=$field;
          //if(count($this->keyFields[$tmpObject->data['table']]) < 2)
          $this->keyFields[$tmpObject->data['table']][]=$field;
        }
      }
      $this->keyFields[$tmpObject->data['table']]=array_unique($this->keyFields[$tmpObject->data['table']]);
      $checkFields[$tmpObject->data['table']]=array_unique($checkFields[$tmpObject->data['table']]);
    }
  
    if($this->keyFields['contractueleUitsluitingen'][1]=='fonds')
      unset($this->keyFields['contractueleUitsluitingen'][1]);
    if($this->keyFields['contractueleUitsluitingen'][2]=='categorie')
      unset($this->keyFields['contractueleUitsluitingen'][2]);
    if($this->keyFields['contractueleUitsluitingen'][4]=='geldrekening')
      unset($this->keyFields['contractueleUitsluitingen'][4]);
    
    if($this->keyFields['uitsluitingenModelcontrole'])
      $this->keyFields['uitsluitingenModelcontrole']=array();
  
    if($this->keyFields['BeleggingssectorPerFonds'])
      $this->keyFields['BeleggingssectorPerFonds']=array('Vermogensbeheerder','Fonds');
  
    if($this->keyFields['orderkosten'])
      $this->keyFields['orderkosten']=array('vermogensbeheerder','portefeuille');

  }
  
  function getExtraChecks()
  {
    $extraChecks=array();
    $extraChecks['ZorgplichtPerPortefeuille']['Vanaf']='0000-00-00';
    $extraChecks['ZorgplichtPerPortefeuille']['Zorgplicht']='';
//$extraChecks['ModelPortefeuillesPerPortefeuille'][]='Beleggingscategorie';
    $extraChecks['ModelPortefeuillesPerPortefeuille']['Vanaf']='0000-00-00';
//$extraChecks['StandaarddeviatiePerPortefeuille'][]='Vanaf';
    $extraChecks['orderkosten']['fondssoort']='';
    $extraChecks['orderkosten']['valuta']='';
    $extraChecks['orderkosten']['transactievorm']='';
    $extraChecks['orderkosten']['beursregio']='';
  
    $extraChecks['contractueleUitsluitingen']['categoriesoort']='';
    $extraChecks['contractueleUitsluitingen']['categorie']='';
    $extraChecks['contractueleUitsluitingen']['fonds']='';
    $extraChecks['contractueleUitsluitingen']['portefeuille']='';
  
    $extraChecks['begrippenRapport']['begrip']='';
  
    $extraChecks['Beleggingsplan']['Datum']='';
  
    $extraChecks['ModelPortefeuillesPerPortefeuille']['Vanaf']='';
    
    return $extraChecks;
  }
  
  function getKeyValues($table,$id)
  {
    
    if(count($this->keyFields[$table]) < 1)
      return '';
    
    $sleutelvelden='';
    $query="SELECT ".implode(",",$this->keyFields[$table])." FROM $table WHERE id='$id'";
    $this->db->SQL($query);
    $data=$this->db->lookupRecord();
    foreach($data as $key=>$value)
      $sleutelvelden.="$value ";
    
    $client='';
    if($table=='Rekeningmutaties' && $data['Rekening']<>'')
    {
      $query="SELECT Portefeuilles.Client FROM Portefeuilles JOIN Rekeningen ON Rekeningen.Portefeuille=Portefeuilles.Portefeuille
     WHERE Rekeningen.Rekening='".mysql_real_escape_string($data['Rekening'])."' AND Portefeuilles.consolidatie=0 ";
      $this->db->SQL($query);
      $data=$this->db->lookupRecord();
      $client=$data['Client'].' ';
    }
    
    return $client.$sleutelvelden;
  }

  function automatischVerwerken()
  {
    $query = "SELECT klantMutaties.id, klantMutaties.Vermogensbeheerder
              FROM
              klantMutaties
              JOIN Vermogensbeheerders ON klantMutaties.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder
              WHERE klantMutaties.verwerkt = '0' AND (Vermogensbeheerders.CrmTerugRapportage = '2' OR Vermogensbeheerders.CrmTerugRapportage='8')
              ORDER BY klantMutaties.add_date";
    $this->db->SQL($query);
    $this->db->Query();
    while($data = $this->db->nextRecord())
    {
      if($this->verwerk($data['id']))
      {
        $this->counter++;
        $this->log[]="klantMutaties.id [".$data['id']."] van [".$data['Vermogensbeheerder']."] verwerkt om ".date('d-m-y H:i').".";
      }
    }
    $this->createNewRecords();
    $this->verwerkVoorlopigeMutaties();
    $this->fondsaanvragenVerwerken();

  }

  function verwerk($id)
  {
    $db = new DB();
    $query = "SELECT klantMutaties.id, klantMutaties.tabel, klantMutaties.recordId, klantMutaties.veld, klantMutaties.oudeWaarde, klantMutaties.nieuweWaarde, klantMutaties.change_user, klantMutaties.emailAdres
    FROM klantMutaties JOIN Vermogensbeheerders ON klantMutaties.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder
    WHERE klantMutaties.verwerkt = '0' AND klantMutaties.id = '$id' ";
    $db->SQL($query);
    $db->Query();
    $data = $db->nextRecord();
    if($data['change_user']=='')
      $data['change_user']='Leeg';

    if(strlen($data['recordId'])==18)
    {
      $this->fakeId=$data['recordId'];
      $this->newRecords[$data['tabel']][$data['recordId']]['fields'][$data['veld']]=$data['nieuweWaarde'];
      $this->newRecords[$data['tabel']][$data['recordId']]['change_user']=$data['change_user'];
      $this->newRecords[$data['tabel']][$data['recordId']]['emailAdres']=$data['emailAdres'];
      $this->newRecords[$data['tabel']][$data['recordId']]['tabel']=$data['tabel'];
      if(in_array($data['veld'],$this->keyFields[$data['tabel']]))
      {
        $this->newRecords[$data['tabel']][$data['recordId']]['keys'][$data['veld']]=$data['nieuweWaarde'];
      }
    }
    else
    {
      $query = "UPDATE ".$data['tabel']." SET ".$data['veld']."='".mysql_real_escape_string($data['nieuweWaarde'])."', change_date=now(), change_user='".$data['change_user']."' WHERE id = '".$data['recordId']."' ";
      $db->SQL($query);
      if(!$db->Query())
      {
        $this->fouten .= "query: $query mislukt. Bewerking afgebroken.<br>\n";
        return false;
      }
      $sleutelWaarden='';
      if(count($this->keyFields[$data['tabel']]) > 0)
      {
        $query='';
        foreach ($this->keyFields[$data['tabel']] as $field)
        {
          if($query=='')
            $query .="SELECT $field ";
          else
            $query .=" ,$field ";
        }
        $query .=" FROM ".$data['tabel']." WHERE id = '".$data['recordId']."' ";
        $db->SQL($query);
        $db->Query();
        while($sleutelData = $db->nextRecord())
        {
          foreach($sleutelData as $key=>$value)
            $sleutelWaarden .="$key=$value";
        }
      }
      $data['sleutelWaarden']=$sleutelWaarden;
      $this->userUpdate[$data['change_user']][]=$data;

      $query = "UPDATE klantMutaties SET verwerkt = '9' WHERE id = '$id'";
      $db->SQL($query);
      $db->Query();
      $this->counter++;
    }
    return $data['id'];

  }

  function fondsaanvragenVerwerken()
  {
    $db=new DB();
    $query="SELECT id, verwerkt FROM fondsAanvragen WHERE verwerkt=0 AND overigeInfo=''";
    $db->SQL($query);
    $db->query();
    $mails=array();
    $verwerkIds=array();
    while($data=$db->nextRecord())
    {
      $txt=verwerkFondsAanvraag($data['id']);
      if(strpos($txt,'is ouder dan')>0)
      {
        $mails[]=$txt;
      }
      $verwerkIds[]=$data['id'];
    }

    if(count($mails)>0)
    {
      $cfg = new AE_config();
      $mailserver = $cfg->getData('smtpServer');
      if ($mailserver != '')
      {
        include_once('../classes/AE_cls_phpmailer.php');
        foreach ($mails as $mailBody)
        {
          $mail = new PHPMailer();
          $mail->IsSMTP();
          $mail->From = 'info@airs.nl';
          $mail->FromName = "Airs";
          $mail->Body = $mailBody;
          $mail->AltBody = html_entity_decode(strip_tags($mailBody));
          $mail->AddAddress('info@airs.nl', 'Fondsaanvraag');
          $mail->Subject = "Fondsaanvraag met verouderde koers.";
          $mail->Host = $mailserver;
          if (!$mail->Send())
          {
            echo "Verzenden van e-mail mislukt.";
          }
          else
          {
            echo "Mail verzonden.";
          }
        }
      }
    }
    //$this->getFondsaanvraagExport($verwerkIds);

  }
/*
  function getFondsaanvraagExport($verwerkIds)
  {
    $db=new DB();
    $db2=new DB();
    $query="SELECT fondsAanvragen.id,fondsAanvragen.Fonds,fondsAanvragen.ISINCode,fondsAanvragen.Vermogensbeheerder,VermogensbeheerdersPerBedrijf.Bedrijf FROM fondsAanvragen
     JOIN Vermogensbeheerders ON fondsAanvragen.Vermogensbeheerder  = Vermogensbeheerders.Vermogensbeheerder
     JOIN VermogensbeheerdersPerBedrijf ON VermogensbeheerdersPerBedrijf.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder
     WHERE fondsAanvragen.verwerkt=1 AND fondsAanvragen.id IN('".implode("','",$verwerkIds)."') AND (fondsAanvragen.Fonds<>'' OR fondsAanvragen.ISINCode<>'')";
    $db->SQL($query);
    $db->query();
    $fondsen=array();
    while($data=$db->nextRecord())
    {
      $query="SELECT id,Fonds FROM Fondsen ";
      $where='';
      if($data['ISINCode']<>'')
        $where.=" ISINCode='".mysql_real_escape_string($data['ISINCode'])."'";
      if($data['Fonds']<>'')
      {
        if($where<>'')
          $where.=" OR ";
        $where .= " Fonds='" . mysql_real_escape_string($data['Fonds']) . "'";
      }
      $query=$query."WHERE ( $where )";

      $db2->SQL($query);
      $db2->query();
      while($dbdata=$db2->nextRecord())
      {
        $dbdata['Vermogensbeheerder']=$data['Vermogensbeheerder'];
        $fondsen[$data['Bedrijf']][$dbdata['Fonds']]=$dbdata;
      }
    }
    $fondsaanvraagExport=array();
    $checkTables=array('BeleggingscategoriePerFonds','BeleggingssectorPerFonds','ZorgplichtPerFonds');

    foreach($fondsen as $bedrijf=>$fondsenVoorBedrijf)
    {
      foreach($fondsenVoorBedrijf as $fonds=>$fondsData)
      {
        $fondsOkay=false;
        foreach($checkTables as $tabel)
        {
          $query = "SELECT id FROM $tabel WHERE Vermogensbeheerder='" . mysql_real_escape_string($fondsData['Vermogensbeheerder']) . "' AND Fonds='" . mysql_real_escape_string($fonds) . "'";
          $db2->SQL($query);
          $db2->query();
          $dbdata=$db2->nextRecord();
          if($dbdata['id'] <> 0)
          {
            $fondsaanvraagExport[$bedrijf][] = array('tabel' => $tabel, 'recordId' => $dbdata['id']);
            $fondsOkay=true;
          }
        }
        if($fondsOkay==true)
          $fondsaanvraagExport[$bedrijf][]=array('tabel'=>'Fondsen','recordId'=>$fondsData['id']);
      }
    }
    $this->fondsaanvraagExport=$fondsaanvraagExport;
  }
  */

  function createQueueUpdates()
  {
    global $__appvar,$USR,$ftpSettings,$exportQuery;
    include_once('../html/queueExportQuery.php');

    if($USR=='')
      $updateUser='systeem';
    else
      $updateUser=$USR;

    $db = new DB();
    $db2 = new DB();
    $query = "SELECT klantMutaties.id, klantMutaties.tabel, klantMutaties.recordId, klantMutaties.veld, klantMutaties.oudeWaarde, klantMutaties.nieuweWaarde, klantMutaties.change_user,klantMutaties.Vermogensbeheerder, klantMutaties.emailAdres,
    VermogensbeheerdersPerBedrijf.Bedrijf
    FROM klantMutaties
    JOIN Vermogensbeheerders ON klantMutaties.Vermogensbeheerder  = Vermogensbeheerders.Vermogensbeheerder
    JOIN VermogensbeheerdersPerBedrijf ON VermogensbeheerdersPerBedrijf.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder
    WHERE klantMutaties.verwerkt = '9'
    ORDER BY VermogensbeheerdersPerBedrijf.Bedrijf";
    $db->SQL($query);
    $db->Query();
    while($data = $db->nextRecord())
    {
      $dataMutaties[$data['Bedrijf']][] = $data;
      $db2->SQL("UPDATE klantMutaties set verwerkt='8',change_date=now(),change_user='$USR' WHERE id = '".$data['id']."'");
      $db2->Query();
    }
    foreach ($dataMutaties as $bedrijf=>$mutatieData)
    {
      $exportId = date("YmdHis").'_'.rand(1000,9999);
      $tofile= 	"export_".$bedrijf."_".$exportId.".sql";
      $vulling="select '93eca1cd0a52f27c8ef5c721c8d562cd8bfb8f2b6f7096ff784e3a51003a38d354dea9844717261e38eef39bbc6f973b8ae832743921ec875c4c7b0fcd5b450b35cd4b8ce4618b0d57ea8fcde22859085add417cc9b8d8f623c8b36f1dbe94cafa119f5b9cac9c1b4588e955a1224dfc0f8f78b938319f5c0a52ab07f13fd2db416216de0eb2e9442bc289dcf4d7c8e457218d8f632ccb5d7f76456a6084bf76a62c56905aead315126fe460eb72a55d8a7d52bd49794406bea4acc6d941133598a3908176a594e589f4d0fb992d85167f415f86d69ff2bfeabd2cf4ff402996e3b916595eab3de79ede2373a664dbaa8ad407d4c323c36ef6929eda952828cb99e569442250e23031196b986625401266be5e98fb948763be73cff6991b8f7e84d3a1d4b5d25cf3c9e3461de47c79ca65f963ae6faf859674f9f22df1da6b2be7a668f8035cc1e692dfdb1f49856522d90382788912d8fe0cdb2beea586fa8d3d63752a271742f6d242357b7632bae5a933f87221f53d4dd3d2b4ce5fe221c502c1ec1a14e2ec5723340e9664b97c0ded6460f69ad4d2d71001ebbfe40d74e6cf6fd443ed2b77111a0d75d36973bcdd3b177fd44dff28d31e05b9ed56bab19a97dd1198f70f1eca78726b36462b5db718828807a308dacfe94ae553f6fefd73cf1513e6eee58e1498f2774ccde8d5f5684f25f6fb34de4518313a162dade543af23499d7d7b29fc928ec9610c08a78fb06e9e44b4ffd7c8912aa36f8ede61a368322d34c2d65dad6b6d6df6e67ca55386899fd5851b5e98c5948fb27aaae8fdccf5318dcb8f3a35fda72be323d135a959d57edef0dc75b570466eaaf4ee77a43187da74143a4e96e86b3fbcb5989032767fdec347df09b679ee94e182b661f39d617cb149eb375529f39c2823b371482235c612d12db0b97bc9e5e2e81ef0499aa19285d8e7afcaf0714b2ba84d74cb833add53678dce355f1c53deb486a955182e2c100def6f04e411af78e8ec111c99d642f2a70127f61d8bd708c4318e15b47c5b2424a73446569d3f42ae4bcc747ecf2b7fe7017d52d876070a041cbe84d25c50707f94d6e966c9124984da9070cb476e3b5e4268e6a3d886e4a36d31c9798239f815ee17cd7f3c55bcd55cb979c38d3fb7b3ae11e12662b5d3347e4cb6b744ccc524479c16bf7e361c27afefb4cf97587d994993f974e12cbcd5e8083a24b48c692e4dd9edac01009c39fd9686f6bccf9fa6d35f51f0c17e81431fcd8ce65e6fc3fd4982fde5bcf97a41ee864ebc25f5ca5be9ea5ba967dced87a8786d6e82f330b2100146df98fafe715fea33af26c95db55f084bdcc704474e9b17ad1e17e0927d1510076a5ae5cbaacfff49d6a8978c078fc7d446cc1b85572322553b025e52d' as niets ;\n";
      if($fp = fopen($__appvar['tempdir'].$tofile, 'w'))
        fwrite($fp, $vulling);
      else
	      $this->_error[]= "<br>\n FOUT: openen van ".$__appvar['tempdir'].$tofile." mislukt.";
  
      $exportRekeningen=false;
      $recordDone=array();
      foreach ($mutatieData as $regel) //data verzamelen
      {
        $key=$regel['tabel'].$regel['recordId'];
        if($regel['id']>0)
        {
          if(in_array($key,$recordDone))
            continue;
          $query = "SELECT * FROM ".$regel['tabel']." WHERE id = '".$regel['recordId']."' ";
          $db->SQL($query);
          $db->Query();
          $data = $db->nextRecord();

          $query = "SELECT * FROM klantMutaties WHERE id = '".$regel['id']."' ";
          $db->SQL($query);
          $db->Query();
          $data2 = $db->nextRecord();

	    	  $rec = serialize($data);
	    	  $rec2= serialize($data2);
   			  // insert Into Queue
  			  $q2 =  "INSERT INTO importdata SET Bedrijf = '".$bedrijf."', tableName = '".$regel['tabel']."', tableId = '".$data['id']."', tableData = '".mysql_escape_string($rec)."', exportId = '".$exportId."', ";
   			  $q2 .= " add_user = '".$updateUser."', add_date = NOW() , change_user = '".$updateUser."', change_date = NOW() ;\n";
   			  $q3 =  "INSERT INTO importdata SET Bedrijf = '".$bedrijf."', tableName = 'klantMutaties', tableId = '".$data2['id']."', tableData = '".mysql_escape_string($rec2)."', exportId = '".$exportId."', ";
   			  $q3 .= " add_user = '".$updateUser."', add_date = NOW() , change_user = '".$updateUser."', change_date = NOW() ;\n";
 	  		  fwrite($fp, $q2);
 	  		  fwrite($fp, $q3);
 	  		  if($regel['tabel']=='GeconsolideerdePortefeuilles')
          {
            $exportRekeningen=true;
          }
          $recordDone[]=$key;
        }
	    }
      fclose($fp);
  
      if($exportRekeningen==true)
      {
        logIt("$bedrijf Extra Rekeningen en Portefeuilles Export ivm GeconsolideerdePortefeuilles");
        $queryValues = array('lastUpdate' => date("Y-m-d H:i", time() - 3600), 'Bedrijf' => $bedrijf);
        $realExport = array('Rekeningen' => $exportQuery['Rekeningen'], 'Portefeuilles' => $exportQuery['Portefeuilles']);
        foreach ($realExport as $key => $val)
        {
          $query = buildQuery($key, $val, $queryValues);
          $aantal=$this->exportTable($key, $query, $__appvar['tempdir'] . $tofile, $bedrijf, $exportId);
          logIt("$bedrijf Export $aantal records voor $key via $exportId");
        }
      }
      
     
/*
      if(isset($this->fondsaanvraagExport[$bedrijf]) && count($this->fondsaanvraagExport[$bedrijf])>0)
      {
        foreach($this->fondsaanvraagExport[$bedrijf] as $regel)
        {
          $query = "SELECT * FROM ".$regel['tabel']." WHERE id = '".$regel['recordId']."' ";
          $db->SQL($query);
          $db->Query();
          $data = $db->nextRecord();
          $rec = serialize($data);
          $q2 =  "INSERT INTO importdata SET Bedrijf = '".$bedrijf."', tableName = '".$regel['tabel']."', tableId = '".$data['id']."', tableData = '".mysql_escape_string($rec)."', exportId = '".$exportId."', ";
          $q2 .= " add_user = '".$updateUser."', add_date = NOW() , change_user = '".$updateUser."', change_date = NOW() ;\n";
          fwrite($fp, $q2);
        }
      }
*/


	    if(!$this->gzcompressfile($__appvar['tempdir'].$tofile))
		    $this->_error[] = "Fout: zippen van bestand mislukt!";
		  unlink($__appvar['tempdir'].$tofile);
		  if(empty($this->_error))
		  {
  		  //$ftpSettings['server'] = "toploader.adm.aeict.net"; // naar queue
	  	  //$ftpSettings['path'] = "updates";
		  	//$ftpSettings['user'] = "airs";
			  //$ftpSettings['password'] = "05airs!05";

    		if($conn_id = ftp_connect($ftpSettings['server']))// login with username and password
	    	{
				  if($login_result = ftp_login($conn_id, $ftpSettings['user'], $ftpSettings['password']))
				  {
				    if (ftp_put($conn_id,$tofile.".gz",$__appvar['tempdir'].$tofile.".gz", FTP_BINARY))
				      $this->log[] = "<br>\n successfully uploaded $tofile\n";
					  else
						  $this->_error[] = "There was a problem while uploading $tofile.gz";
				  }
				  ftp_close($conn_id);
	    	}
	    	else
			    $this->_error[] = "Could not connect";
			  }

			  if(empty($this->_error))
			  {
			    $filesize = filesize($__appvar['tempdir'].$tofile.".gz");
          $query = "INSERT INTO updates SET exportId = '".$exportId."', Bedrijf = '".$bedrijf."', type = 'userqueue', jaar = '".date('Y')."', filename = '".$tofile.".gz', filesize = '".$filesize."',
	                  server = '".$ftpSettings['server']."', username = '".$ftpSettings['user']."', password = '".$ftpSettings['password']."', consistentie = '', add_date = NOW(), add_user = '".$updateUser."',
	                  change_date = NOW(), change_user = '".$updateUser."' ";
	        $queueDB = new DB(2);
	        $queueDB->SQL($query);
	        if($queueDB->Query())
	        {
	          $this->log[]="<br>\nUpdate in queue geplaatst om ".date('d-m-y H:i').".";
	          unlink($__appvar['tempdir'].$tofile.".gz");
	        }
			  }

	  }


    foreach ($dataMutaties as $bedrijf=>$mutatieData)
    {
      foreach ($mutatieData as $regel)
      {
        $query = "UPDATE klantMutaties SET verwerkt='1', change_user = '$updateUser', change_date=now() WHERE id = '".$regel['id']."' ";
        $db->SQL($query);
        $db->Query();
      }
    }
	  listarray($this->_error);

  }

  function gzcompressfile($source,$level=false)
{
	$dest=$source.'.gz';
	$mode='wb'.$level;
	$error=false;
	if($fp_out=gzopen($dest,$mode)){
	   if($fp_in=fopen($source,'r')){
	       while(!feof($fp_in))
	           gzwrite($fp_out,fread($fp_in,1024*512));
	       fclose($fp_in);
	       }
	     else $error=true;
	   gzclose($fp_out);
	   }
	 else $error=true;
	if($error) return false;
	 else return $dest;
}
  
  
  function exportTable($table, $query, $tofile, $bedrijf,$exportId)
  {
    global $USR;
    
    $DB1 = new DB();
    $DB1->SQL($query);
    $DB1->query();
    $aantal = $DB1->Records();
    
    if(!$fp = fopen($tofile, 'a'))
    {
      echo "<br>\n FOUT: openen van ".$tofile." mislukt.";
      exit;
    }
    
    $normalUpdate='';
    $n=0;
    while($tableData = $DB1->NextRecord())
    {
      $n++;
      $data = serialize($tableData);
      // insert Into Queue
      $normalSQLlen=strlen($normalUpdate);
      if($normalSQLlen==0)
         $normalUpdate.="('".$bedrijf."','".$table."','".$tableData['id']."','".mysql_escape_string($data)."',  '".$exportId."',  '".$USR."',NOW() ,'".$USR."',NOW()) ";
      else
         $normalUpdate.=",('".$bedrijf."','".$table."','".$tableData['id']."','".mysql_escape_string($data)."',  '".$exportId."',  '".$USR."',NOW() ,'".$USR."',NOW()) ";
      
      if($normalSQLlen > 1000000 || $n==$aantal)
      {
         $q2 = "INSERT INTO importdata (Bedrijf,tableName,tableId,tableData,exportId,add_user,add_date,change_user,change_date) VALUES $normalUpdate ;\n";
         //echo $q2."<br>\n<br>\n";
         fwrite($fp, $q2);
         $normalUpdate='';
      }
    }
    
    fclose($fp);
    return $aantal;
  }

function getLog()
{
  return $this->log;
}

function getError()
{
  return $this->_error;
}

function createNewRecords()
{
  $db=new DB();
  $tabelZonderSleutelVelden=array('uitsluitingenModelcontrole');
  foreach ($this->newRecords as $table=>$records)
  {
    foreach ($records as $fakeId=>$velden)
    {
      if(count($this->keyFields[$table]) > 0 || in_array($table,$tabelZonderSleutelVelden))
      {
        if(count($this->keyFields[$table]) == count($velden['keys']))
        {
          $query="SELECT id FROM $table WHERE 1 ";
          foreach ($velden['keys'] as $key=>$value)
            $query .= " AND $key = '".mysql_real_escape_string($value)."'";
          foreach($this->extraChecks[$table] as $key=>$defaultValue)
          {
            $value=$velden['fields'][$key];
            if($value=='')
              $value=$defaultValue;
            $query .= " AND $key = '" . mysql_real_escape_string($value) . "'";
          }
          $db->SQL($query);
          logit("Klantmutatie lookup: $query");
          $db->Query();
          if($db->records() > 0 && !in_array($table,$tabelZonderSleutelVelden))
          {
            if($table=='Clienten')
            {
              $record=$db->nextRecord();
              $query="UPDATE klantMutaties SET recordId='".$record['id']."',verwerkt='9' WHERE recordId='$fakeId'";
              $db->SQL($query);
              $db->Query();
              $this->counter++;
              $this->userUpdate[$velden['change_user']][]=$velden;
            }
            else
            {
              echo "Er is al een record met deze sleutelvelden aanwezig. $query <br>\n ";
            }
          }
          else
          {
            $newValues=array();
            $tmpObject=new $this->tableObject[$table]();
            foreach ($tmpObject->data['fields'] as $key=>$defaultData)
              if($defaultData['form_visible'] == true)
                $newValues[$key]=$defaultData['value'];
            foreach ($velden['fields'] as $key=>$value)
              $newValues[$key]=$value;

            unset($newValues['change_user']);
            unset($newValues['change_date']);
            unset($newValues['add_date']);
            unset($newValues['add_user']);

            $query="INSERT INTO $table SET change_user = '".$velden['change_user']."', change_date=now(), add_date=NOW(), add_user='".$velden['change_user']."' ";
            foreach ($newValues as $key=>$value)
              $query .= ", $key = '".mysql_real_escape_string($value)."'";
            $db=new DB();
            $db->SQL($query);
            $db->Query();
            $lastId=$db->last_id();

            $query="UPDATE klantMutaties SET recordId='$lastId',verwerkt='9' WHERE recordId='$fakeId'";
            $velden['recordId']=$lastId;
            $db->SQL($query);
            $db->Query();
            $this->counter++;
            $this->userUpdate[$velden['change_user']][]=$velden;
          }
        }
        else
        {
          echo "Niet alle sleutelvelden zijn gevuld? (Tabel $table , ".count($this->keyFields[$table])." verwacht en ".count($velden['keys'])." gevonden.) <br>\n";
          listarray($this->keyFields[$table]);
          listarray($velden['keys']);
        }
      }
      else
      {
        echo "Tabel $table heeft geen sleutelvelden.<br>\n";
      }
    }
  }
}

function sendEmail()
{
  global $USR;
  $db=new DB();
  foreach ($this->userUpdate as $user=>$recordData)
  { 
    $db->SQL("SELECT * FROM Gebruikers WHERE Gebruiker = '$user' ");
    $gebruiker=$db->lookupRecord();

    $html="Verwerkte records voor <b>$user</b> <br>\n";
    $html .="<table border=1><tr><td><b>Id</b></td><td><b>Tabel</b></td><td><b>Oude waarde</b></td><td><b>Nieuwe waarde</b></td><td><b>Sleutel waarden</b></td></tr>";
    foreach ($recordData as $regel=>$data)
    {
      if($data['emailAdres']<>'')
      {
        $gebruiker['emailAdres']=$data['emailAdres'];
        $gebruiker['naam']=$user;
      }
      if(is_array($data['fields']))
      {
        foreach($data['fields'] as $key=>$value)
        {
           $html .= "<tr><td>" . $data['recordId'] . "</td><td>" . $data['tabel'] . "." . $key . "</td><td>leeg</td><td>" . $value . "</td><td>" . implode(',',array_values($data['keys'])). "</td></tr>";
        }
      }
      else
      {
        $html .= "<tr><td>" . $data['recordId'] . "</td><td>" . $data['tabel'] . "." . $data['veld'] . "</td><td>" . $data['oudeWaarde'] . "</td><td>" . $data['nieuweWaarde'] . "</td><td>" . $data['sleutelWaarden'] . "</td></tr>";
      }
    }
    $html .="<table><br>\n";
    $html .="Verzonden om: ".date("d-m-Y H:i")." (".$USR.")<br>\n";

    $cfg=new AE_config();
    $mailserver=$cfg->getData('smtpServer');
    $emailAddesses=explode(";",$gebruiker['emailAdres']);
    if($gebruiker['emailAdres'] !="" && $mailserver !='')
    {
      include_once('../classes/AE_cls_phpmailer.php');
      $mail = new PHPMailer();
      $mail->IsSMTP();
      $mail->From     = 'info@airs.nl';
      $mail->FromName = "Airs";
      $mail->Body    = $html;
      $mail->AltBody = html_entity_decode(strip_tags($html));
      foreach ($emailAddesses as $id=>$emailadres)
      {
        if($id==0)
          $naam=$gebruiker['naam'];
        else
          $naam=$emailadres;
        $mail->AddAddress($emailadres,$naam);
      }
      //$mail->AddAddress($gebruiker['emailAdres'],$gebruiker['naam']);
      $mail->Subject = "AIRS mutaties verwerkt.";
      $mail->Host=$mailserver;
      if(!$mail->Send())
        echo "Verzenden van e-mail mislukt.";
      else
        echo "Mutatiemail verzonden.";
    }
    else
      echo "Geen email adres voor $user of geen emailserver gespecificeerd.";
  }
  unset($this->userUpdate);
}

  function createInsert($data)
  {
  $insert = "";
	foreach ($data as $key=>$value)
	{
	  if($key <> 'id')
	  {
	    $value=addslashes($value);
		  if($insert == '')
		    $insert .= " $key = '$value' ";
		  else
		    $insert .= ", $key = '$value' ";
		}
  }
	return $insert;
  }

  function verwerkVoorlopigeMutaties()
  {
    $DB = new DB();
    $DB2 = new DB();
    $DB3 = new DB();
    $mutatieIds=array();
    $afschiftIds=array();
    $query = "SELECT VoorlopigeRekeningafschriften.* 
      FROM VoorlopigeRekeningafschriften
      JOIN Rekeningen ON VoorlopigeRekeningafschriften.Rekening = Rekeningen.Rekening
      JOIN Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille
      JOIN Vermogensbeheerders ON Vermogensbeheerders.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder   
      WHERE Rekeningen.consolidatie=0 AND Portefeuilles.consolidatie=0 AND 
		    VoorlopigeRekeningafschriften.verwerkt = '0' AND VoorlopigeRekeningafschriften.Rekening = Rekeningen.Rekening AND
		    (Vermogensbeheerders.CrmTerugRapportage=2 OR Vermogensbeheerders.CrmTerugRapportage=4)";
     
		$DB->SQL($query);
		$DB->Query();
		while($afschriftenData = $DB->NextRecord())
		{ 
		  $TijdelijkeafschriftenData=$afschriftenData;
			$dat = db2jul($afschriftenData['Datum']);
		  $jaar = date("Y",$dat);
			$query = "SELECT Afschriftnummer, NieuwSaldo AS Saldo FROM Rekeningafschriften WHERE Rekening = '".$afschriftenData['Rekening']."' AND YEAR(Rekeningafschriften.Datum) = '".$jaar."' ORDER BY Afschriftnummer DESC LIMIT 1";
			$DB2->SQL($query);
			$DB2->Query();
			if($DB2->Records() > 0)
			{
				$afschriftenDataOld = $DB2->NextRecord();
				$afschriftenData['Afschriftnummer'] = $afschriftenDataOld['Afschriftnummer'] + 1;
				$afschriftenData['Saldo'] = $afschriftenDataOld['Saldo'];
			}
			else
			{
				$afschriftenData['Afschriftnummer'] = $jaar."001";
				$afschriftenData['Saldo'] = 0;
			}

		  $totaalBedrag=0;
			$query = "SELECT * FROM VoorlopigeRekeningmutaties WHERE
			          Rekening = '".$TijdelijkeafschriftenData['Rekening']."' AND Afschriftnummer = '".$TijdelijkeafschriftenData['Afschriftnummer']."' AND verwerkt = '0'	";
			$DB2->SQL($query);
			$DB2->Query();
			while($rekeningmutatieData = $DB2->NextRecord())
			{
		    $totaalBedrag += round($rekeningmutatieData['Bedrag'],2);
		    $rekeningmutatieData['Verwerkt']=1;
		    $rekeningmutatieData['Afschriftnummer']=$afschriftenData['Afschriftnummer'];
		    $insert = $this->createInsert($rekeningmutatieData);
		  	$query= "INSERT INTO Rekeningmutaties SET $insert";
		  	$DB3->SQL($query);
		  	if($DB3->Query())
		  	{
		  	  $mutatieIds[]=$DB3->last_id();
              $this->counter++;
		  	  $query = "UPDATE VoorlopigeRekeningmutaties SET Verwerkt='1' WHERE id='".$rekeningmutatieData['id']."'";
		  	  $DB3->SQL($query);
		  	  $DB3->Query();
		  	}
			}

			$afschriftenData['NieuwSaldo'] = $afschriftenData['Saldo'] + $totaalBedrag;
			$afschriftenData['Verwerkt']=1;
			$insert = $this->createInsert($afschriftenData);
			$query= "INSERT INTO Rekeningafschriften SET $insert";
			$DB3->SQL($query);
		  if($DB3->Query())
		  {
		    $afschriftIds[]=$DB3->last_id();
            $this->counter++;
		    $query = "UPDATE VoorlopigeRekeningafschriften SET Verwerkt='1' WHERE id='".$afschriftenData['id']."'";
		    $DB3->SQL($query);
		    $DB3->Query();
		  }
		}
    if(count($mutatieIds) > 0 && count($afschriftIds) > 0)
    {
		  $this->createRekeningQueueUpdate($mutatieIds,$afschriftIds);
    }
  }
  
function createRekeningQueueUpdate($mutatieIds,$afschiftIds)
{
  global $ftpSettings,$__appvar,$USR;
  $db=new DB();
  $query="SELECT
VermogensbeheerdersPerBedrijf.Bedrijf,
Rekeningmutaties.id as recordId,
'Rekeningmutaties' as tabel
FROM
Rekeningmutaties
Inner Join Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
Inner Join Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille
Inner Join VermogensbeheerdersPerBedrijf ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerBedrijf.Vermogensbeheerder
WHERE Rekeningmutaties.Id IN('".implode("','",$mutatieIds)."')";
  $db->SQL($query);
  $db->Query();
  while($data=$db->nextRecord())
  {
    $bedrijf=$data['Bedrijf'];
    unset($data['Bedrijf']);
    $dataMutaties[$bedrijf][]=$data;
  }
    $query="SELECT
VermogensbeheerdersPerBedrijf.Bedrijf,
Rekeningafschriften.id as recordId,
'Rekeningafschriften' as tabel
FROM
Rekeningafschriften
Inner Join Rekeningen ON Rekeningafschriften.Rekening = Rekeningen.Rekening
Inner Join Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille
Inner Join VermogensbeheerdersPerBedrijf ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerBedrijf.Vermogensbeheerder
WHERE Rekeningafschriften.Id IN('".implode("','",$afschiftIds)."')";
  $db->SQL($query);
  $db->Query();
  while($data=$db->nextRecord())
  {
    $bedrijf=$data['Bedrijf'];
    unset($data['Bedrijf']);
    $dataMutaties[$bedrijf][]=$data;
  }
  
  foreach ($dataMutaties as $bedrijf=>$mutatieData)
  {
    $exportId = date("YmdHis");
    $tofile= 	"export_".$bedrijf."_".$exportId.".sql";
    
    $vulling="select '93eca1cd0a52f27c8ef5c721c8d562cd8bfb8f2b6f7096ff784e3a51003a38d354dea9844717261e38eef39bbc6f973b8ae832743921ec875c4c7b0fcd5b450b35cd4b8ce4618b0d57ea8fcde22859085add417cc9b8d8f623c8b36f1dbe94cafa119f5b9cac9c1b4588e955a1224dfc0f8f78b938319f5c0a52ab07f13fd2db416216de0eb2e9442bc289dcf4d7c8e457218d8f632ccb5d7f76456a6084bf76a62c56905aead315126fe460eb72a55d8a7d52bd49794406bea4acc6d941133598a3908176a594e589f4d0fb992d85167f415f86d69ff2bfeabd2cf4ff402996e3b916595eab3de79ede2373a664dbaa8ad407d4c323c36ef6929eda952828cb99e569442250e23031196b986625401266be5e98fb948763be73cff6991b8f7e84d3a1d4b5d25cf3c9e3461de47c79ca65f963ae6faf859674f9f22df1da6b2be7a668f8035cc1e692dfdb1f49856522d90382788912d8fe0cdb2beea586fa8d3d63752a271742f6d242357b7632bae5a933f87221f53d4dd3d2b4ce5fe221c502c1ec1a14e2ec5723340e9664b97c0ded6460f69ad4d2d71001ebbfe40d74e6cf6fd443ed2b77111a0d75d36973bcdd3b177fd44dff28d31e05b9ed56bab19a97dd1198f70f1eca78726b36462b5db718828807a308dacfe94ae553f6fefd73cf1513e6eee58e1498f2774ccde8d5f5684f25f6fb34de4518313a162dade543af23499d7d7b29fc928ec9610c08a78fb06e9e44b4ffd7c8912aa36f8ede61a368322d34c2d65dad6b6d6df6e67ca55386899fd5851b5e98c5948fb27aaae8fdccf5318dcb8f3a35fda72be323d135a959d57edef0dc75b570466eaaf4ee77a43187da74143a4e96e86b3fbcb5989032767fdec347df09b679ee94e182b661f39d617cb149eb375529f39c2823b371482235c612d12db0b97bc9e5e2e81ef0499aa19285d8e7afcaf0714b2ba84d74cb833add53678dce355f1c53deb486a955182e2c100def6f04e411af78e8ec111c99d642f2a70127f61d8bd708c4318e15b47c5b2424a73446569d3f42ae4bcc747ecf2b7fe7017d52d876070a041cbe84d25c50707f94d6e966c9124984da9070cb476e3b5e4268e6a3d886e4a36d31c9798239f815ee17cd7f3c55bcd55cb979c38d3fb7b3ae11e12662b5d3347e4cb6b744ccc524479c16bf7e361c27afefb4cf97587d994993f974e12cbcd5e8083a24b48c692e4dd9edac01009c39fd9686f6bccf9fa6d35f51f0c17e81431fcd8ce65e6fc3fd4982fde5bcf97a41ee864ebc25f5ca5be9ea5ba967dced87a8786d6e82f330b2100146df98fafe715fea33af26c95db55f084bdcc704474e9b17ad1e17e0927d1510076a5ae5cbaacfff49d6a8978c078fc7d446cc1b85572322553b025e52d' as niets ;\n";
    if($fp = fopen($__appvar['tempdir'].$tofile, 'w'))
    {
      fwrite($fp, $vulling);
      foreach ($mutatieData as $regel) //data verzamelen
      {
        $query = "SELECT * FROM ".$regel['tabel']." WHERE id = '".$regel['recordId']."' ";
        $db->SQL($query);
        $db->Query();
        $data = $db->nextRecord();

    	  $rec = serialize($data);
 			  // insert Into Queue
 			  $q2 =  "INSERT INTO importdata SET Bedrijf = '".$bedrijf."', tableName = '".$regel['tabel']."', tableId = '".$data['id']."', tableData = '".mysql_escape_string($rec)."', exportId = '".$exportId."', ";
 			  $q2 .= " add_user = '".$USR."', add_date = NOW() , change_user = '".$USR."', change_date = NOW() ;\n";
  		  fwrite($fp, $q2);
      }
      fclose($fp);
    }
    else
    {
      echo "Fout: Aanmaken van ".$__appvar['tempdir'].$tofile." mislukt!";
      logIt("Fout: Aanmaken van ".$__appvar['tempdir'].$tofile." mislukt!");
    }

	  if(!$this->gzcompressfile($__appvar['tempdir'].$tofile))
	  {
		  echo "Fout: zippen van bestand mislukt!";
      logIt("Fout: zippen van $tofile mislukt!");
		  $error=1;
	  }
    unlink($__appvar['tempdir'].$tofile);
	  if($error==false)
	  {
   		if($conn_id = ftp_connect($ftpSettings['server']))// login with username and password
	   	{
			  if($login_result = ftp_login($conn_id, $ftpSettings['user'], $ftpSettings['password']))
			  {
			    if (ftp_put($conn_id,$tofile.".gz",$__appvar['tempdir'].$tofile.".gz", FTP_BINARY))
          {
            echo "<br>\n successfully uploaded $tofile\n";
            logIt("Successfully uploaded $tofile");
          }
				  else
				  {
				    $error=1;
					  echo "There was a problem while uploading $tofile.gz";
            logIt("There was a problem while uploading $tofile.gz");
				  }
			  }
			  ftp_close($conn_id);
	  	}
	   	else
	   	{
	   	  $error=1;
		    echo "Could not connect";
	   	}
		}
		if($error==false)
    {
			  $filesize = filesize($__appvar['tempdir'].$tofile.".gz");
        $query = "INSERT INTO updates SET exportId = '".$exportId."', Bedrijf = '".$bedrijf."', type = 'userqueue', jaar = '".date('Y')."', filename = '".$tofile.".gz', filesize = '".$filesize."',
	                server = '".$ftpSettings['server']."', username = '".$ftpSettings['user']."', password = '".$ftpSettings['password']."', consistentie = '', add_date = NOW(), add_user = '".$USR."',
	                change_date = NOW(), change_user = '".$USR."' ";
	      $queueDB = new DB(2);
	      $queueDB->SQL($query);
	      if($queueDB->Query())
	      {
	        echo "<br>\nUpdate $exportId in queue geplaatst om ".date('d-m-y H:i').".";
          logIt("Update $exportId in queue geplaatst om ".date('d-m-y H:i').".");
	      }
        else
        {
           echo "<br>\n $query mislukt!";
           logIt("Update queue met $query mislukt!");
        }
	   }
	   if(file_exists($__appvar['tempdir'].$tofile.".gz"))
	     unlink($__appvar['tempdir'].$tofile.".gz");
	}
}

}
