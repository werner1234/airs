<?php

class FondskoersAanvragen extends Table
{
  /*
  * Object vars
  */
  
  var $data = array();


  /*
  * Constructor
  */
  function FondskoersAanvragen()
  {
    $this->defineData();
    $this->setDefaults();
    $this->set($this->data['identity'],0);
  }

	function addField($name, $properties)
	{
		$this->data['fields'][$name] = $properties;
	}

	/*
	 * Veldvalidatie
	 */
	function validate()
	{
	  global $__appvar;
    if($this->dbId==2)
    {
      $db=new DB();
			if($this->get("Fonds")<>'')
			{
				$query = "SELECT Fonds FROM Fondskoersen WHERE Fonds='" . $this->get("Fonds") . "' AND Datum='" . $this->get("Datum") . "'";
				if ($db->QRecords($query))
				{
					$fonds = $db->nextRecord();
					$error = vtb("Fonds (%s) is al aanwezig op %s.", array($fonds['Fonds'], $this->get("Datum")));
					$this->setError("Fonds", $error);
				}
			}
    }

    $db=new DB();
    $db->lookupRecordByQuery("SELECT Fonds FROM Fondsen WHERE Fonds = '". mysql_real_escape_string($this->get("Fonds"))."' ");
    if($db->records() <= 0) {
      $this->setError("Fonds",vtb("%s is een onbekend fonds", array($this->get("Fonds"))));
    }

    ($this->get("verwerkt")==1)?$this->setError("verwerkt",vt("Record is niet automatisch verwerkt.")):true;
    ($this->get("emailAdres")=="")?$this->setError("emailAdres",vt("Mag niet leeg zijn!")):true;
    ($this->get("Fonds")=="")?$this->setError("Fonds",vt("Mag niet leeg zijn!")):true;
    ($this->get("Datum")=="")?$this->setError("Datum",vt("Mag niet leeg zijn!")):true;
    
    if(db2jul($this->get('Datum'))>time())
    {
      $this->setError("Datum",vt("De opgegeven datum ligt in de toekomst."));
    }
    
		$valid = ($this->error==false)?true:false;
		return $valid;
	}
	
	/*
	 * Toegangscontrole
	 */
	function checkAccess($type)
	{
	 global $__appvar;
	 $db=new DB();
	 if($__appvar['bedrijf']=='HOME')
   { 
	 	if(($type=='delete' || $type=='edit') && $this->get('verwerkt')==1)
      return false;
    if(($type=='delete') && $db->QRecords("SELECT id FROM fondsAanvragen WHERE verwerkt=-1 AND id='".$this->get('id')."'")==1)
      return false;
   }
   if($_SESSION['usersession']['gebruiker']['fondsaanvragenAanleveren']==1)
     return true;
     
	 return checkAccess();
	}
  
  
  function sendFondskoersaanvraagEmail()
  {
    $cfg=new AE_config();
    $mailserver=$cfg->getData('smtpServer');
    $fondsEmail=$cfg->getData('fondsEmail');
    include_once('../AE_cls_phpmailer.php');
    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->From     = $fondsEmail;
    $mail->FromName = "Airs";
    if($mail->ValidateAddress($this->get('emailAdres')))
    {
			$body = "Fondskoersaanvraag voor " . $this->get('Fonds') . " " . $this->get('Datum') . " heeft op " . date('d-m-Y H:i:s') . " de status " . strtolower($this->data['fields']['verwerkt']['form_options'][$this->get('verwerkt')]) . " gekregen.<br>\n";
			$subject="Fondskoersaanvraag ".$this->get('Fonds');
			if($this->get('verwerkt') > 0)
        $body.="<br>\nDeze mutatie zal in een volgende update worden opgenomen.";
          
      $mail->Body    = $body;
      $mail->AltBody = html_entity_decode(strip_tags($body));
			storeControleMail('fondskoersaanvraag',$subject,$body);
      $mail->AddAddress($this->get('emailAdres'),$this->get('emailAdres'));
      $mail->Subject = $subject;
      $mail->Host=$mailserver;
      if(!$mail->Send())
      {
        echo "Verzenden van e-mail mislukt.";
      }
    }
  }

	function createQueueUpdates($recordId)
	{
		$log=array();
		global $__appvar,$USR,$ftpSettings;
		if($USR=='')
			$updateUser='systeem';
		else
			$updateUser=$USR;

		$queueDB = new DB(2);
		$queueDB->SQL("SHOW TABLE STATUS like '%updates%'");
		$status=$queueDB->lookupRecord();
		$lastId=$status['Auto_increment'];

		$db = new DB();

		$query = "SELECT
fondskoersAanvragen.id,
fondskoersAanvragen.Fonds,
fondskoersAanvragen.Datum,
VermogensbeheerdersPerBedrijf.Bedrijf
FROM
fondskoersAanvragen
INNER JOIN Fondsen ON Fondsen.Fonds = fondskoersAanvragen.Fonds
INNER JOIN VermogensbeheerdersPerBedrijf ON Fondsen.KoersVBH = VermogensbeheerdersPerBedrijf.Vermogensbeheerder
WHERE fondskoersAanvragen.id='".$recordId."'";
		$db->SQL($query);
		$db->Query();
    $dataMutaties=array();
		while($data = $db->nextRecord())
		{
			$dataMutaties[$data['Bedrijf']][] = $data;
		}

		foreach ($dataMutaties as $bedrijf=>$mutatieData)
		{
			$exportId = date("YmdHis").'_'.$lastId;
			$tofile= 	"export_".$bedrijf."_".$exportId.".sql";
			$vulling="select '93eca1cd0a52f27c8ef5c721c8d562cd8bfb8f2b6f7096ff784e3a51003a38d354dea9844717261e38eef39bbc6f973b8ae832743921ec875c4c7b0fcd5b450b35cd4b8ce4618b0d57ea8fcde22859085add417cc9b8d8f623c8b36f1dbe94cafa119f5b9cac9c1b4588e955a1224dfc0f8f78b938319f5c0a52ab07f13fd2db416216de0eb2e9442bc289dcf4d7c8e457218d8f632ccb5d7f76456a6084bf76a62c56905aead315126fe460eb72a55d8a7d52bd49794406bea4acc6d941133598a3908176a594e589f4d0fb992d85167f415f86d69ff2bfeabd2cf4ff402996e3b916595eab3de79ede2373a664dbaa8ad407d4c323c36ef6929eda952828cb99e569442250e23031196b986625401266be5e98fb948763be73cff6991b8f7e84d3a1d4b5d25cf3c9e3461de47c79ca65f963ae6faf859674f9f22df1da6b2be7a668f8035cc1e692dfdb1f49856522d90382788912d8fe0cdb2beea586fa8d3d63752a271742f6d242357b7632bae5a933f87221f53d4dd3d2b4ce5fe221c502c1ec1a14e2ec5723340e9664b97c0ded6460f69ad4d2d71001ebbfe40d74e6cf6fd443ed2b77111a0d75d36973bcdd3b177fd44dff28d31e05b9ed56bab19a97dd1198f70f1eca78726b36462b5db718828807a308dacfe94ae553f6fefd73cf1513e6eee58e1498f2774ccde8d5f5684f25f6fb34de4518313a162dade543af23499d7d7b29fc928ec9610c08a78fb06e9e44b4ffd7c8912aa36f8ede61a368322d34c2d65dad6b6d6df6e67ca55386899fd5851b5e98c5948fb27aaae8fdccf5318dcb8f3a35fda72be323d135a959d57edef0dc75b570466eaaf4ee77a43187da74143a4e96e86b3fbcb5989032767fdec347df09b679ee94e182b661f39d617cb149eb375529f39c2823b371482235c612d12db0b97bc9e5e2e81ef0499aa19285d8e7afcaf0714b2ba84d74cb833add53678dce355f1c53deb486a955182e2c100def6f04e411af78e8ec111c99d642f2a70127f61d8bd708c4318e15b47c5b2424a73446569d3f42ae4bcc747ecf2b7fe7017d52d876070a041cbe84d25c50707f94d6e966c9124984da9070cb476e3b5e4268e6a3d886e4a36d31c9798239f815ee17cd7f3c55bcd55cb979c38d3fb7b3ae11e12662b5d3347e4cb6b744ccc524479c16bf7e361c27afefb4cf97587d994993f974e12cbcd5e8083a24b48c692e4dd9edac01009c39fd9686f6bccf9fa6d35f51f0c17e81431fcd8ce65e6fc3fd4982fde5bcf97a41ee864ebc25f5ca5be9ea5ba967dced87a8786d6e82f330b2100146df98fafe715fea33af26c95db55f084bdcc704474e9b17ad1e17e0927d1510076a5ae5cbaacfff49d6a8978c078fc7d446cc1b85572322553b025e52d' as niets ;\n";
			if($fp = fopen($__appvar['tempdir'].$tofile, 'w'))
				fwrite($fp, $vulling);
			else
				$error[]= "<br>\n FOUT: openen van ".$__appvar['tempdir'].$tofile." mislukt.";
			

			foreach ($mutatieData as $fondskoersAaanvraag) //data verzamelen
			{
				$query = "SELECT * FROM Fondskoersen WHERE Fonds = '" . mysql_real_escape_string($fondskoersAaanvraag['Fonds']) . "' AND Datum = '" . mysql_real_escape_string($fondskoersAaanvraag['Datum']) . "' ";
				$db->SQL($query);
				$db->Query();
				if($db->records())
				{
					while($data = $db->nextRecord())
					{
						$rec = serialize($data);
						$q2 = "INSERT INTO importdata SET Bedrijf = '" . $bedrijf . "', tableName = 'Fondskoersen', tableId = '" . $data['id'] . "', tableData = '" . mysql_escape_string($rec) . "', exportId = '" . $exportId . "', ";
						$q2 .= " add_user = '" . $updateUser . "', add_date = NOW() , change_user = '" . $updateUser . "', change_date = NOW() ;\n";
						fwrite($fp, $q2);
					}
				}
			}
			fclose($fp);

			if(!$this->gzcompressfile($__appvar['tempdir'].$tofile))
				$error[] = "Fout: zippen van bestand mislukt!";

			unlink($__appvar['tempdir'].$tofile);
			if(empty($error))
			{
				if($conn_id = ftp_connect($ftpSettings['server']))// login with username and password
				{
					if($login_result = ftp_login($conn_id, $ftpSettings['user'], $ftpSettings['password']))
					{
            ftp_pasv($conn_id, true);
						if (ftp_put($conn_id,$tofile.".gz",$__appvar['tempdir'].$tofile.".gz", FTP_BINARY))
							$log[] = "<br>\n successfully uploaded $tofile\n";
						else
							$error[] = "There was a problem while uploading $tofile.gz size:".filesize($__appvar['tempdir'].$tofile.".gz");
					}
					ftp_close($conn_id);
				}
				else
					$error[] = "Could not connect";
			}

			if(empty($error))
			{
				$filesize = filesize($__appvar['tempdir'].$tofile.".gz");
				$query = "INSERT INTO updates SET exportId = '".$exportId."', Bedrijf = '".$bedrijf."', type = 'userqueue', jaar = '".date('Y')."', filename = '".$tofile.".gz', filesize = '".$filesize."',
	                  server = '".$ftpSettings['server']."', username = '".$ftpSettings['user']."', password = '".$ftpSettings['password']."', consistentie = '', add_date = NOW(), add_user = '".$updateUser."',
	                  change_date = NOW(), change_user = '".$updateUser."' ";
				$queueDB = new DB(2);
				$queueDB->SQL($query);
				if($queueDB->Query())
				{
					$log[]="<br>\nUpdate in queue geplaatst om ".date('d-m-y H:i').".";
					unlink($__appvar['tempdir'].$tofile.".gz");
				}
			}
		}

		if(count($error) > 0)
		{
			echo "<br>\n<b>Error:</b><br>\n";
			foreach($error as $melding)
				echo $melding."<br>\n";
		}
		if(count($log) > 0)
		{
			echo "<br>\n<b>UpdateLog:</b><br>\n";
			foreach($log as $melding)
				echo $melding."<br>\n";
		}
		//return array('log'=>$log,'error'=>$error);
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

  
  function exportToPdf()
  {
    global $__appvar,$USR;
    define('FPDF_FONTPATH',$__appvar["basedir"]."/html/font/");
    include_once("../classes/AE_cls_fpdf.php");
    include_once("../html/rapport/rapportVertaal.php");
    include_once("../html/rapport/PDFOverzicht.php");
    $pdf = new PDFOverzicht('P','mm');
    
    $pdf->SetFont('Arial','B',8);
    $pdf->AddPage();
    $pdf->SetWidths(array(200));
    $pdf->SetAligns(array('L','L'));
    $pdf->Row(array("Fondsaanvraag print door $USR op ".date('d-m-Y H:i:s')));
    $pdf->ln();
    $pdf->SetFont('Arial','B',8);
    $pdf->SetAligns(array('L','L'));
    $pdf->SetWidths(array(60,120));
    $pdf->Row(array('Veld','Waarde'));
    $pdf->SetFont('Arial','',8);
    foreach($this->data['fields'] as $veld=>$waarden)
    { 
       if(is_array($waarden['form_options']) && $waarden['form_options'][$waarden['value']] <> '' )
       {
          $value=$waarden['form_options'][$waarden['value']];
       }
       else
         $value=$waarden['value'];
    
      $pdf->Row(array($waarden['description'],$value));
    }

   // header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	//	$pdf->Output("fondsaanvraag_".$this->get('Vermogensbeheerder').".pdf","D");
    $pdf->Output("fondskoersaanvraag.pdf",'I');
    //exit;
  }
	
	/*
  * Table definition
  */
  function defineData()
  {
    global $__appvar,$USR;
    $this->data['name']  = "";
    $this->data['table']  = "fondskoersAanvragen";
    $this->data['identity'] = "id";

		$this->addField('id',
													array("description"=>"id",
													"default_value"=>"",
													"db_size"=>"11",
													"db_type"=>"int",
													"form_type"=>"text",
													"form_size"=>"11",
													"form_visible"=>false,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('Fonds',
										array("description"  => "Fonds",
													"db_size"      => "25",
													"db_type"      => "varchar",
                          "form_select_option_notempty"=>false,
                          "select_query" =>"SELECT Fondsen.Fonds , concat(Fondsen.Fonds,' - ',Fondsen.Omschrijving) FROM Fondsen WHERE Fondsen.KoersVBH='". $__appvar['bedrijf']."' AND Fondsen.koersmethodiek=5",
                          "form_type"=>"selectKeyed",
                          "form_size"=>"10",
                          "form_visible"=>true,
													"list_width" => "150",
													"list_visible" => true,
													"list_align"   => "left",
													"list_search"  => true,
													"list_order"   => "true",
													"keyIn"        =>"Fondsen"));
  
    $this->addField('Datum',
                    array("description"=>"Datum",
                          "db_size"=>"0",
                          "db_type"=>"datetime",
                          "default_value"=>"lastworkday",
                          "form_type"=>"calendar",
                          "form_class"=> "AIRSdatepicker AIRSdatepickerPreviousMonth",
                          "form_extra"=>" onchange=\"date_complete(this);\"",
                          "form_visible"=>true,
                          "list_visible"=>true,
                          "list_align"=>"left",
                          "noHtmlspecialchars"=>true,
                          "list_search"=>false,
                          "list_order"=>"true"));
  
  
    $this->addField('Koers',
                    array("description"=>"Koers",
                          "db_size"=>"0",
                          "db_type"=>"double",
                          "form_type"=>"text",
                          "form_visible"=>true,
                          "form_format"=>"%01.8f",
                          "list_format"=>"%01.8f",
                          "list_visible"=>true,
                          "list_align"=>"right",
                          "list_search"=>false,
                          "list_order"=>"true"));
  
  
    if($__appvar['bedrijf']=='HOME')
   $verwerktZichtbaar=true;
 else
   $verwerktZichtbaar=false;  
                      
		$this->addField('verwerkt',
													array("description"=>"verwerkt",
													"default_value"=>"",
													"db_size"=>"3",
													"db_type"=>"tinyint",
													"form_type"=>"selectKeyed",
                          "form_options"=>array(-1=>'Verwijderd',0=>'Onverwerkt',2=>'Verwerkt'),
													"form_size"=>"3",
													"form_visible"=>$verwerktZichtbaar,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
  
  
  
    $this->addField('emailAdres',
                    array("description"=>"emailAdres",
                          "default_value"=>$_SESSION['usersession']['gebruiker']['emailAdres'],
                          "db_size"=>"50",
                          "db_type"=>"varchar",
                          "form_type"=>"text",
                          "form_size"=>"50",
                          "form_visible"=>true,
                          "form_extra"=>'READONLY',
                          "list_visible"=>true,
                          "list_width"=>"100",
                          "list_align"=>"left",
                          "list_search"=>false,
                          "list_order"=>"true"));
  
  
    $this->addField('add_date',
													array("description"=>"add_date",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"datetime",
													"form_type"=>"calendar",
													"form_size"=>"0",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('add_user',
													array("description"=>"add_user",
													"default_value"=>"",
													"db_size"=>"10",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"10",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('change_date',
													array("description"=>"change_date",
													"default_value"=>"",
													"db_size"=>"0",
													"db_type"=>"datetime",
													"form_type"=>"calendar",
													"form_size"=>"0",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

		$this->addField('change_user',
													array("description"=>"change_user",
													"default_value"=>"",
													"db_size"=>"10",
													"db_type"=>"varchar",
													"form_type"=>"text",
													"form_size"=>"10",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));

  }
}
?>